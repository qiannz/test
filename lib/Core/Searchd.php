<?php

/**
 * Search Class
 *
 */
require_once ROOT_PATH . 'lib/Third/SphinxApi.php';

class Core_Searchd {
    
    protected $_searchType = array('other', 'user', 'article');
    protected $_cfg = array();
    protected $_excerptsFields = array();
    protected $_excerptsOptions = NULL;
    protected $_option = array();
    protected $_weights = array();
    protected $_indexs = '*';
    protected $_filter = array();
    protected $_sortBy = array();
    protected $_limit = 20;
    protected $_offset = 0;
    protected $_keyword = '';
    protected $_matchMode = SPH_MATCH_ANY;
    protected $_rankingMode = '';
    protected $_sortMode = '';
    protected $_sphinx;
    protected $_ignore = false;

    /**
     * 构造函数 实例化API
     */
    public function __construct(){
        $this->_sphinx = new SphinxClient();
        
        $servers = $GLOBALS['GLOBAL_CONF']['Sphinx_CoreSeek_Servers'];
        shuffle($servers); // 乱序随机选择
        

        // 随机选一台连上就OK
        foreach($servers as $host){
            $arr = explode(':', $host);
            $host = isset($arr[0]) ? $arr[0] : '';
            $port = isset($arr[1]) ? (int) $arr[1] : 9313;
            if(!$host){
                continue;
            }
            $this->_sphinx->SetServer($host, $port);
            if($this->_sphinx->status()){
                break;
            }
        }
        
        $this->_sphinx->SetConnectTimeout(3);
        $this->_sphinx->SetArrayResult(true);
    }
    
    /**
     * 初始化设置
     *
     * @param string $keyword
     * @param array $option
     * @param string $config
     */
    public function init($keyword, $option = array()){
        $this->_keyword = trim($keyword);
        $this->_option = $option;
        
        $this->_setConfig();
        $this->_setKeyword();
        $this->_setOption();
        
        // 设置匹配模式
        $this->_sphinx->SetMatchMode($this->_matchMode);
        
        // 设置偏移量  TODO 坑爹的偏移量
        $this->_sphinx->SetLimits($this->_offset, $this->_limit, ($this->_limit > 999) ? $this->_limit : 999);
        
        // 设置字段权重
        if($this->_weights){
            $this->_sphinx->SetFieldWeights($this->_weights);
        }
    }
    
    /**
     * 载入配置
     * 
     * @param string $type
     * @param string $perfix 
     */
    public function loadConfig($type, $perfix = ''){
        $this->_type = $perfix . $type;
        
        // 未定义类型
        if(!in_array($type, $this->_searchType)){
            // 未实现
            return false;
        }
        
        // 载入指定类型配置文件
        $this->_cfg = include ROOT_PATH . 'resource/search/' . $this->_type . '.php';
    }
    
    /**
     * 设置配置项
     *
     * @param string|array $index 配置项下标
     * @param string $value 配置项的值
     */
    public function setConfig($index, $value = null){
        if(is_array($index)){
            $this->_cfg = array_merge($this->_cfg, $index);
        }else{
            $this->_cfg[$index] = $value;
        }
    }
    
    /**
     * 设置关键词
     *
     */
    private function _setKeyword(){
        if($this->_keyword){
            // 字符串编码转换
            $this->_keyword = Custom_String::strToUtf8($this->_keyword);
            // 转义特殊字符
            //$this->_keyword = $this->_sphinx->EscapeString($this->_keyword);
            if(substr_count($this->_keyword, '#') == 2){
                // TODO 半实现
                $this->_keyword = '"' . $this->_keyword . '"';
                $this->_matchMode = SPH_MATCH_EXTENDED2;
            }else{
                $this->_matchMode = SPH_MATCH_EXTENDED2;
            }
            $this->_sphinx->SetRankingMode(SPH_RANK_SPH04);
        }else{
            // 关键字为空时，自动启用完整扫描模式，前提条件：查询字符串为空，docinfo 存储方式为 extern
            // 覆盖默认设置
            $this->_matchMode = SPH_MATCH_FULLSCAN;
        }
    }
    
