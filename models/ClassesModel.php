<?php
/**
 * 班级ClassesModel类
 */
class ClassesModel extends CEbhModel{
    /**
     * 获取教师的班级列表
     * @param type $crid
     * @param type $uid
     * @return type
     */
	public function getTeacherClassList($crid,$uid) {
        $sql = 'select c.classid,c.classname,c.stunum,c.grade,c.district from ebh_classteachers ct '.
                'join ebh_classes c on (ct.classid = c.classid) '.
                'where c.crid='.$crid.' and ct.uid = '.$uid.' and c.`status`=0 order by c.classid';
        return $this->ebhdb->query($sql)->list_array();
    }
    /**
     * 获取班级学生列表
     * @param array $queryarr
     * @return array
     */
    public function getClassStudentList($queryarr) {   
        $sql = 'select cs.classid,cs.uid,u.username,u.realname,u.sex,u.email,u.mobile,u.face from ebh_classstudents cs '.
                'join ebh_users u on (u.uid = cs.uid) ';
        $wherearr = array();
        $wherearr[] = 'u.status = 1';
        if(!empty($queryarr['classid']))
            $wherearr[] = 'cs.classid='.$queryarr['classid'];
        if(!empty($queryarr['classidlist']))
            $wherearr[] = 'cs.classid in ('.$queryarr['classidlist'].')';
        if(!empty($queryarr['q'])) {
            $wherearr[] = '(u.username like \'%'.$this->ebhdb->escape_str($queryarr['q']).'%\''.
                    ' or u.realname like \'%'.$this->ebhdb->escape_str($queryarr['q']).'%\')';
        }
        $sql .= ' WHERE '.implode(' AND ', $wherearr);
        if(!empty($queryarr['order'])) {
            $sql .= ' ORDER BY '.$queryarr['order'];
        } else {
            $sql .= ' ORDER BY cs.classid';
        }
		if(!empty($queryarr['limit']))
			$sql .= ' limit '.$queryarr['limit'];
		else {
			 if(empty($queryarr['page']) || $queryarr['page'] < 1)
				$page = 1;
			else
				$page = $queryarr['page'];
			$pagesize = empty($queryarr['pagesize']) ? 10 : $queryarr['pagesize'];
			$start = ($page - 1) * $pagesize ;
			$sql .= ' limit '.$start.','.$pagesize;
		}
        return $this->ebhdb->query($sql)->list_array();
    }
    /**
     * 获取班级学生记录数
     * @param array $queryarr
     * @return array
     */
    public function getClassStudentCount($queryarr) {
        $count = 0;
        $sql = 'select count(*) count from ebh_classstudents cs '.
                'join ebh_users u on (u.uid = cs.uid) ';
        $wherearr = array();
        $wherearr[] = 'u.status = 1';
        if(!empty($queryarr['classid']))
            $wherearr[] = 'cs.classid='.$queryarr['classid'];
        if(!empty($queryarr['classidlist']))
            $wherearr[] = 'cs.classid in ('.$queryarr['classidlist'].')';
        if(!empty($queryarr['q'])) {
            $wherearr[] = '(u.username like \'%'.$this->ebhdb->escape_str($queryarr['q']).'%\''.
                    ' or u.realname like \'%'.$this->ebhdb->escape_str($queryarr['q']).'%\')';
        }
        $sql .= ' WHERE '.implode(' AND ', $wherearr);
        $countrow = $this->ebhdb->query($sql)->row_array();
        if(!empty($countrow))
            $count = $countrow['count'];
        return $count;
    }
	/**
	*获取教师所在班级的班级作业
	*/
    public function getTeacherclassexam($param) {
        if(empty($param['uid']) || empty($param['crid']))
            return FALSE;
        $sql = 'select c.classid,c.classname,c.stunum,c.grade,c.district from ebh_classteachers ct '.
                'join ebh_classes c on (ct.classid=c.classid) '.
                'where ct.uid='.$param['uid'].' and c.crid='.$param['crid'].' and c.status=0';
        $cexams = $this->ebhdb->query($sql)->list_array();
        for($i = 0; $i < count($cexams); $i ++) {
            $cexams[$i]['examscount'] = 0;
            $cexams[$i]['quescount'] = 0;
            $cexams[$i]['lastexamdate'] = '';
			if(!empty($cexams[$i]['grade'])) {	//按所在年级算，那么就要算上教师的科目
				$myfolder = $this->getFolderByGrade($param['crid'],$cexams[$i]['grade'],$param['uid'],$cexams[$i]['district']);
				$folderid = empty($myfolder) ? 0 : $myfolder['folderid'];
				$countsql = 'select count(se.eid) examscount ,sum(se.quescount) as quescount from ebh_schexams se '.
					'where (se.classid='.$cexams[$i]['classid'].' and se.uid='.$param['uid'].') or (se.grade='.$cexams[$i]['grade'].' and se.district='.$cexams[$i]['district'].' and se.folderid='.$folderid.')';

				$lastsql = 'SELECT se.dateline from ebh_schexams se where (se.classid='.$cexams[$i]['classid'].' and se.uid='.$param['uid'].') or (se.grade='.$cexams[$i]['grade'].' and se.district='.$cexams[$i]['district'].' and se.folderid='.$folderid.') order by se.eid desc limit 0,1';
			} else {
				$countsql = 'select count(se.eid) examscount ,sum(se.quescount) as quescount from ebh_schexams se '.
					'where se.classid='.$cexams[$i]['classid'].' and se.uid='.$param['uid'];

				$lastsql = 'SELECT se.dateline from ebh_schexams se where se.classid='.$cexams[$i]['classid'].' order by se.eid desc limit 0,1';
			}
            $countrow = $this->ebhdb->query($countsql)->row_array();
            if(!empty($countrow)) {
                $cexams[$i]['examscount'] = $countrow['examscount'];
                $cexams[$i]['quescount'] = $countrow['quescount'];
            }
            
            $lastrow = $this->ebhdb->query($lastsql)->row_array();
            if(!empty($lastrow['dateline']))
                $cexams[$i]['lastexamdate'] = $lastrow['dateline'];
        } 
        return $cexams;
    }
	/**
	*根据年级等信息获取教师对应的科目编号
	*/
	public function getFolderByGrade($crid,$grade,$uid,$district) {
		$sql = "select f.folderid,f.foldername from ebh_teacherfolders tf join ebh_folders f ".
				"ON(tf.folderid = f.folderid) ".
				"where f.crid=$crid and tf.tid=$uid and f.grade=$grade and f.district=$district";
		return $this->ebhdb->query($sql)->row_array();
	}
	
