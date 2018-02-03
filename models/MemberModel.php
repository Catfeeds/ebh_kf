<?php
/**
 *用户Model类
 */
class MemberModel extends CEbhModel
{	
	/**
	 * 获取用户列表
	 * @param array $param 参数
	 * @return array 返回用户列表
	 */
	public function getMemberList($param)
	{
		$wherearr = array();
		$sql = 'SELECT distinct(u.uid),u.username,u.realname,u.dateline,u.lastloginip,u.lastlogintime,u.logincount,u.status,b.is_mobile,b.is_email,b.is_qq,b.is_wx FROM ebh_users u';
        $sql .= ' left join ebh_binds b on u.uid=b.uid';
		if (!empty($param['crids']))
		{
			$sql .= ' JOIN ebh_roomusers ru on ru.uid=u.uid';
		}
        if (!empty($param['lastuid'])){
            $wherearr[] =  'u.uid>'.intval($param['lastuid']);
        }
        if(!empty($param['qtel'])) {
            $wherearr[] = 'u.mobile= '.$this->ebhdb->escape($param['qtel']);
        }
		if(isset($param['q']))
		{
			//如果$param['aq']为真则表示按username精确查询,否则按username,realname,nickname模糊查询
			if(!empty($param['aq'])){
				$wherearr[] =  'u.username=' . $this->ebhdb->escape($param['q']);
			}else{
				$wherearr[] =  "(u.username like '%" . $this->ebhdb->escape_str($param['q']) . "%' or u.realname like '%" . $this->ebhdb->escape_str($param['q']) . "%' or u.nickname like '%" . $this->ebhdb->escape_str($param['q']) . "%')";
			}
		}	
		if (!empty($param['crids']))
		{
			$wherearr[] =  'ru.crid in (' . $this->ebhdb->escape($param['crids']) . ')';
		}
		if (!empty($param['isstudent']))
		{
			$wherearr[] =  'u.groupid=6';
		}
		if(!empty($wherearr))
			$sql.= ' WHERE '.implode(' AND ',$wherearr);	
		if(!empty($param['displayorder'])) {
            $sql .= ' ORDER BY '.$param['displayorder'];
        } else {
            $sql .= ' ORDER BY uid desc';
        }
		if(!empty($param['limit']))
			$sql.= ' limit ' . $param['limit'];
		return $this->ebhdb->query($sql)->list_array();
	}
	
	/*
     * 用户总数
     * @param array $param
     * @return int 返回用户总数
    */
    public function getMemberCount($param)
	{
    	$wherearr = array();
    	$sql = 'select count(distinct(u.uid)) as count from ebh_users u ';
        $sql .= ' left join ebh_binds b on u.uid=b.uid';
        if (!empty($param['crids'])){
            $sql .= ' JOIN ebh_roomusers ru on ru.uid=u.uid';
        }
        if (!empty($param['lastuid'])){
            $wherearr[] =  'u.uid>'.intval($param['lastuid']);
        }
        if(!empty($param['qtel'])) {
            $wherearr[] = 'u.mobile= '.$this->ebhdb->escape($param['qtel']);
        }
		if(isset($param['q']))
		{
			//如果$param['aq']为真则表示按username精确查询,否则按username,realname,nickname模糊查询
			if(!empty($param['aq'])){
				$wherearr[] =  'u.username=' . $this->ebhdb->escape($param['q']);
			}else{
				$wherearr[] =  "(u.username like '%" . $this->ebhdb->escape_str($param['q']) . "%' or u.realname like '%" . $this->ebhdb->escape_str($param['q']) . "%' or u.nickname like '%" . $this->ebhdb->escape_str($param['q']) . "%')";
			}
		}	
		if (!empty($param['crids']))
		{
			$wherearr[] =  'ru.crid in (' . $this->ebhdb->escape($param['crids']) . ')';
		}
		if (!empty($param['isstudent']))
		{
			$wherearr[] =  'u.groupid=6';
		}
    	if (!empty($wherearr))
		{
    		$sql.= ' WHERE '.implode(' AND ',$wherearr);
    	}
		//echo $sql;
    	$row = $this->ebhdb->query($sql)->row_array();
    	return $row['count'];
    }
	
