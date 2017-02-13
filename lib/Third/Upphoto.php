<?php

class Upphoto {
    private $previewsize = 0.125; //预览图片比例
    public  $preview = 0; //是否生成预览，是为1，否为0
    private $datetime; //随机数
    public  $ph_name; //上传图片文件名
    private $ph_tmp_name; //图片临时文件名
    private $ph_path = "./user_img/"; //上传文件存放路径
    private $ph_type; //图片类型
    private $ph_size; //图片大小
    private $imgsize; //上传图片尺寸，用于判断显示比例
    private $al_ph_type = array(
            'image/jpg', 
            'image/jpeg', 
            'image/png', 
            'image/pjpeg', 
            'image/gif', 
            'image/bmp', 
            'image/x-png'
    ); //允许上传图片类型
    private $al_ph_size = 1000000; //允许上传文件大小
    
    function __construct(){
        $this->set_datatime();
    }

    public function set_datatime(){
        $this->datetime = date("YmdHis").rand(1000, 9999);
    }

    //获取文件存放路径
    public function get_ph_path($path){
        $this->ph_path = $path;
    }

    //获取文件类型
    public function get_ph_type($phtype){
        $this->ph_type = $phtype;
    }

    //获取文件大小
    public function get_ph_size($phsize){
        $this->ph_size = $phsize . "<br>";
    }

    //获取上传临时文件名
    public function get_ph_tmpname($tmp_name){
        $this->ph_tmp_name = $tmp_name;
        $this->imgsize = getimagesize($tmp_name);
    }

    //获取原文件名
    public function get_ph_name($phname){
        $this->ph_name = $this->ph_path . $this->datetime . strrchr($phname, "."); //strrchr获取文件的点最后一次出现的位置
        //$this->ph_name=$this->datetime.strrchr($phname,"."); //strrchr获取文件的点最后一次出现的位置
        return $this->ph_name;
    }

    //判断上传文件存放目录
    public function check_path(){
        if(!file_exists($this->ph_path)){
            mkdir($this->ph_path);
        }
    }

    //判断上传文件是否超过允许大小
    public function check_size(){
        if($this->ph_size > $this->al_ph_size){
            $this->showerror("上传图片超过2000KB:" . $this->ph_size);
        }
    }

    //判断文件类型
    public function check_type(){
        if(!in_array($this->ph_type, $this->al_ph_type)){
            $this->showerror("上传图片类型错误:" . $this->ph_type);
        }
    }

    //上传图片
    public function up_photo(){
        if(!move_uploaded_file($this->ph_tmp_name, $this->ph_name)){
            $this->showerror("上传文件出错");
        }
    }

    //图片预览
    public function showphoto(){
        if($this->preview == 1){
            if($this->imgsize[0] > 2000){
                $this->imgsize[0] = $this->imgsize[0] * $this->previewsize;
                $this->imgsize[1] = $this->imgsize[1] * $this->previewsize;
            }
            echo ("<img src=\"{$this->ph_name}\" width=\"{$this->imgsize['0']}\" height=\"{$this->imgsize['1']}\">");
        }
    }

    //错误提示
    public function showerror($errorstr){
        echo "<script language=javascript>alert('$errorstr');location='javascript:history.go(-1);';</script>";
        exit();
    }

    /*
	 * @param string $formFile file文件的name值
	 * @param string $path 上传路径
     */
    public function save($formFile, $path){             
        $this->check_path();
        $this->check_size();
        $this->check_type();
        $this->up_photo();
        $this->showphoto();
    }
}