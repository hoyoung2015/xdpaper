<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/19
 * Time: 10:11
 */
namespace Teacher\Controller;
use \Think\Controller;
use Think\Log;
class IndexController extends TeacherController{
    public function index(){

        Log::record('Teacher IndexController',Log::DEBUG);
        $this->display();
    }
    public function center(){

        $this->display();
    }
}