	/*
	修改会员
	@param array $param
	@return int
	*/
	public function editmember($param){
		$afrows=0;
		//修改user表信息
		if(!empty($param['username']))
			$userarr['username'] = $param['username'];
		if(!empty($param['password']))
			$userarr['password'] = md5($param['password']);
		if(isset($param['status']))
			$userarr['status'] = $param['status'];
		if(isset($param['realname']))
			$userarr['realname'] = $param['realname'];
		if(isset($param['nickname']))
			$userarr['nickname'] = $param['nickname'];
		if(isset($param['sex']))
			$userarr['sex'] = $param['sex'];
		if(isset($param['mobile']))
			$userarr['mobile'] = $param['mobile'];
		if(isset($param['email']))
			$userarr['email'] = $param['email'];
		if(isset($param['citycode']))
			$userarr['citycode'] = $param['citycode'];
		if(isset($param['address']))
			$userarr['address'] = $param['address'];
		if(isset($param['face']))
			$userarr['face'] = $param['face'];
		if(isset($param['lastlogintime']))
			$userarr['lastlogintime'] = $param['lastlogintime'];
		$wherearr = array('uid'=>$param['uid']);
		if (!empty($userarr)) {
            $afrows+= $this->ebhdb->update('ebh_users', $userarr, $wherearr);
        }
		//修改member表信息
		
		if(isset($param['birthdate']))
			$memberarr['birthdate'] = intval($param['birthdate']);
		if(isset($param['phone']))
			$memberarr['phone'] = $param['phone'];
		if(isset($param['qq']))
			$memberarr['qq'] = $param['qq'];
		if(isset($param['msn']))
			$memberarr['msn'] = $param['msn'];
		if(isset($param['native']))
			$memberarr['native'] = $param['native'];
		if(isset($param['profile']))
			$memberarr['profile'] = $param['profile'];
		if(isset($param['realname']))
			$memberarr['realname'] = $param['realname'];
		if(isset($param['nickname']))
			$memberarr['nickname'] = $param['nickname'];
		if(isset($param['sex']))
			$memberarr['sex'] = $param['sex'];
		if(isset($param['mobile']))
			$memberarr['mobile'] = $param['mobile'];
		if(isset($param['email']))
			$memberarr['email'] = $param['email'];
		if(isset($param['citycode']))
			$memberarr['citycode'] = $param['citycode'];
		if(isset($param['address']))
			$memberarr['address'] = $param['address'];
		if(isset($param['familyname']))
			$memberarr['familyname'] = $param['familyname'];
		if(isset($param['familyphone']))
			$memberarr['familyphone'] = $param['familyphone'];
		if(isset($param['familyjob']))
			$memberarr['familyjob'] = $param['familyjob'];
		if(isset($param['familyemail']))
			$memberarr['familyemail'] = $param['familyemail'];
		if(isset($param['hobbies']))
			$memberarr['hobbies'] = $param['hobbies'];
		if(isset($param['lovemusic']))
			$memberarr['lovemusic'] = $param['lovemusic'];
		if(isset($param['lovemovies']))
			$memberarr['lovemovies'] = $param['lovemovies'];
		if(isset($param['lovegames']))
			$memberarr['lovegames'] = $param['lovegames'];
		if(isset($param['lovecomics']))
			$memberarr['lovecomics'] = $param['lovecomics'];
		if(isset($param['lovesports']))
			$memberarr['lovesports'] = $param['lovesports'];
		if(isset($param['lovebooks']))
			$memberarr['lovebooks'] = $param['lovebooks'];
			
		$wherearr = array('memberid'=>$param['uid']);
		if (!empty($memberarr)) {
			$afrows+= $this->ebhdb->update('ebh_members', $memberarr, $wherearr);
        }
		return $afrows;
	}
	