	/*
	添加班级
	@param array $param crid,classname
	@return int $classid 班级号
	*/
	public function addclass($param){
		$setarr['crid'] = $param['crid'];
		$setarr['classname'] = trim($param['classname'],' ');
		$setarr['classname'] = str_replace('　','',$setarr['classname']);
		if(isset($param['grade']))
			$setarr['grade'] = $param['grade'];
		$setarr['dateline'] = SYSTIME;
		$this->ebhdb->insert('ebh_classes',$setarr);
	}
	
	/*
	班级名是否存在
	@param array $param crid,classname
	*/
	public function classnameexists($param){
		$wherearr[] = 'crid='.$param['crid'];
		$wherearr[] = 'classname=\''.$this->ebhdb->escape_str($param['classname']).'\'';
		if(!empty($param['classid']))
			$wherearr[] = 'classid='.$param['classid'];
		$sql = 'select 1 from ebh_classes';
		$sql.= ' where '.implode(' AND ',$wherearr);
		return $this->ebhdb->query($sql)->row_array();
	}
	
	/**
     * 获取学校的班级列表
     * @param type $crid
     * @return array
     */
    public function getroomClassList($crid) {
        $sql = 'select c.classid,c.crid,c.classname,c.stunum,c.grade from ebh_classes c '.
                'where c.crid='.$crid.' and c.`status`=0 order by c.classid';
        // echo $sql;
        return $this->ebhdb->query($sql)->list_array();
    }
	
