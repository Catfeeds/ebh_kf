<?php
/**
 * 用户角色权限控制器
 */
class PrivilegeController extends CAdminControl
{
	public function __construct()
	{
		parent::__construct();
		//检查权限
		Ebh::app()->lib('Access')->checkModuleAccess('adminprivilege');
	}
	
	/**
	 * 客服列表
	 */
	public function userlist()
	{
		$role_array = $this->model('role')->getRoleArray();
		//分页	
		$param['q'] = $this->input->get('q');
		if ($param['q'] == '请输入账号或姓名')
		{
			$param['q'] = '';
		}
		$param['roleid'] = $this->input->get('roleid');
		$param['page_size'] = 20;
		$param['record_count'] = $this->model('user')->getUserCount($param);
		$param = page_and_size($param);//分页信息
		$userlist = $this->model('user')->getUserList($param);
		$pagestr = show_page($param['record_count'], $param['page_size']);
		
		foreach ($userlist as $key => $value)
		{
			$userlist[$key]['role'] = $this->model('role')->getRoleName($value['roleid'], $role_array);
		}
		
		$this->assign('pagestr', $pagestr);
		$this->assign('numberstart', ($param['page'] - 1) * $param['page_size']);
		$this->assign('q', $param['q']);
		$this->assign('roleid', $param['roleid']);
		$this->assign('userlist', $userlist);
		$this->assign('role_array', $role_array);
		$this->display('privilege/userlist');
	}

	/**
	 * 添加客服
	 */
	public function useradd()
	{
		$user = array();
		$dopost = $this->input->post('dopost');
		if (!empty($dopost) && $dopost == 'add')
		{
			$user['username']	= $this->input->post('username');
			$user['password']	= $this->input->post('password');
			$user['realname']	= $this->input->post('realname');
			$user['sex'] 		= $this->input->post('sex');
			$user['roleid'] 	= $this->input->post('roleid');
			$user['mobile']		= $this->input->post('mobile');
			$user['address']	= $this->input->post('address');
			$user['email']		= $this->input->post('email');
			$user['remark']		= $this->input->post('remark');
			$user['dateline']	= SYSTIME;
			
			if (empty($user['username']))
			{
				show_message('用户名不能为空');
			}
			elseif (strlen($user['username']) < 6 || strlen($user['username']) > 16)
			{
				show_message('用户名不要超过6-16个字符');
			}
			else
			{
				if ($this->isUserExist($user['username']))
				{
					show_message('用户名已存在');
				}
			}
			
			if (empty($user['password']))
			{
				show_message('密码不能为空');
			}
			elseif (strlen($user['password']) < 6 || strlen($user['password']) > 12)
			{
				show_message('密码不要超过6-12个字符');
			}
			
			if (empty($user['realname']))
			{
				show_message('姓名不能为空');
			}
			
			if (!empty($user['roleid']))
			{
				$user['roleid'] = implode(',', $user['roleid']);
			}
			
			if ($insert_id = $this->model('user')->add($user))
			{
				admin_log('权限设置', '添加客服', $user['username'], $insert_id);
				close_dialog();
			}
			else
			{
				show_message('添加客服失败！');
			}
		}
		else
		{
			$this->assign('rolearray', $this->model('role')->getRoleArray(false));
			$this->display('privilege/useradd');
		}
	}