    private function _setConfig(){
        // 忽略，当回调函数返回数组下标不统一时，暂用于动态
        if(substr($this->_type, -5) == $this->_searchType[0]){
            $this->_ignore = true;
        }
        
        // 高亮字段与选项
        if(isset($this->_cfg['excerpts']) && is_array($this->_cfg['excerpts'])){
            $this->_excerptsFields = array_shift($this->_cfg['excerpts']); // 高亮指定字段
            $this->_excerptsOptions = $this->_cfg['excerpts']; // 高亮选项
        }
        
        // 字段权重
        if(isset($this->_cfg['weights']) && is_array($this->_cfg['weights'])){
            $this->_weights = $this->_cfg['weights'];
        }
        
        // 指定索引
        if(isset($this->_cfg['indexs']) && !empty($this->_cfg['indexs'])){
            $this->_indexs = $this->_cfg['indexs'];
        }
        
        // 限定过滤字段
        if(isset($this->_cfg['filter']) && !empty($this->_cfg['filter'])){
            $this->_filter = $this->_cfg['filter'];
        }
        
        // 排序
        if(isset($this->_cfg['sort_by']) && !empty($this->_cfg['sort_by'])){
            $this->_sortBy = $this->_cfg['sort_by'];
        }
        
        // 排序模式
        if(isset($this->_option['sort_mode']) && !empty($this->_option['sort_mode'])){
            //$this->_sortMode = $this->_option['sort_mode'];
        }
        
        // 评分模式
        if(isset($this->_option['ranking_mode']) && !empty($this->_option['ranking_mode'])){
            //$this->_rankingMode = $this->_option['ranking_mode'];
        }
        
        // 匹配模式
        if(isset($this->_option['match_mode']) && !empty($this->_option['match_mode'])){
            //$this->_matchMode = $this->_option['match_mode'];
        }
    }
    
    /**
     * 设置选项
     *
     * @param array $option
     */
    private function _setOption(){
        if(isset($this->_option["limit"]) && (intval($this->_option["limit"]) > 0)){
            $this->_limit = intval($this->_option["limit"]);
        }
        
        if(isset($this->_option["offset"]) && (intval($this->_option["offset"]) > 0)){
            $this->_offset = intval($this->_option["offset"]);
        }
    }
    
