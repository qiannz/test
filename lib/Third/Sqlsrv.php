<?php
define("DATABASETYPE", "2"); //定义数据库类型:1为MySql;2为SQL Server;3为Oracle;4为Odbc
define("serverName", "local2.mplife.com"); //数据库服务器名称或者ip地址(local)
define("Database", "CMS"); //要连接的数据库名
define("UID", "sa"); //用于连接数据库的用户名
define("PWD", "cbsomething"); //用于连接数据库的密码	
define("CHARSET", "utf-8"); //字符集

//  define("serverName","117.135.140.14");	    //数据库服务器名称或者ip地址(local)
//	define("Database","glmts");			//要连接的数据库名
//	define("PWD","glmts@2010");				//用于连接数据库的密码	
//  define("CHARSET","gb2312");				//字符集
class Database {
    var $dbLink; //连接句柄 
    var $result; //查询句柄 
    var $insId; //Insert()成功返回AUTO_INCREMENT列的值
    var $rows; //返回数据数组
    var $numRows; //返回数据数目
    //var $dbHost, $dbUser, $userPassword, $database;
    var $serverName;
    var $uid;
    var $pwd;
    var $Database;

    function Database($serverName = serverName, $uid = UID, $pwd = PWD, $Database = Database){
        $connectionInfo = array(
                "UID"=>$uid, 
                "PWD"=>$pwd, 
                "Database"=>$Database, 
                'ReturnDatesAsStrings'=>true
        ); // 
        $this->dbLink = sqlsrv_connect($serverName, $connectionInfo);
        if($this->dbLink == false){
            echo "连接失败！";
            die(print_r(sqlsrv_errors(), true));
        }
         //'ReturnDatesAsStrings'=>true 默认值是 false，这意味着 datetime、Date、Time、DateTime2 和 DateTimeOffset 类型将返回为 PHP Datetime 类型。
    }

    //统计记录个数
    function Statistics_count($table, $columns, $condition = 1){
        $sql = "SELECT $columns FROM $table WHERE $condition ";
        $this->result = sqlsrv_query($this->dbLink, $sql);
        unset($this->rows);
        if($this->result){
            $Statistics_count = 0;
            while($row = sqlsrv_fetch_array($this->result, SQLSRV_FETCH_ASSOC)){
                $Statistics_count++;
            }
            return $Statistics_count;
        }else{
            $this->Halt($sql);
            return false;
        }
    }

    /*SQL:SQL SERVER2008() 返回为false无结果***程序分页访问数据库功能*/
    function SELECT_fenye($sql){
        $this->result = sqlsrv_query($this->dbLink, $sql);
        unset($this->rows);
        if($this->result){
            $i = 0;
            if(!($this->rows = array(
                    "$i"=>sqlsrv_fetch_array($this->result, SQLSRV_FETCH_ASSOC)
            ))) return false;
            if(($this->numRows = count(sqlsrv_num_rows($this->result))) == 0) return false;
            while($tempRows = sqlsrv_fetch_array($this->result, SQLSRV_FETCH_ASSOC)){
                array_push($this->rows, $tempRows);
            }
        }else{
            $this->Halt($sql);
            return false;
        }
        return true;
    }

    /*SQL:SQL SERVER2008() 返回为false无结果***程序分页访问数据库功能*/
    function SELECT_array_unique($sql){
        $this->result = sqlsrv_query($this->dbLink, $sql);
        unset($this->rows);
        if($this->result){
            $i = 0;
            if(!($this->rows = array_unique(array(
                    "$i"=>sqlsrv_fetch_array($this->result, SQLSRV_FETCH_ASSOC)
            )))) return false;
            if(($this->numRows = count(sqlsrv_num_rows($this->result))) == 0) return false;
            while($tempRows = sqlsrv_fetch_array($this->result, SQLSRV_FETCH_ASSOC)){
                array_push($this->rows, $tempRows);
            }
        }else{
            $this->Halt($sql);
            return false;
        }
        return true;
    }

    /*SQL:Select() 返回为false无结果*/
    function Select($table, $columns, $condition = 1){
        $sql = "select $columns from $table where $condition ";
        $this->result = sqlsrv_query($this->dbLink, $sql);
        unset($this->rows);
        if($this->result){
            $i = 0;
            if(!($this->rows = array(
                    "$i"=>sqlsrv_fetch_array($this->result, SQLSRV_FETCH_ASSOC)
            ))) return false;
            if(($this->numRows = count(sqlsrv_num_rows($this->result))) == 0) return false;
            while($tempRows = sqlsrv_fetch_array($this->result, SQLSRV_FETCH_ASSOC)){
                array_push($this->rows, $tempRows);
            }
        }else{
            $this->Halt($sql);
            return false;
        }
        return true;
    }

    /*SQL:Selectunion() UNION*/
    //UNION命令可以用来选择两个有关联的信息，和JOIN命令非常相似。然而当使用UNION命令时得保证所选择的栏目数据类型相同
    function Selectunion($table, $columns, $condition = 1, $table2, $columns2, $condition2 = 1, $rellimit){
        $sql = "(select $columns from $table where $condition) UNION (select $columns2 from $table2 where $condition2) " . $rellimit;
        $this->result = sqlsrv_query($sql, $this->dbLink);
        unset($this->rows);
        if($this->result){
            $i = 0;
            if(!($this->rows = array(
                    "$i"=>sqlsrv_fetch_array($this->result, SQLSRV_FETCH_ASSOC)
            ))) return false;
            if(($this->numRows = count(sqlsrv_num_rows($this->result))) == 0) return false;
            while($tempRows = sqlsrv_fetch_array($this->result, SQLSRV_FETCH_ASSOC)){
                array_push($this->rows, $tempRows);
            }
        }else{
            $this->Halt($sql);
            return false;
        }
        return true;
    }