	/**
	 * 编辑客服
	 */
	public function useredit()
	{
		$user = array();
		$dopost = $this->input->post('dopost');
		if (!empty($dopost) && $dopost == 'edit')
		{
			$uid = $this->input->post('uid');
			if ($uid == 1)
			{
				show_message('该客服不能被编辑。');
			}

			$user['password']	= $this->input->post('password');
			$user['realname']	= $this->input->post('realname');
			$user['sex'] 		= $this->input->post('sex');
			$user['roleid'] 	= $this->input->post('roleid');
			$user['mobile']		= $this->input->post('mobile');
			$user['address']	= $this->input->post('address');
			$user['email']		= $this->input->post('email');
			$user['remark']		= $this->input->post('remark');
			
			if (!empty($user['password']) && (strlen($user['password']) < 6 || strlen($user['password']) > 12))
			{
				show_message('密码不要超过6-12个字符');
			}
			
			if (empty($user['realname']))
			{
				show_message('姓名不能为空');
			}
			
			if (!empty($user['roleid']))
			{
				$user['roleid'] = implode(',', $user['roleid']);
			}
			else
			{
				$user['roleid'] = '';
			}
			
			if ($this->model('user')->update($user, $uid))
			{
				$user_info = $this->model('user')->getUserInfo($uid);
				admin_log('权限设置', '编辑客服', $user_info['username'] , $uid);
				//判断是否要刷新权限
				$login_user = $user = Ebh::app()->user->getloginuser();
				if ($login_user['uid'] == $uid)
				{
					//刷新权限
					Ebh::app()->lib('Access')->saveAccessList($login_user['uid']);
				}
				//关闭dialog
				close_dialog();
			}
			else
			{
				show_message('编辑客服失败！');
			}
		}
		else
		{
			$uid = $this->input->get('uid');
			if ($uid == 1)
			{
				show_message('该用户不能被编辑。');
			}
			$user = $this->model('user')->getOneByUid($uid);

			$this->assign('user', $user);
			$this->assign('rolearray', $this->model('role')->getRoleArray(false));
			$this->display('privilege/useredit');
		}
	}

	//删除客服
	public function userdelete()
	{
		$uid = $this->input->get('uid');
		if ($uid > 1)
		{
			$user_info = $this->model('user')->getUserInfo($uid);
			if($this->model('user')->delete($uid))
			{
				admin_log('权限设置', '删除客服', $user_info['username'], $uid);
				echo json_encode(array('status' => true, 'msg' => '删除成功！'));
				exit;
			}
		}
		echo json_encode(array('status' => false, 'msg' => '删除失败！'));
		exit;
	}
	
	//锁定客服
	public function userlock()
	{
		$uid = $this->input->get('uid');
		if ($uid > 1)
		{
			if($this->model('user')->lock($uid))
			{
				$user_info = $this->model('user')->getUserInfo($uid);
				admin_log('权限设置', '锁定客服', $user_info['username'], $uid);
				echo json_encode(array('status' => true, 'msg' => '锁定成功！'));
				exit;
			}
		}
		echo json_encode(array('status' => false, 'msg' => '锁定失败！'));
		exit;
	}
	
	//解锁客服
	public function userunlock()
	{
		$uid = $this->input->get('uid');
		if ($uid > 1)
		{
			if($this->model('user')->unlock($uid))
			{
				$user_info = $this->model('user')->getUserInfo($uid);
				admin_log('权限设置','解锁客服',$user_info['username'],$uid);
				echo json_encode(array('status' => true, 'msg' => '解锁成功！'));
				exit;
			}
		}
		echo json_encode(array('status' => false, 'msg' => '解锁失败！'));
		exit;
	}
	
	/**
	 * ajax检查用户名是否可用
	 * 可用则输出1，不可用则输出0
	 */
	function checkIsUserExist()
	{
		$username = $this->input->post('username');
		if (!empty($username) && $this->isUserExist($username) === false)
		{
			echo '1';
			exit;
		}
		else
		{
			echo '0';
			exit;
		}
	}

	/**
	 * 检查用户名是否唯一
	 * @username string 用户名
	 * @return boolean 已存在返回false，不存在返回true
	 */
	function isUserExist($username)
	{
		$user_info = $this->model('user')->getLoginInfo($username);
		if (empty($user_info))
		{
			return false;
		}
		return true;
	}
	
