<?php
/**
* 审核管理控制器
*/
class CheckController extends CAdminControl{

    public function __construct()
    {
        parent::__construct();
        //检查权限
        Ebh::app()->lib('Access')->checkModuleAccess('admindata');//检测权限
    }
    /**
     * 审核汇总
     */
    public function summary(){
//        p($_SESSION);die;
//        p($_REQUEST);die;
        $request = $this->input->get();
        $sModel = $this->model('summary');
        $page = Ebh::app()->getUri()->page;//当前页
        $request['page']=$page;
        $pagesize = 50;
        if($request['reflash']){
            $request = array(
                'cat' => $request['cat'],
            );
        }
        if(!empty($request['checkname'])){
            $admin_uid = $sModel->getIdByName($request['checkname']);//获取uid
            $admin_uid = array_map('end',$admin_uid);//取id值放入一维数组
            $request['admin_uid'] = implode(',',$admin_uid);//转字符串
            if(empty($request['admin_uid'])) $request['admin_uid'] = -1;
        }
        $param= array(
            'cat' => $request['cat'],
            'role'=>'admin',
            'pagesize'=>$pagesize,
            'limit'=>(max(0,($page-1)*$pagesize)).", {$pagesize}",
            'type' => $request['ctype'],
            'admin_uid' => $request['admin_uid'],
            'startdate' => $request['startdate'],
            'enddate' => $request['enddate'],
        );
        $summary = $sModel->getCheckList($param);
        $count = $summary['count'];
        unset($summary['count']);
        foreach($summary as $k=> $v){
            $username = $this->model('user')->getOneByUid($v['admin_uid']);
            $kinds = $this->model('summary')->getKindsCount($v['admin_uid'],$param);//获取撤销和审核不通过数量
            $summary[$k]['username'] = $username['username'];
            $summary[$k]['realname'] = $username['realname'];
            $summary[$k]['uncheck'] = $kinds['uncheck'];
            $summary[$k]['revoke'] = $kinds['revoke'];
            
        }
        if(empty($request['cat'])){
            $type = array(
                1 => '课件审核',
                2 => '附件审核',
                3 => '评论审核',
                4 => '答疑审核',
                5 => '回答审核',
                7 => '作业审核',
                12 => '汇款单审核',
                14 => '作业2.0审核'
            );
        }elseif($request['cat']==1){
            $type = array(
                8 => '新鲜事审核',
                9 => '日志审核',
                10 => '相册审核',
            );
        }/*elseif($request['cat']==3){
            $type = array(
                14 => '作业2.0审核'
            );
        }*/else{
            $type = array(
                11 => '云盘审核',
            );
        }
        $userlist = $this->model('user')->getUsernameList();
        array_multisort($userlist);//排序
        $this->assign('userlist',$userlist);
        //分页
        $pagestr = show_page($count, $pagesize);
        $this->assign('type', $type);
        $this->assign('pagestr', $pagestr);
        $this->assign("summary", $summary);
        // var_dump($attachments);
        $this->assign("request",$request);
        $this->display('check/summary');
    }
    public function test(){
        $this->display('check/test');
    }
    public function test1(){
        echo json_encode('lic');
    }
}