<?php 
class Model_Home_Index extends Base
{
	private static $_instance;
	private $_table = '';

	public static function getInstance()
	{
		if (!is_object(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	/**
	 * 根据推荐位名称 获取推荐列表
	 * @param unknown_type $identifier
	 */
	public function getRecommendListByIdentifier($identifier, $city, $limit) {
		//缓存键值
		$cacheKey = 'recommend_list_by_' . $city  . '_' . $identifier . '_' . $limit;
		$data = $this->getData($cacheKey);
		
		if(empty($data)) {
			$pos_id = $this->getPosId($identifier, $city);
			$listArray = $this->select(
					"`pos_id` = '{$pos_id}'", 'oto_recommend', 
					'come_from_id, come_from_type, title, summary, www_url, img_url', 
					'sequence asc, created desc', 
					$limit
			);
			if($limit == 1) {
				$listTmpArray = array();
				$listTmpArray[] = $listArray;
				$listArray = $listTmpArray;
			}
			foreach ($listArray as $skey => $listItem) {
				$listArray[$skey]['img_url'] = $GLOBALS['GLOBAL_CONF']['IMG_URL'] . '/buy/recommend/' . $listItem['img_url'];
				if($listItem['come_from_type'] == 1) {
					$goodRow = $this->select(
							"`good_id` = '{$listItem['come_from_id']}'", 
							'oto_good', 
							'shop_id,shop_name,dis_price,is_auth,favorite_number,concerned_number', 
							'', 
							true
							);
					$listArray[$skey]['shop_id'] = $goodRow['shop_id'];
					$listArray[$skey]['shop_name'] = $goodRow['shop_name'];
					$listArray[$skey]['dis_price'] = floor($goodRow['dis_price']);
					$listArray[$skey]['is_auth'] = $goodRow['is_auth'];
					$listArray[$skey]['favorite_number'] = $goodRow['favorite_number'];	//收藏数
					$listArray[$skey]['concerned_number'] = $goodRow['concerned_number']; //喜欢数
				} elseif($listItem['come_from_type'] == 2) {
					$ticketsort = $this->getTicketSortById(0, 'ticketsort');
					$ticketRow = $this->select(
							"`ticket_id` = '{$listItem['come_from_id']}' and `ticket_status` = '1' and `is_auth` = '1' and `end_time` > '" . REQUEST_TIME . "' and `start_time` < '" . REQUEST_TIME . "'",
							'oto_ticket',
							'ticket_type,ticket_summary,shop_id,shop_name,par_value,selling_price,start_time,end_time',
							'',
							true
							);
					if(empty($ticketRow)) {unset($listArray[$skey]);continue;}
					
					$listArray[$skey]['shop_id'] = $ticketRow['shop_id'];
					$listArray[$skey]['shop_name'] = $ticketRow['shop_name'];
					if($ticketsort[$ticketRow['ticket_type']]['sort_detail_mark'] == 'coupon') {
						$listArray[$skey]['dis_price'] = floor($ticketRow['par_value']);
					} elseif($ticketsort[$ticketRow['ticket_type']]['sort_detail_mark'] == 'voucher') {
						$listArray[$skey]['dis_price'] = floor($ticketRow['selling_price']);
					}					
					$listArray[$skey]['valid_time'] = date('Y', $ticketRow['start_time']).'年'.date('m.d', $ticketRow['start_time']).'-'.date('m.d', $ticketRow['end_time']).'日';
					$listArray[$skey]['sort_name'] = $ticketsort[$ticketRow['ticket_type']]['sort_detail_name'];
				}
			}
			$data = $listArray;
			unset($listArray);
			$this->setData($cacheKey, $data);
		}
		return $data;

	}
	/**
	 * 根据父级分类获取整个子级分类 及 对应的 推荐列表
	 * @param unknown_type $identifier
	 * @return Ambigous <multitype:, multitype:, string, unknown>
	 */
	public function getRecommendClassificationGood($identifier, $city) {
		//缓存键值
		$cacheKey = 'recommend_classification_good_list_by_' . $identifier;
		$data = $this->getData($cacheKey);
		if(empty($data)) {
			$pos_id = $this->getPosId($identifier, $city);
			$posIdentifierArray = $this->select(
						"`pos_pid` = '{$pos_id}'",
						'oto_position',
						'pos_name,identifier,pos_url',
						'sequence asc,pos_id'
					);
			foreach($posIdentifierArray as $key =>$identifierItem) {
				$listArray[$key] = $identifierItem;
				$listArray[$key]['child'] = $this->getRecommendListByIdentifier($identifierItem['identifier'], $city, 5);
			}
			$data = $listArray;
			unset($listArray);
			$this->setData($cacheKey, $data);
		}
		return $data;
	}

    public function getAmountAwards(){
    	$configArray = @include VAR_PATH . 'config/config.php';
        $trueAward =  $this->_db->fetchOne('select sum(award) as award from oto_task_log where task_type != 5 ');
        return number_format($trueAward + $configArray['TASK_INITIAL_VALUE']);
    }


}
