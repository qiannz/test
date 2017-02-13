<?php
class Model_Admin_Day extends Base {
	private static $_instance;
	protected $_where;
    protected $_monday;

	public static function getInstance() {
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function __construct() {
		parent::__construct();
		$this->_where = '';
        $this->_monday = strtotime(date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y"))));
	}

    public function setWhere($getData) {
        if($getData['user_name']){
            $where .= "  and user_name = '{$getData['user_name']}'";
        }
        if (($getData['start_time']) && ($getData['end_time'])){
            $stime = strtotime($getData['start_time']);
            $etime = strtotime($getData['end_time']);
            $where .= "  and created >= '{$stime}'";
            $where .= "  and created <= '{$etime}'";
        }
        $this->_where = $where;
    }

    public function getDayList($page, $pagesize = PAGESIZE) {
        $start = ($page - 1) * $pagesize;
        if(!$this->_where){
            $sql = "select * from oto_task_every_day  where created >= '{$this->_monday}' order by created desc";
        }else{
            $sql = "select * from oto_task_every_day  where 1=1" . $this->_where . " order by created desc";
        }
        $result = $this->_db->limitQuery($sql, $start, $pagesize);
        return $result?$result:array();
    }

    public function getCount() {
        if(!$this->_where){
            return $this->_db->fetchOne("select count(*) from oto_task_every_day where created >='{$this->_monday}'");
        }else{
            return $this->_db->fetchOne("select count(*) from oto_task_every_day where 1=1". $this->_where ."");
        }
    }

    public function getStatistics(){
        if(!$this->_where){
            $sql ="select count(distinct user_id)  as total,sum(award) as total_amount from oto_task_every_day where created >= '{$this->_monday}'";
        }else{
            $sql ="select count(distinct user_id) as total,sum(award) as total_amount from oto_task_every_day where 1=1 ".$this->_where ."";
        }
        return $this->_db->fetchRow($sql);
    }

}