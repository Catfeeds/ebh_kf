<?php
/**
 * 用户空间控制器
 */
class SpaceController extends CAdminControl{
	public function __construct()
	{
		parent::__construct();
		//检查权限
		Ebh::app()->lib('Access')->checkModuleAccess('adminspace');//检测权限
	}

	/**
	 * 新鲜事
	 */
	public function newthing(){
		$request = $this->input->get();
		$feedsmodel = $this->model('feeds');
		$page = Ebh::app()->getUri()->page;//当前页
		$request['page']=$page;
		$pagesize = 20;
		$astatus = intval($request['admin_status']);
		if ($request['cat'] != '')
		{
			$cat = intval($request['cat']);
		}
		else
		{
			$request['cat'] = '';
			$cat = -1;
		}

		$param= array(
			'role'=>'admin',
			'pagesize'=>$pagesize,
			'limit'=>(max(0,($page-1)*$pagesize)).", {$pagesize}",
			'cat'=>$cat,
			'admin_status'=>$astatus,
			'q'=>$request['q']
			);
		$count = 0;
		$feedslist = array();
		$count = $feedsmodel->getfeedscount($param);
		$feedslist = $feedsmodel->getfeedslist($param);
		if(!empty($feedslist)){
			foreach($feedslist as &$v){
	            $v['message'] = json_decode($v['message'],true);
	            $v['message'] = $this->model('billchecks')->str_change($v['message']);
	            if(!empty($v['message']['refer'])){
	                $v['message']['refer'] = $this->model('billchecks')->str_change($v['message']['refer']);
	            }
	        }
	        $uid_array = array();
			$users = array();
			foreach($feedslist as $key=>$value)
			{
				$uid_array[] = $value['fromuid'];
	            $checkname = $this->model('user')->getOneByUid($value['admin_uid']);
	            $feedslist[$key]['checkname'] = $checkname['username'];
			}
			$users = $this->model('Ebhuser')->getUserArray($uid_array);
		}
//        p($feedslist);die;
		
		$this->assign('users', $users);

		$pagestr = show_page($count, $pagesize);
		// var_dump($request);

		//类别
		$category = array(
			'1' => '心情',
			'2' => '原创日志',
			'3' => '照片',
			'4' => '转载日志'
		);
		$this->assign('category', $category);

		$this->assign('pagestr', $pagestr);
		$this->assign("feedslist", $feedslist);
		$request['pagesize'] =$pagesize;
		$this->assign("request",$request);
		$this->display('space/newthing');
	}

	public function diary(){
		$request = $this->input->get();
		$blogmodel = $this->model('blog');
		$page = Ebh::app()->getUri()->page;//当前页
		$request['page']=$page;
		$pagesize = 20;
		$astatus = intval($request['admin_status']);
		if ($request['cat'] != '')
		{
			$cat = intval($request['cat']);
		}
		else
		{
			$request['cat'] = '';
			$cat = -1;
		}

		$param= array(
			'role'=>'admin',
			'pagesize'=>$pagesize,
			'limit'=>(max(0,($page-1)*$pagesize)).", {$pagesize}",
			'cat'=>$cat,
			'admin_status'=>$astatus,
			'q'=>$request['q']
			);
		$count = 0;
		$bloglist = array();
		$count = $blogmodel->getblogcount($param);
		$bloglist = $blogmodel->getbloglist($param);
//        $bloglist = $this->model('billchecks')->str_change($bloglist,true);//敏感字处理
		$uid_array = array();
		$users = array();
		if(!empty($bloglist)){
			foreach($bloglist as &$value)
			{
				$uid_array[] = $value['uid'];
	            $checkname = $this->model('user')->getOneByUid($value['admin_uid']);
	            $value['checkname'] = $checkname['username'];
	            $value['title'] = $this->model('billchecks')->str_change($value['title']);//敏感字处理
			}
	//        p($bloglist);die;
			$users = $this->model('Ebhuser')->getUserArray($uid_array);
		}
		$this->assign('users', $users);

		$pagestr = show_page($count, $pagesize);
		// var_dump($request);

		$this->assign('pagestr', $pagestr);
		$this->assign("bloglist", $bloglist);
		$request['pagesize'] =$pagesize;
		$this->assign("request",$request);
		$this->display('space/diary');
	}

