<?php

/**
 * Created by PhpStorm.
 * User: @wjf
 * Date: 2016/10/9
 * Time: 13:37
 * 奖金发放类对应kf_bonusissues表
 */
class BonusModel extends CModel
{

    //添加记录到记录表中
    public function addRecord($param){
        $arr=array();
        if(!empty($param['jsonstr']))
            $arr['jsonstr'] = $param['jsonstr'];
        if(!empty($param['dateline']))
            $arr['dateline'] = $param['dateline'];
        if(!empty($param['director']))
            $arr['director'] =$this->db->escape_str($param['director']) ;
        if(!empty($param['operator']))
            $arr['operator'] = $this->db->escape_str($param['operator']);
        if(!empty($param['uid']))
            $arr['uid'] = $param['uid'];
        if(!empty($param['status']))
            $arr['status'] = $param['status'];
        if(!empty($param['title']))
            $arr['title'] = $this->db->escape_str($param['title']);
        if(!empty($param['totalmoney']))
            $arr['totalmoney'] = $this->db->escape_str($param['inputamount']);

        $row = $this->db->insert('kf_bonusissues',$arr);
        return $row;
    }
    //获取记录列表
    public function  getRecordList($param){
        $sql='select bid,uid,jsonstr,operator,uid,dateline,title,director,totalmoney from kf_bonusissues ';
        if(!empty($param['q'])){
            $wherearr[] = '(title like \'%'. $this->db->escape_str($param['q']) .'%\')';
            $wherearr[] ='status=1';
        }else{
            $wherearr[] ='status=1';
        }
        if(!empty($wherearr))
            $sql.= ' WHERE '.implode(' AND ',$wherearr);
        if (!empty($param['order'])) {
            $sql .= ' order by ' . $param['order'];
        } else {
            $sql .= ' order by bid desc ';
        }
        if(!empty($param['limit'])) {
            $sql .= ' limit '. $param['limit'];
        }else{
            if (empty($param['page']) || $param['page'] < 1)
                $page = 1;
            else
                $page = $param['page'];
            $pagesize = empty($param['pagesize']) ? 10 : $param['pagesize'];
            $start = ($page - 1) * $pagesize;
            $sql .= ' limit ' . $start . ',' . $pagesize;
        }

        $res=$this->db->query($sql)->list_array();
        return $res;
    }
    //统计条数
    public  function getRecordCount($param){
        $sql='select count(*) count from kf_bonusissues ';
        if(!empty($param['q'])){
            $wherearr[] = '(title like \'%'. $this->db->escape_str($param['q']) .'%\')';
            $wherearr[] ='status=1';
        }else{
            $wherearr[] ='status=1';
        }
        if(!empty($wherearr))
            $sql.= ' WHERE '.implode(' AND ',$wherearr);
        if(!empty($param['order'])){
            $sql.= ' order by ' .$param['order'];
        }
        else{
            $sql.= ' order by bid desc';
        }
        $count = $this->db->query($sql)->row_array();
        return $count['count'];

    }
    //获取一条记录
    public function getViewByBid($bid){
        if(!empty($bid))
            $bid=intval($bid);
        $sql='select jsonstr, dateline from kf_bonusissues WHERE bid ='.$bid;
        $row = $this->db->query($sql)->row_array();
        return $row;
    }
}