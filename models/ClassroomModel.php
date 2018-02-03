<?php
/**
 * 网校Model类
 */
class ClassroomModel extends CEbhModel
{
	    /**
     * 添加教室对应的课件数
     * @param int $crid 课程编号
     * @param int $num 如为正数则添加，负数则为减少
     */
    public function addcoursenum($crid,$num = 1) {
        $where = 'crid='.$crid;
        $setarr = array('coursenum'=>'coursenum+'.$num);
        $this->ebhdb->update('ebh_classrooms',array(),$where,$setarr);
    }
    /**
     * 添加教室对应的学生数
     * @param int $crid 教室编号
     * @param int $num 如为正数则添加，负数则为减少
     */
    public function addstunum($crid,$num = 1) {
        $where = 'crid='.$crid;
        $setarr = array('stunum'=>'stunum+'.$num);
        $this->ebhdb->update('ebh_classrooms',array(),$where,$setarr);
    }
	
		/*
	详情
	@param int $crid
	@return array
	*/
	public function getclassroomdetail($crid){
		$sql = 'select c.catid,c.crid,c.crname,c.begindate,c.banner,c.upid,c.enddate,c.dateline,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.grade,c.template,c.profitratio,c.crprice,c.displayorder,c.property,u.username,u.uid,c.floatadimg,c.floatadurl,c.showusername,c.defaultpass from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid='.$crid;
		return $this->ebhdb->query($sql)->row_array();
	}
	/*
	后台获取教室列表
	*/
	public function getClassroomList($param){
		$sql = 'select c.crid,c.crname,c.begindate,c.enddate,c.dateline,c.domain,c.crprice from ebh_classrooms c';
		if(!empty($param['q']))
			$wherearr[] = '( c.crname like \'%'. $this->ebhdb->escape_str($param['q']) .'%\' or c.domain like \'%'. $this->ebhdb->escape_str($param['q']) .'%\')';
		if(!empty($param['crid']))
			$wherearr[] = ' crid = '.$param['crid'];
		if(!empty($param['isschool'])){
			$wherearr[] = ' isschool='.$param['isschool'];
		}
		if(!empty($param['access'])){
			$wherearr[] = 'c.crid in ('.$param['access'].')';//使用访问权限列表
		}
		if(!empty($param['notfree']))
			$wherearr[] = ' (c.isschool = 6 or c.isschool = 2 or c.isschool = 7)';
	 
		if(!empty($param['dt'])){
		    if($param['dt'] ==1){//最近一个月过期+将要过期的网校
		        $wherearr[] = 'c.begindate is not null and c.begindate < '.strtotime("-1 month").' and c.enddate is not null and c.enddate > '.strtotime("-1 month").' and c.enddate <'.strtotime("+1 month");
		    }elseif($param['dt'] ==2){
		        $wherearr[] = 'c.begindate is not null and c.begindate < '.strtotime("-1 month").' and c.enddate is not null and  c.enddate <'.strtotime("+1 month");
		    }
		}
		if(!empty($wherearr))
			$sql.= ' WHERE '.implode(' AND ',$wherearr);
		if(!empty($param['orderby'])){
		    $sql.= ' ORDER BY '.$param['orderby'];
		}else{
		    $sql.=' ORDER BY crid desc';
		}
		if(!empty($param['limit']))
			$sql.= ' limit ' . $param['limit'];
			//log_message($sql);
		return $this->ebhdb->query($sql)->list_array();
	}
	
