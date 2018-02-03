<?php
/**
 * 课件附件相关SummaryModel类
 */
class SummaryModel extends CModel
{

    public function __construct(){
        parent::__construct();
        $this->pandb = Ebh::app()->getOtherDb("pandb");
        $this->snsdb = Ebh::app()->getOtherDb("snsdb");
        $this->ebhdb = Ebh::app()->getOtherDb('ebhdb');
    }

    /**
     * @param $param
     * @return mixed
     * 获取审核人审核包括撤销次数列表
     */
    public function getCheckList($param){
        $where = ' admin_status in (0,1,2,3)';
        if(!empty($param['type'])){
            $where.=' and type ='.$param['type'];
        }
        if(!empty($param['admin_uid'])){
            $where.=' and admin_uid in ('.$param['admin_uid'].')';
        }
        if(!empty($param['startdate'])&&!empty($param['enddate'])){
            $where.=' and admin_dateline between UNIX_TIMESTAMP("'.$param['startdate'].'") and UNIX_TIMESTAMP("'.$param['enddate'].'")+86400';
        }elseif(!empty($param['startdate'])&&empty($param['enddate'])){
            $where.=' and admin_dateline > UNIX_TIMESTAMP("'.$param['startdate'].'")';
        }elseif(empty($param['startdate'])&&!empty($param['enddate'])){
            $where.=' and admin_dateline < UNIX_TIMESTAMP("'.$param['enddate'].'")+86400';
        }
        if(!empty($where)){
            $where = ' where'.$where;
        }
        if(empty($param['cat'])){
            $table = 'ebh_billchecks';
            $database = $this->ebhdb;
        }elseif($param['cat']==1){
            $table = 'ebh_sns_billchecks';
            $database = $this->snsdb;
        }elseif($param['cat']==2){
            $table = 'pan_billchecks';
            $database = $this->pandb;
        }elseif($param['cat']==3){
            $table = 'ebh_examchecks';
            $database = $this->examdb;
        }
        $sql = 'select admin_uid,count(*) as count from '.$table;
        $sql .= $where;
        $sql.=' group by admin_uid';
        $listall = $database->query($sql)->list_array();
        $count = count($listall);
        $sql.= ' limit ' . $param['limit'];
        $list = $database->query($sql)->list_array();
        $list['count'] = $count;
        return $list;
    }

//    public function getweekcount($param){
//        $sql = 'select count(*) as count from ebh_billchecks';
//        $WHERE = ' where admin_uid ='.$param.' and admin_dateline + 864000*7 > UNIX_TIMESTAMP(NOW())';
//        $sql .=$WHERE;
//        $count =  $this->ebhdb->query($sql)->row_array();
//        return $count;
//    }

    /**
     * 根据客服id获取审核不通过和撤销数量
     * @param $uid  客服id
     * @param $param
     * @return mixed
     */
    public function getKindsCount($uid,$param){
        if(empty($param['cat'])){
            $table = 'ebh_billchecks';
            $database = $this->ebhdb;
        }elseif($param['cat']==1){
            $table = 'ebh_sns_billchecks';
            $database = $this->snsdb;
        }elseif($param['cat']==2){
            $table = 'pan_billchecks';
            $database = $this->pandb;
        }elseif($param['cat']==3){
            $table = 'ebh_examchecks';
            $database = $this->examdb;
        }
        $sql = 'select count(*) as count from '.$table;
        $where = ' where admin_status = 2 and admin_uid ='.$uid;
        if(!empty($param['type'])){
            $where.=' and type ='.$param['type'];
        }
        if(!empty($param['startdate'])&&!empty($param['enddate'])){
            $where.=' and admin_dateline between UNIX_TIMESTAMP("'.$param['startdate'].'") and UNIX_TIMESTAMP("'.$param['enddate'].'")+86400';
        }elseif(!empty($param['startdate'])&&empty($param['enddate'])){
            $where.=' and admin_dateline > UNIX_TIMESTAMP("'.$param['startdate'].'")';
        }elseif(empty($param['startdate'])&&!empty($param['enddate'])){
            $where.=' and admin_dateline < UNIX_TIMESTAMP("'.$param['enddate'].'")+86400';
        }
        $arr = array();
        $sql .= $where;
        $uncheck = $database->query($sql)->row_array();
        $arr['uncheck'] = $uncheck['count'];
        $sql = str_replace('admin_status = 2','admin_status in(0,3)',$sql);
        $revoke = $database->query($sql)->row_array();
        $arr['revoke'] = $revoke['count'];
        return $arr;
    }

    public function getIdByName($param){
        $sql = 'select uid from kf_user where username like \'%'.$param.'%\' or realname like \'%'.$param.'%\'';
        return $this->db->query($sql)->list_array();
    }
}