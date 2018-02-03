<?php
/**
 * Ebh用户管理控制器
 */
class MemberController extends CAdminControl
{
	/**
	 * 支付类型
	 * @var array
	 */
	private $payfrom = array('','年卡（激活卡）开通','快钱开通','支付宝','人工开通','内部测试','农行支付','银联支付','余额支付','微信支付');

	public function __construct()
	{
		parent::__construct();
		//检查权限
		Ebh::app()->lib('Access')->checkModuleAccess('adminmember');
	}
	/**
	 * 用户搜索页面
	 */
	public function index()
	{
		//分页	
		$param['q'] = $this->input->get('q');
		$param['aq'] = $this->input->get('aq');
		
		$showtip = '';//未找到用户或未查询时候的提示信息
		//仅限有权限的学校
		$classroom_access = Ebh::app()->lib('Access')->getAccessCookie('classroom_access');
		$classroomstr = $this->formatToClassroomStr($classroom_access);
		//默认不显示用户列表，有搜索时才显示用户列表
		if (!empty($param['q']) && !empty($classroom_access))
		{
			if ($classroom_access != 'ALL')
			{
				$param['crids'] = $classroom_access;
			}
			$param['isstudent'] = 1;
			$param['page_size'] = 10;
			$param['record_count'] = $this->model('member')->getMemberCount($param);
			$param = page_and_size($param);//分页信息
			$memberlist = $this->model('member')->getMemberList($param);
			$pagestr = show_page($param['record_count'], $param['page_size']);
			if (empty($memberlist))
			{
				$showtip = '未找到该用户';
			}			
		}
		else
		{
			$memberlist = array();
			$pagestr = '';
			$showtip = '';
		}
				
		$firstuid = empty($memberlist) ? '' : $memberlist[0]['uid'];
		$this->assign('pagestr', $pagestr);
		$this->assign('numberstart', ($param['page'] - 1) * $param['page_size']);
		$this->assign('classroomstr', $classroomstr);
		$this->assign('q', $param['q']);
		$this->assign('aq', $param['aq']);
		$this->assign('memberlist', $memberlist);
		$this->assign('showtip', $showtip);
		$this->assign('firstuid', $firstuid);
		$this->display('member/index');
	}
	
	//添加用户
	public function add()
	{
		$rec = safeHtml($this->input->post());
		$dopost = $this->input->post('dopost');
		if (!empty($dopost) && $dopost == 'add')
		{
			$member = $this->model('member');
			$param['username'] = $rec['username'];
            $param['password'] = $rec['password'];
            $param['realname'] = $rec['realname'];
            $param['nickname'] = $rec['nickname'];
            $param['sex'] = (int)$rec['sex'];
			$param['dateline'] = time();
			if(!empty($rec['birthdate'])){
				$param['birthdate'] = strtotime($rec['birthdate']);
			}
			$param['phone'] = $rec['phone'];
			$param['mobile'] = $rec['mobile'];
			$param['email'] = $rec['email'];
			$param['qq'] = $rec['qq'];
			$param['msn'] = $rec['msn'];
			$param['native'] = $rec['native'];
			$param['citycode'] = $rec['address_qu']?$rec['address_qu']:($rec['address_shi']?$rec['address_shi']:$rec['address_sheng']);
			$param['address'] = $rec['address'];
			$param['face'] = $rec['face']['upfilepath'];
			$param['profile'] = $rec['profile'];
			
			if (empty($param['username']))
			{
				show_message('用户名不能为空');
			}
			elseif (strlen($param['username']) < 6 || strlen($param['username']) > 16)
			{
				show_message('用户名不要超过6-16个字符');
			}
			else
			{
				if ($this->model('ebhuser')->exists($param['username']) )
				{
					show_message('用户名已存在');
				}
			}
			
			if (empty($param['password']))
			{
				show_message('密码不能为空');
			}
			elseif (strlen($param['password']) < 6 || strlen($param['password']) > 12)
			{
				show_message('密码不要超过6-12个字符');
			}
			elseif ($param['password'] != $rec['confirm'])
			{
				show_message('两次密码输入不一致');
			}			
			
			$res = $member->addmember($param);
			$this->model('credit')->addCreditlog(array('uid'=>$res,'ruleid'=>1));
			$returnurl = !empty($rec['nextsubmit'])?'/member/add.html':'/member/add.html';
			if(isset($res))
			{
				admin_log('用户管理', '添加用户', $param['username'], $res);
				//同步SNS数据(当用户注册成功时同步)
				Ebh::app()->lib('Sns')->do_sync($res, 5);
				close_dialog();
			}
			else
			{
				$this->goback('添加失败!');
			}
		
		}
		else{
			$this->display('member/add');
		}		
	}
	