	/*
	后台获取教室数量
	*/
	public function getClassroomCount($param){
		$sql = 'select count(*) count from ebh_classrooms c ';
		if(!empty($param['q']))
			$wherearr[] = '(c.crname like \'%'. $this->ebhdb->escape_str($param['q']) .'%\' or c.domain like \'%'. $this->ebhdb->escape_str($param['q']) .'%\')';
		if(!empty($param['beginprice']))
			$wherearr[] = ' c.crprice >= '.$param['beginprice'];
		if(!empty($param['endprice']))
			$wherearr[] = ' c.crprice <= '.$param['endprice'];
		if(!empty($param['crid']))
			$wherearr[] = ' crid = '.$param['crid'];
		if(!empty($param['isschool']))
	 		$wherearr[] = 'isschool = '.intval($param['isschool']);
		if(!empty($param['access'])){
			$wherearr[] = 'c.crid in ('.$param['access'].')';//使用访问权限列表
		}
	 	if(!empty($param['property']))
	 		$wherearr[] = 'property = '.intval($param['property']);
		if(!empty($param['grade']))
			$wherearr[] = 'c.grade = '.intval($param['grade']);
		if(!empty($param['citycode']))
			$wherearr[] = 'c.citycode like \''.$this->ebhdb->escape_str($param['citycode']).'%\'';
		if(!empty($param['subject'])){
			$wherearr[] = ' c.crname like \'%'. $this->ebhdb->escape_str($param['subject']) .'%\'';
		}
		if(!empty($param['notfree']))
			$wherearr[] = ' (c.isschool = 6 or c.isschool = 2 or c.isschool = 7)';
		if(!empty($param['dt'])){
		    if($param['dt'] ==1){//最近一个月过期+将要过期的网校
		        $wherearr[] = 'c.begindate is not null and c.begindate < '.strtotime("-1 month").' and c.enddate is not null and c.enddate > '.strtotime("-1 month").' and c.enddate <'.strtotime("+1 month");
		    }elseif($param['dt'] ==2){
		        $wherearr[] = 'c.begindate is not null and c.begindate < '.strtotime("-1 month").' and c.enddate is not null and  c.enddate <'.strtotime("+1 month");
		    }
		}
		if(!empty($wherearr))
			$sql.= ' WHERE '.implode(' AND ',$wherearr);
		$count = $this->ebhdb->query($sql)->row_array();
		return $count['count'];
	}
	
		/*
	后台获取教室列表
	*/
	public function getClassroomListByIds($ids){
		$sql = 'select crid,crname from ebh_classrooms';
		if (!empty($ids))
		{
			$wherearr[] = ' crid in (' . $this->ebhdb->escape_str($ids) . ')';
		}
		if(!empty($wherearr))
		{
			$sql.= ' WHERE '.implode(' AND ',$wherearr);
		}
		return $this->ebhdb->query($sql)->list_array();
	}

	/**
	 * 获取包含多个网校的数组
	 * @param  array $crid_array crid数组
	 * @return array            网校数组
	 */
	public function getClassRoomArray($crid_array) {
		$classroom_array = array();
		if (!empty($crid_array) && is_array($crid_array))
		{
			$crid_array = array_unique($crid_array);
			$sql = 'SELECT crid,crname from ebh_classrooms WHERE crid IN(' . implode(',', $crid_array) . ')';
			$row = $this->ebhdb->query($sql)->list_array();
			foreach ($row as $v)
			{
				$classroom_array[$v['crid']] = $v['crname'];
			}
		}
		return $classroom_array;
	}
		/*
	简单无条件查询，供下拉菜单使用
	*/
	public function getsimpleclassroomlist(){
		$sql = 'select c.crid,c.crname from ebh_classrooms c';
		return $this->ebhdb->query($sql)->list_array();
	}
	
	/**
     *获取用户所在的收费教室
     */
    public function getUserClassroom($uid = 0){
    	$sql = 'select cr.crid,cr.crname from ebh_roomusers r join ebh_classrooms cr on r.crid = cr.crid where r.uid = '.intval($uid) . ' AND cr.isschool in (2,6,7)';
    	return $this->ebhdb->query($sql)->list_array();
    }
		
	/**
	*根据教室编号获取教室对应的信息
	*/
	public function getRoomByCrid($crid) {
		$domain = '';
		$sql = "select crid,crname,domain,isschool,good,useful,bad,score,viewnum from ebh_classrooms where crid=$crid";
		return $this->ebhdb->query($sql)->row_array();
	}

}