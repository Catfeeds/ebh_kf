<?php
/**
 * 分类model类
 */
class CategoryModel extends CEbhModel{
        /**
     * 根据相关条件查询相关分类信息;
     * @param arary $where
     * @return array
     */
    public function getCategoriesByParam($where=array()){
        $sql='select catid,upid,name,type,code,position,visible,displayorder,system from ebh_categories c';
        $sql.=$this->parseWhere($where);
        $sql.=' order by position asc , displayorder asc';
        return $this->ebhdb->query($sql)->list_array();
       
    }
        /*
        简单的where条件处理;
        用法:传入 array(cid=>1,name="a");返回 where cid=1 and name='a';
        主要用于生成简单的sql语句的where条件
    */
    private function parseWhere($where=array()){
        if(isset($where[0])&&!is_array($where[0])){
            return ' where '.$where[0];
        }
        $where = $this->db->escape_str($where);
        if(count($where)==0){
            return;
        }
        $newwhere=' where ';
        foreach ($where as $key => $value) {
            if($value==''){continue;}
            $value=trim($value);
            if(preg_match("/^[0-9]+$/",$value)){
                $newwhere.=$key.'='.$value.' and ';
            }else{
                $newwhere.=$key.'='."'$value' and ";
            }
           
        }
        if(trim($newwhere)=='where')return '';
        return rtrim($newwhere,'and ');
    }

    /**
     * 获取分类名
     * @param  integer $catid 编号
     * @return string        名称
     */
    public function getCatName($catid) {
        $sql = 'select `name` from ebh_categories where catid=' . intval($catid);
        $row = $this->ebhdb->query($sql)->row_array();
        return $row['name'];
    }

}
