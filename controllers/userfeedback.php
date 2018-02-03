<?php
/**
 * Created by PhpStorm.
 * User: dpl
 * Date: 2016/8/26
 * Time: 9:36
 */

class UserfeedbackController extends CControl {
    public function __construct(){
        parent::__construct();
    }

    /**
     * 用户反馈列表
     */
    public function feedbacklist(){
        $request = $this->input->get();
        $page = Ebh::app()->getUri()->page;//当前页
        $request['page']=$page;
        $pagesize =20;
        $status = empty($request['hid'])?"":intval($request['hid']);
        //$status=intval($request['hid']);
        $q=empty($request['q'])?"":$request['q'];
        //$cat=empty($request['cat'])?"":intval($request['cat']);
        $param= array(

            'page_size'=>$pagesize,
            'limit'=>(max(0,($page-1)*$pagesize)).", {$pagesize}",
            //'cat'=>$cat,
            'hid'=>$status,
            'q'=>$q,
        );

        $param['record_count'] = $this->model('Projectfeedback')->getFeedbackCount($param);
        $feedbacks = $this->model('Projectfeedback')->getFeedbackList($param);
        $pg = show_page($param['record_count'], $param['page_size']);//分页

        $feedbacklist = array();
        if(!empty($feedbacks)){
            foreach($feedbacks as $feedback){
                $feedback['role'] = '未知';
                if(!empty($feedback['groupid'])){
                    if(intval($feedback['groupid']) == '1'){
                        $feedback['role'] = '管理员';
                    }
                    if(intval($feedback['groupid']) == '6'){
                        $feedback['role'] = '学生';
                    }
                    if(intval($feedback['groupid']) == '5'){
                        $feedback['role'] = '教师';
                    }
                }
                $feedback['date'] = '-';
                if(!empty($feedback['dateline']) && intval($feedback['dateline']) >= 0){
                    if(intval($feedback['dateline']) == 0){
                        $feedback['date'] = '-';
                    }
                    if(intval($feedback['dateline']) > 0){
                        $feedback['date'] = date('Y-m-d H:i', intval($feedback['dateline']));
                    }
                }
                if(empty($feedback['email'])){
                    $feedback['email'] = '-';
                }
                if(mb_strwidth($feedback['feedback'], 'utf8') > 120){
                    $feedback['feedbacklimit'] = mb_strimwidth($feedback['feedback'], 0, 120, '...', 'utf8');
                }else{
                    $feedback['feedbacklimit'] = $feedback['feedback'];
                }

                $feedbacklist[] = $feedback;
            }
        }
        $this->assign('pg', $pg);
        $this->assign('q', $param['q']);
        $this->assign('feedbacklist', $feedbacklist);
        $this->assign('request',$request);
//        echo '<pre>';
       //var_dump($feedbacklist) ;die;
        $this->display('log/userfeedback');
    }

    /**
     * 添加反馈处理
     */
    public function addprocess(){
        $content = Ebh::app()->getInput()->post('content');
        $fbid = Ebh::app()->getInput()->post('fbid');
        if(empty($fbid) || intval($fbid) < 0){
            echo json_encode(array('ins'=>'false'));
            return false;
        }
        if(!isset($content) || mb_strlen($content, 'utf8') > 200){
            echo json_encode(array('ins'=>'false'));
            return false;
        }
        $ip = getip();
        $user = Ebh::app()->user->getloginuser();
        $param = array(
            'content' => $content,
            'hid' => $user['uid'],
            'hname' => $user['realname'],
            'hip' => $ip,
            'hdateline' => time()
        );
        $where = array(
            'fbid' => $fbid
        );
        $fbmodel = $this->model('Projectfeedback');
        $row = $fbmodel->addprocess($param, $where);
        $feedbackinfo = $fbmodel->getEmailByFbid($fbid);
        if(!empty($feedbackinfo['email'])){
            $feedbackinfo['content'] = $content;
            $this->_sendEmail($feedbackinfo);
        }
        if($row > 0){
            echo json_encode(array('ins'=>'done'));
        }
    }

    /**
     * 用户反馈处理详情
     */
    public function view(){
        $fbid = $this->input->get('fbid');
        if(empty($fbid) || intval($fbid) <= 0){
            return false;
        }
        $fbmodel = $this->model('Projectfeedback');
        $param = array(
          'fbid'=>intval($fbid)
        );
        $detail = $fbmodel->getdetail($param);
//        echo '<pre>';
//        var_dump($detail);

        if(empty($detail['hid'])){
            $detail['hid'] = '-';
        }
        if(empty($detail['hname'])){
            $detail['hname'] = '-';
        }
        if(empty($detail['hip'])){
            $detail['hip'] = '-';
        }
        // 格式化时间
        if(empty($detail['hdateline'])){
            $detail['hdateline'] = '-';
        }
        if(!empty($detail['hdateline']) && intval($detail['hdateline']) >= 0){
            if(intval($detail['hdateline']) == 0){
                $detail['hdateline'] = '-';
            }
            if(intval($detail['hdateline']) > 0){
                $detail['hdateline'] = date('Y-m-d H:i', intval($detail['hdateline']));
            }
        }
        // 格式化内容
        if(!isset($detail['content'])){
            $detail['content'] = '-';
            $detail['hcontent'] = '-';
        }
        if(mb_strwidth($detail['content'], 'utf8') > 200){
            $detail['hcontent'] = mb_strimwidth($detail['content'], 0, 200, '...', 'utf8');
        }else{
            $detail['hcontent'] = $detail['content'];
        }
        $this->assign("info",$detail);
//        echo '<pre>';
//        var_dump($deatil);
        $this->display('/log/view');
    }


    //删除反馈内容
    public function  delFeedBack(){
       $fbid=$this->input->post('fbid');
        if(empty($fbid) || intval($fbid) <= 0){
            return false;
        }
        $fbmodel = $this->model('Projectfeedback');
        $param = array(
            'fbid'=>intval($fbid)
        );
        $res=$fbmodel->del($param);
    }
    //发送邮件
    private function _sendEmail($param){
        $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
        if(preg_match($pattern, $param['email'])){
            $param['email'] = $param['email'];
        }else{
            $param['email'] = intval($param['email']).'@qq.com';
        }
        $EBHMailer = Ebh::app()->lib('EBHMailer');
        $emailcontent = "" . '<p>亲爱的：' .$param['username'].' 你好</p>' . "\n" . '----------------------------------------------------------------------<br />' . '您的反馈内容:'.$param['feedback'].'有了新的回复:<br />' . "\n" . '回复内容:'.$param['content'].'<br />'. "\n" . '<br />' . '----------------------------------------------------------------------<br />' . "\n"  . 'e板会系统';
        // var_dump($emailcontent);
        // echo $param['email'];
        $EBHMailer->sendMessage(array('email'=>$param['email']),'意见反馈',$emailcontent);
    }
} 