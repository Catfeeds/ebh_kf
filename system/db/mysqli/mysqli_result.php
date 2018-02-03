<?php

/**
 * Description of mysqli_result
 *
 * @author Administrator
 */
class CMysqli_result extends CResult {
    public function _row_array() {
        if(empty($this->resultobj) || !is_object($this->resultobj)) {
            return false;
        }
        $row = $this->resultobj->fetch_array(MYSQLI_ASSOC);
        return $row;
    }
    public function _list_array() {
        if(empty($this->resultobj) || !is_object($this->resultobj)) {
            return false;
        }
        $resultarr = array();
        while($row = $this->resultobj->fetch_array(MYSQLI_ASSOC)) {
            $resultarr[] = $row;
        }
        return $resultarr;
    }
    public function close() {
        if(!empty($this->resultobj) && is_object($this->resultobj)) {
            $this->resultobj->free();
        }
    }
}