	/**
	 * 角色列表
	 */
	public function rolelist()
	{
		//分页	
		$param['q'] = Ebh::app()->getInput()->get('q');
		$param['page_size'] = 20;
		$param['record_count'] = $this->model('role')->getRoleCount($param);
		$param = page_and_size($param);//分页信息
		$rolelist = $this->model('role')->getRoleList($param);
		$pagestr = show_page($param['record_count'], $param['page_size']);

		//模块列表
		$modulelist = $this->model('menu')->getModuleList();
		
		//获得该客服的模块权限和学校权限
		$temp_array = array();
		foreach ($rolelist as $key => $value)
		{
			$rolelist[$key]['access'] = implode('/', $this->formatToAccessArray($value['access'], $modulelist));
			$rolelist[$key]['classroom'] = $this->formatToClassroomStr($value['classroom']);
		}
		
		
		$this->assign('pagestr', $pagestr);
		$this->assign('numberstart', ($param['page'] - 1) * $param['page_size']);
		$this->assign('q', $param['q']);		
		$this->assign('rolelist', $rolelist);
		$this->display('privilege/rolelist');
	}
	
	/**
	 * 模块ID字符串转换成模块名称数组
	 * @param string accessIds
	 * @param array 返回键名为模块代码，键值为模块名称的数字。例如:array('adminprivilege' => '权限设置')
	 */	
	public function formatToAccessArray($accessIds, $modulelist)
	{
		$temp_array = array();
		$module_array = array();
		if ($accessIds == 'ALL')
		{
			$module_array = $modulelist;
		}
		elseif (!empty($accessIds))
		{
			$temp_array = explode(',', $accessIds);
			foreach ($temp_array as $value)
			{
				$module_array[$value] = $modulelist[$value];
			}
		}
		
		return $module_array;
	}
	
	/**
	 * 学校ID字符串转换成学校名称数组
	 * @param string accessIds
	 * @param array 返回键名为学校ID，键值为学校名称
	 */
	public function formatToClassroomStr($classroomIds)
	{
		$classroom_str = '';
		if ($classroomIds == 'ALL')
		{
			$classroom_str = '所有学校';
		}
		elseif (!empty($classroomIds))
		{
			$classroom_list = $this->model('classroom')->getClassroomListByIds($classroomIds);
			$classroom_list = array_reduce($classroom_list, create_function('$v,$w', '$v[$w["crid"]]=$w["crname"];return $v;'));
			if (is_array($classroom_list))
			{
				$classroom_str = implode('/', $classroom_list);
			}
		}
		
		return $classroom_str;
	}
	
	/**
	 * 添加角色
	 */
	public function roleadd()
	{
		$role = array();
		$dopost = $this->input->post('dopost');
		//有post时添加
		if (!empty($dopost) && $dopost == 'add')
		{
			$role['rolename']	= $this->input->post('rolename');
			$role['access']		= $this->input->post('access');
			$role['classroom']	= $this->input->post('classroom');
			$role['status']		= 1;
			$role['remark']		= $this->input->post('remark');
			$role['dateline']	= SYSTIME;
			
			$role['access'] = empty($role['access']) ? '' : implode(',', $role['access']);
			
			if (empty($role['rolename']))
			{
				show_message('角色名称不能为空');
			}
			
			if ($this->input->post('rangetype') == 0)
			{
				$role['classroom'] = 'ALL';
			}
			elseif ($this->input->post('rangetype') == 1)
			{
				if (!empty($role['classroom']))
				{
					$role['classroom'] = array_unique($role['classroom']);
					$role['classroom'] = implode(',', $role['classroom']);
				}
				else
				{
					$role['classroom'] = '';
				}
			}
			
			//print_r($role);
			if ($insert_id = $this->model('role')->add($role))
			{
				admin_log('权限设置', '添加角色', $role['rolename'], $insert_id);
				close_dialog();
			}
		}
		else
		{
			//功能模块列表
			$modulelist = $this->model('menu')->getModuleList();
			$this->assign('modulelist', $modulelist);
			$this->display('privilege/roleadd');
		}
		
	}
	
