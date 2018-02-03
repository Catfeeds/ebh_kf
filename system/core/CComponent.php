<?php

/**
 * CComponent组件基类
 */
class CComponent {
    /**
     * 加载model类
     * @param string $modelname 模板名称
     * @return object model对象
     */
    public function model($modelname) {
        return Ebh::app()->model($modelname);
    }
}