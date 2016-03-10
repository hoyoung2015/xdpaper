<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/18
 * Time: 16:26
 */
namespace Teacher\Controller;
use Think\Controller;
use Think\Log;
class AuthController extends Controller{
    public function login($username = '', $password = '', $verify = ''){
        Log::record('Teacher登录',Log::DEBUG);
        if(IS_POST){
            /* 检测验证码 */
//            if(!check_verify($verify,2)){
//                $this->error('验证码输入错误！');
//            }
            $model = M('Teacher');
            $tea = $model->where(array('username'=>$username))->limit(1)->select();
            Log::record('导师信息'.json_encode($tea),Log::DEBUG);
            Log::record('密码'.json_encode($password),Log::DEBUG);
            if($tea && is_array($tea) && $tea[0]['password']===$password){
                $tea = $tea[0];
                if($tea['status']==0){
                    $this->error('该账号已被禁用！');
                }
                if($tea['status']==-1){
                    $this->error('该账号已被删除！');
                }

                //登录次数加1
                $model->where('id='.$tea['id'])->save(array(
                    'login'=>$tea['login']+1,
                    'last_login_time'=>time(),
                    'last_login_ip'=>get_client_ip()
                ));

                $map = array(
                    'usertype'=>'teacher',
                    'username'=>$username,
                    'uid'=>$tea['id']
                );
                session('user_auth',$map);
                $userAuth = session('user_auth');
                $this->ajaxReturn(array(
                    'status'=>1,
                    'url'=>U('Teacher/Index/index')
                ));
            }else{
                $this->error('用户名或密码错误！');
            }


        }else{
            //判断是否已登录
            $userAuth = session('user_auth');
            if(empty($userAuth)){
                $this->display('login');
            }else{//已登录
                $this->redirect('Index/index');
            }
        }
    }
    public function verify(){
        $verify = new \Think\Verify();
        $verify->entry(2);
    }
    /* 退出登录 */
    public function logout() {
        if (is_teacher_login ()) {
            session ( 'user_auth', null );
            if (isset ( $_GET ['no_tips'] )) {
                $this->redirect ( 'User/login' );
            }
             $this->success ( '退出成功！', U ( 'Teacher/Auth/login' ) );
        } else {
            $this->redirect ( 'Teacher/Auth/login' );
        }
    }
}