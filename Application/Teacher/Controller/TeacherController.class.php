<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/19
 * Time: 10:11
 */
namespace Teacher\Controller;
use Common\Controller\CommonController;
use \Think\Controller;
use Think\Log;
class TeacherController extends CommonController{
    public function _initialize(){
        $userAuth = session('user_auth');

        if(empty($userAuth)){
            //尚未登录
            $this->redirect('Auth/login');
        }

        $this->myinfo = M('Teacher')->where('id='.$userAuth['uid'])->find();

        //统计未读消息
        $this->notReadIndex = M('TeacherMsg')->where(array(
            'tid'=>$userAuth['uid'],
            'read'=>0
        ))->count();

        Log::record('我的信息'.json_encode($this->myinfo),Log::DEBUG);
    }
}