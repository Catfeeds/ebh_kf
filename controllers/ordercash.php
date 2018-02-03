<?php

/**
 * 汇款单审核控制器
 */
class OrdercashController extends CAdminControl
{
    public function __construct()
    {
        parent::__construct();
        //检查权限
        Ebh::app()->lib('Access')->checkModuleAccess('adminordercash');//检测权限
    }

    /**
     * 汇款单审核
     */
    public function index()
    {
        $request = $this->input->get();
        $model = $this->model('ordercash');
        $page = Ebh::app()->getUri()->page;//当前页
        $request['page'] = $page;
        $pagesize = 20;
        $astatus = intval($request['admin_status']);
        $crid = intval($request['crid']);
        if ($request['cat'] != '') {
            $cat = intval($request['cat']);
        } else {
            $request['cat'] = '';
            $cat = -1;
        }
        $param = array(
            'role' => 'admin',
            'pagesize' => $pagesize,
            'limit' => (max(0, ($page - 1) * $pagesize)) . ", {$pagesize}",
            'cat' => $cat,
            'admin_status' => $astatus,
            'crid' => $crid,
            'q' => trim($request['q']),
            'type' => 1
        );
        $access = Ebh::app()->lib('Access')->getAccessCookie('classroom_access');//检测权限
        $list = $model->getlist($param);
        $count = $model->getcount($param);
        //格式化数据
        $uid_array = array();
        $users = array();
        $crid_array = array();
        $classrooms = array();
        foreach ($list as $value) {
            $uid_array[] = $value['uid'];
            $crid_array[] = $value['crid'];
        }
        $users = $this->model('Ebhuser')->getuserarray($uid_array);
        $classrooms = $this->model('Classroom')->getClassRoomArray($crid_array);
        foreach ($list as $key => $value) {
            $list[$key]['username'] = $users[$value['uid']]['username'];
            $list[$key]['realname'] = $users[$value['uid']]['realname'];
            $list[$key]['crname'] = $classrooms[$value['crid']];
            $checkname = $this->model('user')->getOneByUid($value['admin_uid']);
            $list[$key]['checkname'] = $checkname['username'];
        }
        //分页
        $pagestr = show_page($count, $pagesize);
        $this->assign('list', $list);
        $this->assign('pagestr', $pagestr);
        //图片路径前缀
        $upcnf = Ebh::app()->getConfig()->load('upconfig');
        $this->assign('prepath', $upcnf['pic']['showpath']);
        $request['pagesize'] = $pagesize;
        $this->assign("request", $request);
//        p($request);die;
        $this->display('ordercash/ordercash');
    }

    /**
     * 汇款单详情
     */
    public function ordercashview()
    {
        $id = intval($this->input->get('toid'));
        if ($id <= 0) {
            echo 'param error';
            exit;
        }
        $ordercashmodel = $this->model('ordercash');
        $rows = $ordercashmodel->getlist(array('id' => $id));
        if (empty($rows)) {
            echo 'content error';
            exit;
        }
        //格式化数据
        $uid_array = array($rows[0]['uid']);
        $crid_array = array($rows[0]['crid']);
        $users = $this->model('Ebhuser')->getuserarray($uid_array);
        $classrooms = $this->model('Classroom')->getClassRoomArray($crid_array);
        foreach ($rows as $key => $value) {
            $rows[$key]['username'] = $users[$value['uid']]['username'];
            $rows[$key]['realname'] = $users[$value['uid']]['realname'];
            $rows[$key]['crname'] = $classrooms[$value['crid']];
            $checkname = $this->model('user')->getOneByUid($value['admin_uid']);
            $rows[$key]['checkname'] = $checkname['username'];
        }
        //获取相关服务包详情
        $queryArr['crid'] = $rows[0]['crid'];
        $queryArr['itemidlist'] = $rows[0]['itemids'];
        $list = $this->model('Payitem')->getItemList($queryArr);
        $list = $this->_insertIfHasBuyInfo($list, $rows[0]['uid']);
        if (!empty($list)) {
            foreach ($list as $k => $v) {
                $str = '';
                $sertime = !empty($v['imonth']) ? $v['imonth'] . '个月' : $v['iday'] . '天';
                if ($v['hasbuy']) {
                    $str .= '<b>服务项名称</b>：<span style="color:#ff0000;">' . $v['iname'] . '</span>(已购买)';
                } else {
                    $str .= '<b>服务项名称</b>：' . $v['iname'];
                }
                $str .= ' <b>所属服务包分类</b>：' . $v['pname'] . ' <b>所属平台</b>：' . $v['crname'] . ' <b>价格</b>：' . $v['iprice'] . ' <b>持续时间</b>：' . $sertime . ' <b>添加时间</b>：' . date('Y-m-d H:i:s', $v['dateline']);
                $serlist[] = $str;
            }
        } else {
            $serlist[] = '无';
        }
        $upcnf = Ebh::app()->getConfig()->load('upconfig');
        $this->assign('detail', $rows[0]);
        $this->assign('prepath', $upcnf['pic']['showpath']);
        $this->assign('serlist', $serlist);
        $this->display('ordercash/ordercashview');
    }