    /*SQL:Selectunion() wehere*/
    //where 关联
    function Select_where($table, $columns, $condition = 1){
        $sql = "SELECT $columns FROM $table WHERE $condition";
        $this->result = sqlsrv_query($this->dbLink, $sql);
        unset($this->rows);
        if($this->result){
            $i = 0;
            if(!($this->rows = array(
                    "$i"=>sqlsrv_fetch_array($this->result, SQLSRV_FETCH_ASSOC)
            ))) return false;
            if(($this->numRows = count(sqlsrv_num_rows($this->result))) == 0) return false;
            while($tempRows = sqlsrv_fetch_array($this->result, SQLSRV_FETCH_ASSOC)){
                array_push($this->rows, $tempRows);
            }
        }else{
            $this->Halt($sql);
            return false;
        }
        return true;
    }

    /*SQL:GetRows() 返回查询的记录总数*/
    function GetRows($table, $condition = 1){
        $sql = "select count(*) as count from $table where $condition";
        $this->result = sqlsrv_query($this->dbLink, $sql);
        //$numRows=sqlsrv_num_rows($this->result);	
        if($this->result){
            $this->num = sqlsrv_num_rows($this->result);
            $this->numRows = count($this->num);
        }else{
            $this->Halt($sql);
            return false;
        }
        return $this->numRows;
    }

    //对记录操作添加、删除、修改
    /*SQL:Insert()*/
    function Insert($table, $columns, $values){
        $sql = "INSERT INTO $table($columns) VALUES ($values)";
        $this->result = sqlsrv_query($this->dbLink, $sql);
        return true;
    }

    /*SQL:Update()*/
    function Update($table, $setings, $condition){
        $sql = "UPDATE $table SET $setings WHERE $condition";
        //echo $sql;
        $this->result = sqlsrv_query($this->dbLink, $sql);
        return true;
    }

    /*SQL:Delete*/
    function Delete($table, $condition){
        $sql = "DELETE FROM $table WHERE $condition";
        $this->result = sqlsrv_query($this->dbLink, $sql);
        return true;
    }

    function Halt($msg)//出错转向
{
        if($this->msgFlag == "yes"){
            printf("<b>数据库查询错误:</b> %s<br>\n", $msg);
            printf("<b>SQL server 2008 Error:</b> %s<br>\n", sqlsrv_errors(), true);
        }else
            echo "<META HTTP-EQUIV=REFRESH CONTENT='0;URL=../'>"; //自定一个出错提示文件
        return false;
    }
}
$db = new Database();

function formathtml($convert)//取出编辑器的
{
    //$C_char=HTMLSpecialChars($C_char); //将特殊字元转成 HTML 格式。 
    //$convert=nl2br($convert); //将回车替换为<br> 
    $convert = str_replace('&lt;', '<', $convert); //替换&lt;替换为< 
    $convert = str_replace('&gt;', '>', $convert);
    $convert = str_replace("&nbsp;", " ", $convert);
    $convert = str_replace("&amp;lt;", "<", $convert);
    $convert = str_replace("", "", $convert);
    $convert = str_replace("&#039;", "'", $convert);
    $convert = str_replace('&quot;', '"', $convert);
    $convert = str_replace("&amp;gt;", ">", $convert);
    $convert = str_replace("&amp;nbsp;", " ", $convert);
    $convert = str_replace("\&quot;", '"', $convert);
    //$convert=str_replace('&lt;h1&gt;','',$convert);
    //$convert=str_replace('&lt;/h1&gt;','',$convert);
    //$convert=str_replace('&lt;H1&gt;','',$convert);
    //$convert=str_replace('&lt;/H1&gt;','',$convert);
    //$convert=str_replace('<h1>','',$convert);
    //$convert=str_replace('</h1>','',$convert); 
    //$convert=str_replace('<H1>','',$convert);
    //$convert=str_replace('</H1>','',$convert);
    //$convert=str_replace('<IMG','<IMG alt=中国床垫网',$convert);
    //$convert=str_replace('&lt;IMG','<IMG alt=中国床垫网',$convert);
    return $convert;
}

function convert_to_html($C_char)//存入编辑器的html
{
    $C_char = str_replace('<', '&lt;', $C_char); //替换<替换为 &lt;
    $C_char = str_replace('>', '&gt;', $C_char);
    $C_char = str_replace(' ', '&nbsp;', $C_char);
    $C_char = str_replace('<', '&amp;lt;', $C_char);
    $C_char = str_replace('\"', '"', $C_char);
    $C_char = str_replace('"', '&quot;', $C_char);
    $C_char = str_replace("'", '&#039;', $C_char);
    $C_char = str_replace('', '', $C_char);
    $C_char = str_replace('>', '&amp;gt;', $C_char);
    $C_char = str_replace(' ', '&amp;nbsp;', $C_char);
    return $C_char;
}

//友情提醒
function Exitmessage($message){
    echo "<script language=\"JavaScript\">\r\n";
    echo " alert(\"" . $message . "\");\r\n";
    echo "history.back();\r\n";
    echo "</script>";
    exit();
}

function shuiyin($file, $shuiyin, $uploadfile){
    include_once ("../../administor/admin/configs/PhpUpImageWater.php");
    $h = new PhpUpImageWater("$file", 5);
    $h->setWaterImageInfo("$shuiyin", 10); //透明度是png,要求必须得要没有背景
    //$h->setWaterTextInfo("上海泛雅科技实业有限公司","#f5f5f5","20"); //文字汉字乱码，注意设置一下文字是什么字体
    $h->makeWater("");
     //$h->makeWater("wa_"); 
}
?>