	//编辑用户
	public function edit()
	{
		$member = $this->model('member');		
		$dopost = $this->input->post('dopost');
		if (!empty($dopost) && $dopost == 'edit')
		{			
			$param = safeHtml($this->input->post());
			$this->check($param);
			
			//仅限有权限的学校下的用户
			$check_access = false;
			$classroom_access = Ebh::app()->lib('Access')->getAccessCookie('classroom_access');
			if ($classroom_access == '')
			{
				$check_access = false;
			}
			elseif ($classroom_access == 'ALL')
			{
				$check_access = true;
			}
			else
			{
				//所在学校
				$roomlist = $this->model('roomuser')->getroomlist($param['uid']);
				if (!empty($roomlist))
				{
					foreach ($roomlist as $key => $value)
					{
						if ($check_access === false)
						{
							$check_access =  Ebh::app()->lib('Access')->checkClassroomAccess($value['crid']);
						}
					}
				}
			}
			if ($check_access === false)
			{
				$this->goback('修改失败');	
			}
			
			if(!empty($param['birthdate'])){
				$param['birthdate'] = strtotime($param['birthdate']);
			}
			$param['face'] = $param['face']['upfilepath'];
			$param['citycode'] = $this->input->post('address_qu')?$this->input->post('address_qu'):($this->input->post('address_shi')?$this->input->post('address_shi'):$this->input->post('address_sheng'));
			
			if (!empty($param['password']) && (strlen($param['password']) < 6 || strlen($param['password']) > 12))
			{
				show_message('密码不要超过6-12个字符');
			}
			
			if($member->editmember($param)>0){
				$user_info = $this->model('ebhuser')->getuserbyuid($param['uid']);
				admin_log('用户管理', '编辑用户', $user_info['username'], $param['uid']);
				close_dialog();
			}else{
				$this->goback('修改失败或没有任何修改');
			}
		}
		else{
			$uid = $this->input->get('uid');
			$this->assign('token',createToken());
			$this->assign('formhash',formhash($uid));
			$memberdetail = $member->getmemberdetail($uid);
			$this->assign('memberdetail',$memberdetail);
			$this->display('member/edit');
		}
	}

	//用户详情
	public function detail()
	{
		$uid = $this->input->post('uid');
		
		//所在学校
		$roomlist = $this->model('roomuser')->getroomlist($uid);
		$myclass = array();//所在班级
		$payfolderlist = array();//已开通课程
		if (!empty($roomlist))
		{
			foreach ($roomlist as $key => $value)
			{
				//获取用户所在班级
				$myclass[$key] = $this->model('classes')->getClassByUid($value['crid'],$uid);
				$roomlist[$key]['classname'] = empty($myclass[$key]) ? '无' : $myclass[$key]['classname'];
				//获取用户已开通的课程
				$payfolderlist[$key] = $this->model('userpermission')->getUserPayFolderList(array('uid' => $uid, 'crid' => $value['crid']));
			}
		}		
		
		//开通记录
		$orderlist = $this->model('Payorder')->getOrderList(array('status' => 1, 'uid' => $uid));

		$this->assign('orderlist', $orderlist);
		$this->assign('payfrom', $this->payfrom);
		$this->assign('roomlist',$roomlist);
		$this->assign('payfolderlist',$payfolderlist);
		$this->assign('uid', $uid);
		$this->display('member/detail');
	}
	
	//开通网校
/*	public function classroomopen()
	{
		$uid = $this->input->get('uid');
		$userinfo = $this->model('ebhuser')->getuserbyuid($uid);
		$this->assign('userinfo', $userinfo);		
		$this->display('member/classroomopen');
	}*/
	