	/*
	获取学校的班级数量
	@param int $crid
	*/
	public function getroomclasscount($crid){
		$sql = 'select count(*) count from ebh_classes c
			where c.crid='.$crid.' and c.`status`=0';
		$count = $this->ebhdb->query($sql)->row_array();
		return $count['count'];
	}
	/*
	删除班级
	@param array $param  classid
	*/
	public function deleteclass($param){
		$wherearr['classid'] = $param['classid'];
		$this->ebhdb->begin_trans();
		$this->ebhdb->delete('ebh_classteachers',$wherearr);
		$this->ebhdb->delete('ebh_classes',$wherearr);
		if ($this->ebhdb->trans_status() === FALSE) {
            $this->ebhdb->rollback_trans();
            return FALSE;
        } else {
            $this->ebhdb->commit_trans();
        }
		return TRUE;
	}
	/*
	班级详情
	@param array $param  classid,crid
	*/
	public function getclassdetail($param){
		$sql = 'select classid,classname,stunum,grade,district from ebh_classes
			where classid = '.$param['classid'].' and crid='.$param['crid'];
		return $this->ebhdb->query($sql)->row_array();
	}
	/*
	修改班级
	@param array $param  classname,classid
	*/
	public function editclass($param){
		$setarr = array();
		if(!empty($param['classname'])){
			$setarr['classname'] = trim($param['classname'],' ');
			$setarr['classname'] = str_replace('　','',$setarr['classname']);
		}
		if(!empty($param['stunum']))
			$ssetarr['stunum'] = 'stunum+'.$param['stunum'];
		if(isset($param['grade']))
			$setarr['grade'] = $param['grade'];
		$wherearr = array('classid'=>$param['classid']);
		
		return $this->ebhdb->update('ebh_classes',$setarr,$wherearr,$ssetarr);
	}
	
	/*
	选择班级的任课教师
	@param array $param  classid,teacherids
	*/
    public function chooseteacher($param){
        if(!empty($param['classid'])){
            $wherearr['classid'] = $param['classid'];
            //return $wherearr;
            $this->ebhdb->delete('ebh_classteachers',$wherearr);
        }
        $idarr = explode(',',$param['teacherids']);
        foreach($idarr as $id){
            $ctarr = array('uid'=>$id,'classid'=>$param['classid']);
            $this->ebhdb->insert('ebh_classteachers',$ctarr);
        }
    }
	
	/*
	删除班级学生
	@param array $param crid classid uid
	*/
	public function deletestudent($param){
		$this->ebhdb->begin_trans();
		if(!empty($param['classid']) && !empty($param['uid'])){
			$classarr['classid'] = $param['classid'];
			$classarr['uid'] = $param['uid'];
			$this->ebhdb->update('ebh_classes',array(),array('classid'=>$param['classid']),array('stunum'=>'stunum-1'));			
			$this->ebhdb->delete('ebh_classstudents',$classarr);
		}
		if(!empty($param['crid']) && !empty($param['uid'])){
			$roomarr['crid'] = $param['crid'];
			$roomarr['uid'] = $param['uid'];
			$this->ebhdb->update('ebh_classrooms',array(),array('crid'=>$param['crid']),array('stunum'=>'stunum-1'));
			$this->ebhdb->delete('ebh_roomusers',$roomarr);
		}
		if($this->ebhdb->trans_status()===FALSE) {
            $this->ebhdb->rollback_trans();
            return FALSE;
        } else {
            $this->ebhdb->commit_trans();
        }
        return TRUE;
	}
	