	/*
	添加会员
	@param array $param
	@return int
	*/
	public function addmember($param){
		if(!empty($param['username']))
			$userarr['username'] = $param['username'];
		if(!empty($param['password']))
			$userarr['password'] = md5($param['password']);
		if(isset($param['realname']))
			$userarr['realname'] = $param['realname'];
		if(isset($param['nickname']))
			$userarr['nickname'] = $param['nickname'];
		if(!empty($param['dateline']))
			$userarr['dateline'] = $param['dateline'];
		if(isset($param['sex']))
			$userarr['sex'] = $param['sex'];
		if(!empty($param['mobile']))
			$userarr['mobile'] = $param['mobile'];
		if(!empty($param['citycode']))
			$userarr['citycode'] = $param['citycode'];
		if(isset($param['address']))
			$userarr['address'] = $param['address'];
		if(!empty($param['email']))
			$userarr['email'] = $param['email'];
		if(!empty($param['face']))
			$userarr['face'] = $param['face'];
		if(!empty($param['qqopid']))
			$userarr['qqopid'] = $param['qqopid'];
		if(!empty($param['sinaopid']))
			$userarr['sinaopid'] = $param['sinaopid'];
		if(!empty($param['schoolname']))
			$userarr['schoolname'] = $param['schoolname'];
		$userarr['status'] = 1;
		$userarr['groupid'] = 6;
		// var_dump($userarr);
		$uid = $this->ebhdb->insert('ebh_users',$userarr);
		if($uid){
			$memberarr['memberid'] = $uid;
			if(isset($param['realname']))
				$memberarr['realname'] = $param['realname'];
			if(isset($param['nickname']))
				$memberarr['nickname'] = $param['nickname'];
			if(isset($param['sex']))
				$memberarr['sex'] = $param['sex'];
			if(!empty($param['birthdate']))
				$memberarr['birthdate'] = $param['birthdate'];
			if(!empty($param['phone']))
				$memberarr['phone'] = $param['phone'];
			if(!empty($param['mobile']))
				$memberarr['mobile'] = $param['mobile'];
			if(!empty($param['native']))
				$memberarr['native'] = $param['native'];
			if(!empty($param['citycode']))
				$memberarr['citycode'] = $param['citycode'];
			if(isset($param['address']))
				$memberarr['address'] = $param['address'];
			if(!empty($param['msn']))
				$memberarr['msn'] = $param['msn'];
			if(!empty($param['qq']))
				$memberarr['qq'] = $param['qq'];
			if(!empty($param['email']))
				$memberarr['email'] = $param['email'];
			if(!empty($param['face']))
				$memberarr['face'] = $param['face'];
			if(isset($param['profile']))
				$memberarr['profile'] = $param['profile'];
			$memberid = $this->ebhdb->insert('ebh_members',$memberarr);
			// var_dump($uid.'___'.$memberid.'````');
			
		}
		return $uid;
	}
	
	/*
	会员详情
	@param int $uid
	@return array
	*/
	public function getmemberdetail($uid){
		$sql = 'select u.uid,u.username,u.realname,u.nickname,u.face,u.groupid,u.citycode,u.address,u.email,u.sex,m.phone,u.mobile,u.mysign,m.birthdate,m.qq,m.msn,m.native,m.credit,m.profile from ebh_users u left join ebh_members m on u.uid = m.memberid where uid = '.$uid;
		//echo $sql;exit;
		return $this->ebhdb->query($sql)->row_array();
	}
	