    /**
     * 审核处理(与ebh总后台人工开通处理逻辑一致)
     */
    public function checkprocess()
    {
        $id = intval($this->input->post('toid'));
        $ids = trim($this->input->post('ids'));
        $status = intval($this->input->post('admin_status'));
        $remark = trim($this->input->post('admin_remark'));
        $type = intval($this->input->post('type'));
        $uid = intval($this->input->post('uid'));
        $toids = $type == 0 ? $id : $ids;
        if (empty($toids) || !in_array($status, array(1, 2))) {
            echo json_encode(array('code' => -1, 'msg' => '参数错误'));
            exit;
        }
        $check = true;//初始化审核状态
        //审核主表
        $ckmodel = $this->model('billchecks');
        $user = EBH::app()->user->getloginuser();
        $param = array(
            'role' => 'admin',
            'admin_uid' => $user['uid'],
            'admin_status' => $status,
            'admin_remark' => $remark,
            'ids' => $toids,
            'admin_ip' => getip(),
            'type' => 12
        );
        $ret = $ckmodel->multcheck($param);
        //操作日志中的信息描述
        $desc = $status == 1 ? '审核通过' : '审核不通过';
        $desc .= !empty($remark) ? ' (' . $remark . ') ' : '';
        if ($ret) {
            //发送邮件
            $uid_array = explode(',',$uid);
            $info = $this->model('Ebhuser')->getuserarray($uid_array);//获取用户组
            if($status == 1){
                $subject = '汇款单审核通过通知';
                $body = '尊敬的ebh客户,您好。您的汇款单审核已经通过,相关服务已经开通';
            }else{
                $subject = '汇款单审核不通过通知';
                $body = '尊敬的ebh客户,您好。您的汇款单审核未通过,请咨询相关客服';
            }
            $emailerror = '';
            $mail = Ebh::app()->lib('EBHMailer');
            foreach($info as $value){
                $data = array(
                    'email' => $value['email'],
                    'username' => $value['realname']
                );
                $r = $mail->sendMessage($data,$subject,$body);//邮件发出
                if($r['status']){
                    $emailerror.=$value['username'].'邮件发送失败';
                }else{
                    $emailerror.=$value['username'].'邮件发送成功';
                }
            }
            //记录日志
            $id_array = explode(',', $toids);
            foreach ($id_array as $myid) {
                admin_log('汇款单管理', '汇款单审核', 'ordercash', $myid, $desc);//添加日志
            }
            if ($status == 1) {
                //开通服务
                $check = $this->manualnotify($toids);
            }
        } else {
            $check = false;
        }
        $code = $check ? 0 : -1;
        echo json_encode(array('code' => $code, 'msg' => $emailerror));
    }

//    //撤销审核
//
//    public function revoke()
//    {
////        p($_POST);die;
//        $toid = intval($this->input->post('toid'));
//        $status = intval($this->input->post('status'));
//        $uid = intval($_POST['uid']);
//        $user = Ebh::app()->user->getloginuser();
//        if (empty($toid)) {
//            echo json_encode(array('code' => -1, 'msg' => '参数错误'));
//            exit;
//        }
//        $ckmodel = $this->model('billchecks');
//        $param = array(
//            'toid' => $toid,
//            'admin_status' => 0,
//            'admin_uid' => 0,
//            'type' => 12,
//            'status' => $status
//        );
//        $result = $ckmodel->revoke($param);
//        if ($result) {
//            $userinfo = $this->model('Ebhuser')->getuserbyuid($uid);
//            $data = array(
//                'email' => $userinfo['email'],
//                'subject' => '汇款单审核撤销通知',
//                'body' => '您的汇款单审核已被撤销,请等待重新审核'
//            );
//            admin_log('汇款单管理', '汇款单审核', 'ordercash', $toid, '', '撤销审核,撤销人ID:' . $user['uid']);
//            $mail = $this->model('billchecks')->mail($data);
//            if($mail){
//                echo json_encode(array('code' => 0));
//            }else{
//                echo json_encode(array('code' => 2));
//            }
//        } else {
//            echo json_encode(array('code' => 1));
//        }
//    }
    /**
     *开通服务处理
     */
    private function manualnotify($toidstr = '')
    {
        if (empty($toidstr)) {
            return false;
        }
        $toids = explode(',', $toidstr);
        $ordercashmodel = $this->model('ordercash');
        $rows = $ordercashmodel->getlist(array('ids' => $toids));
        if (empty($rows)) {
            return false;
        }
        $PayitemModel = $this->model('Payitem');
        foreach ($rows as $item) {
            $queryArr['crid'] = $item['crid'];
            $queryArr['itemidlist'] = $item['itemids'];
            $serlist = $PayitemModel->getItemList($queryArr);
            //批量处理
            foreach ($serlist as $item) {
                $orderdetail['crid'] = $item['crid'];
                $orderdetail['folderid'] = $item['folderid'];
                $orderdetail['itemid'] = $item['itemid'];
                $orderdetail['money'] = $item['iprice'];
                $orderdetail['type'] = 4;
                $orderdetail['oday'] = $item['iday'];
                $orderdetail['omonth'] = $item['imonth'];
                $orderdetail['uid'] = $rows[0]['uid'];
                //检测是否该学校权限
                if (!Ebh::app()->lib('Access')->checkClassroomAccess($orderdetail['crid'])) {
                    log_message('管理员没有[' . $item['crname'] . ']权限，开通失败');
                    continue;
                }
                //生成订单
                $orderparam = $this->buildOrder($orderdetail);
                if (empty($orderparam)) {
                    return false;
                }
                $ret = $this->notifyOrder($orderparam);
                if (!$ret) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     *生成订单信息
     * @param $payfrom 来源
     */
    private function buildOrder($detail)
    {
        $itemidlist = array($detail['itemid']);
        if (empty($itemidlist))
            return FALSE;
        foreach ($itemidlist as $itemid) {    //详情编号必须都为正整数
            if (!is_numeric($itemid) || $itemid <= 0)
                return FALSE;
        }
        $itemidstr = implode(',', $itemidlist);
        $pitemmodel = $this->model('PayItem');
        $itemparam = array('itemidlist' => $itemidstr);
        $itemlist = $pitemmodel->getItemList($itemparam);
        if (empty($itemlist))
            return FALSE;
        $payordermodel = $this->model('PayOrder');
        $orderparam = array();

        $orderparam['dateline'] = SYSTIME;
        $orderparam['ip'] = $this->input->getip();
        $orderparam['uid'] = $detail['uid'];
        $orderparam['payfrom'] = $detail['type'];
        $orderparam['invalid'] = $detail['invalid'];
        $orderparam['status'] = $detail['status'];
        $ordername = '';    //订单名称
        $remark = '';        //订单备注
        $totalfee = 0;
        $comfee = 0;    //公司分到总额
        $roomfee = 0;    //平台分到总额
        $providerfee = 0;    //内容提供商分到总额
        $pid = 0;    //订单所属服务包编号
        for ($i = 0; $i < count($itemlist); $i++) {
            if (!empty($detail['money']) || ($orderparam['payfrom'] == 5)) {
                $itemlist[$i]['iprice'] = $detail['money'];
            }
            if ($orderparam['payfrom'] == 5) {
                $itemlist[$i]['fee'] = $itemlist[$i]['iprice'] = 0;
            } else {
                $itemlist[$i]['fee'] = $itemlist[$i]['iprice'];
            }
            $itemlist[$i]['pid'] = $itemlist[$i]['pid'];
            $pid = $itemlist[$i]['pid'];
            $itemlist[$i]['oname'] = $itemlist[$i]['iname'];
            $itemlist[$i]['omonth'] = $itemlist[$i]['imonth'];
            $itemlist[$i]['oday'] = $itemlist[$i]['iday'];
            $itemlist[$i]['osummary'] = $itemlist[$i]['isummary'];
            $itemlist[$i]['uid'] = $detail['uid'];
            $itemlist[$i]['rname'] = $itemlist[$i]['crname'];


            if ($orderparam['payfrom'] == 5) {
                $itemlist[$i]['roomfee'] = 0;
                $itemlist[$i]['providerfee'] = 0;
                $itemlist[$i]['comfee'] = 0;
            } else {
                $itemlist[$i]['roomfee'] = $itemlist[$i]['roomfee'];
                $itemlist[$i]['providerfee'] = $itemlist[$i]['providerfee'];
                $itemlist[$i]['comfee'] = $itemlist[$i]['comfee'];
                $totalfee += $itemlist[$i]['iprice'];
                $comfee += $itemlist[$i]['comfee'];
                $roomfee += $itemlist[$i]['roomfee'];
                $providerfee += $itemlist[$i]['providerfee'];
            }


            if (empty($ordername))
                $ordername = $itemlist[$i]['oname'];
            else
                $ordername .= ',' . $itemlist[$i]['oname'];
            $theremark = $itemlist[$i]['iname'] . '_' . (empty($itemlist[$i]['omonth']) ? $itemlist[$i]['oday'] . ' 天 _' : $itemlist[$i]['omonth'] . ' 月 _') . $itemlist[$i]['fee'] . ' 元';
            if (empty($remark)) {
                $remark = $theremark;
            } else {
                $remark .= '/' . $theremark;
            }
            $providercrid = $itemlist[$i]['providercrid'];
        }
        $orderparam['crid'] = $itemlist[0]['crid'];
        $orderparam['providercrid'] = $itemlist[0]['providercrid'];    //来源平台crid
        $orderparam['pid'] = $pid;
        $orderparam['itemlist'] = $itemlist;
        if ($orderparam['payfrom'] == 5) {
            $orderparam['totalfee'] = 0;
            $orderparam['comfee'] = 0;
            $orderparam['roomfee'] = 0;
            $orderparam['providerfee'] = 0;
        } else {
            $orderparam['totalfee'] = $totalfee;
            $orderparam['comfee'] = $comfee;
            $orderparam['roomfee'] = $roomfee;
            $orderparam['providerfee'] = $providerfee;
        }
        $orderparam['ordername'] = '开通 ' . $ordername . ' 服务';
        $orderparam['remark'] = $remark;
        $orderid = $payordermodel->addOrder($orderparam);
        if ($orderid > 0) {
            $orderparam['orderid'] = $orderid;
            return $orderparam;
        } else {
            return 0;
        }
    }

    /**
     *支付成功后的订单处理
     */
    private function notifyOrder($param)
    {
        $this->sync_crlist = array();//初始化同步学校列表
        $this->sync_classlist = array();//初始化同步班级列表

        //商户订单号
        $orderid = $param['orderid'];
        //交易号
        // $ordernumber = $param['ordernumber'];
        $buyer_id = empty($param['buyer_id']) ? '' : $param['buyer_id'];
        $buyer_info = empty($param['buyer_info']) ? '' : $param['buyer_info'];
        Ebh::app()->getDb()->set_con(0);
        $providercrids = array();    //订单下内容提供商的crid列表，如果大于1，需要拆分订单
        $pordermodel = $this->model('PayOrder');

        $myorder = $pordermodel->getOrderById($orderid);
        if (empty($myorder)) {//订单不存在
            return FALSE;
        }
        if ($myorder['status'] == 1) {//订单已处理，则不重复处理
            return $myorder;
        }
        // $myorder['detaillist'] = $param['itemlist'];
        //处理订单详情中的内容
        if (empty($myorder['detaillist'])) {
            return FALSE;
        }

        foreach ($myorder['detaillist'] as $detail) {
            $detail['uid'] = $myorder['uid'];
            $this->doOrderItem($detail);
            $detailprovidercrid = $detail['providercrid'];
            if (!empty($detailprovidercrid) && !isset($providercrids[$detailprovidercrid]))
                $providercrids[$detailprovidercrid] = $detailprovidercrid;
        }
        $myorder['itemlist'] = $myorder['detaillist'];
        //更新订单状态
        $myorder['status'] = 1;
        $myorder['payip'] = $this->input->getip();
        $myorder['paytime'] = SYSTIME;
        // $myorder['ordernumber'] = $ordernumber;
        $myorder['buyer_id'] = $buyer_id;
        $myorder['buyer_info'] = $buyer_info;

        //拆分订单处理，当订单明细的提供商crid不同时，则将订单改成每个订单明细对应一个订单。
        $providercount = count($providercrids);
        if ($providercount > 1) {
            for ($i = 0; $i < count($myorder['detaillist']); $i++) {
                if ($i == 0) {
                    $myorder['providercrid'] = $myorder['detaillist'][$i]['providercrid'];
                    if ($myorder['payfrom'] == 5) {
                        $myorder['totalfee'] = 0;
                        $myorder['comfee'] = 0;
                        $myorder['roomfee'] = 0;
                        $myorder['providerfee'] = 0;
                    } else {
                        $myorder['totalfee'] = $myorder['detaillist'][$i]['fee'];
                        $myorder['comfee'] = $myorder['detaillist'][$i]['comfee'];
                        $myorder['roomfee'] = $myorder['detaillist'][$i]['roomfee'];
                        $myorder['providerfee'] = $myorder['detaillist'][$i]['providerfee'];
                    }

                    $myorder['ordername'] = '开通 ' . $myorder['detaillist'][$i]['oname'] . ' 服务';
                    $myorder['remark'] = $myorder['detaillist'][$i]['oname'] . '_' . (empty($myorder['detaillist'][$i]['omonth']) ? $myorder['detaillist'][$i]['oday'] . ' 天 _' : $myorder['detaillist'][$i]['omonth'] . ' 月 _') . $myorder['detaillist'][$i]['fee'] . ' 元';
                } else {
                    $neworder = $myorder;
                    if ($myorder['payfrom'] == 5) {
                        $neworder['totalfee'] = 0;
                        $neworder['comfee'] = 0;
                        $neworder['roomfee'] = 0;
                        $neworder['providerfee'] = 0;
                    } else {
                        $neworder['totalfee'] = $myorder['detaillist'][$i]['fee'];
                        $neworder['comfee'] = $myorder['detaillist'][$i]['comfee'];
                        $neworder['roomfee'] = $myorder['detaillist'][$i]['roomfee'];
                        $neworder['providerfee'] = $myorder['detaillist'][$i]['providerfee'];
                    }
                    $neworder['providercrid'] = $myorder['detaillist'][$i]['providercrid'];

                    $neworder['ordername'] = '开通 ' . $myorder['detaillist'][$i]['oname'] . ' 服务';
                    $neworder['remark'] = $myorder['detaillist'][$i]['oname'] . '_' . (empty($myorder['detaillist'][$i]['omonth']) ? $myorder['detaillist'][$i]['oday'] . ' 天 _' : $myorder['detaillist'][$i]['omonth'] . ' 月 _') . $myorder['detaillist'][$i]['fee'] . ' 元';
                    $neworderid = $pordermodel->addOrder($neworder, TRUE);
                    $myorder['detaillist'][$i]['orderid'] = $neworderid;
                }
            }
        }

        $myorder['itemlist'] = $myorder['detaillist'];

        $res = $pordermodel->updateOrder($myorder);

        //更新学校学生缓存和同步SNS数据
        if (!empty($this->sync_crlist)) {
            foreach ($this->sync_crlist as $crid) {
                //更新学校学生缓存
                Ebh::app()->lib('Sns')->updateRoomUserCache(array('crid' => $crid, 'uid' => $myorder['uid']));
                //同步SNS数据(网校操作)
                Ebh::app()->lib('Sns')->do_sync($myorder['uid'], 4);
            }
        }
        //更新班级学生缓存
        if (!empty($this->sync_classlist)) {
            foreach ($this->sync_classlist as $classid) {
                //更新班级学生缓存
                Ebh::app()->lib('Sns')->updateClassUserCache(array('classid' => $classid, 'uid' => $myorder['uid']));
            }
        }
        //记录日志
        if ($res) {
            $user_info = $this->model('ebhuser')->getuserbyuid($myorder['uid']);
            $pack_info = $this->model('paypackage')->getPackByPid($myorder['pid']);
            admin_log('用户管理', '开通服务', $user_info['username'], $myorder['uid'], $myorder['ordername'] . ' (所属网校：' . $pack_info['crname'] . ' 所属服务包：' . $pack_info['pname'] . ')');
        }
        return $res;
    }

    /**
     *支付成功后处理订单详情（主要为生成权限）
     */
    private function doOrderItem($orderdetail)
    {
        $crid = $orderdetail['crid'];
        $folderid = $orderdetail['folderid'];
        $uid = $orderdetail['uid'];
        $omonth = $orderdetail['omonth'];
        $oday = $orderdetail['oday'];

        $roommodel = $this->model('Classroom');
        $roominfo = $roommodel->getRoomByCrid($crid);
        if (empty($roominfo))
            return FALSE;
        $usermodel = $this->model('Ebhuser');
        $user = $usermodel->getuserbyuid($uid);
        if (empty($user))
            return FALSE;
        //获取用户是否在此平台
        $rumodel = $this->model('Roomuser');
        $ruser = $rumodel->getroomuserdetail($crid, $uid);
        $type = 0;
        if (empty($ruser)) {    //不存在
            $enddate = 0;
            if (!empty($crid)) {
                if (!empty($omonth)) {
                    $enddate = strtotime("+$omonth month");
                } else {
                    $enddate = strtotime("+$oday day");
                }
            }
            $param = array('crid' => $crid, 'uid' => $user['uid'], 'begindate' => SYSTIME, 'enddate' => $enddate, 'cnname' => $user['realname'], 'sex' => $user['sex']);
            $result = $rumodel->insert($param);
            $type = 1;
            if ($result !== FALSE) {
                if ($roominfo['isschool'] == 6 || $roominfo['isschool'] == 7) {    //如果是收费学校，则会将账号默认添加到学校的第一个班级中
                    $this->setmyclass($crid, $user['uid']);
                } else {
                    //更新教室学生数
                    $roommodel->addstunum($crid);
                }
                //记录需要更新缓存和SNS同步操作的学校项目
                $this->sync_crlist[] = $crid;
            }
        } else {    //已存在
            if ($roominfo['isschool'] == 6 || $roominfo['isschool'] == 7) {
                $this->setmyclass($roominfo['crid'], $user['uid']);//防止中途改变学校类型,导致学生在学校里面但是不在班级里面(网校改成学校) zkq 2014.07.22
            }
            $enddate = $ruser['enddate'];
            $newenddate = 0;
            if (!empty($crid)) {
                if (!empty($omonth)) {
                    if (SYSTIME > $enddate) {//已过期的处理
                        $newenddate = strtotime("+$omonth month");
                    } else {    //未过期，则直接在结束时间后加上此时间
                        $newenddate = strtotime(date('Y-m-d H:i:s', $enddate) . " +$omonth month");
                    }
                } else {
                    if (SYSTIME > $enddate) {//已过期的处理
                        $newenddate = strtotime("+$oday day");
                    } else {    //未过期，则直接在结束时间后加上此时间
                        $newenddate = strtotime(date('Y-m-d H:i:s', $enddate) . " +$oday day");
                    }
                }
            }
            $param = array('crid' => $crid, 'uid' => $user['uid'], 'enddate' => $newenddate, 'cstatus' => 1);
            $result = $rumodel->update($param);
            $type = 2;
        }
        //处理用户权限
        $userpmodel = $this->model('UserPermission');
        if (empty($orderdetail['folderid'])) {
            $myperm = $userpmodel->getPermissionByItemId($orderdetail['itemid'], $uid);
        } else {
            $myperm = $userpmodel->getPermissionByFolderId($orderdetail['folderid'], $uid);
        }
        $startdate = 0;
        $enddate = 0;
        if (empty($myperm)) {    //不存在则添加权限，否则更新
            $startdate = SYSTIME;
            if (!empty($omonth)) {
                $enddate = strtotime("+$omonth month");
            } else {
                $enddate = strtotime("+$oday day");
            }
            $ptype = 0;
            if (!empty($folderid) || !empty($crid)) {
                $ptype = 1;
            }
            $perparam = array('itemid' => $orderdetail['itemid'], 'type' => $ptype, 'uid' => $uid, 'crid' => $crid, 'folderid' => $folderid, 'cwid' => 0, 'startdate' => $startdate, 'enddate' => $enddate);
            $result = $userpmodel->addPermission($perparam);
        } else {
            $enddate = $myperm['enddate'];
            $newenddate = 0;
            if (!empty($omonth)) {
                if (SYSTIME > $enddate) {//已过期的处理
                    $newenddate = strtotime("+$omonth month");
                } else {    //未过期，则直接在结束时间后加上此时间
                    $newenddate = strtotime(date('Y-m-d H:i:s', $enddate) . " +$omonth month");
                }
            } else {
                if (SYSTIME > $enddate) {//已过期的处理
                    $newenddate = strtotime("+$oday day");
                } else {    //未过期，则直接在结束时间后加上此时间
                    $newenddate = strtotime(date('Y-m-d H:i:s', $enddate) . " +$oday day");
                }
            }
            $enddate = $newenddate;
            $myperm['enddate'] = $enddate;
            if (!empty($orderdetail['itemid'])) {
                $myperm['itemid'] = $orderdetail['itemid'];
            }
            $result = $userpmodel->updatePermission($myperm);
        }
        //用户平台信息更新成功则生成记录并更新年卡信息
        return $result;
    }

    /**
     *设置用户的默认班级信息
     * 一般为收费学校用户开通学校服务时候处理，需要将学生加入到默认的班级中
     * 如果不存在新班级，则需要创建一个默认班级
     */
    private function setmyclass($crid, $uid)
    {
        $classmodel = $this->model('Classes');
        //先判断是否已经加入班级，已经加入则无需重新加入
        $myclass = $classmodel->getClassByUid($crid, $uid);
        if (empty($myclass)) {
            $classid = 0;
            $defaultclass = $classmodel->getDefaultClass($crid);
            if (empty($defaultclass)) {    //不存在默认班级，则创建默认班级
                $param = array('crid' => $crid, 'classname' => '默认班级');
                $classid = $classmodel->addclass($param);
            } else {
                $classid = $defaultclass['classid'];
            }
            $param = array('crid' => $crid, 'classid' => $classid, 'uid' => $uid);
            $classmodel->addclassstudent($param);

            //记录需要更新缓存的班级项目
            $this->sync_classlist[] = $classid;
        }
    }

    /**
     *将指定用户是否购买过该课程的信息注入到课程信息中去
     */
    private function _insertIfHasBuyInfo($list, $uid)
    {
        if (empty($list)) {
            return array();
        }
        //第一步，获取当前数组中的所有itemid
        $itemidArr = array();
        foreach ($list as &$eachone) {
            $eachone['hasbuy'] = 0;
            array_push($itemidArr, $eachone['itemid']);
        }
        $itemidArr = array_unique($itemidArr);
        $orderlist = $this->model('payorder')->getOrdersByItemidsAndUid($itemidArr, $uid);
        $orderlistWithKey = array();
        if (!empty($orderlist)) {
            foreach ($orderlist as $order) {
                $key = 'k_' . $order['itemid'];
                if (array_key_exists($key, $orderlistWithKey)) {
                    if ($orderlistWithKey[$key]['paytime'] < $order) {
                        $orderlistWithKey[$key] = $order;
                    } else {
                        continue;
                    }
                }
                $orderlistWithKey[$key] = $order;
            }
        } else {
            return $list;
        }
        foreach ($list as &$eachone) {
            $key = 'k_' . $eachone['itemid'];
            if (array_key_exists($key, $orderlistWithKey)) {
                $eachone['hasbuy'] = 1;
            }
        }
        return $list;
    }
}