	/*
	添加学生到classstudent表
	@param array $param crid classid uid
	*/
	public function addclassstudent($param){
		$setarr['uid'] = $param['uid'];
		$setarr['classid'] = $param['classid'];
		$this->ebhdb->update('ebh_classes',array(),array('classid'=>$param['classid']),array('stunum'=>'stunum+1'));
		$this->ebhdb->update('ebh_classrooms',array(),array('crid'=>$param['crid']),array('stunum'=>'stunum+1'));
		return $this->ebhdb->insert('ebh_classstudents',$setarr);
	}
	/**
	*获取用户所在的班级信息
	*@param int $crid教室编号
	*@param int $uid 用户编号
	*/
	public function getClassByUid($crid,$uid) {
        if(empty($crid) || empty($uid)){
            return FALSE;
        }
		$sql = "SELECT cs.classid,c.classname,c.grade,c.district from  ebh_classstudents cs ".
				"JOIN ebh_classes c on (c.classid = cs.classid) ".
				"WHERE c.crid=$crid and cs.uid = $uid";
		return $this->ebhdb->query($sql)->row_array();

	}
	
	/*
	获取学校班级列表与作业数
	*/
	public function getRoomClassListExamCount($param) {
        $sql = 'select c.classid,c.classname,c.stunum,st.count,st.quescount,st.lastexamdate from ebh_classes c 
				left join (select se.classid,count(*) as count,sum(se.quescount) as quescount,max(se.dateline) as lastexamdate from ebh_schexams se where se.crid ='.$param['crid'].' group by se.classid) st on (st.classid=c.classid)';
                // 'where c.crid='.$param['crid'].' and c.`status`=0 order by c.classid';
		if(!empty($param['uid'])){
			$sql.='join ebh_classteachers ct on(ct.classid = c.classid)';
			$wherearr[]= 'ct.uid='.$param['uid'];
		}
		$wherearr[]= 'c.crid='.$param['crid'];
		$wherearr[]= 'c.`status`=0';
		$sql.=' where '.implode(' AND ',$wherearr);
		$sql.=' order by c.classid';
        return $this->ebhdb->query($sql)->list_array();
    }
	/**
	*获取教室下默认的班级信息，一般是最新添加的班级
	*/
	public function getDefaultClass($crid) {
		$sql = "select classid,classname from ebh_classes where crid=$crid and status=0 order by classid asc limit 1";
		return $this->ebhdb->query($sql)->row_array();

	}
	
	/*
	按班级名(和crid)获取班级信息
	*/
	public function getClassByClassname($param){
		if( empty($param['crid']) || empty($param['classname']) )
			return false;
		$sql = 'select classid from ebh_classes where crid='.$param['crid'].' and classname=\''.$this->ebhdb->escape_str($param['classname']).'\'';
		// echo $sql;
		return $this->ebhdb->query($sql)->row_array();
	}
	
	/*
	添加一个教师到班级
	*/
	public function addTeacherToClass($classtarr){
		// $ctarr = array('uid'=>$param['tid'],'classid'=>$param['classid']);
		// $this->ebhdb->insert('ebh_classteachers',$ctarr);
		$sql = 'insert into ebh_classteachers (uid,classid) values ';
		$oldsql = $sql;
		foreach($classtarr as $teacher){
			if(!empty($teacher['classidarr'])){
				foreach($teacher['classidarr'] as $classid){
					// $classid = $teacher['classid'];
					$uid = $teacher['uid'];
					$sql.= "($uid,$classid),";
				}
			}
		}
		if($sql == $oldsql){
			return;
		}
		$sql = rtrim($sql,',');
		$this->ebhdb->query($sql);
	}
	
	public function addMultipleStudent($classarr){
		$sql = 'insert into ebh_classstudents (uid,classid) values ';
		$uniqueclasses = array();
		foreach($classarr as $class){
			$classid = $class['classid'];
			$uid = $class['uid'];
			$sql.= "($uid,$classid),";
			if(!isset($uniqueclasses[$classid])){
				$uniqueclasses[$classid]=1;
			}
			else{
				$uniqueclasses[$classid]++;
			}
		}
		$crid = $classarr[0]['crid'];
		$stunum = count($classarr);
		foreach($uniqueclasses as $k=>$v){
			$this->ebhdb->update('ebh_classes',array(),array('classid'=>$k),array('stunum'=>'stunum+'.$v));
		}
		$this->ebhdb->update('ebh_classrooms',array(),array('crid'=>$crid),array('stunum'=>'stunum+'.$stunum));
		$sql = rtrim($sql,',');
		$this->ebhdb->query($sql);
	}