	/*
	解绑binds表
	*/
	public function unbind($param){
		if(empty($param['uid']) || empty($param['type']))
			return false;
		if(in_array($param['type'],array('mobile','email','qq','wx','weibo','paypass','bank'))){
			$bindtype = $param['type'];
			$setarr['is_'.$bindtype] = 0;
			$setarr[$bindtype.'_str'] = '';
			$wherearr['uid'] = $param['uid'];
			$this->ebhdb->update('ebh_binds',$setarr,$wherearr);
		}
	}

    /**
     * 获取绑定用户列表
     * @param array $param 参数
     * @return array 返回用户列表
     */
    public function getBindList($param){
        $wherearr = array();
        $sql = 'SELECT u.uid,u.username,u.realname,u.dateline,u.lastloginip,u.lastlogintime,u.logincount,u.status,b.is_mobile,b.is_email,b.is_qq,b.is_wx FROM ebh_users u';
        $sql .= ' left join ebh_binds b on u.uid=b.uid';
        if(isset($param['q']) || !empty($param['qtel'])){
            $param['q'] = $this->ebhdb->escape_str($param['q']);
            //加载sphinx
            $sphinxClient = Ebh::app()->lib('SphinxClient');
            $sphinx_config = Ebh::app()->getConfig()->load('sphinx');
            $sphinxClient->setServer($sphinx_config['host'], $sphinx_config['port']);
            $sphinxClient->setMatchMode(SPH_MATCH_EXTENDED2);
            //设置超时时间（毫秒）
            $sphinxClient->setMaxQueryTime(30000);
            $limit = explode(',',$param['limit']);
            //设置分页
            $sphinxClient->SetLimits($limit[0],$limit[1]);
            $sphinxClient->SetSortMode(SPH_SORT_EXTENDED,'@id DESC,@weight DESC');
            // 精确查询mobile,1个字段，模糊查询username,realname,nickname3个字段
            if(!empty($param['qtel'])){
                $res = $sphinxClient->query('@(mobile) *'.$param['qtel'].'*' , 'user');
            }else{
                $res = $sphinxClient->query('@(username,realname,nickname) *'.$param['q'].'*' , 'user');
            }
            if($res === false){
                return false;
            }else{
                if($res['total'] && $res['total'] > 0){
                    $total = $res['total'];
                    $ids = array();
                    foreach($res['matches'] as $uid=>$resultItem){
                        $ids[] = $uid;
                    }
                    $wherearr[] = 'u.uid in (' .implode(',',$ids) . ')';
                    unset($param['limit']);
                }
            }
        }
        if(!empty($wherearr)){
            $sql.= ' WHERE '.implode(' AND ',$wherearr);
        }
        $sql .= ' ORDER BY uid desc';
        if(!empty($param['limit'])){
            $sql.= ' limit ' . $this->ebhdb->escape_str($param['limit']);
        }
        return $this->ebhdb->query($sql)->list_array();
    }

    /*
     * 绑定用户总数
     * @param array $param
     * @return int 返回用户总数
    */
    public function getBindCount($param){
        $wherearr = array();
        $sql = 'SELECT count(1) as count FROM ebh_users u LEFT JOIN ebh_binds b on b.uid=u.uid';
        if(isset($param['q']) || !empty($param['qtel'])){
            //加载sphinx
            $sphinxClient = Ebh::app()->lib('SphinxClient');
            $sphinx_config = Ebh::app()->getConfig()->load('sphinx');
            $sphinxClient->setServer($sphinx_config['host'], $sphinx_config['port']);
            $sphinxClient->setMatchMode(SPH_MATCH_EXTENDED2);
            //设置超时时间（毫秒）
            $sphinxClient->setMaxQueryTime(30000);
            // 精确查询mobile,1个字段，模糊查询username,realname,nickname3个字段
            if(!empty($param['qtel'])){
                $res = $sphinxClient->query('@(mobile) *'.$param['qtel'].'*' , 'user');
            }else{
                $res = $sphinxClient->query('@(username,realname,nickname) *'.$param['q'].'*' , 'user');
            }
            if($res === false){
                return false;
            }else {
                if (isset($res['total'])) {
                    return $res['total'];
                }
            }
        }
        if(!empty($wherearr)){
            $sql.= ' WHERE '.implode(' AND ',$wherearr);
        }
        $row = $this->ebhdb->query($sql)->row_array();
        $row['count'] = isset($row['count']) ? $row['count'] : 0 ;
        return $row['count'];
    }

