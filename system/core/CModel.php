<?php
/**
 * Description of CModel
 */
class CModel {
   var $db = NULL;
   function __construct() {
       $this->db = Ebh::app()->getDb();
   }
}