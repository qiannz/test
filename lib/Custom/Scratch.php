<?php
/**
 * 刮奖函数
 * @author qiannz
 *
 */
class Custom_Scratch {
	/**
	 * 街友刮奖
	 */
	public static function client() {
		$randArray = array();
		//街友中奖数组
		$effectiveArray = self::getEffectiveArray('CLIENT_EFFECTIVE');
		foreach ($effectiveArray as $key => $val) {
			$randArray[$val['id']] = $val['v'];
		}
		$rid = self::getRand($randArray); //根据概率获取奖项id
		return $effectiveArray[$rid - 1];	
	}
	/**
	 * 店员刮奖
	 */
	public static function clerk() {
		$randArray = array();
		//店员中奖数组
		$effectiveArray = self::getEffectiveArray('CLERK__EFFECTIVE');
		foreach ($effectiveArray as $key => $val) {
			$randArray[$val['id']] = $val['v'];
		}
		
		$rid = self::getRand($randArray); //根据概率获取奖项id
		return $effectiveArray[$rid - 1];
	}
	
	private static function getEffectiveArray($config_key) {
		$effectiveArray = array();
		$configArray = @include VAR_PATH . 'config/config.php';
		$clientArray = $configArray[$config_key];
		foreach ($clientArray as $key => $row) {
			$volume[$key]  = $row['pro'];
		}
		array_multisort($volume, SORT_ASC, $clientArray);

		$clientNum = count($clientArray);
		
		$maxPro = $clientArray[$clientNum - 1]['pro'];
		
		$winningNumber = 0;
		for($i = 0; $i < $clientNum; $i++) {
			$magnification = ceil($maxPro / $clientArray[$i]['pro']); //倍率
			$effectiveArray[$i] = array(
					'id' => $i + 1,
					'award' => $clientArray[$i]['award'],
					'amount' => $clientArray[$i]['amount'],
					'ex' => $clientArray[$i]['ex'],
					'v' => $magnification,
			);
				
			$winningNumber += $magnification;
		}
		$effectiveArray = array_merge($effectiveArray, array(
				array(
						'id' => $clientNum + 1,
						'award' => 0,
						'amount' => 0,
						'ex' => '谢谢参与',
						'v' => $maxPro - $winningNumber
				)
		));
		return $effectiveArray;	
	}

	/**
	 * APP大转盘刮奖
	 */
	public static function appStart($clientArray) {
		$randArray = array();
		//街友中奖数组
		$effectiveArray = array();
	
		foreach ($clientArray as $key => $row) {
			$volume[$key]  = $row['pro'];
		}
		array_multisort($volume, SORT_ASC, $clientArray);
	
		$clientNum = count($clientArray);
	
		$maxPro = $clientArray[$clientNum - 1]['pro'];
	
		$winningNumber = 0;
		for($i = 0; $i < $clientNum; $i++) {
			$magnification = ceil($maxPro / $clientArray[$i]['pro']); //倍率
			$effectiveArray[$i] = array(
					'id' => $i + 1,
					'type' => $clientArray[$i]['type'],
					'name' => $clientArray[$i]['award_name'],
					'number' => $clientArray[$i]['award_number'],
					'dayLimit' => $clientArray[$i]['every_day_limit'],
					'totalLimit' => $clientArray[$i]['total_limit'],
					'v' => $magnification,
					'msg' => $clientArray[$i]['msg']
			);
	
			$winningNumber += $magnification;
		}
		$effectiveArray = array_merge($effectiveArray, array(
				array(
						'id' => $clientNum + 1,
						'type' => 'thanks',
						'name' => '谢谢你',
						'number' => 1,
						'dayLimit' => 1,
						'totalLimit' => 1,
						'v' => $maxPro - $winningNumber,
						'msg' => ''
				)
		));
	
		foreach ($effectiveArray as $key => $val) {
			$randArray[$val['id']] = $val['v'];
		}
		$rid = self::getRand($randArray); //根据概率获取奖项id
		return $effectiveArray[$rid - 1];
	}
		
	private static function getRand($proArr) { 
	    $result = ''; 
	    //概率数组的总概率精度
	    $proSum = array_sum($proArr);	
	    //概率数组循环
	    foreach ($proArr as $key => $proCur) { 
	        $randNum = mt_rand(1, $proSum); 
	        if ($randNum <= $proCur) { 
	            $result = $key; 
	            break; 
	        } else { 
	            $proSum -= $proCur; 
	        } 
	    } 
	    unset ($proArr);	
	    return $result; 
	}
}