    /*
    解绑用户
    @param array $param
    @return int
    */
    public function unbinduser($param){
        $afrows = false;
        if(empty($param['uid']) || empty($param['type'])){
            return $afrows;
        }
        //修改user表信息
        $userarr = array();
        $wherearr = array();
        if(!empty($param['username']))
            $userarr['username'] = $param['username'];
        if(!empty($param['password']))
            $userarr['password'] = md5($param['password']);
        if(isset($param['status']))
            $userarr['status'] = $param['status'];
        if(isset($param['realname']))
            $userarr['realname'] = $param['realname'];
        if(isset($param['nickname']))
            $userarr['nickname'] = $param['nickname'];
        if(isset($param['sex']))
            $userarr['sex'] = $param['sex'];
        if(isset($param['mobile']))
            $userarr['mobile'] = $param['mobile'];
        if(isset($param['email']))
            $userarr['email'] = $param['email'];
        if(isset($param['qqopid']))
            $userarr['qqopid'] = $param['qqopid'];
        if(isset($param['wxopid']))
            $userarr['wxopid'] = $param['wxopid'];
        if(isset($param['wxopenid']))
            $userarr['wxopenid'] = $param['wxopenid'];
        if(isset($param['wxunionid']))
            $userarr['wxunionid'] = $param['wxunionid'];
        if(isset($param['citycode']))
            $userarr['citycode'] = $param['citycode'];
        if(isset($param['address']))
            $userarr['address'] = $param['address'];
        if(isset($param['face']))
            $userarr['face'] = $param['face'];
        if(isset($param['lastlogintime']))
            $userarr['lastlogintime'] = $param['lastlogintime'];
        $wherearr = array('uid'=>intval($param['uid']));
        $this->ebhdb->begin_trans();
        if (!empty($userarr)) {
            $this->ebhdb->update('ebh_users', $userarr, $wherearr);
            $afrows= true;
        }
        if ($this->ebhdb->trans_status() === false) {
            $this->ebhdb->rollback_trans();
            return false;
        }
        if(in_array($param['type'],array('mobile','email','qq','wx'))){
            $setarr = array();
            $whenarr = array();
            $bindtype = $param['type'];
            $setarr['is_'.$bindtype] = 0;
            $setarr[$bindtype.'_str'] = '';
            if($param['type'] == 'mobile'){
                $setarr['mobile'] = '';
            }
            $whenarr['uid'] = intval($param['uid']);
            $this->ebhdb->update('ebh_binds',$setarr,$whenarr);
            $afrows= true;
            if ($this->ebhdb->trans_status() === false) {
                $this->ebhdb->rollback_trans();
                return false;
            }
        }
        $this->ebhdb->commit_trans();
        return $afrows;
    }

    /*
    获取学生的班级和网校id
    @param int $uid
    @return array
    */
    public function getstudentinfo($uid){
        $sql = 'select cs.uid,cs.classid,cl.crid from ebh_classstudents cs 
                join ebh_classes cl on cs.classid=cl.classid
                join ebh_classrooms cr on cl.crid=cr.crid
                where cl.status=0 and cr.status=1 and cs.uid='.intval($uid);
        return $this->ebhdb->query($sql)->list_array();
    }
    /*
    获取教师的网校id
    @param int $uid
    @return array
    */
    public function getteacherinfo($uid){
        $sql = 'select tid,crid from ebh_roomteachers where status=1 and tid='.intval($uid);
        return $this->ebhdb->query($sql)->list_array();
    }
}