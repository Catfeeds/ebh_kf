<?php
/**
 * 数据库结果集类
 */
class CResult {
    var $resultobj = NULL;
    public function __construct($obj) {
        $this->resultobj = $obj;
    }

    public function row_array() {
        return $this->_row_array();
    }
    public function list_array() {
        return $this->_list_array();
    }
    public function __destruct() {
        $this->close();
    }
}