	public function photo(){
		$request = $this->input->get();
		$imagemodel = $this->model('Image');
		$page = Ebh::app()->getUri()->page;//当前页
		$request['page']=$page;
		$pagesize = 20;
		$astatus = intval($request['admin_status']);
		if ($request['cat'] != '')
		{
			$cat = intval($request['cat']);
		}
		else
		{
			$request['cat'] = '';
			$cat = -1;
		}

		$param= array(
			'role'=>'admin',
			'pagesize'=>$pagesize,
			'limit'=>(max(0,($page-1)*$pagesize)).", {$pagesize}",
			'cat'=>$cat,
			'admin_status'=>$astatus
			);
		if (!empty($request['begindate']))
		{
			$param['begindate'] = strtotime($request['begindate']);
		}
		if (!empty($request['enddate']))
		{
			$param['enddate'] = strtotime($request['enddate']) + 86400;
		}
		$count = 0;
		$imagelist = array();
		$count = $imagemodel->getimagecount($param);
		$imagelist = $imagemodel->getimagelist($param);
		$uid_array = array();
		$users = array();
		if(!empty($imagelist)){
			foreach($imagelist as $k=>$value)
			{
				$uid_array[] = $value['uid'];
	            $checkname = $this->model('user')->getOneByUid($value['admin_uid']);
	            $imagelist[$k]['checkname'] = $checkname['username'];
			}
			$users = $this->model('Ebhuser')->getUserArray($uid_array);
			$this->assign('users', $users);
		}
		$pagestr = show_page($count, $pagesize);
		// var_dump($request);

		//获得图片服务器地址
		$upconfig = Ebh::app()->getConfig()->load('upconfig');
		$showpath = $upconfig['pic']['showpath'];

		$this->assign('showpath', $showpath);
		$this->assign('pagestr', $pagestr);
		$this->assign("imagelist", $imagelist);
		$request['pagesize'] =$pagesize;
		$this->assign("request",$request);
		$this->display('space/photo');
	}

	/**
	 * 审核处理
	 */
	public function checkprocess()
	{
		$request = $this->input->post();
		$stat = intval($request['admin_status']);
		$type= intval($request['type']);
		if($type<=0){
			exit(0);
		}
		//var_dump($request);exit();
        $user=Ebh::app()->user->getloginuser();
		$param = array(
			'role'=>'admin',
			'admin_uid'=>$user['uid'],
			'admin_status'=>$stat,
			'admin_remark'=>$request['admin_remark'],
			'toid'=>	intval($request['toid']),
			'admin_ip'=>getip(),
			'type'=>$type
			);
		$ret = $this->model('snsbillchecks')->check($param);
		if($ret>0){
			$info=$this->getDataInfo($param['toid'],$type);
			$name=$info['name'];
			$alltype=array(
				'8' => '用户新鲜事',
				'9' => '用户日志',
				'10' => '用户相册'
			);
			$allresult=array('','通过','不通过');
			echo json_encode(array('code'=>0,'msg'=>'处理成功'));
			$remark=' 审核结果为'.$allresult[$stat];
			admin_log('用户空间审核',$alltype[$type].'审核',$name,$param['toid'],$remark);//添加日志
		}else{
			echo json_encode(array('code'=>1,'msg'=>'处理失败'));
		}

	}
    /**
     * 审核撤销
     */
    public function revoke(){
        $request = $this->input->post();
        $status = intval($request['status']);
        $toid= intval($request['toid']);
        $type = intval($request['type']);
        if($type<=0){
            exit(0);
        }
        $userinfo = Ebh::app()->user->getloginuser();
        $param = array(
            'toid' => $toid,
            'admin_status' => 3,
            'admin_uid' => $userinfo['uid'],
            'type' => $type,
            'status' => $status
        );
        $ret = $this->model('snsbillchecks')->revoke($param);
        if($ret){
            $info=$this->getDataInfo($param['toid'],$type);
            $name=$info['name'];
            $alltype=array(
                '8' => '用户新鲜事',
                '9' => '用户日志',
                '10' => '用户相册'
            );
            admin_log('用户空间审核', $alltype[$type].'审核', $name, $toid,'撤销审核');
            echo json_encode(array('code' => 0));
        }else{
            echo json_encode(array('code' => 1));
        }
    }
	/**
	 * 批量审核处理
	 */
	public function multcheckprocess(){
		$request = $this->input->post();
		//$user = Ebh::app()->user->getloginuser();
		$stat = intval($request['admin_status']);
		$ckmodel = $this->model('snsbillchecks');
		$type= intval($request['type']);
		if($type<=0){
			exit(0);
		}
        $user=Ebh::app()->user->getloginuser();
		//var_dump($request);exit();
		$param = array(
				'role'=>'admin',
				'admin_uid'=>$user['uid'],
				'admin_status'=>$stat,
				'admin_remark'=>h($request['admin_remark']),
				'ids'=>	h($request['ids']),
				'admin_ip'=>getip(),
				'type'=>$type
		);
		if($ckmodel->multcheck($param)){
			$id_array = explode(',', $request['ids']);
			foreach ($id_array as $toid)
			{
				$info=$this->getDataInfo($toid,$type);
				$name=$info['name'];
				$alltype=array(
					'8' => '用户新鲜事',
					'9' => '用户日志',
					'10' => '用户相册'
				);
				$allresult=array('','通过','不通过');
				$remark=' 审核结果为'.$allresult[$stat];
				admin_log('用户空间审核',$alltype[$type].'审核',$name,$toid,$remark);//添加日志

			}

			echo json_encode(array('code'=>0,'msg'=>'处理成功'));
		}else{
			echo json_encode(array('code'=>1,'msg'=>'处理失败'));
		}
	}

