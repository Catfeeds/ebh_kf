<?php
/**
*e板会与Sns关联操作类库
*可处理学校/学生等关联数据的缓存处理等
*/
class Sns {
	//更新学校信息
	public function updateClassRooomCache($param){
		$redis = Ebh::app()->getCache('cache_redis');
		$roominfokey = 'room_info_'.$param['crid'];
		$roominfovalue = array('domain'=>$param['domain'],'crname'=>$param['crname'],'cface'=>$param['cface']);
		$redis->hMset($roominfokey,$roominfovalue);
	}
	//更新班级学生
	public function updateClassUserCache($param){
		$redis = Ebh::app()->getCache('cache_redis');
		$classuserkey = 'class_ulist_'.$param['classid'];
		$redis->hMset($classuserkey,array($param['uid']=>$param['uid']));
	}
	//更新学校学生
	public function updateRoomUserCache($param){
		$redis = Ebh::app()->getCache('cache_redis');
		$roomuserkey = 'room_ulist_'.$param['crid'];
		$redis->hMset($roomuserkey,array($param['uid']=>$param['uid']));
	}
	//删除班级学生
	public function delClassUserCache($param){
		$redis = Ebh::app()->getCache('cache_redis');
		$redis->hdel('class_ulist_'.$param['classid'],$param['uid']);
	}
	//删除班级全部用户
	public function delClassUserCacheAll($param){
		$redis = Ebh::app()->getCache('cache_redis');
		if (!empty($param['classid']))
		{
			$redis->del('class_ulist_'.$param['classid']);
		}
	}
	//删除学校学生
	public function delRoomUserCache($param){
		$redis = Ebh::app()->getCache('cache_redis');
		$redis->hdel('room_ulist_'.$param['crid'],$param['uid']);
	}
	//批量班级学生
	public function updateClassUserCacheM($users){
		$redis = Ebh::app()->getCache('cache_redis');
		$culist = array();
		foreach($users as $user){
			$classid = $user['classid'];
			$uid = $user['uid'];
			$culist[$classid][$uid] = $uid;
			
		}
		foreach($culist as $classid=>$cu){
			$classuserkey = 'class_ulist_'.$classid;
			
			$redis->hMset($classuserkey,$cu);
		}
	}
	//批量学校学生
	public function updateRoomUserCacheM($users){
		$redis = Ebh::app()->getCache('cache_redis');
		$culist = array();
		foreach($users as $user){
			$crid = $user['crid'];
			$uid = $user['uid'];
			$culist[$crid][$uid] = $uid;
			
		}
		foreach($culist as $crid=>$cu){
			$roomuserkey = 'room_ulist_'.$crid;
			
			$redis->hMset($roomuserkey,$cu);
		}
	}
	//批量删除网校学生
	public function delRoomUserCacheM($roomstulist){
		$redis = Ebh::app()->getCache('cache_redis');
		foreach($roomstulist as $rs){
			$redis->hdel('room_ulist_'.$rs['crid'],$rs['uid']);
		}
	}
	//批量删除班级学生
	public function delClassUserCacheM($clsstulist){
		$redis = Ebh::app()->getCache('cache_redis');
		foreach($clsstulist as $cs){
			$redis->hdel('class_ulist_'.$cs['classid'],$cs['uid']);
		}
	}

	/**
	 * 同步操作
	 * 使用方法Ebh::app()->lib('Sns')->do_sync($uid, 1);
	 * @param  int $uid  用户编号
	 * @param  int $type 类型 1问题数+1,2学习数+1,3作业数+1,4网校操作（包括移动用户到新的网校，为用户添加新的网校，删除网校）,5添加用户（注册）,6删除用户,-1问题数-1,-3作业数-1。
	 * @return boolean   true成功false失败
	 */
	function do_sync($uid, $type)
	{
		//从配置文件获得adminkey
		$syncconfig = Ebh::app()->getConfig()->load('snssync');
		$adminkey = $syncconfig['adminkey'];
		if (empty($uid) || empty($type) || empty($adminkey))
		{
			return false;
		}
		//使用curl调用接口
		$url = 'http://sns.ebh.net/auth/syncoperate.html?uid=' . $uid . '&type=' . $type . '&adminkey=' . $adminkey;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		$ret = json_decode($output);

		if (empty($ret) || empty($ret->code) || $ret->code !== true)
		{
			log_message('SNS同步错误 url:' . $url . ' output:' . $output);
			return false;
		}
		return true;
	}
}
?>