	//获取一个学校下面的所有的年级
	public function getAllGrades($crid = 0){
		if(empty($crid)){
			return false;
		}
		$sql = 'select c.classid,c.classname,grade,district from ebh_classes c where c.grade!=\'\' AND c.crid ='.intval($crid);
		return $this->ebhdb->query($sql)->list_array();
	}
	/**
	 *获取一个学校的所有老师布置的作业(包含已经提交的[istj],未提交的[nottj],提交已经批改的[pg])
	 *用于学校导出作业用
	 *
	 */
	public function getschoolexams($crid){
		$sql = 'select c.classname,c.stunum,se.eid,(select count(*) from ebh_schexamanswers scaa where scaa.eid = se.eid and scaa.tid!=0) as pg,se.answercount as tj ,se.title,se.crid,se.classid,se.grade,se.district,se.dateline,u.username,u.realname,f.foldername,se.folderid from ebh_classes  c 
				join ebh_schexams se on c.classid = se.classid   
				join ebh_users u on u.uid = se.uid
				left join ebh_folders f on f.folderid = se.folderid 
				where c.crid = '.$crid.' and se.status= 1 order by se.grade desc';
		return $this->ebhdb->query($sql)->list_array();
	}
	public function getstunum($crid,$grade,$district){
		$sql = 'select sum(stunum) as stunum from  ebh_classes  where crid = '.$crid.' AND grade='.$grade.' AND district = '.$district;
		$res =  $this->ebhdb->query($sql)->row_array();
		return $res['stunum'];
	}
	/**
	 *获取学校下对应年级所有的班级
	 */
	public function getClasses($param = array()){
		$sql = 'select * from ebh_classes cs ';
		$whereArr = array();
		if(!empty($param['crid'])){
			$whereArr[] = 'cs.crid = '.$param['crid'];
		}
		if(!empty($param['grade'])){
			$whereArr[] = 'cs.grade = '.$param['grade'];
		}
		if(!empty($whereArr)){
			$sql.= ' WHERE '.implode(' AND ', $whereArr);
		}
		return $this->ebhdb->query($sql)->list_array();
	}
	
	/*
	升班批量删除原有的班级对应关系,并添加新的对应关系
	*/
	public function studentUpgrade($param,$userlist){
		
		$sql = 'select cs.uid,cs.classid from ebh_classstudents cs join ebh_classes c on cs.classid=c.classid join ebh_classrooms cr on cr.crid=c.crid join ebh_users u on u.uid=cs.uid';
		$wherearr[] = ' cr.crid='.$param['crid'];
		$wherearr[] = ' u.username in ('.$param['usernames'].')';
		$sql.= ' where '.implode(' AND ',$wherearr);
		$clsstulist = $this->ebhdb->query($sql)->list_array();
		
		//删除旧的对应
		$csstr = '';
		$oldclassarr = array();
		foreach($clsstulist as $cs){
			$classid = $cs['classid'];
			$csstr.= '('.$cs['uid'].','.$classid.'),';
			if(isset($oldclassarr[$classid]))
				$oldclassarr[$classid]++;
			else
				$oldclassarr[$classid] = 1;
		}
		$csstr = rtrim($csstr,',');
		$delsql = 'delete from ebh_classstudents where (uid,classid) in ('.$csstr.');';
		$this->ebhdb->query($delsql);
		foreach($oldclassarr as $k=>$v){
			$this->ebhdb->update('ebh_classes',array(),array('classid'=>$k),array('stunum'=>'stunum-'.$v));
		}
		
		
		//增加新的对应
		$newcsstr = '';
		foreach($userlist as $iuser) {
			$uid = $iuser['uid'];
			$classid = $iuser['classid'];
			$newcsstr.= '('.$uid.','.$classid.'),';
			if(isset($newclassarr[$classid]))
				$newclassarr[$classid]++;
			else
				$newclassarr[$classid] = 1;
		}
		$newcsstr = rtrim($newcsstr,',');
		$insertsql = 'insert into ebh_classstudents (uid,classid) values '.$newcsstr;
		$this->ebhdb->query($insertsql);
		foreach($newclassarr as $k=>$v){
			$this->ebhdb->update('ebh_classes',array(),array('classid'=>$k),array('stunum'=>'stunum+'.$v));
		}
	}

