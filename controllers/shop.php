<?php
/**
 * 商城管理
 */
class ShopController extends CAdminControl{
	const PAGE_SIZE = 20;
	public function __construct()
	{
		parent::__construct();
		//检查权限
		Ebh::app()->lib('Access')->checkModuleAccess('admindata');//检测权限
		$_UP = Ebh::app()->getConfig()->load('upconfig');
	    $showpath =$_UP['mall']['showpath'];
	    $this->assign('showpath', $showpath);
	    $shopconfig = Ebh::app()->getConfig()->load('shopconfig');
		if(!empty($shopconfig['baseurl'])){
			$this->_baseurl = $shopconfig['baseurl'];
		}else{
			$this->_baseurl = '';
		}
	}
	// 商品审核列表
	public function check(){
		$access = Ebh::app()->lib('Access')->getAccessCookie('classroom_access');

		if(empty($access)){
			$this->assign('checks', array());
			$this->display('shop/check');
			return;
		}
		$params   			= parsequery();
		$params['pagesize'] = self::PAGE_SIZE;
		$params['status'] 	= $this->input->get('status');
		$params['action']   = 'lists';
		$shopconfig = Ebh::app()->getConfig()->load('shopconfig');
		$api = $shopconfig['baseurl'].'shop/goods.html';
		$ret = do_shop_post($api,$params);
		$paginate = json_decode($ret,true);
		$paginate['html'] = show_page($paginate['html'],self::PAGE_SIZE);
		$this->assign('paginate', $paginate);
		$this->display('shop/check');
	}
	//查看详情
	public function detail(){
		$shopconfig = Ebh::app()->getConfig()->load('shopconfig');
		$api = $shopconfig['baseurl'].'shop/goods.html';
		$gid 				  = $this->input->get('gid');
		$params['gid'] 		  = $gid;
		$params['action']     = 'detail';
		$ret = do_shop_post($api,$params);
		$this->assign('good', json_decode($ret,true));
		$this->display('shop/detail');
	}
	//审核更新
	public function update(){

		$params 	= $this->input->post();
		$data 		= array();
		$user 		= Ebh::app()->user->getloginuser();
		$params['ckuid']   = $user['uid'];
		$params['ip'] 	    = getip();
		$params['dateline'] = SYSTIME;
		$params['operator'] = empty($user['realname']) ?$user['username']:$user['realname'] ;
		if($params['status'] == 2){
			if(empty($params['remark']) || mb_strlen($params['remark'],'UTF-8') > 200){
				$data['status'] = true;
				$data['msg'] = false;
				echo json_encode($data);exit;
			}
		}
		$shopconfig = Ebh::app()->getConfig()->load('shopconfig');
		$api = $shopconfig['baseurl'].'shop/goods.html';
		$params['action'] = 'check';
		$ret = do_shop_post($api,$params);
		$params['action'] = 'detail';
		$ret = do_shop_post($api,$params);
		$goodsInfo = json_decode($ret, true);
		// 审核撤销0 审核通过 1 审核退回 2
		switch ($params['status']) {
			case 0:
				$data['status'] = true;
				$data['msg'] = '撤销成功';
				admin_log('商城管理','商品审核',$goodsInfo['gname'],$goodsInfo['uid'],'撤销商品审核成功');//添加日志
				break;
			case 1:
				$data['status'] = true;
				$data['code'] = 1;
				$data['msg'] = '审核通过';
				admin_log('商城管理','商品审核',$goodsInfo['gname'],$goodsInfo['uid'],'商品审核通过');//添加日志
				break;
			case 2:
				$data['status'] = true;
				$data['code'] = 2;
				$data['msg'] = '商品已退回';
				admin_log('商城管理','商品审核',$goodsInfo['gname'],$goodsInfo['uid'],'商品审核退回');//添加日志
				break;
			default:
				# code...
				break;
		}

		echo json_encode($data);exit;
	}
	//订单记录
	public function order(){
		$access = Ebh::app()->lib('Access')->getAccessCookie('classroom_access');
		if(empty($access)){
			$this->assign('checks', array());
			$this->display('shop/order');
			return;
		}
		$params 			= parsequery();
		$input = $this->input->get();
		if(empty($input)){
			$input = array();
		}
		$params = array_merge($params,$input);
		$params['pagesize'] = self::PAGE_SIZE;
		$params['action'] = 'list';
		$orders 			= do_shop_post($this->_baseurl.'shop/order.html',$params);
		$orders = json_decode($orders,true);
		$orders = $orders['data'];
		//分页
		$paginate = array(
			'list'   => $orders['list'],
			'html'   => show_page($orders['total'],self::PAGE_SIZE),
			'params' => $params
		);
		$this->assign('paginate', $paginate);
		$this->display('shop/order');
	}
	//订单详情
	public function orderdetail(){
		$oid 						= $this->input->get('orderid');
		$uid 						= $this->input->get('buyer_uid');
		$params['oid'] = $oid;
		$params['buyeruid'] = $uid;
		$params['action'] = 'detail';
		$orderdetails     			= do_shop_post($this->_baseurl.'shop/order.html',$params);
		$orderdetails = json_decode($orderdetails,true);
		$this->assign('orderdetails', $orderdetails['data']);
		$this->display('shop/orderdetail');
	}
	//获取商品的用户信息
	private function getGoodsUser($goods){
		$goodsModel = $this->model('goods');
		foreach ($goods as &$good) {
			$user = $goodsModel->getUserInfo($good['uid']);
			$good['username']     = $user['username'];
		}
		return $goods;
	}
	//获取订单相关信息
	private function getOrdersInfo($orders){
		$odModel = $this->model('shoporderdetails');
		if(!empty($orders)){
			foreach ($orders as &$order) {
				//获取卖家信息
				$seller = $odModel->getUserInfo($order['seller_uid']);
				$order['seller'] = $seller['username'];
				//获取卖家信息
				$customer = $odModel->getUserInfo($order['buyer_uid']);
				$order['customer'] = $customer['username'];
				//获取网校信息
				$classroom = $odModel->getClassroom($order['crid']);
				$order['classroom'] = $classroom['crname'];
			}
		}
		return $orders;
	}
	//获取订单商品名
	private function getOrderGoodsName($orders){
		$goodsModel = $this->model('goods');
		foreach ($orders as &$order) {
			$goods = $goodsModel->getDetail($order['gid']);
			$order['gname'] = $goods['gname'];
			 if($goods['version'] != $order['version']){            
                    $snapshot = $goodsModel->getSnapShot($goods['gid'], $order['version']);
                    $beferstr = json_decode($snapshot['beferstr'],true);           
                    $order['gname'] = $snapshot['gname'];
            }
		}
		return $orders;
	}
}