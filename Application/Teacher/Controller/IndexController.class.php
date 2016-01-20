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
    /*
     * 修改密码
     */
    public function chpassd($oldpassword='',$password='',$repassword=''){
        if(IS_POST){
            $password = I ( 'post.old' );
            $repassword = I ( 'post.repassword' );
            $data ['password'] = I ( 'post.password' );
            empty ( $password ) && $this->error ( '请输入原密码' );
            empty ( $data ['password'] ) && $this->error ( '请输入新密码' );
            empty ( $repassword ) && $this->error ( '请输入确认密码' );

            if ($data ['password'] !== $repassword) {
                $this->error ( '您输入的新密码与确认密码不一致' );
            }
            $uid = session('user_auth')['uid'];
            $model = D ( 'Admin/Teacher' );
            $res = $model->updateUserFields ( $uid, $password, $data );
            if ($res !== false) {
                session ( 'user_auth', null );
                $this->success ( '修改密码成功，请重新登录！',U ( 'Auth/login' ));
            } else {
                $this->error ( $model->getError() );
            }
        }else{
            $this->display();
        }
    }
}