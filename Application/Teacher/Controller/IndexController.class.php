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

        $uid = session('user_auth')['uid'];

        $info = M('Teacher')->where('id='.$uid)->limit(1)->select();
        if(empty($info)){
            $this->error('出错啦');
        }

        $this->assign('info',$info[0]);
        $this->display();
    }

    public function updateInfo(){

        $model = D('Admin/Teacher');

        $result = $model->update(array(
            'id'=>session('user_auth')['uid']
        ));
        if($result===false){
            $this->error($model->getError());
        }else{
            $this->success('个人信息保存成功');
        }

    }
}