		/**
	 * 删除处理
	 *
	 */
	public function delprocess(){
		$request = $this->input->post();
		$ckmodel = $this->model('snsbillchecks');

		$toid = intval($request['toid']);
		$type = intval($request['type']);
		if($toid<=0||$type<=0){
			exit(0);
		}
		$param = array(
				'toid'=>$toid,
				'type'=>$type
		);
		$ckmodel->del($param);
		//生成备注
		$alltype=array(
			'8' => '用户新鲜事',
			'9' => '用户日志',
			'10' => '用户相册'
		);
		$info=$this->getDataInfo($toid,$type);
		$name=$info['name'];
		admin_log('用户空间审核',$alltype[$type].'删除',$name,$toid,'');
		exit(0);
	}

	/**
	 * 获取一条数据信息
	 * @param  int $toid 编号
	 * @param  int $type 类型
	 * @return array       数据
	 */
	private function getDataInfo($toid,$type){
		$info=array();
		if($type==8){//新鲜事
			$feeds = $this->model('feeds')->getfeedsbyfid($toid);
			$message = json_decode($feeds['message'], true);
			if ($feeds['category'] == 1)
				$info['name'] = shortstr($message['content'], 40);
			else
				$info['name'] = shortstr($message['title'], 40);
			return $info;
		}elseif($type==9){//日志
			$blog = $this->model('blog')->getblogbybid($toid);
			$info['name'] = shortstr($blog['title'], 40);
			return $info;
		}elseif($type==10){//照片
			$image = $this->model('image')->getimagebygid($toid);
			$info['name'] = $image['path'];
			return $info;
		}else{
			return array('name'=>'审核');
		}
	}

	/**
	 * 查看详情
	 * @return [type] [description]
	 */
	public function view(){
		$request = $this->input->get();
		$fid = intval($request['fid']);//8用户新鲜事
		$bid= intval($request['bid']);//9用户日志
		$gid = intval($request['gid']);//10用户照片

		if($fid>0){//用户新鲜事
			Ebh::app()->helper('feedhtml');
			//类别
			$category = array(
				'1' => '心情',
				'2' => '原创日志',
				'3' => '照片',
				'4' => '转载日志'
			);
			$info = $this->model('feeds')->getfeedsbyfid($fid);
            $checkname = $this->model('User')->getOneByUid($info['admin_uid']);
            $info['checkname'] = $checkname['username'];
			$info['categoryname'] = $category[$info['category']];
			$user = $this->model('Ebhuser')->getuserbyuid($info['fromuid']);
			if (!empty($user))
			{
				$info['username'] = $user['username'];
				$info['realname'] = $user['realname'];
			}
			$info['message'] = json_decode($info['message'],true);

			//校验转发的父级是否被删除
			if($info['iszhuan']==1){
				$info['refer_top_delete'] = $this->model('feeds')->checkfeedsdelete($info['tfid']);
			}else{
				$info['refer_top_delete'] = false;
			}
            $info['message'] = $this->model('billchecks')->str_change($info['message']);//敏感字处理
			//获取关联图片列表
			$imagelist = array();
			if (!empty($info['message']['images']))
			{
				$param= array(
					'role' => 'admin',
					'limit' => '100',
					'cat' => -1,
					'ids' => $info['message']['images'],
					'orderbby' => 'i.gid ASC'
				);
				$imagelist = $this->model('image')->getimagelist($param);
			}
			$this->assign('imagelist', $imagelist);

		}elseif($bid>0){//用户日志
			$info = $this->model('blog')->getblogbybid($bid);
            $checkname = $this->model('User')->getOneByUid($info['admin_uid']);
            $info['checkname'] = $checkname['username'];
			$user = $this->model('Ebhuser')->getuserbyuid($info['uid']);
			if (!empty($user))
			{
				$info['username'] = $user['username'];
				$info['realname'] = $user['realname'];
			}
            $info = $this->model('billchecks')->str_change($info);//敏感字处理
			//获取关联图片列表
			$imagelist = array();
			if (!empty($info['images']))
			{
				$param= array(
					'role' => 'admin',
					'limit' => '100',
					'cat' => -1,
					'ids' => $info['images'],
					'orderbby' => 'i.gid ASC'
				);
				$imagelist = $this->model('image')->getimagelist($param);
			}
			$this->assign('imagelist', $imagelist);

		}elseif($gid>0){//用户照片
			$info = $this->model('image')->getimagebygid($gid);
            $checkname = $this->model('User')->getOneByUid($info['admin_uid']);
            $info['checkname'] = $checkname['username'];
			$user = $this->model('Ebhuser')->getuserbyuid($info['uid']);
			if (!empty($user))
			{
				$info['username'] = $user['username'];
				$info['realname'] = $user['realname'];
			}
		}else{
			exit;
		}
		//获得图片服务器地址
		$upconfig = Ebh::app()->getConfig()->load('upconfig');
		$showpath = $upconfig['pic']['showpath'];
		$this->assign('showpath', $showpath);
		$this->assign("info",$info);
		$this->assign("request", $request);
		$this->display('space/view');
	}
}