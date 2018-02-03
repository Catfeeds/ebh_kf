<?php
/**
 * 访问权限类
 *@author fb
*/
class Access{
	private $uri = NULL;
	private $input = NULL;
	private $not_check_login_modules = array();//不需要检查登录状态的模块
	private $accesslist = NULL;//访问权限列表
		
	public function __construct()
	{
        $this->uri					= Ebh::app()->getUri();
        $this->input				= Ebh::app()->getInput();
		$this->not_check_login_modules	= array('default/login', 'default/check', 'play');
	}
	
	//登录检查
	public function checkLogin()
	{
		if (in_array($this->uri->codepath, $this->not_check_login_modules) || Ebh::app()->user->getloginuser() !== false)
		{
			return true;
		}
		else
		{
			header("Location:/default/login.html");
			exit;
		}
	}
	
	/** 
	 * 获取有访问权限的ID字符串
	 * 使用方法 Ebh::app()->lib('Access')->getAccessCookie('classroom_access')
	 * @param string $type 类型（module_access模块，classroom_access学校）
	 * @return 返回字符串，如果是所有权限，返回ALL,如果是部分权限，返回ID字符串，每个ID以“,”分隔
	 */	
	public function getAccessCookie($type = 'module_access')
	{
		if (!isset($this->accesslist))
		{
			$this->accesslist = authcode($this->input->cookie('accesslist'), 'DECODE');
		}
		list($module_access, $classroom_access) = explode("\t", $this->accesslist);
		return $$type;
	}
	//检查访问权限
	public function checkModuleAccess($module)
	{
		$module_access = $this->getAccessCookie('module_access');
		if (empty($module))
		{
			return false;
		}
		elseif ($module_access == 'ALL')
		{
			return true;	
		}
		elseif (strpos(',' . $module_access . ',', ',' . $module . ',') !== false)
		{
			return true;
		}
		else
		{
			show_message('您没有该模块权限，请联系管理员！', "window.location.href='/default/main.html'");
		}
		return false;
	}

	//检查学校访问权限
	public function checkClassroomAccess($classroom)
	{
		$classroom_access = $this->getAccessCookie('classroom_access');
		if (empty($classroom))
		{
			return false;
		}
		elseif ($classroom_access == 'ALL')
		{
			return true;	
		}
		elseif (strpos(',' . $classroom_access . ',', ',' . $classroom . ',') !== false)
		{
			return true;
		}
		else
		{
			return false;
		}
		return false;		
	}
	
	//检查菜单中的项目是否有权限
	public function checkMenuAccess($menulist)
	{
		$module_access = $this->getAccessCookie('module_access');		
		foreach ($menulist as $key => $value)
		{
			if ($module_access != 'ALL' && strpos(',' . $module_access . ',', ',admin' .$value['codepath'] . ',') === false)
			{
				unset($menulist[$key]);
			}
		}
		return $menulist;
	}
	//获取权限访问列表
	public function getAccessList($uid)
	{
		$module_access_list = array();
		$classroom_access_list = array();
		
		$user = Ebh::app()->model('user')->getUserInfo($uid);
		//获取该用户所有角色
		if (!empty($user['roleid']))
		{
			$param['roleid'] = $user['roleid'];
			$role_list = Ebh::app()->model('role')->getRoleList($param);
			if (!empty($role_list))
			{
				//处理模块访问列表
				foreach ($role_list as $role)
				{
					if (!empty($role['access']))
					{
						if ($role['access'] == 'ALL')
						{
							$module_access_list = array('ALL');
							break;
						}
						else
						{
							$module_access_list = array_merge($module_access_list, explode(',', $role['access']));
						}
					}
				}
				
				//处理学校访问列表
				foreach ($role_list as $role)
				{
					if (!empty($role['classroom']))
					{
						if ($role['classroom'] == 'ALL')
						{
							$classroom_access_list = array('ALL');
							break;
						}
						else
						{
							$classroom_access_list = array_merge($classroom_access_list, explode(',', $role['classroom']));
						}
					}
				}	
				
			}
		}
		else
		{
			$module_access_list = array();
			$classroom_access_list = array();
		}
		//print_r($module_access_list);
		//print_r($classroom_access_list);
		$module_access_list = array_unique($module_access_list);
		$classroom_access_list = array_unique($classroom_access_list);
		$access_str = implode(',', $module_access_list) . "\t" . implode(',', $classroom_access_list);
		//echo $access_str;
		return authcode($access_str, 'ENCODE');
	}

	//保存权限访问列表
	public function saveAccessList($uid)
	{
		$accesslist = $this->getAccessList($uid);
		//cookie在会话结束时过期			
		$this->input->setcookie('accesslist',$accesslist);
	}
	
	//删除
	public function deleteAccessList()
	{
		$this->input->setcookie('accesslist','',-3600);
	}
}
?>