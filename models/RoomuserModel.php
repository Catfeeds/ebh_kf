<?php

/*
  教室与会员,前台会员->云教育网校
 */

class RoomuserModel extends CEbhModel {
    
    /**
     * 插入ebh_roomusers记录，主要用于学员和教室的绑定
     * @param type $param
     * @return boolean
     */
    public function insert($param) {
        if (empty($param['crid']) || empty($param['uid']))
            return FALSE;
        $setarr = array();
        $setarr['crid'] = $param['crid'];
        $setarr['uid'] = $param['uid'];
        if (!empty($param ['cdateline'])) { //记录添加时间
            $setarr ['cdateline'] = $param ['cdateline'];
        } else {
            $setarr ['cdateline'] = SYSTIME;
        }
        if (!empty($param ['begindate'])) { //服务开始时间
            $setarr ['begindate'] = $param ['begindate'];
        }
        if (!empty($param ['enddate'])) {   //服务结束时间
            $setarr ['enddate'] = $param ['enddate'];
        }
        if (!empty($param ['cnname'])) {   //学生真实姓名，此处只做存档用
            $setarr ['cnname'] = $param ['cnname'];
        }
		if (isset($param ['cstatus'])) { //状态，1正常 0 锁定
            $setarr ['cstatus'] = $param['cstatus'];
        }
        if (isset($param ['sex'])) {   //性别
            $setarr ['sex'] = $param ['sex'];
        }
        if (isset($param ['birthday'])) {   //出生日期
            $setarr ['birthday'] = $param ['birthday'];
        }
        if (!empty($param ['mobile'])) {   //联系方式
            $setarr ['mobile'] = $param ['mobile'];
        }
        if (!empty($param ['email'])) {   //邮箱
            $setarr ['email'] = $param ['email'];
        }

        $afrows = $this->ebhdb->insert('ebh_roomusers',$setarr);
        return $afrows;
    }
    /**
     * 更新教室内的学员信息，需要带上$crid和$uid
     * @param type $param
     */
    public function update($param) {
        if (empty($param['crid']) || empty($param['uid']))
            return FALSE;
        $wherearr = array('crid'=>$param['crid'],'uid'=>$param['uid']);
        $setarr = array();
        if (!empty($param ['begindate'])) { //服务开始时间
            $setarr ['begindate'] = $param ['begindate'];
        }
        if (!empty($param ['enddate'])) {   //服务结束时间
            $setarr ['enddate'] = $param ['enddate'];
        }
        if (isset($param['cstatus'])) { //状态，1正常 0 锁定
            $setarr ['cstatus'] = $param['cstatus'];
        }
        if (!empty($param ['rbalance'])) {  //学员在教室内余额，单用于一个教室
            $setarr['rbalance'] = $param['rbalance'];
        }
        if(empty($setarr))
            return FALSE;
        $afrows = $this->ebhdb->update('ebh_roomusers',$setarr,$wherearr);
		return $afrows;
    }
    /**
     * 删除教室内的学员并更新教室学生数
     * @param type $param
     * @return boolean
     */
    public function del($param) {
        if (empty($param['crid']) || empty($param['uid']))
            return FALSE;
        $wherearr = array('crid'=>$param['crid'],'uid'=>$param['uid']);
        $this->ebhdb->begin_trans();
        $afrows = $this->ebhdb->delete('ebh_roomusers',$wherearr);
        if($afrows > 0) {
            $this->ebhdb->update('ebh_classrooms',array(),array('crid'=>$param['crid']),array('stunum'=>'stunum-1'));
        }
        if($this->ebhdb->trans_status()===FALSE) {
            $this->ebhdb->rollback_trans();
            return FALSE;
        } else {
            $this->ebhdb->commit_trans();
        }
        return TRUE;
    }
    /**
     * 根据教室和学员编号获取学员在教室内的信息详情
     * @param type $crid
     * @param type $uid
     * @return type
     */
    public function getroomuserdetail($crid,$uid) {
        $sql = "select ru.cstatus,ru.rbalance,ru.begindate,ru.enddate from ebh_roomusers ru where ru.crid=$crid and ru.uid=$uid";
        return $this->ebhdb->query($sql)->row_array();
    }
    /*
      会员加入的网校数
      @param int $uid
     */