	/**
	 *获取一个班级的老师
	 *
	 */
	public function getClassTeacherByClassid($classid = 0){
		$sql = 'select uid,classid,folderid from ebh_classteachers where classid = '.$classid;
		return $this->ebhdb->query($sql)->list_array();
	}
	
	/*
	班级学生的id
	*/
	public function getClassStudentUid($classid){
		$sql = 'select uid from ebh_classstudents where classid='.$classid;
		return $this->ebhdb->query($sql)->list_array();
	}
	/**
	*根据年级获取学生uid列表
	*/
	public function getStudentListByGrade($crid,$grade) {
		if(empty($crid) || empty($grade))
			return FALSE;
		$classsql = 'select cs.uid,cs.classid,c.classname from ebh_classstudents cs join ebh_classes c on (cs.classid = c.classid) where c.crid='.$crid.' and c.grade='.$grade.' order by c.classid';
		$classlist = $this->ebhdb->query($classsql)->list_array();
		if(empty($classlist))
			return FALSE;
		$uidstr = '';
		$studentlist = array();
		foreach($classlist as $myclass) {
			if(empty($uidstr))
				$uidstr = $myclass['uid'];
			else
				$uidstr .= ','.$myclass['uid'];
			$studentlist[$myclass['uid']] = $myclass;
		}
		//获取用户列表
		$usersql = 'select u.uid,u.username,u.realname,u.sex from ebh_users u where u.uid in ('.$uidstr.')';
		$userlist = $this->ebhdb->query($usersql)->list_array();
		foreach($userlist as $myuser) {
			if(isset($studentlist[$myuser['uid']])) {
				$studentlist[$myuser['uid']]['username'] = $myuser['username'];
				$studentlist[$myuser['uid']]['realname'] = $myuser['realname'];
				$studentlist[$myuser['uid']]['sex'] = $myuser['sex'];
			}
		}
		return $studentlist;

	}
	/*
	批量删除班级学生并移出学校
	*/
	public function deleteMultiStudentFromClass($param){
		$this->ebhdb->begin_trans();
		
		$this->ebhdb->delete('ebh_classstudents','classid='.$param['classid']);
		if(!empty($param['uids'])){
			$sql = 'delete from ebh_roomusers where crid='.$param['crid'].' and uid in ('.$param['uids'].')';
			$this->ebhdb->query($sql);
		}
		$this->ebhdb->update('ebh_classes',array(),array('classid'=>$param['classid']),array('stunum'=>'stunum-'.$param['stunum']));
		$this->ebhdb->update('ebh_classrooms',array(),array('crid'=>$param['crid']),array('stunum'=>'stunum-'.$param['stunum']));
		if ($this->ebhdb->trans_status() === FALSE) {
            $this->ebhdb->rollback_trans();
            return FALSE;
        } else {
            $this->ebhdb->commit_trans();
        }
		return TRUE;
	}

    public function getClass($classid){
        $sql='select c.classid,c.classname,c.crid,c.grade from ebh_classes c  where c.classid='.$this->ebhdb->escape($classid);
        return $this->ebhdb->query($sql)->row_array();
    }
        /*
    设置年级
    */
    public function setGrade($param){
    	return $this->ebhdb->update('ebh_classes',array('grade'=>intval($param['grade'])),array('classid'=>$param['classid']));
    }
}
