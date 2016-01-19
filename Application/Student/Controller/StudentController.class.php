<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/19
 * Time: 10:11
 */
namespace Student\Controller;
use \Think\Controller;
use Think\Log;
class StudentController extends Controller{
    public function _initialize(){
        $userAuth = session('user_auth');
        Log::record('Student初始化'.json_encode($_SESSION),Log::DEBUG);
        if(empty($userAuth)){
            //尚未登录
            $this->redirect('Auth/login');
        }
    }
}