<?php
/*
用户权限,用于服务包
*/
class UserpermissionModel extends CEbhModel{
	/**
	*根据订单明细内容生成订单信息
	*/
	public function addPermission($param = array()) {
		if(empty($param))
			return FALSE;
		$setarr = array();
		if(!empty($param['itemid']))
			$setarr['itemid'] = $param['itemid'];
		if(!empty($param['type']))
			$setarr['type'] = $param['type'];
		if(!empty($param['powerid']))
			$setarr['powerid'] = $param['powerid'];
		if(!empty($param['uid']))
			$setarr['uid'] = $param['uid'];
		if(!empty($param['crid']))
			$setarr['crid'] = $param['crid'];
		if(!empty($param['folderid']))
			$setarr['folderid'] = $param['folderid'];
		if(isset($param['cwid']))
			$setarr['cwid'] = $param['cwid'];
		if(!empty($param['startdate']))
			$setarr['startdate'] = $param['startdate'];
		if(!empty($param['enddate']))
			$setarr['enddate'] = $param['enddate'];
		if(!empty($param['dateline']))
			$setarr['dateline'] = $param['dateline'];
		else 
			$setarr['dateline'] = SYSTIME;
		$pid = $this->ebhdb->insert('ebh_userpermisions',$setarr);
		return $pid;
	}