    public function getroomcount($uid) {
        $sql = 'select count(*) count from ebh_roomusers where uid=' . $uid;
        $count = $this->ebhdb->query($sql)->row_array();
        return $count['count'];
    }

    /*
      会员加入的网校列表
      @param int $uid
     */

    public function getroomlist($uid) {
        $sql = 'select c.cface,c.domain,c.crname,c.crid,c.summary,r.cdateline,r.enddate,c.coursenum,c.isschool,c.coursenum,c.examcount from ebh_roomusers r 
			join ebh_classrooms c on r.crid=c.crid 
			where r.uid=' . intval($uid);
        return $this->ebhdb->query($sql)->list_array();
    }

    /**
     * 根据教室编号获取学员列表，一般适合于教师网校的学员列表
     * @param type $param
     * @return boolean
     */
    public function getroomuserlist($param) {
        if (empty($param['crid']))
            return FALSE;
        if (empty($param['page']) || $param['page'] < 1)
            $page = 1;
        else
            $page = $param['page'];
        $pagesize = empty($param['pagesize']) ? 10 : $param['pagesize'];
        $start = ($page - 1) * $pagesize;
        $sql = 'select u.uid,u.username,u.realname,u.sex,u.face,u.email,u.mobile,ru.cstatus,ru.rbalance,ru.begindate,ru.enddate from ebh_roomusers ru ' .
                'join ebh_users u on (ru.uid = u.uid) ';
        $wherearr = array();
        $wherearr[] = 'ru.crid=' . $param['crid'];
        if (isset($param['status']))
            $wherearr[] = 'ru.cstatus=' . $param['status'];
        if (!empty($param['q'])) {
            $q = $this->ebhdb->escape_str($param['q']);
            $wherearr[] = '(u.username like \'%' . $q . '%\' OR u.realname like \'%' . $q . '%\')';
        }
        if (!empty($wherearr))
            $sql .= ' WHERE ' . implode(' AND ', $wherearr);
        if (!empty($param['order']))
            $sql .= ' ORDER BY ' . $param['order'];
        else
            $sql .= ' ORDER BY ru.begindate DESC';
        $sql .= ' limit ' . $start . ',' . $pagesize;
        return $this->ebhdb->query($sql)->list_array();
    }

    /**
     * 根据教室编号获取学员列表，一般适合于教师网校的学员列表
     * @param type $param
     * @return boolean
     */
    public function getroomusercount($param) {
        $count = 0;
        if (empty($param['crid']))
            return $count;
        $sql = 'select count(*) count from ebh_roomusers ru ' .
                'join ebh_users u on (ru.uid = u.uid) ';
        $wherearr = array();
        $wherearr[] = 'ru.crid=' . $param['crid'];
        if (isset($param['status']))
            $wherearr[] = 'ru.cstatus=' . $param['status'];
        if (!empty($param['q'])) {
            $q = $this->ebhdb->escape_str($param['q']);
            $wherearr[] = '(u.username like \'%' . $q . '%\' OR u.realname like \'%' . $q . '%\')';
        }
        if (!empty($wherearr))
            $sql .= ' WHERE ' . implode(' AND ', $wherearr);

        $row = $this->ebhdb->query($sql)->row_array();
        if (!empty($row))
            $count = $row['count'];
        return $count;
    }
	