	//开通服务
	public function serviceopen()
	{
		$uid = $this->input->get('uid');
		$userinfo = $this->model('ebhuser')->getuserbyuid($uid);
		$this->assign('userinfo', $userinfo);
		//获取学生所在学校的信息
		$crlist = $this->model('classroom')->getUserClassroom($uid);
		$crname = "";
		$crid = 0;
		if(!empty($crlist)){
			$crname = $crlist[0]['crname'];
			$crid = $crlist[0]['crid'];
		}
		
		//检查管理员是否有该学校权限
		if (!Ebh::app()->lib('Access')->checkClassroomAccess($crid))
		{
			$crname = "";
			$crid = 0;
		}
		$this->assign('crid',$crid);
		$this->assign('crname',$crname);
		
		$this->display('member/serviceopen');
	}
	
	//订单详情
	public function orderview()
	{
		$orderid = $this->input->get('orderid');
		$order = $this->model('payorder')->getOrderById($orderid);
		$payfrom = array('','年卡开通','快钱开通','支付宝','人工开通','内部测试','农行支付','银联支付','余额支付','微信支付');
		$this->assign('payfrom', $this->payfrom);
		$this->assign('order', $order);
		$this->display('member/orderview');
	}
	
	/**
	 *操作跳转方法
	 */
	public function goback($note="操作成功,正在努力跳转中...",$returnurl="/"){
		$this->widget('note_widget',array('note'=>$note,'returnurl'=>$returnurl));
		exit;
	}
	/**
	 *安检方法,对页面提交过来的数据进行安全检查
	 *@author zkq
	 *
	 */
	public function check($param=array()){
		if(checkToken($param['token'])===false){
			$this->goback('请勿重复提交!');
		}
		if(formhash($param['uid'])!=$param['formhash']){
			$this->goback('参数被篡改!');
		}
		$message = array();
		$message['code'] = true;
		//其它检测...预留
		if($message['code']===false){
			$this->goback(implode('<br />',$message));
		}
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
	 * ajax检查用户名是否可用
	 * 可用则输出1，不可用则输出0
	 */
	function checkIsUserExist()
	{
		$username = $this->input->post('username');
		if (!empty($username) && $this->model('ebhuser')->exists($username) === false)
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
     * 奖金发放
     */
    public function bonus(){
        $bonus=$this->model('bonus');
        $request = $this->input->get();
        $page = Ebh::app()->getUri()->page;//当前页
        $pagesize = 10;
        $request['page']=$page;
        $param=array(
            'pagesize'=>$pagesize,
            'limit'=>(max(0,($page-1)*$pagesize)).", {$pagesize}",
        );
        $recordlist=$bonus->getRecordList($param);
        $count = $bonus->getRecordCount($param);
        //print_r($count);
        $pagestr = show_page($count, $pagesize);

        $this->assign('pagestr', $pagestr);
        $request['pagesize'] =$pagesize;
        $this->assign('recordlist',$recordlist);
        $this->display('member/bonus');
    }

    /**
     * 发放奖金页面
     */
    public function bonusissue(){

        $result=$this->input->post();
        $dopost = $this->input->post('dopost');
        $loginuser= Ebh::app()->user->getloginuser();
        if (!empty($result)&&$dopost == 'add'){
            if (empty($result['director'])){
                show_message('销售主管不能为空');
            }
            if (empty($result['title'])){
                show_message('标题不能为空');
            }
            $user= Array();
            if(!empty($result['jsonstr'])){
                foreach ($result['jsonstr']  as $key =>$v){
                    $user[$v[0]]['username']=$v[0];
                    $user[$v[0]]['balance']=$v[3];
                }
            }
            //查询用户
            $usernamearr = array_map(function(&$userl){return $userl['username'];}, $user);
            $userarr=$this->model('ebhuser')->getUserByUsernameArr($usernamearr);
            if(!empty($userarr)){
                foreach ( $userarr as $auser){
                    $balance = $user[$auser['username']]['balance'];
                    $this->process($auser,$balance);//人工充值
                }

            }
            $param=array(
                'status'=>1,
                'jsonstr'=>json_encode($result['jsonstr']),
                'director'=>$result['director'],
                'dateline'=>time(),
                'operator'=>$loginuser['realname'],
                'uid'=>$loginuser['uid'],
                'title'=>$result['title'],
                'totalmoney'=>$result['totalmoney']
            );
            $bonus = $this->model('bonus');
            $res=$bonus->addRecord($param);
            if($res){
                show_message('操作成功');
            }else{
                show_message('操作失败');
            }
        }else{
            $this->display('member/bonusissue');

        }

    }

    /**
     * 获取发放奖金的账户
     */
    public function getaccount(){
        $account = $this->input->post('account');
        $userinfo = $this->model('bonus')->getAccount($account);
        if(empty($userinfo)){
            echo json_encode(array('code'=>1,'msg'=>'处理失败','data'=>false));
        }else{
            echo json_encode(array('code'=>0,'msg'=>'处理成功','data'=>$userinfo));
        }

    }

    /**
     * 显示操作记录
     */
    public function  recordview(){
        $bid=$this->input->get('bid');
        $detail=$this->model('bonus')->getViewByBid($bid);
        $detail['jsonstr']=json_decode($detail['jsonstr'],true);
        $user= array();
        if(!empty($detail['jsonstr'])){
            foreach ($detail['jsonstr']  as $key =>$v){
                $user[$key]=$v[0];
            }
        }
        $userarr=$this->model('ebhuser')->getUserByUsernameArr($user);
        $detaillist = array();
        if ($userarr){
            if (!empty($detail['jsonstr'])){
                foreach ($detail['jsonstr'] as $key=> $v){
                    $detaillist[$key]=$v;
                    $detaillist[$key]['dateline']=$detail['dateline'];
                    foreach ( $userarr as  $val){
                        if($v['5']==$val['uid']){
                            $detaillist[$key]['balance']=$val['balance'];
                            $detaillist[$key]['prebalance']=(intval($val['balance'])-intval($v['3']));

                        }
                    }
                }
            }

        }

        $this->assign('detaillist',$detaillist);
        $this->display('member/bonusview');

    }

    /**
     * 手动充值处理操作
     *
     */

    public  function process($user,$balance){
        $rdata = array(
            'uid'=>$user['uid'],
            'cate'=>1,
            'dateline'=>time(),
            'status'=>0
        );
        $rdmodel = $this->model("Record");
        $rid = $rdmodel->add($rdata);
        if($rid<=0){
            exit();
        }
        $status = 0;	//支付返回时候再更新此值
        $chmodel = $this->model("Chargel");
        $fromip = $this->input->getip();
        $curvalue = $user['balance']+intval($balance);
        $chdata = array(
            'rid'=>$rid,
            'useuid'=>$user['uid'],
            'type'=>10,
            'value'=>$balance,
            'curvalue'=>$curvalue,
            'status'=>$status,
            'fromip'=>$fromip,
            'dateline'=>time()
        );
        $chargeid =  $chmodel->add($chdata);
        if($chargeid <= 0||$balance<=0||!is_numeric($balance)) {
            $msg = "参数不合法:chargeid:{$chargeid},blance:{$balance},";
            log_message($msg);
            echo $msg;
            exit();
        }else{
            //sleep(5);
            //充值成功 修改操作
            //支付宝交易号
            $ordernumber = '';
            $buyer_id = $user['uid'];
            $buyer_info = '';

            $chmodel = $this->model("Chargel");
            $charge = $chmodel->getOneByChargeid($chargeid);

            if(empty($charge)) {//订单不存在
                $msg = "订单不存在:chargeid:{$chargeid}";
                log_message($msg);
                echo $msg;
                exit();
            }
            if($charge['status'] == 1) {//
                $msg = '订单已处理，则不重复处理';
                log_message($msg);
                echo $msg;
                exit();
            }
            $param= array(
                'status'=>1,
                'ordernumber'=>$ordernumber,
                'paytime' => time(),
                'buyer_id' => $buyer_id,
                'buyer_info' => $buyer_info
            );
            //更新充值记录
            $ck = $chmodel->update($param,$chargeid);

            //更新用户账户余额
            $umodel = $this->model("ebhuser");
            $umodel->update(array('balance'=>$charge['value']),$charge['useuid']);
            //更新充值记录主表
            $rdmodel=$this->model("Record");
            $rdmodel->update(array('status'=>1,'dateline'=>time()),$charge['rid']);

            $msg = "用户:{$user['username']},充值前余额:{$user['balance']},充值金额:{$balance},充值后余额:{$charge['curvalue']} <br />";
            echo $msg;
            log_message($msg);
        }

    }
	/*
	解绑用户搜索页面
	*/
	public function delete(){
        $param = array();
        $memberlist = array();
		$qtel = $this->input->get('qtel');//电话号码
        $param['page_size'] = 10;
        $param['isstudent'] = 1;
        $q = $this->input->get('q');//关键字(用户名)
        $showtip = '';//未找到用户或未查询时候的提示信息

        //默认不显示用户列表，有搜索时才显示用户列表
        if (!empty($qtel) || ($q != null && $q != '')){
            if(!empty($qtel)){//手机号(精确搜索)
                $mobile = preg_match ('/^1[3-8]{1}\d{9}$/', $qtel);
                if($mobile){ $param['qtel'] = h($qtel); }
            }else{//(模糊搜索)
                $param['q'] = h($q);
            }
            $param['lastuid'] = Ebh::app()->lib('SphinxUtil')->getLastUid();//查询今天之前最后一条uid
            if(!empty($param['lastuid']) && ($param['lastuid'])>0){
                $todaycount = $this->model('member')->getMemberCount($param);   //获取当天新增用户数
                $todaypage = ceil($todaycount/$param['page_size']);
                $param['sphinx_count'] = $this->model('member')->getBindCount($param);
                $param['sphinx_count'] = !empty($param['sphinx_count']) ? $param['sphinx_count'] : 0;   //通过sphinx查到的用户数量
                $param['record_count'] = $todaypage*$param['page_size'] + $param['sphinx_count'];       //用于计算分页展示的总数量
                $param = page_and_size($param);     //分页信息

                //先展示当天新增用户
                if ($param['page'] <= $todaypage) {
                    $memberlist = $this->model('member')->getMemberList($param);
                } else {//展示sphinx查询到的用户
                    if(!empty($param['sphinx_count'])) {//sphinx有查询到用户则展示
                        $limit = explode(',', $param['limit']);
                        $limit[0] = ($limit[0]) - $todaypage * $param['page_size'];
                        $param['limit'] = implode(',', $limit);
                        $memberlist = $this->model('member')->getBindList($param);
                    }
                }
            }
            //分页展示
            $memberlist = !empty($memberlist) ? $memberlist : array();
            $pagestr = show_page($param['record_count'], $param['page_size']);
			if (empty($memberlist)){
                $showtip = '未找到该用户';
			}
		}else{
			$memberlist = array();
			$pagestr = '';
			$showtip = '';
		}
		$firstuid = empty($memberlist) ? '' : $memberlist[0]['uid'];
		$this->assign('pagestr', $pagestr);
		$this->assign('numberstart', ($param['page'] - 1) * $param['page_size']);
		$this->assign('qtel', $param['qtel']);
		$this->assign('q', $param['q']);
		$this->assign('memberlist', $memberlist);
		$this->assign('showtip', $showtip);
		$this->assign('firstuid', $firstuid);
		$this->display('member/delete');
	}

	/*
	删除用户
	*/
	public function dodelete(){
		$uid = $this->input->post('uid');
		$membermodel = $this->model('member');
		$user = $membermodel->getmemberdetail($uid);
		if(empty($user)){
			$msg = '用户不存在';
			echo json_encode(array('status'=>0,'msg'=>$msg));
		}elseif(!preg_match("/^1[3-8]{1}\d{9}$/",$user['username'])){
			$msg = '数据不匹配';
			echo json_encode(array('status'=>0,'msg'=>'数据不匹配'));
		}else{
			$msg = '成功';
			$param['username'] = $user['username'].'_0';
			$param['mobile'] = '';
			$param['uid'] = $user['uid'];
			$membermodel->editmember($param);
			$membermodel->unbind(array('uid'=>$param['uid'],'type'=>'mobile'));
			echo json_encode(array('status'=>1,'msg'=>'删除成功'));
		}
		$username = empty($user['username'])?'':$user['username'];
		admin_log('用户管理','删除用户','删除',$uid,"删除用户id:$uid,用户名:$username,结果:$msg");

	}
    /**
    *解绑用户
    */
    public function dounbind(){
        $post = $this->input->post();
        $uid = intval($post['uid']);
        $unbindtype = h($post['unbindtype']);
        if(empty($uid) || empty($unbindtype)){
            echo json_encode(array('status'=>0,'msg'=>'参数错误'));
            exit;
        }
        $membermodel = $this->model('member');
        $user = $membermodel->getmemberdetail($uid);
        $username = empty($user['username'])?'':$user['username'];
        if(empty($user)){
            $msg = '用户不存在';
            echo json_encode(array('status'=>0,'msg'=>$msg));
            exit;
        }else{
            $msg = '成功';
            $operation = '解绑';
            $param['uid'] = $user['uid'];
            if($unbindtype == 'delete'){
                $param['status'] = 0;
                $operation = '删除用户';
            }elseif($unbindtype == 'mobile'){
                $param[$unbindtype] = '';
                $operation = '解绑手机号';
            }elseif($unbindtype == 'email'){
                $param[$unbindtype] = '';
                $operation = '解绑邮箱';
            }elseif($unbindtype == 'wx'){
                $param['wxopid'] = '';
                $param['wxopenid'] = '';
                $param['wxunionid'] = '';
                $operation = '解绑微信';
            }elseif($unbindtype == 'qq'){
                $param['qqopid'] = '';
                $operation = '解绑QQ';
            }else{
                echo json_encode(array('status'=>0,'msg'=>'解绑类型不符'));
                exit;
            }

            $param['type'] = $unbindtype;
            $res = $membermodel->unbinduser($param);
            if(!empty($res) && ($res==true)){
                echo json_encode(array('status'=>1,'msg'=>$operation.'成功'));
                if(($unbindtype == 'delete') && !empty($user['groupid'])){
                    fastcgi_finish_request();//删除成功后同步处理用户信息
                    $this->apiServer = Ebh::app()->getApiServer('ebh');
                    if($user['groupid'] == 6){//学生
                        $students = $membermodel->getstudentinfo($uid);
                        if(!empty($students) && is_array($students)){
                            foreach ($students as $info){
                                if(!empty($info['uid']) && !empty($info['classid']) && !empty($info['crid'])){
                                    $ret = $this->apiServer->reSetting()->setService('Aroomv3.Student.del')->addParams($info)->request();
                                    if(empty($ret) || ($ret!=true)){
                                        log_message('客服系统完成删除学生操作后，删除用户id:'.$info['uid'].',用户名:'.$username.',所在班级:'.$info['classid'].',所在网校:'.$info['crid'].'信息失败');
                                    }
                                }
                            }
                        }
                    }elseif($user['groupid'] == 5){//教师
                        $teachers = $membermodel->getteacherinfo($uid);
                        if(!empty($teachers) && is_array($teachers)){
                            foreach ($teachers as $teacher){
                                if(!empty($teacher['tid']) && !empty($teacher['crid'])){
                                    $teacher['uid'] = $teacher['tid'];
                                    $ret = $this->apiServer->reSetting()->setService('Aroomv3.Teacher.del')->addParams($teacher)->request();
                                    if(empty($ret) || ($ret!=true)){
                                        log_message('客服系统完成删除教师操作后，删除用户id:'.$teacher['uid'].',用户名:'.$username.',所在网校:'.$teacher['crid'].'信息失败');
                                    }
                                }
                            }
                        }
                    }
                }
            }else{
                echo json_encode(array('status'=>0,'msg'=>$operation.'失败'));
            }
        }
        admin_log('用户管理',$operation,$operation,$uid,$operation."用户id:$uid,用户名:$username,结果:$msg");

    }
	

}
?>