	/**
	 * 编辑角色
	 */
	public function roleedit()
	{
		$role = array();
		$dopost = $this->input->post('dopost');
		//有post时添加
		if (!empty($dopost) && $dopost == 'edit')
		{
			$roleid				= $this->input->post('roleid');
			$role['rolename']	= $this->input->post('rolename');
			$role['access']		= $this->input->post('access');
			$role['classroom']	= $this->input->post('classroom');
			$role['remark']		= $this->input->post('remark');

			$role['access'] = empty($role['access']) ? '' : implode(',', $role['access']);
			
			if (empty($role['rolename']))
			{
				show_message('角色名称不能为空');
			}
			
			if ($this->input->post('rangetype') == 0)
			{
				$role['classroom'] = 'ALL';
			}
			elseif ($this->input->post('rangetype') == 1)
			{
				if (!empty($role['classroom']))
				{
					$role['classroom'] = array_unique($role['classroom']);
					$role['classroom'] = implode(',', $role['classroom']);
				}
				else
				{
					$role['classroom'] = '';
				}
			}
			
			//print_r($role);
			$this->model('role')->update($role, $roleid);
			admin_log('权限设置', '编辑角色', $role['rolename'], $roleid);
			
			//判断是否要刷新权限
			$login_user = $user = Ebh::app()->user->getloginuser();
			if (strpos(',' . $login_user['roleid'] . ',', ',' . $roleid . ',') !== false)
			{
				//刷新权限
				Ebh::app()->lib('Access')->saveAccessList($login_user['uid']);
			}
						
			close_dialog();			
		}
		else
		{
			$roleid = $this->input->get('roleid');
			$role = $this->model('role')->getOneByRoleid($roleid);
			if ($role['classroom'] == 'ALL')
			{
				$role['rangetype'] = 0;
			}
			elseif (!empty($role['classroom']))
			{
				$role['rangetype'] = 1;
				$role['classroom_list'] = $this->model('classroom')->getClassroomListByIds($role['classroom']);
			}
			else
			{
				$role['rangetype'] = 1;
			}
		
			//print_r($role);
			//功能模块列表
			$modulelist = $this->model('menu')->getModuleList();
			$this->assign('role', $role);
			$this->assign('modulelist', $modulelist);
			$this->display('privilege/roleedit');
		}
		
	}
	
	//角色删除
	public function roledelete()
	{
		$roleid = $this->input->get('roleid');
		if ($roleid > 1)
		{			
			$role_info = $this->model('role')->getOneByRoleid($roleid);
			if($this->model('role')->delete($roleid))
			{
				admin_log('权限设置', '删除角色', $role_info['rolename'], $roleid);
				echo json_encode(array('status' => true, 'msg' => '删除成功！'));
				exit;
			}
		}
		echo json_encode(array('status' => false, 'msg' => '删除失败！'));
		exit;
	}
	
	
	//修改密码
	public function passwordedit()
	{
		$password = array();
		$dopost = $this->input->post('dopost');
		//有post时修改
		if (!empty($dopost) && $dopost == 'edit')
		{
			$old_password = $this->input->post('old_password');
			$new_password = $this->input->post('new_password');
			$new_password_repeat = $this->input->post('new_password_repeat');
			if (!empty($old_password) && !empty($new_password))
			{
				if ($new_password != $new_password_repeat)
				{
					show_message('两次密码输入不一致！');
				}
				else
				{
					$user = Ebh::app()->user->getloginuser();
					if ($user['password'] == md5($old_password))
					{
						$this->model('user')->update(array('password' => $new_password), $user['uid']);
						admin_log('权限设置', '修改密码');
						show_message('密码修改成功，请重新登录！', "window.top.location.href='/default/logout.html'");
					}
					else
					{
						show_message('原始密码错误！');
					}
				}
			}
			else
			{
				show_message('请输入密码！');
			}
		}
		else
		{
			$this->display('privilege/passwordedit');
		}
	}

}