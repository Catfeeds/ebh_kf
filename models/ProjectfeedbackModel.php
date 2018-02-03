<?php
/**
 * Created by PhpStorm.
 * User: dpl
 * Date: 2016/8/26
 * Time: 9:42
 */

class ProjectfeedbackModel extends CEbhModel {

    /**
     * 获取反馈列表
     * @param $param
     * @return mixed
     */
    public function getFeedbackList($param){
        $sql = 'select p.*,p.schoolname,u.username,p.urole groupid from ebh_projectfeedbacks p
                left join ebh_users u on(p.uid = u.uid)';
        if (isset($param['q'])&&$param['q']!=''){
            $qstr = $this->ebhdb->escape_str($param['q']);
            $wherearr[] = '(p.feedback like \'%' . $qstr. '%\' )';
        }
        if(empty($param['hid'])){
            $wherearr[] = 'p.del !=1';
        }
        if(!empty($param['hid'])&&$param['hid']==1){
            $wherearr[] = 'p.hid !=0 ';
            $wherearr[]='p.del =0';
        }
        if(!empty($param['hid'])&&$param['hid']==2){
            $wherearr[] = 'p.hid =0';
        }
        if(!empty($param['hid'])&&$param['hid']==3){
            $wherearr[]='p.del=1';
            $wherearr[]='p.hid !=0';
        }
        if(!empty($wherearr)){
            $sql .= ' WHERE '.implode(' AND ', $wherearr);
        }
        $sql.=' order by p.dateline desc';
        if (isset($param['limit'])){
            $sql.= ' limit ' . $param['limit'];
        }
        //print_r($sql);die;
        $result = $this->ebhdb->query($sql)->list_array();
        return $result;
    }

    /**
     * 获取反馈列表总条数
     * @param $param
     * @return mixed
     */
    public function getFeedbackCount($param){
        $sql = 'select count(*) count from ebh_projectfeedbacks p';
        if(isset($param['q'])&&$param['q']!=''){
            $qstr = $this->ebhdb->escape_str($param['q']);
            $wherearr[] = '(p.feedback like \'%' . $qstr. '%\' )';
        }
        if(empty($param['hid'])){
            $wherearr[] = 'p.del !=1';
        }
        if(!empty($param['hid'])&&$param['hid']==1){
            $wherearr[] = 'p.hid !=0';
            $wherearr[]='p.del =0';
        }
        if(!empty($param['hid'])&&$param['hid']==2){
            $wherearr[] = 'p.hid =0';
        }
        if(!empty($param['hid'])&&$param['hid']==3){
            $wherearr[]='p.del=1';
            $wherearr[]='p.hid !=0';
        }
        if(!empty($wherearr)){
            $sql .= ' WHERE '.implode(' AND ', $wherearr);
        }
        $result = $this->ebhdb->query($sql)->row_array();

        return $result['count'];
    }

    /**
     * 获取用户反馈和反馈处理的详细信息
     * @param $param
     * @return bool
     */
    public function getdetail($param){
        if(empty($param['fbid']) || intval($param['fbid']) <= 0){
            return false;
        }
        $sql = 'select * from ebh_projectfeedbacks where fbid='.$param['fbid'];
        $result = $this->ebhdb->query($sql)->row_array();
        return $result;
    }

    /**
     * 添加客服处理
     * @param $param 添加数组
     * @param $where 条件数组
     * @return mixed 影响条数
     */
    public function addprocess($param, $where){
        $arr = array();
        if(!empty($param['hip'])){
            $arr['hip'] = $param['hip'];
        }
        if(!empty($param['hid']) && $param['hid'] > 0){
            $arr['hid'] = $param['hid'];
        }
        if(!empty($param['hname'])){
            $arr['hname'] = $param['hname'];
        }
        if(isset($param['content'])){
            $arr['content'] = $param['content'];
        }
        if(!empty($param['hdateline'])){
            $arr['hdateline'] = $param['hdateline'];
        }
        $wherearr = array();
        if(!empty($where['fbid'])){
            $wherearr['fbid'] = $where['fbid'];
        }
        $row = $this->ebhdb->update('ebh_projectfeedbacks', $arr, $wherearr);
        return $row;
    }
    //删除反馈
    public  function  del($param){
        if(empty($param['fbid']) || intval($param['fbid']) <= 0){
            return false;
        }
        $setArr['del'] = 1;
        $whereArr['fbid'] = $param['fbid'];
        return $this->ebhdb->update("ebh_projectfeedbacks",$setArr,$whereArr);

    }
    //通过fbid获取email地址
    public function getEmailByFbid($fbid){
        if(empty($fbid)){
            return false;
        }
        $sql = 'select fd.email,fd.feedback,u.username from `ebh_projectfeedbacks` fd left join `ebh_users` u on (fd.uid = u.uid) where fbid = '.intval($fbid).' limit 1';
        $result = $this->ebhdb->query($sql)->row_array();
        return $result;
    }
} 