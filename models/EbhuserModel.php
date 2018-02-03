<?php
/**
 *EBH用户Model类
 */
class EbhUserModel extends CEbhModel {

	/**
	 * 根据uid获取用户基本信息
	 * @param int $uid
	 * @return array 
	 */
	public function getuserbyuid($uid) {
		$sql = 'SELECT uid,username,groupid,realname,status,lastlogintime,sex,credit,email FROM ebh_users WHERE uid=' . intval($uid);
		return $this->ebhdb->query($sql)->row_array();
	}
	/*
	用户名是否存在
	@param string $username
	@return true存在 false不存在
	*/
	public function exists($username){
		$sql = 'select uid from ebh_users where username = \''.$this->db->escape_str($username).'\' limit 1';
		$row = $this->ebhdb->query($sql)->row_array();
		if($row)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 根据学校编号获取可以生产KEY的教师信息
	 * @param  int $crid 学校编号
	 * @return array       教师信息
	 */
	public function getKeyUser($crid) {
		$sql = 'SELECT u.uid,u.password FROM ebh_roomteachers rt join ebh_users u on u.uid = rt.tid where u.groupid=5 AND rt.crid=' . intval($crid) . ' AND u.status=1 AND rt.status=1 LIMIT 1';
		$user = $this->ebhdb->query($sql)->row_array();
		return $user;
	}

	/**
	 * 获取包含多个用户的数组
	 * @param  array $uid_array uid数组
	 * @return array            用户数组
	 */
	public function getuserarray($uid_array) {
		$user_array = array();
		if (!empty($uid_array) && is_array($uid_array))
		{
			$uid_array = array_unique($uid_array);
			$sql = 'SELECT uid,username,realname,email FROM ebh_users WHERE uid IN(' . implode(',', $uid_array) . ')';
			$row = $this->ebhdb->query($sql)->list_array();
			foreach ($row as $v)
			{
				$user_array[$v['uid']] = array('username' => $v['username'], 'realname' => $v['realname'],'email' => $v['email']);
			}
		}
		return $user_array;
	}


    //更据用户帐号查找用户信息
    public function getUserByUsernameArr($usernameArr,$isCount=false){
        if(is_array($usernameArr)){
            $sql = " select uid,username,balance,credit from ebh_users where username in ( "."'" . implode("','", $usernameArr) . "'"." )";
        }else{
            $sql = " select uid,username,balance,credit from ebh_users where username = '".$this->ebhdb->escape_str($usernameArr)."' limit 1";
        }

        if(count($usernameArr)>1){
            $row = $this->ebhdb->query($sql)->list_array();
            if($isCount==true){
                return count($row);
            }else{
                return $row;
            }
        }else{
            $row = $this->ebhdb->query($sql)->list_array();
            return ($isCount==true) ? 1 : $row;
        }
    }
    //更新表
    public function update($param,$uid) {
        $afrows = FALSE;    //影响行数
        $userarr = array();
        //修改user表信息
        if(!empty($param['username'])){
            $userarr['username'] = $param['username'];
        }
        if (!empty($param['password']))
            $userarr['password'] = md5($param['password']);
        if (isset($param['status']))
            $userarr['status'] = $param['status'];
        if (isset($param['realname']))
            $userarr['realname'] = $param['realname'];
        if (isset($param['nickname']))
            $userarr['nickname'] = $param['nickname'];
        if (isset($param['sex']))
            $userarr['sex'] = $param['sex'];
        if (isset($param['mobile']))
            $userarr['mobile'] = $param['mobile'];
        if (isset($param['email']))
            $userarr['email'] = $param['email'];
        if (isset($param['citycode']))
            $userarr['citycode'] = $param['citycode'];
        if (isset($param['address']))
            $userarr['address'] = $param['address'];
        if (isset($param['face']))
            $userarr['face'] = $param['face'];
        if(!empty($param['qqopid']))
            $userarr['qqopid'] = $param['qqopid'];
        if(!empty($param['sinaopid']))
            $userarr['sinaopid'] = $param['sinaopid'];
        if(!empty($param['lastlogintime']))
            $userarr['lastlogintime'] = $param['lastlogintime'];
        if(!empty($param['lastloginip']))
            $userarr['lastloginip'] = $param['lastloginip'];
        $sarr = array();
        if(isset($param['logincount']))
            $sarr['logincount'] = 'logincount+1';
        if(isset($param['balance'])){
            $sarr['balance'] = 'balance+'.$param['balance'];
        }
        $wherearr = array('uid' => $uid);

        $afrows = $this->ebhdb->update('ebh_users', $userarr, $wherearr, $sarr);

        return $afrows;
    }

    //获取帐号信息
    public  function getUserByName($param){
        if (empty($param)){
            return false;
        }else{
            $param=$this->ebhdb->escape_str($param);
        }
        $sql = "select username,realname,uid,credit from ebh_users WHERE username='$param'";
        //print_r($sql);die;
        $rows =  $this->ebhdb->query($sql)->row_array();
        return $rows;
    }

}