    /**
     * 过滤
     */
    public function setFilters($params){
        // 清除过滤器
        $this->_sphinx->ResetFilters();
        
        // debug
        Core_Request::getInstance()->getQuery('debug') && print_r($params);
        
        // 设置过滤
        foreach($params as $attribute=>$value){
            // 非指定字段跳过
            if(!in_array($attribute, $this->_filter)){
                continue;
            }
            if(is_numeric($value)){
                $this->_sphinx->SetFilter($attribute, array($value)); // 无操作符，适用于city = 1
            }else if(is_array($value) && !empty($value)){ // 有操作符
                if(1 == count($value)){ // 仅有一个操作符
                    $op = key($value);
                    if('$ne' == $op){ // 不等于，适用于city <> 1
                        if(is_numeric($value['$ne'])){ // 传字符串转换成数组，传数组直接使用，适用于 '$ne' => array(1) 或 '$in' => 1
                            $value['$ne'] = array($value['$ne']);
                        }
                        $this->_sphinx->SetFilter($attribute, $value['$ne'], true);
                    }else if('$in' == $op){ // in，适用于city = 1 or city = 2 or ...
                        if(is_numeric($value['$in'])){ // 传字符串转换成数组，传数组直接使用，适用于 '$in' => array(1,2,3) 或 '$in' => '1,2,3'
                            $value['$in'] = array($value['$in']);
                        }
                        $this->_sphinx->SetFilter($attribute, $value['$in']);
                    }else if('$nin' == $op){ // not in，适用于city <> 1 or city <> 2 or ...
                        if(is_numeric($value['$nin'])){ // 传字符串转换成数组，传数组直接使用，适用于 '$nin' => array(1,2,3) 或 '$nin' => '1,2,3'
                            $value['$nin'] = array($value['$nin']);
                        }
                        $this->_sphinx->SetFilter($attribute, $value['$nin'], true);
                    }
                }else if(2 == count($value)){ // 有两个操作符，则为范围操作，即between
                    /*if (isset($value['$lt']) && isset($value['$gt'])) { // 如果有 < 和 > 操作符，< 和 > 即为 >= 和 <= 的反选
                        if (is_int($value['$lt']) && is_int($value['$gt'])) { // 如果为整型
                            $this->_sphinx->SetFilterRange($attribute, $value['$lt'], $value['$gt'], true);
                        } else if (is_float($value['$lt']) && is_float($value['$gt'])) { // 如果为浮点型
                            $this->_sphinx->SetFilterFloatRange($attribute, $value['$lt'], $value['$gt'], true);
                        }
                    } else*/
                    if(isset($value['$lte']) && isset($value['$gte'])){ // 如果有 <= 和 >= 操作符
                        if(is_int($value['$lte']) && is_int($value['$gte'])){ // 如果为整型
                            $this->_sphinx->SetFilterRange($attribute, $value['$gte'], $value['$lte']);
                        }else if(is_float($value['$lte']) && is_float($value['$gte'])){ // 如果为浮点型
                            $this->_sphinx->SetFilterFloatRange($attribute, $value['$gte'], $value['$lte']);
                        }
                    }
                }
            }
        }
        
        // 按经纬度过滤
        if(isset($params['_geo']) && !empty($params['_geo'])){
            // 计算距离
            $this->_sphinx->SetGeoAnchor('latitude', 'longitude', deg2rad($params['_geo']['lat']), deg2rad($params['_geo']['lon']));
            // 范围过滤
            $this->_sphinx->SetFilterFloatRange('@geodist', (float) $params['_geo']['min'], (float) $params['_geo']['max']);
        }
    }
    
    /**
     * 排序
     *
     * @param array $params
     */
    public function setSortBy($params){
        $params = empty($params['order_by']) ? array('@id'=>'DESC') : $params['order_by'];
        
        // 设置排序
        $sortby = array();
        foreach($params as $k=>$v){
            // 仅处理配置文字中指定的类型
            if(in_array(trim($k), $this->_sortBy)){
                $sortby[] = trim($k) . ' ' . strtoupper(trim($v)); // eg. create_time DESC
            }
        }
        
        $this->_sphinx->SetSortMode(SPH_SORT_EXTENDED, implode(', ', $sortby));
    }
    
    /**
     * 添加一条批量查询
     */
    public function addQuery(){
        $this->_sphinx->AddQuery($this->_keyword, $this->_indexs);
    }
    
