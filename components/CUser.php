<?php

/**
 * CUser用户组件类
 */
class CUser extends CComponent {

    private $user = NULL;
    
    public function getloginuser() {
        if (isset($this->user))
            return $this->user;
        $input = EBH::app()->getInput();
        $usermodel = $this->model('user');
        $auth = $input->cookie('auth');
        if (!empty($auth)) {
            list($password, $uid) = explode("\t", authcode($auth, 'DECODE'));
            $uid = intval($uid);
            if ($uid <= 0) {
                return FALSE;
            }
            $user = $usermodel->getUserInfo($uid);
			
			if(empty($user) || $user['password'] != $password || $user['status'] == 0) {
				return false;
			}
            if(!empty($user)) {
            	//获取用户角色
/*            	if($user['type']==1){
            		$groupmodel = $this->model('Group');
            		$group = $groupmodel->getgroupbyuid($user['uid']);
            		$user['managegroupid'] = $group['groupid']; 
            	}else{
            		$user['managegroupid'] = 0;
            	}*/
				
                $lastlogintime = $input->cookie('lasttime');
                $lastloginip = $input->cookie('lastip');
                $user['lastlogintime'] = empty($lastlogintime) ? '' : date('Y-m-d H:i',$lastlogintime);
                $user['lastloginip'] = $lastloginip;
				$this->user = $user;
				return $user;
            }
        }
        return FALSE;
    }

}