	/*
	获取学校学生
	@param array $param crid classid q
	*/
	public function getaroomstudentlist($param){
		$wherearr = array();
		$sql = 'SELECT ru.crid,u.email,u.mobile,ru.cnname,u.uid,u.sex,u.username,cl.classid,cl.classname,u.realname,u.allowip 
			FROM ebh_roomusers ru 
			LEFT JOIN ebh_users u ON ru.uid = u.uid 
			LEFT JOIN ebh_classstudents st ON u.uid=st.uid 
			LEFT JOIN ebh_classes cl ON st.classid = cl.classid';
			
		$wherearr[]= 'ru.crid = '.$param['crid'];
		$wherearr[]= 'cl.crid = '.$param['crid'];
		$wherearr[]= 'cl.status = 0';
		if(!empty($param['classid']))
			$wherearr[]= 'cl.classid = '.$param['classid'];
		if(!empty($param['q'])){
			$q = $this->ebhdb->escape_str($param['q']);
			$wherearr[]= '(u.username like \'%'.$q.'%\' OR ru.cnname like \'%'.$q.'%\')';
		}
		$sql.= ' where '.implode(' AND ',$wherearr);
		$sql.=' ORDER BY ru.uid desc ';
		if(!empty($param['limit']))
			$sql .= ' limit '.$param['limit'];
		else {
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
	/*
	获取学校学生数量
	@param array $param crid classid q
	*/
	public function getaroomstudentcount($param){
		$wherearr = array();
		$sql = 'SELECT count(*) count 
			FROM ebh_roomusers ru 
			LEFT JOIN ebh_users u ON ru.uid = u.uid 
			LEFT JOIN ebh_classstudents st ON u.uid=st.uid 
			LEFT JOIN ebh_classes cl ON st.classid = cl.classid';
			
		$wherearr[]= 'ru.crid = '.$param['crid'];
		$wherearr[]= 'cl.crid = '.$param['crid'];
		$wherearr[]= 'cl.status = 0';
		if(!empty($param['classid']))
			$wherearr[]= 'cl.classid = '.$param['classid'];
		if(!empty($param['q'])){
			$q = $this->ebhdb->escape_str($param['q']);
			$wherearr[]= '(u.username like \'%'.$q.'%\' OR ru.cnname like \'%'.$q.'%\')';
		}
		$sql.= ' where '.implode(' AND ',$wherearr);
		$count = $this->ebhdb->query($sql)->row_array();
		return $count['count'];
	}
	/*
	学校学生详情
	@param int $crid
	@param int $uid	
	*/
	public function getaroomstudentdetail($crid,$uid){
		$sql = 'select u.uid,u.username,u.realname,ru.cnname,u.sex,m.birthdate,u.mobile,u.email,c.classid 
			from ebh_users u 
			join ebh_members m on u.uid = m.memberid 
			join ebh_classstudents cs on cs.uid = u.uid 
			join ebh_classes c on c.classid = cs.classid 
			join ebh_roomusers ru on ru.crid = c.crid
			where u.uid = '.$uid.' and c.crid = '.$crid;
		return $this->ebhdb->query($sql)->row_array();
	}
	/*
	编辑学生详情的查询(替代getaroomstudentdetail方法)
	*/
	public function getaroomstudentdetails($crid,$uid){
		$sql = 'SELECT ru.crid,u.email,u.mobile,ru.cnname,u.uid,u.sex,u.username,u.realname,m.birthdate,cl.classid,cl.classname 
			FROM ebh_roomusers ru 
			LEFT JOIN ebh_users u ON ru.uid = u.uid 
			LEFT JOIN ebh_members m on u.uid = m.memberid 
			LEFT JOIN ebh_classstudents st ON u.uid=st.uid 
			LEFT JOIN ebh_classes cl ON st.classid = cl.classid
			where cl.crid='.$crid.' and ru.crid = '.$crid.' and ru.uid = '.$uid;
		return $this->ebhdb->query($sql)->row_array();
	}
	/*
	修改学校学生信息
	@param array $param 
	*/
	public function editstudent($param){
		if(!empty($param['cnname']))
			$ruarr['cnname'] = $param['cnname'];
		if(!empty($param['sex']))
			$ruarr['sex'] = $param['sex'];
		
		$wherearr = array('crid'=>$param['crid'],'uid'=>$param['uid']);
		if(!empty($ruarr))
			$afrows = $this->ebhdb->update('ebh_roomusers',$ruarr,$wherearr);
		
		if(!empty($param['classid'])){
			$csarr['classid'] = $param['classid'];
			$wherearr = array('uid'=>$param['uid'],'classid'=>$param['oldclassid']);
			$this->ebhdb->update('ebh_classes',array(),array('classid'=>$param['classid']),array('stunum'=>'stunum+1'));
			$this->ebhdb->update('ebh_classes',array(),array('classid'=>$param['oldclassid']),array('stunum'=>'stunum-1'));
			$afrows = $this->ebhdb->update('ebh_classstudents',$csarr,$wherearr);
		}
		return $afrows;
	}
	/*
	*stores的学习大纲数量
	*/
	public function getstudycount($params){
		$sql = 'SELECT count(1) as listcount FROM ebh_2012.ebh_roomcourses r LEFT JOIN ebh_classrooms cr ON cr.crid = r.crid LEFT JOIN ebh_coursewares c ON c.cwid = r.cwid LEFT JOIN ebh_users u ON c.uid=u.uid LEFT JOIN ebh_folders f ON f.folderid=r.folderid ';
		$wherearr = array();
		if (!empty($params['crid'])) {
            $wherearr[] = ' r.crid = '.$params['crid'] ;
        }
		if (!empty($params['q'])) {
            $wherearr[] = ' c.title like \'%'. $this->ebhdb->escape_str($params['q']) .'%\'';
        }
		if (!empty($params['isfree'])) {
            $wherearr[] = ' r.isfree = '.$params['isfree'] ;
        }
		if(!empty($wherearr)) {
            $sql .= ' WHERE '.implode(' AND ',$wherearr);
        }
        if(!empty($params['displayorder'])) {
            $sql .= ' ORDER BY '.$params['displayorder'];
        } else {
            $sql .= ' ORDER BY r.displayorder';
        }
		return $this->ebhdb->query($sql)->row_array();
	}

	/*
	*
	*/
	public function getroomuser(){
		
	}
	
	public function addMultipleStudent($roomarr){
		$sql = 'insert into ebh_roomusers (crid,uid,cnname,sex,cdateline) values ';
		foreach($roomarr as $room){
			$crid = $room['crid'];
			$uid = $room['uid'];
			$cnname = $room['cnname'];
			$sex = $room['sex'];
			$cdateline = SYSTIME;
			$sql.= "($crid,$uid,'$cnname',$sex,$cdateline),";
		}
		$sql = rtrim($sql,',');
		$this->ebhdb->query($sql);
	}
	
	public function getRoomuserByUsername($param){
		$sql = 'select username,u.uid from ebh_roomusers ru join ebh_users u on u.uid=ru.uid ';
		$wherearr[] = 'u.username in ('.$param['usernames'].')';
		$wherearr[] = 'ru.crid='.$param['crid'];
		$sql.= ' where '.implode(' AND ',$wherearr);
		return $this->ebhdb->query($sql)->list_array();
	}
	
	/*
	性别人数
	*/
	public function getSexCount($param){
		$sql = 'select count(*) count from ebh_users u join ebh_roomusers ru on u.uid=ru.uid';
		if(!empty($param['crid']))
			$wherearr[] = 'ru.crid='.$param['crid'];
		if(isset($param['sex']))
			$wherearr[] = 'u.sex='.$param['sex'];
		if(!empty($wherearr))
			$sql.= ' where '.implode(' AND ',$wherearr);
		$sexcount = $this->ebhdb->query($sql)->row_array();
		return $sexcount['count'];
	}
	/*
	登录次数,按性别
	*/
	public function getLoginCount($param){
		$sql = 'select sum(u.logincount) sum from ebh_users u join ebh_roomusers ru on u.uid=ru.uid';
		if(!empty($param['crid']))
			$wherearr[] = 'ru.crid='.$param['crid'];
		if(isset($param['sex']))
			$wherearr[] = 'u.sex='.$param['sex'];
		if(!empty($wherearr))
			$sql.= ' where '.implode(' AND ',$wherearr);
		$sexcount = $this->ebhdb->query($sql)->row_array();
		return $sexcount['sum'];
	}
	
	/*
	学生开通的课程
	*/
	public function getstucourse($param){
		$sql = 'select p.pid,p.itemid,p.crid,p.folderid,i.iname from ebh_userpermisions p 
			join ebh_users u on p.uid = u.uid
			join ebh_pay_items i on i.itemid=p.itemid
			';
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
	
}

?>