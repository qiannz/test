<?php

/**
 * 日志
 *
 */

class Custom_Log {

	/**
	 * 记录日志
	 * @param unknown_type $aid 管理员ID
	 * @param unknown_type $content 内容
	 * @param unknown_type $pmodule 一级模块
	 * @param unknown_type $cmodule 二级模块
	 * @param unknown_type $activity 动作/行为
	 * @param unknown_type $time 时间
	 */
    public static function log($aid, $content, $pmodule, $cmodule, $activity, $type = NULL, $from_id = 0, $time = NULL)
    {
    	$db = Core_DB::get('superbuy', null, true);
    	if(!in_array($activity, 
		    			array
		    			(
		    				'add', //添加
		    				'del', //删除
		    				'delAll', //批量删除
		    				'unDel', //取消删除
		    				'edit', //编辑
		    				'mod',  //修改
		    				'audit', //审核
		    				'gag', //禁言
		    				'black', //黑名单
		    				'ip', //封IP
		    				'recommend', // 推荐
		    				'top', // 置顶
		    				'merge', //店铺合并
		    				'export', //导出
                            'loans', //放款
                            'awards', //发奖
		    			)
    			)
    		) {
    		return false;
    	}
    	$time = is_null($time) ? REQUEST_TIME : $time;
    	$logArray = array(
							'admin_id' => $aid,
							'pmodule' => $pmodule,
					        'cmodule'  => $cmodule,
    						'from_id' => $from_id,
							'activity' => $activity,
    						'type' => $type,
							'operat_info' => saddslashes($content),					
							'created' => $time
						);  
        $db->insert('oto_log_log', $logArray);
    }
    /**
     * 刮奖流水
     * @param unknown_type $user_id 刮奖人
     * @param unknown_type $logMsgArray 刮奖详情 （刮奖状态 100 ：成功  101：失败  102：今日奖励已发完  103：未中奖）
     * @param unknown_type $task_type 刮奖类型  （client：街友  clerk：店员)
     */
    public static function logLog($user_id, $logMsgArray, $task_type = 'client') {
    	$db = Core_DB::get('superbuy', null, true);
    	$logArray = array(
    			'user_id' => $user_id,
    			'task_type' => $task_type,
    			'log_msg' => serialize($logMsgArray),
    			'ip' => CLIENT_IP,
    			'created' => REQUEST_TIME
    	);
    	$db->insert('oto_task_log_log', $logArray);
    }
}