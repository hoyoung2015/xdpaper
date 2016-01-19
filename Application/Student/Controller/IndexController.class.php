<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/19
 * Time: 10:11
 */
namespace Student\Controller;
use \Think\Controller;
class IndexController extends StudentController{
    public function index(){

        $this->display();
    }
}