	/**
	*更新订单信息，如果包含明细，则同时更新明细信息
	*/
	public function updatePermission($param = array()) {
		if(empty($param) || empty($param['pid']))
			return FALSE;
		$setarr = array();
		$wherearr = array('pid'=>$param['pid']);
		if(!empty($param['itemid']))
			$setarr['itemid'] = $param['itemid'];
		if(!empty($param['type']))
			$setarr['type'] = $param['type'];
		if(!empty($param['powerid']))
			$setarr['powerid'] = $param['powerid'];
		if(!empty($param['uid']))
			$setarr['uid'] = $param['uid'];
		if(!empty($param['crid']))
			$setarr['crid'] = $param['crid'];
		if(!empty($param['folderid']))
			$setarr['folderid'] = $param['folderid'];
		if(!empty($param['cwid']))
			$setarr['cwid'] = $param['cwid'];
		if(!empty($param['startdate']))
			$setarr['startdate'] = $param['startdate'];
		if(!empty($param['enddate']))
			$setarr['enddate'] = $param['enddate'];
		$afrows = $this->ebhdb->update('ebh_userpermisions',$setarr,$wherearr);
		return $afrows;
	}
	/**
	*获取权限列表
	*/
	public function getPermissionList($param = array()) {
		if(empty($param))
			return FALSE;
		$sql = 'select p.pid,p.itemid,p.type,p.powerid,p.uid,p.crid,p.folderid,p.cwid,p.startdate,p.enddate,p.dateline from ebh_userpermisions p';
		$wherearr = array();
		if(!empty($param['itemid'])) {
			$wherearr[] = 'p.itemid='.$param['itemid'];
		}
		if(!empty($param['type'])) {
			$wherearr[] = 'p.type='.$param['type'];
		}
		if(!empty($param['powerid'])) {
			$wherearr[] = 'p.powerid='.$param['powerid'];
		}
		if(!empty($param['uid'])) {
			$wherearr[] = 'p.uid='.$param['uid'];
		}
		if(!empty($param['crid'])) {
			$wherearr[] = 'p.crid='.$param['crid'];
		}
		if(!empty($param['folderid'])) {
			$wherearr[] = 'p.folderid='.$param['folderid'];
		}
		if(!empty($param['cwid'])) {
			$wherearr[] = 'p.cwid='.$param['cwid'];
		}
		if(empty($wherearr))
			return FALSE;
		if(!empty($param['limit'])) {
            $sql .= ' limit '. $param['limit'];
        } else {
			if (empty($param['page']) || $param['page'] < 1)
				$page = 1;
			else
				$page = $param['page'];
			$pagesize = empty($param['pagesize']) ? 10 : $param['pagesize'];
			$start = ($page - 1) * $pagesize;
            $sql .= ' limit ' . $start . ',' . $pagesize;
        }
		return $this->ebhdb->query($sql)->list_array();
	}
	/**
	*根据用户编号和itemid编号获取权限
	*/
	public function getPermissionByItemId($itemid,$uid) {
		$sql = "select p.pid,p.itemid,p.type,p.powerid,p.uid,p.crid,p.folderid,p.cwid,p.startdate,p.enddate,p.dateline from ebh_userpermisions p where p.itemid=$itemid and p.uid = $uid";
		return $this->ebhdb->query($sql)->row_array();
	}
	/**
	 *根据用户编号和folderid编号获取权限
	 */
	public function getPermissionByFolderId($folderid,$uid) {
		$sql = "select p.pid,p.itemid,p.type,p.powerid,p.uid,p.crid,p.folderid,p.cwid,p.startdate,p.enddate,p.dateline from ebh_userpermisions p where p.folderid=$folderid and p.uid = $uid";
		return $this->ebhdb->query($sql)->row_array();
	}
	/**
	*判断用户是否有平台权限
	* @return int 返回验证结果，1表示有权限 2表示已过期 0表示用户已停用 -1表示无权限 -2参数非法
	*/
	public function checkUserPermision($uid,$param = array()) {
		if(empty($param['powerid']) && empty($param['crid']) && empty($param['folderid'])) 
			return -2;
		$flag = 0;	//默认平台权限
		if(!empty($param['powerid']))	//powerid功能点权限
			$flag = 1;
		else if(!empty($param['folderid']))	//课程权限
			$flag = 2;
		if($flag == 1) {
			$sql = 'select p.startdate,p.enddate from ebh_userpermisions p where p.uid = '.$uid.' and p.powerid='.$param['powerid']; 
		} else if($flag == 2) {
			$sql = 'select p.startdate,p.enddate from ebh_userpermisions p where p.uid = '.$uid.' and p.crid='.$param['crid'].' and p.folderid='.$param['folderid']; 
		} else {
			$sql = 'select p.startdate,p.enddate from ebh_userpermisions p where p.uid = '.$uid.' and p.crid='.$param['crid'].' and p.folderid=0'; 
		}
		$peritem = $this->ebhdb->query($sql)->row_array();

		if(empty($peritem)) {	//无权限		
			return -1;
		}

		if (!empty($peritem['enddate']) && $peritem['enddate'] < (EBH_BEGIN_TIME - 86400))
            return 2;
		return 1;
	}
	/**
	*根据功能点或者平台等信息获取支付服务项
	*@param array $param
	*/
	public function getUserPayItem($param = array()) {
		if(empty($param['powerid']) && empty($param['crid']) && empty($param['folderid'])) 
			return FALSE;
		$flag = 0;	//默认平台权限
		if(!empty($param['powerid']))	//powerid功能点权限
			$flag = 1;
		else if(!empty($param['folderid']))	//课程权限
			$flag = 2;
		if($flag == 2) {
			$sql = 'select i.itemid,i.pid,i.iname,i.isummary,i.crid,i.folderid,i.iprice,i.imonth,i.iday from ebh_pay_items i where i.folderid='.$param['folderid'];
		}  else {
			$sql = 'select i.itemid,i.pid,i.iname,i.isummary,i.crid,i.folderid,i.iprice,i.imonth,i.iday from ebh_pay_items i where i.crid='.$param['crid'];
		}
		$payitem = $this->ebhdb->query($sql)->row_array();
		return $payitem;
	}
	/**
	*获取用户已开通的课程
	*/
	public function getUserPayFolderList($param = array()) {
		if(empty($param['uid']))
			return FALSE;
		$sql = "select p.pid,p.itemid,p.crid,p.folderid,p.startdate,p.enddate,f.foldername from ebh_userpermisions p left join ebh_folders f on f.folderid = p.folderid";
		$wherearr = array();
		$wherearr[] = 'p.uid='.$param['uid'];
		if(!empty($param['crid'])) {
			$wherearr[] = 'p.crid='.$param['crid'];
		}
		if(!empty($param['filterdate'])) {	//过滤已过期
			$enddate = SYSTIME - 86400;
			$wherearr[] = 'p.enddate>'.$enddate;
		}
		$sql .= ' WHERE '.implode(' AND ',$wherearr);
		return $this->ebhdb->query($sql)->list_array();
	}
	/**
	*获取学校下所有的服务项
	*/
	public function getPayItemByCrid($crid) {
		$sql = "select i.itemid,i.pid,i.crid,i.folderid,i.iname from ebh_pay_items i where i.crid=$crid";
		return $this->ebhdb->query($sql)->list_array();
	}
	/**
	*获取学校下所有的服务项及相关课程
	*/
	public function getPayItemByCridWithFolder($crid) {
		$sql = "select i.itemid,i.pid,i.crid,i.folderid,i.iname,f.fprice,f.foldername,p.pname,f.coursewarenum,f.img from ebh_pay_items i 
		join ebh_pay_packages p on i.pid=p.pid join ebh_folders f on i.folderid=f.folderid
		where i.crid=$crid order by p.displayorder desc";
		return $this->ebhdb->query($sql)->list_array();
	}
	/**
	*根据课程编号获取已开通此课程的用户id列表
	*/
	public function getUserIdListByFolder($folderid) {
		if(empty($folderid))
			return FALSE;
		$uidsql = 'select uid from ebh_userpermisions up where up.folderid='.$folderid;
		$uidlist = $this->ebhdb->query($uidsql)->list_array();
		return $uidlist;
	}
	/**
	*根据平台编号和课程编号以及已开通权限的uid组合获取班级用户列表
	*/
	public function getUserAndClassListByUidStr($crid,$folderid,$uidstr) {
		if(empty($crid) || empty($folderid) || empty($uidstr))
			return FALSE;
		//获取用户列表
		$usersql = 'select u.uid,u.username,u.realname,u.sex from ebh_users u where u.uid in ('.$uidstr.')';
		$userlist = $this->ebhdb->query($usersql)->list_array();
		if(empty($userlist))
			return FALSE;
		$myuserlist = array();
		foreach($userlist as $myuser) {
			$myuserlist[$myuser['uid']] = $myuser;
		}

		//获取用户对应班级信息
		$classusersql = 'select cs.uid,c.classid,c.classname,c.grade from ebh_classstudents cs join ebh_classes c on (cs.classid=c.classid) where cs.uid in ('.$uidstr.') and c.crid='.$crid .' order by c.classid,cs.uid';
		$classrows = $this->ebhdb->query($classusersql)->list_array();
		$mylist = array();
		foreach($classrows as $classrow) {
			if(isset($myuserlist[$classrow['uid']])) {
				$userrow = $myuserlist[$classrow['uid']];
				$userrow['classid'] = $classrow['classid'];
				$userrow['classname'] = $classrow['classname'];
				$userrow['grade'] = $classrow['grade'];
				$mylist[$classrow['uid']] = $userrow;
			}
		}	
		return $mylist;
	}
}

?>