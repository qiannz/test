<?php
/**
 * 过滤器, 按关键字过滤
 * Custom_KeywordFilter.php
 */
class Custom_KeywordFilter {
    private $keyword_file;
    private $dict;
    public $result;
    private static $_instance;
    
    public function __construct(){
        $this->keyword_file = file(dirname(__FILE__) . '/keywords.txt');
        $this->keyword_file = array_unique(array_filter(array_map('trim', $this->keyword_file)));
    }
    
    public function filter($resource){
        $this->dict = $this->getDict();
        $len = strlen($resource);
        for($i = 0; $i < $len; ++$i){
            $key = substr($resource, $i, 2);
            if(array_key_exists($key, $this->dict)){
                $this->deal(substr($resource, $i, $this->dict[$key]['max']), $key, $af);
                $i += $af;
            }else{
                $this->result .= substr($resource, $i, 1);
            }
        }
        return $this->result;
    }
    
    /**
     * 匹配到了关键字时的处理
     *
     * @param String $res 源字符串
     * @param Array  $key 关键字数组
     * @param unknown_type $af
     */
    private function deal($res, $key, &$af){
        $af = 0;
        foreach($this->dict[$key]['list'] as $keyword){
            if(strpos($res, $keyword) !== false){
                $len = strlen($keyword);
                $af = $len - 1;
                $this->result .= str_repeat("*", ceil($len / 3));
                return;
            }
        }
        $this->result .= substr($res, 0, 1);
    }
    
    private function getDict(){
        $keywords = $this->keyword_file;
        $dict = array();
        foreach($keywords as $keyword){
            if(empty($keyword)){
                continue;
            }
            $key = substr($keyword, 0, 2);
            $dict[$key]['list'][] = $keyword;
            @$dict[$key]['max'] = max($dict[$key]['max'], strlen($keyword));
        }
        return $dict;
    }
}