<?php
/**
 * Created on 2011-6-9
 *
 */
class Third_FastDFS {
    /*
 	 * @param obj
 	 */
    private $connect_server;
    private $server;
    
    /*
     * 伪单例function
     */
    private static $instance;
    public static function getInstance(){
        if(self::$instance === NULL){
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function get_connect_state(){
        return $this->connect_server;
    }
    
    /*
	 *connect to the server
	 *@return false or array
	 */
    public function get_connect_server(){
        $fdfs_tracker_servers = $GLOBALS['GLOBAL_CONF']['fdfs_tracker_servers'];
        $fdfs_tracker_server_count = count($fdfs_tracker_servers);
        $fdfs_tracker_server_index = $GLOBALS['GLOBAL_CONF']['fdfs_tracker_server_index'];
        
        if($fdfs_tracker_server_count == 0){
            error_log(date('Y-m-d H:i:s') . "no tracker server!\n", 3, LOG_PATH . 'fdfs_tracker_error.log');
            return false;
        }
        $fdfs_tracker_server_index = rand(0, $fdfs_tracker_server_count - 1); //随即获取连接的服务器
        $tracker = $fdfs_tracker_servers[$fdfs_tracker_server_index];
        
        if(isset($this->server) && is_array($this->server) && ($this->server['sock'] > 1)){
            return $this->server['ip_addr'];
        }
        $this->server = $this->connect_server($tracker['ip_addr'], $tracker['port']);
        
        if(($this->server['sock'] > 1) && is_array($this->server)){
            $fdfs_tracker_server_index++;
            if($fdfs_tracker_server_index >= $fdfs_tracker_server_count){
                $fdfs_tracker_server_index = 0;
            }
            return $tracker;
        }
        
        for($i = $fdfs_tracker_server_index + 1; $i < $fdfs_tracker_server_count; $i++){
            $this->server = $this->connect_server($fdfs_tracker_servers[$i]['ip_addr'], $fdfs_tracker_servers[$i]['port']);
            if(($this->server['sock'] > 1) && is_array($this->server)){
                return $fdfs_tracker_servers[$i];
            }
        }
        
        for($i = 0; $i < $fdfs_tracker_server_index; $i++){
            $this->server = $this->connect_server($fdfs_tracker_servers[$i]['ip_addr'], $fdfs_tracker_servers[$i]['port']);
            if(($this->server['sock'] > 1) && is_array($this->server)){
                return $fdfs_tracker_servers[$i];
            }
        }
        
        return false;
    }
    
    /*
	 * 根据文件路径上传图片
	 * @param string $filePath (*file path)
	 * @param sting $file_ext_name
	 * @param array $meta_list
	 * @param string $group_name
	 * @param array $tracker_server
	 * @param array $storage_server
	 */
    public function upload_by_filename($filePath, $file_ext_name, $meta_list, &$group_name, &$remote_filename, $tracker_server = NULL, $storage_server = NULL){
        if(!empty($filePath)){
            $this->get_connect_server();
            
            //获取链接
            $file_info = $this->storage_upload_by_filename($filePath, $file_ext_name, $meta_list, $group_name);
            if($file_info){
                $remote_filename = $file_info["filename"];
                $group_name = $file_info["group_name"];
                $this->disconnect_server($this->server);
                return 0;
            }
        
        }
        $this->disconnect_server($this->server);
        return 1;
    
    }
    
    /*
	 * 根据二进制流上传图片
	 * @param string $file_buff (*file buff)
	 * @param sting  $file_ext_name
	 * @param array  $meta_list
	 * @param string $group_name
	 * @param array  $tracker_server
	 * @param array  $storage_server
	 **/
    public function storage_upload_by_filebybuff($tracker_server, $storage_server, $file_buff, $file_ext_name, $meta_list, &$group_name, &$remote_filename){
        if($file_buff != false){
            $this->get_connect_server();
            if(!empty($group_name)){
                $file_info = $this->storage_upload_by_filebuff($file_buff, $file_ext_name, $meta_list, $group_name); //二进制流
            }else{
                $file_info = $this->storage_upload_by_filebuff($file_buff, $file_ext_name, $meta_list); //二进制流
            }
            if($file_info){
                $remote_filename = $file_info["filename"];
                $group_name = $file_info["group_name"];
                $this->disconnect_server($this->server);
                return 0;
            }
        
        }
        $this->disconnect_server($this->server);
        return 1;
    }
    
    /*
	 * 读取图片get file content from storage server
	 * @param string $group_name *
	 * @param sting  $remote_filename *
	 * @param long   $file_offset
	 * @param long   $download_bytes
	 * @param array  $tracker_server
	 * @param array  $storage_server
	 */
    public function download_file_to_buff($group_name, $remote_filename, $file_offset = 0, $download_bytes = 0, $tracker_server = array(), $storage_server = array()){
        
        $this->get_connect_server();
        $file_content = "";
        
        $file_content = $this->storage_download_file_to_buff($group_name, $remote_filename);
        $this->disconnect_server($this->server);
        
        return $file_content;
    
    }
    
    /*
	 * delete file from storage server
	 * @param string $group_name *
	 * @param sting  $remote_filename *
	 * @param array  $tracker_server
	 * @param array  $storage_server
	 */
    public function delete_file($group_name, $remote_filename, $tracker_server = NULL, $storage_server = NULL){
        
        $this->connect_server = $this->get_connect_server();
        
        $state = $this->storage_delete_file($group_name, $remote_filename);
        $this->disconnect_server($this->server);
        return $state;
    }
    
    /**
     * @param string $group_name
     * @param string $remote_filename
     * @param string $local_filename
     * @return true for success, false for error
     */
    public function download_file_to_creat($group_name, $remote_filename, $local_filename){
        $state = $this->fastdfs_storage_download_file_to_file($group_name, $remote_filename, $local_filename);
        return $state;
    }
    
    /**
     * get meta data of the file
     * @param string $group_name
     * @param string $remote_filename
     * @param array  $meta_list
     * @param resource $tracker_server
     * @param resource $storage_server
     * @return integer
     */
    public function get_metadate($group_name, $remote_filename, &$meta_list, $tracker_server = NULL, $storage_server = NULL){
        $this->get_connect_server();
        $meta_list = $this->storage_get_metadata($group_name, $remote_filename);
        $this->disconnect_server($this->server);
        return is_array($meta_list) ? 0 : 1;
    }
    
    /**
     * 设置头信息
     * @param string $group_name
     * @param string $remote_filename
     * @param array $meta_list
     * @return return true for success, false for error
     */
    public function setMetaData($group_name, $remote_filename, $meta_list){
        $this->get_connect_server();
        $reulst = $this->storage_set_metadata($group_name, $remote_filename, $meta_list);
        $this->disconnect_server($this->server);
        return $reulst;
    }
    
    /**
     * disconnect server
     * @param resource $server
     * @return null
     */
    public function disconnect_server($server){
        if(isset($server) && isset($this)){
            $this->tracker_close_all_connections();
            $this->disconnect_server($server);
            unset($server);
        }
    }
    
    /**
     * slave file
     * @param string $file_buff
     * @param string $prefix_name
     * @param string $group_name
     * @param string $remote_filename
     * @param integer $with
     * @param integer $height
     * @return  assoc array for success, false for error.
     * the returned array includes elements: group_name and filename
     */
    public function slave_file($file_buff, $prefix_name, $group_name, $remote_filename, $with, $height, $meta_list = array()){
        $prefix_name = self::getFilePrefix($prefix_name, $with, $height);
        $this->get_connect_server();
        $slave_file_info = $this->storage_upload_slave_by_filebuff($file_buff, $group_name, $remote_filename, $prefix_name, '', $meta_list);
        //        var_dump($this->get_last_error_no());
        //        var_dump($this->get_last_error_info());
        //    	if (is_array($slave_file_info) && $meta_list){//上传头信息
        //            $this->setMetaData($slave_file_info['group_name'], $slave_file_info['filename'], $meta_list);
        //    	}
        $this->disconnect_server($this->server);
        return $slave_file_info;
    
    }
    
    /**
     * generate slave filename by master filename, prefix name and file extension name
     * @param string $master_filename
     * @param string $prefix_name
     * @param string $file_ext_name
     */
    public function genSlaveFileName($master_filename, $prefix_name, $file_ext_name = ''){
        $this->get_connect_server();
        $filename = $this->gen_slave_filename($master_filename, $prefix_name, $file_ext_name);
        $this->disconnect_server($this->server);
        return $filename;
    }
    
    /**
     * 获取slave名字
     * @param string  $prefix_name
     * @param integer $with
     * @param integer $height
     * @return string
     */
    public static function getFilePrefix($prefix_name, $with = 0, $height = 0, $mastFile = ''){
        if($with > 0 || $height > 0){
            $prefix_name = '_' . $with . '_' . $height . '.' . $prefix_name;
        }
        if(!empty($mastFile)){
            $pattern = "/([0-9a-zA-z]{2}\/)([0-9a-zA-z]{2}\/)(.*)\.([a-zA-z]{3})/i";
            $replacement = "\${1}\${2}\${3}";
            $mastFile = preg_replace($pattern, $replacement, $mastFile);
            $prefix_name = $mastFile . $prefix_name;
        }
        return $prefix_name;
    
    }
    
    /**
     * 析造函数
     *
     * @return void
     */
    public function __destruct(){
        $this->disconnect_server($this->server);
    }
}
?>
