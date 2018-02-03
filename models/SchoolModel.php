<?php
/**
 *网校Model类
 */
class SchoolModel extends CEbhModel{

	public function getSchoolCount($param){
		$whereArray=array();
		$sql='select count(*) from ebh_classrooms';
		if (!empty($param['q']))
		{
    		$wherearr[] =  "( crname like '%" . $this->ebhdb->escape_str($param['q']) . "%' or domain like '%" . $this->ebhdb->escape_str($param['q']) . "%')";
    	}
        if (!empty($param['access']))
        {
            $wherearr[] =  " crid in (" . $this->ebhdb->escape_str($param['access']) . ")";
        }
        if (!empty($param['catids']))
        {
            $wherearr[] =  ' catid in (' . $this->ebhdb->escape_str($param['catids']) . ')';
        }
		if (isset($param['hastv'])){
			$wherearr[] = ' hastv = ' . intval($param['hastv']);
		}
		if (isset($param['ctype'])){
			$wherearr[] = ' ctype = ' . intval($param['ctype']);
		}
		if (!empty($wherearr))
    	{
			$sql.= ' WHERE '.implode(' AND ',$wherearr);
    	}

    	$row=$this->ebhdb->query($sql)->row_array();
    	return $row['count(*)'];

	}
	/**
	 * 获取网校信息
	 * @param  [type] $param [description] 条件（分页，关键字查询等）
	 * @return [type]        [description]网校信息数组
	 */
	public function getSchoolList($param){
		$whereArray=array();//查询条件
		$sql='select cr.crid,cr.domain,cr.crname,cr.begindate,cr.stunum,cr.status,u.username,u.realname,u.mobile from ebh_classrooms cr left join ebh_users u on u.uid = cr.uid';
		if (!empty($param['q']))
		{
    		$wherearr[] =  "( cr.crname like '%" . $this->ebhdb->escape_str($param['q']) . "%' or cr.domain like '%" . $this->ebhdb->escape_str($param['q']) . "%')";
    	}
        if (!empty($param['access']))
        {
            $wherearr[] =  " cr.crid in (" . $this->ebhdb->escape_str($param['access']) . ")";
        }
        if (!empty($param['catids']))
        {
            $wherearr[] =  ' cr.catid in (' . $this->ebhdb->escape_str($param['catids']) . ')';
        }
		if (isset($param['hastv'])){
			$wherearr[] = ' cr.hastv = ' . intval($param['hastv']);
		}
		if (isset($param['ctype'])){
			$wherearr[] = ' cr.ctype = ' . intval($param['ctype']);
		}
		if (!empty($wherearr))
    	{
			$sql.= ' WHERE '.implode(' AND ',$wherearr);
    	}

		$sql .= ' ORDER BY crid desc';
    	if (!empty($param['limit']))
		{
    		$sql.= ' limit ' . $param['limit'];
    	}

		return $this->ebhdb->query($sql)->list_array();
	}
	/*
	教室权限
	$param int $upid 区分老师/学生权限
	@return array
	*/
	public function getroompowerlist($upid){
		$sql = 'select c.catid,c.name from ebh_categories c where c.system=0 and c.visible=1 and c.upid ='.$upid;
		return $this->ebhdb->query($sql)->list_array();
	}
		/*
	共享平台分配列表
	@return array
	*/
	public function getsharelist(){
		$sql = 'select c.crid,c.crname from ebh_classrooms c where isshare = 1';
		return $this->ebhdb->query($sql)->list_array();
		
	}
	 public function getroomlist($param = array(), $select = '') {
        if (!empty($select))
            $sql = 'select ' . $select . ' from ebh_classrooms cr ';
        else
            $sql = 'select cr.crid,cr.upid,cr.catid,cr.crname,cr.summary,cr.dateline,cr.cface,cr.domain,cr.crprice  from ebh_classrooms cr ';
        $wherearr = array();
        if (isset($param['status']))
            $wherearr[] = 'cr.status=' . $param['status'];
        if (isset($param['upid'])) {
            $wherearr[] = 'cr.upid=' . $param['upid'];
        }
        if(isset($param['property'])){
        	if(is_array($param['property'])){
        		$wherearr[] = 'cr.property in ('.implode(',',$param['property']).')';
        	}else{
        		$wherearr[] = 'cr.property ='.intval($param['property']);
        	}
        }
        if(isset($param['isschool'])){
        	if(is_array($param['isschool'])){
        		$wherearr[] = 'cr.isschool in ('.implode(',',$param['isschool']).')';
        	}else{
        		$wherearr[] = 'cr.isschool ='.intval($param['isschool']);
        	}
        }
        if (!empty($param['filterorder']))
            $wherearr[] = 'cr.displayorder < ' . $param['filterorder'];
        if (!empty($wherearr))
            $sql .= ' WHERE ' . implode(' AND ', $wherearr);
        if (!empty($param['order']))
            $sql .= ' ORDER BY ' . $param['order'];
        else
            $sql .= ' ORDER BY cr.crid desc ';
        if (!empty($param['limit']))
            $sql .= ' limit ' . $param['limit'];
        else
            $sql .= ' limit 0,10';
        return $this->ebhdb->query($sql)->list_array();
    }
        /*
    域名是否存在
    @param string $domain
    */
    public function exists_domain($domain){
        $sql = 'select 1 from ebh_classrooms where domain = \''.$domain .'\' limit 1';
        return $this->ebhdb->query($sql)->row_array();
    }
    /*
    网校名是否存在
    @param string $domain
    */
    public function exists_crname($crname){
        $sql = 'select 1 from ebh_classrooms where crname = \''.$crname .'\' limit 1';
        return $this->ebhdb->query($sql)->row_array();
    }
    /*
    添加教室
    @param array $param
    @return int
    */
    public function addclassroom($param){
        if(isset($param['status']))
            $setarr['status'] = $param['status'];
        if(!empty($param['crname']))
            $setarr['crname'] = $param['crname'];
        if(!empty($param['cface']))
            $setarr['cface'] = $param['cface'];
        if(!empty($param['uid']))
            $setarr['uid'] = $param['uid'];
        if(!empty($param['catid']))
            $setarr['catid'] = $param['catid'];
        if(isset($param['upid']))
            $setarr['upid'] = $param['upid'];
        if(!empty($param['citycode']))
            $setarr['citycode'] = $param['citycode'];
        if(!empty($param['craddress']))
            $setarr['craddress'] = $param['craddress'];
        if(!empty($param['crphone']))
            $setarr['crphone'] = $param['crphone'];
        if(!empty($param['cremail']))
            $setarr['cremail'] = $param['cremail'];
        if(!empty($param['crqq']))
            $setarr['crqq'] = $param['crqq'];
                if(!empty($param['lng']))
            $setarr['lng'] = $param['lng'];
                if(!empty($param['lat']))
            $setarr['lat'] = $param['lat'];
        if(!empty($param['domain']))
            $setarr['domain'] = $param['domain'];
        if(!empty($param['maxnum']))
            $setarr['maxnum'] = $param['maxnum'];
        if(isset($param['crlabel']))
            $setarr['crlabel'] = $param['crlabel'];
        if(!empty($param['summary']))
            $setarr['summary'] = htmlspecialchars($param['summary']);
                if(!empty($param['message']))
            $setarr['message'] = $param['message'];
        if(isset($param['ispublic'])){
            $setarr['ispublic'] = $param['ispublic'];
        }else{
            $setarr['ispublic'] = 0;
        }
            
        if(isset($param['isshare'])){
            $setarr['isshare'] = $param['isshare'];
        }else{
            $setarr['isshare'] = 0;
        }
        if(isset($param['isschool']))
            $setarr['isschool'] = $param['isschool'];
        if(isset($param['grade']))
            $setarr['grade'] = $param['grade'];
        if(!empty($param['begindate']))
            $setarr['begindate'] = $param['begindate'];
        if(!empty($param['enddate']))
            $setarr['enddate'] = $param['enddate'];
        if(!empty($param['template']))
            $setarr['template'] = $param['template'];
        if(isset($param['modulepower']))
            $setarr['modulepower'] = $param['modulepower'];
        if(isset($param['crprice']))
            $setarr['crprice'] = $param['crprice'];
        if(isset($param['stumodulepower']))
            $setarr['stumodulepower'] = $param['stumodulepower'];
        if(isset($param['displayorder']))
            $setarr['displayorder'] = $param['displayorder'];
        if(isset($param['banner']))
            $setarr['banner'] = $param['banner'];
        if(isset($param['property'])){
            $setarr['property'] = $param['property'];
        }
        // $setarr = $this->db->escape_str($setarr);
        if(isset($param['profitratio'])){
            $setarr['profitratio'] = $param['profitratio'];

        }
        if(isset($param['floatadimg']))
            $setarr['floatadimg'] = $param['floatadimg'];
        if(isset($param['floatadurl']))
            $setarr['floatadurl'] = $param['floatadurl'];
        if(!empty($param['roompermission']))
        {
            $rparr = $param['roompermission'];
        }
        if(isset($param['showusername']))
            $setarr['showusername'] = $param['showusername'];
        if(isset($param['defaultpass']))
            $setarr['defaultpass'] = $param['defaultpass'];
        $setarr['dateline'] = time();
        $res = $this->ebhdb->insert('ebh_classrooms',$setarr);
        if($res && $rparr){//共享平台分配
            foreach ($rparr as $rv) {
            $rParam = array(
                            'crid'=>$res,
                            'moduleid'=>$rv,
                            'moduletype'=>1
                            );
            $this->ebhdb->insert('ebh_roompermissions',$rParam);
            }
        }
        
        return $res;
    }
        /*
    详情
    @param int $crid
    @return array
    */
    public function getclassroomdetail($crid){
        if(empty($crid)){
            return FALSE;
        }
        $sql = 'select c.catid,c.stunum,c.teanum,c.crid,c.crname,c.begindate,c.banner,c.upid,c.enddate,c.dateline,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.grade,c.template,c.profitratio,c.crprice,c.displayorder,c.property,u.username,u.uid,c.floatadimg,c.floatadurl,c.showusername,c.defaultpass from ebh_classrooms c left join ebh_users u on u.uid = c.uid where c.crid='.$crid;
        return $this->ebhdb->query($sql)->row_array();
    }
        /*
    教室所使用的共享平台
    @param int $crid
    @return array
    */
    public function getroompermission($crid){
        $sql = 'select r.moduleid from ebh_roompermissions r where r.crid='.$crid;
        return $this->ebhdb->query($sql)->list_array();
    }
    /*
    编辑教室
    @param array $param
    @return int
    */
    public function editclassroom($param){
        if(empty($param['crid'])){
            return false;
        }
        $param['crid'] = intval($param['crid']);
        if(isset($param['status']))
            $setarr['status'] = $param['status'];
        if(!empty($param['crname']))
            $setarr['crname'] = $param['crname'];
        if(isset($param['cface']))
            $setarr['cface'] = $param['cface'];
        if(!empty($param['uid']))
            $setarr['uid'] = $param['uid'];
        if(!empty($param['catid']))
            $setarr['catid'] = $param['catid'];
        if(isset($param['upid']))
            $setarr['upid'] = $param['upid'];
        if(!empty($param['citycode']))
            $setarr['citycode'] = $param['citycode'];
        if(isset($param['banner']))
            $setarr['banner'] = $param['banner'];
        if(!empty($param['craddress']))
            $setarr['craddress'] = $param['craddress'];
        if(!empty($param['crphone']))
            $setarr['crphone'] = $param['crphone'];
        if(!empty($param['cremail']))
            $setarr['cremail'] = $param['cremail'];
        if(isset($param['property'])){
            $setarr['property'] = $param['property'];
        }
        if(!empty($param['crqq']))
            $setarr['crqq'] = $param['crqq'];
                if(!empty($param['lng']))
            $setarr['lng'] = $param['lng'];
                if(!empty($param['lat']))
            $setarr['lat'] = $param['lat'];
        if(!empty($param['weibosina']))
            $setarr['weibosina'] = $param['weibosina'];
        if(!empty($param['domain']))
            $setarr['domain'] = $param['domain'];
        if(!empty($param['maxnum']))
            $setarr['maxnum'] = $param['maxnum'];
        if(isset($param['crlabel']))
            $setarr['crlabel'] = $param['crlabel'];
        if(!empty($param['summary']))
            $setarr['summary'] = htmlspecialchars($param['summary']);
                if(!empty($param['message']))
            $setarr['message'] = $param['message'];
        if(isset($param['ispublic']))
            $setarr['ispublic'] = $param['ispublic'];
        if(isset($param['isshare']))
            $setarr['isshare'] = $param['isshare'];
        
        if(isset($param['isschool']))
            $setarr['isschool'] = $param['isschool'];
        if(isset($param['grade']))
            $setarr['grade'] = $param['grade'];
        if(!empty($param['begindate']))
            $setarr['begindate'] = $param['begindate'];
        if(!empty($param['enddate']))
            $setarr['enddate'] = $param['enddate'];
        if(!empty($param['template']))
            $setarr['template'] = $param['template'];
        if(isset($param['modulepower']))
            $setarr['modulepower'] = $param['modulepower'];
        if(isset($param['crprice']))
            $setarr['crprice'] = $param['crprice'];
        if(isset($param['stumodulepower']))
            $setarr['stumodulepower'] = $param['stumodulepower'];
        
        if(isset($param['displayorder']))
            $setarr['displayorder'] = $param['displayorder'];
        // $setarr = $this->db->escape_str($setarr);
        if(isset($param['profitratio'])){
            $setarr['profitratio'] = $param['profitratio'];
        }
        if(isset($param['floatadimg']))
            $setarr['floatadimg'] = $param['floatadimg'];
        if(isset($param['floatadurl']))
            $setarr['floatadurl'] = $param['floatadurl'];
        if(isset($param['showusername']))
            $setarr['showusername'] = $param['showusername'];
        if(isset($param['defaultpass']))
            $setarr['defaultpass'] = $param['defaultpass'];
        $wherearr = array('crid'=>intval($param['crid']));
        $row = $this->ebhdb->update('ebh_classrooms',$setarr,$wherearr);
        
        return $row;
    }
        //修改共享平台分配
    public function editroompermission($rparr,$crid){
        $this->ebhdb->delete('ebh_roompermissions',array('crid'=>$crid));
        foreach ($rparr as $rv) {
            $rParam = array(
                'crid'=>$crid,
                'moduleid'=>$rv,
                'moduletype'=>1
            );
        $this->ebhdb->insert('ebh_roompermissions',$rParam);
        }
    }

}