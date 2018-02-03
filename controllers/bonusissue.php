<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/13
 * Time: 15:50
 */
class BonusissueController extends CAdminControl
{
    /**
     * 奖金发放
     */
    public function index(){
        $bonus=$this->model('Bonus');
        $request = $this->input->get();
        $page = Ebh::app()->getUri()->page;//当前页
        $pagesize = 20;
        $request['page']=$page;
        $param=array(
            'pagesize'=>$pagesize,
            'limit'=>(max(0,($page-1)*$pagesize)).", {$pagesize}",
            'q'=>$request['q'],
        );
        $recordlist=$bonus->getRecordList($param);
        $count = $bonus->getRecordCount($param);
        //print_r($count);
        $pagestr = show_page($count, $pagesize);
        $this->assign('pagestr', $pagestr);
        $request['pagesize'] =$pagesize;
        $this->assign('request',$request);
        $this->assign('recordlist',$recordlist);
        $this->display('bonusissue/index');
    }

    /**
     * 发放奖金页面
     */
    public function addbonus(){

        $result=$this->input->post();
        $dopost = $this->input->post('dopost');
        $loginuser= Ebh::app()->user->getloginuser();
        if (!empty($result)&&$dopost == 'add'){

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
//            print_r($result['jsonstr']);
//            print_r(json_encode($result['jsonstr']));die;
            $param=array(
                'status'=>1,
                'jsonstr'=>json_encode($result['jsonstr']),
                'director'=>$result['director'],
                'dateline'=>time(),
                'operator'=>$loginuser['realname'],
                'uid'=>$loginuser['uid'],
                'title'=>$result['title'],
                'totalmoney'=>$result['totalmoney'],
                'inputamount'=>$result['inputamount']

            );
            if (empty($param['director'])){
                show_message('销售主管不能为空');
            }
            if (empty($param['title'])){
                show_message('标题不能为空');
            }
            if($param['inputamount'] != $param['totalmoney']){
                show_message('奖金数额不一致');
            }
            $bonus = $this->model('bonus');
            $res=$bonus->addRecord($param);
            if($res){
                echo json_encode(array('code'=>0,'msg'=>'添加成功','data'=>$res));
            }else{
                echo json_encode(array('code'=>1,'msg'=>'处理失败','data'=>false));
            }
        }else{
            $this->display('bonusissue/addbonus');

        }

    }

    /**
     * 获取发放奖金的账户
     */
    public function getaccount(){
        $account = $this->input->post('account');
        $userinfo = $this->model('Ebhuser')->getUserByName($account);
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
        $detail=$this->model('Bonus')->getViewByBid($bid);
        //print_r($detail);die;
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
                            $detaillist[$key]['credit']=$val['credit'];

                        }
                    }
                }
            }

        }
       //print_r($detaillist);die;
        $this->assign('detaillist',$detaillist);
        $this->display('bonusissue/bonusview');

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
        $chmodel = $this->model("Charge");
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
            exit();
        }else{
            //sleep(5);
            //充值成功 修改操作
            //支付宝交易号
            $ordernumber = '';
            $buyer_id = $user['uid'];
            $buyer_info = '';

            $chmodel = $this->model("Charge");
            $charge = $chmodel->getOneByChargeid($chargeid);

            if(empty($charge)) {//订单不存在
                $msg = "订单不存在:chargeid:{$chargeid}";
                log_message($msg);
                exit();
            }
            if($charge['status'] == 1) {//
                $msg = '订单已处理，则不重复处理';
                log_message($msg);
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
            log_message($msg);
        }

    }
}