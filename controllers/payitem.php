<?php
/*
服务项
*/
class PayitemController extends CAdminControl{
	public function getlist()
	{		
		$htmlstr = '';
		//分页
		$param['page'] = $this->input->post('page');
		$param['page'] = (empty($param['page']) || intval($param['page']) <= 0)? 1 : intval($param['page']);
		$param['pid'] = $this->input->post('pid');
		if (empty($param['pid']))
		{
			echo '<tr><td colspan="5"><font color="red">未选择服务包！</font></td></tr>';
			exit;
		}
		
		$param['page_size'] = 8;
		$param['record_count'] = $this->model('payitem')->getItemListCount($param);
		$param = page_and_size($param);
		
		if (!empty($param['record_count']))
		{
			$list = $this->model('payitem')->getItemList($param);
			
			$pagestr = show_page_ajax($param['record_count'], $param['page_size'], 'goList');
			
			$uid = $this->input->post('uid');	
			$list = $this->_insertIfHasBuyInfo($list,$uid);
			
			foreach($list as $value)
			{	
				$htmlstr .= '<tr style="cursor:pointer" itemid=' . $value['itemid'] . ' iname="' . $value['iname'] . '" onclick="renderForm(\'' . $value['itemid'] . '\', \'' . $value['iname'] . '\', \'' . $value['iprice'] . '\', \'' . $value['folderid'] . '\', \'' . $value['crid'] . '\', \'' . $value['imonth'] . '\', \'' . $value['iday'] . '\')" ><td><input tag=' . $value['itemid'] . ' type="checkbox" name="ckbox[]" value="' . $value['itemid'] . '" onclick="nocheck(\'' . $value['itemid'] . '\')" /></td>';
				if ($value['hasbuy'] ==1)
				{
					$htmlstr .= '<td><span style="color:#ff0000;font-weight:bolder;">(购买过了) </span>' . $value['iname'] . '</td>';
				}
				else
				{
					$htmlstr .= '<td>' . $value['iname'] . '</td>';
				}
				$htmlstr .= '<td>' . $value['pname'] . '</td><td class="tablink">' . $value['iprice'] . '</td>';
				if ($value['imonth'] > 0)
				{
					$htmlstr .= '<td>' . $value['imonth'] . '月</td>';
				}
				else
				{
					$htmlstr .= '<td>' . $value['iday'] . '天</td>';
				}
				
			}
			
			$htmlstr .= '<tr><td colspan="5">'. $pagestr . '</td></tr>';
		}
		else
		{
			$htmlstr = '<tr><td colspan="5"><font color="red">未找到符合条件的服务项！</font></td></tr>';
		}
		  
		echo $htmlstr;
	}
	
	/**
	 *将指定用户是否购买过该课程的信息注入到课程信息中去
	 */
	private function _insertIfHasBuyInfo($list,$uid){
		if(empty($list)){
			return array();
		}
		//第一步，获取当前数组中的所有itemid
		$itemidArr = array();
		foreach ($list as &$eachone) {
			$eachone['hasbuy'] = 0;
			array_push($itemidArr, $eachone['itemid']);
		}
		$itemidArr = array_unique($itemidArr);
		$orderlist = $this->model('payorder')->getOrdersByItemidsAndUid($itemidArr,$uid);

		$orderlistWithKey = array();
		if(!empty($orderlist)){
			foreach ($orderlist as $order) {
				$key = 'k_'.$order['itemid'];
				if(array_key_exists($key, $orderlistWithKey)){
					continue;
				}
				$orderlistWithKey[$key] = $order;
			}
		}else{
			return $list;
		}
		foreach ($list as &$eachone) {
			$key = 'k_'.$eachone['itemid'];
			if(array_key_exists($key, $orderlistWithKey)){
				$eachone['hasbuy'] = 1;
			}
		}
		return $list;
	}
}
?>