    /**
     * 处理
     *
     * @param array $data sphinx返回的匹配数组
     * @return array
     */
    public function process($data){
        // 初始化返回数组
        $rs = array('ids'=>array(), 'list'=>array(), 'total'=>isset($data['total']) ? $data['total'] : 0, // 用于分页的总数
'total_found'=>isset($data['total_found']) ? $data['total_found'] : 0, // 用于显示的总数
'error'=>isset($data['error']) ? $data['error'] : '', 'warning'=>isset($data['warning']) ? $data['warning'] : '', 'time'=>isset($data['warning']) ? $data['warning'] : '', 'words'=>isset($data['warning']) ? $data['warning'] : array());
        
        // 无匹配时直接返回空数组
        if(!isset($data['matches'])){
            return $rs;
        }
        
        // 获取匹配ID数组
        $ids = array();
        foreach($data['matches'] as $v){
            $ids[] = $v['id'];
        }
        
        // 无回调函数直接返回ID数组供模块自己处理
        if(!isset($this->_cfg['callback'])){
            $rs['ids'] = $rs['list'] = $ids;
            return $rs;
        }
        
        // 回调指定模型方法，获取所有数据。有参数则传参数
        $list = isset($data['_params']) ? $this->_getList($ids, $data['_params']) : $this->_getList($ids);
        
        // 保持原有顺序
        $tmp = array();
        foreach($ids as $k=>$id){
            if($this->_ignore){
                // 无关联索引时直接取下标
                if(isset($list[$k])){
                    $tmp[$id] = $list[$k];
                }else{
                    // 无相应数据，总数递减
                    $data['total']--;
                    $data['total_found']--;
                }
            }else{ // 匹配关联索引，保持顺序
                if(isset($list[$id])){
                    $tmp[$id] = $list[$id];
                }else{
                    // 无相应数据，总数递减
                    $data['total']--;
                    $data['total_found']--;
                }
            }
        }
        
        // 赋处理过的新值
        $list = $tmp;
        
        // 关键字高亮
        if($this->_excerptsOptions !== NULL){
            $list = $this->_highLight($list);
        }
        
        // 赋新值
        $rs['ids'] = $ids;
        $rs['list'] = $list;
        $rs['total'] = max(0, $data['total']); // 防递减后为负值，下同
        $rs['total_found'] = max(0, $data['total_found']);
        
        return $rs;
    }
    
    /**
     * 从指定模型取数据
     *
     * @param array $ids sphinx返回的ids
     * @param array $params 为回调函数附加的参数，默认为NULL
     * @return array
     */
    private function _getList($ids, $params = NULL){
        $config = $this->_cfg['callback'];
        
        // 类名
        $className = $config[0];
        // 方法名
        $function = $config[1];
        
        // 实例化
        $handler = new $className();
        // 是否带参数
        return $params ? $handler->$function($ids, $params) : $handler->$function($ids);
    }
    
    /**
     * 高亮
     *
     * @param array $list
     * @return array
     */
    private function _highLight($list){
        // 修改原数组，所以用引用
        foreach($list as &$row){
            // 不实现了，微博全是对象，如果改动了，对应的模板全要改
            //            if (!is_array($row) && !is_object($row)) {
            //                continue;
            //            }
            //
            //            if (is_object($row)) {
            //                $row = $row->feed;
            //                $row = get_object_vars($row);
            //            }
            // 非数组跳过
            if(!is_array($row)){
                continue;
            }
            
            // 仅高亮指定字段
            $docs = array();
            foreach($this->_excerptsFields as $index){
                $docs[$index] = $row[$index];
            }
            
            // 高亮指定字段
            $index = current(explode(" ", $this->_indexs));
            $index = str_replace(array('_local', '_dist'), array('0', '0'), $index);
            $rs = $this->_sphinx->BuildExcerpts(array_values($docs), $index, $this->_keyword, $this->_excerptsOptions, true);
            
            // 创建一个高亮后的数组
            $newDocs = empty($rs) ? $docs : array_combine(array_keys($docs), $rs);
            
            // 合并到原数组
            $row = array_merge($row, $newDocs);
        }
        
        return $list;
    }
    
    /**
     * 可单独调用实现高亮
     *
     * @param array $docs 要高亮的字符数组
     * @param string $index 索引，多个用空格隔开
     * @param string $words 要高亮的关键词
     * @param array $opts 高亮选项
     * @return array
     */
    public function highLight($docs, $index, $words, $opts = array()){
        return $this->_sphinx->BuildExcerpts($docs, $index, $words, $opts);
    }
    
    // 回调函数
    public function __call($name, $arguments){
        return call_user_func_array(array($this->_sphinx, $name), $arguments);
    }

}
