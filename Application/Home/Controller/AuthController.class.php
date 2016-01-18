<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/18
 * Time: 16:26
 */
namespace Home\Controller;
use Think\Controller;
class AuthController extends Controller{
    public function login($username = '', $password = '', $verify = ''){
        $this->u = I('u','s');
        if(IS_POST){
            /* 检测验证码 */
            if(!check_verify($verify)){
                $this->error('验证码输入错误！');
            }

            if('s'==$this->u){//学生





            }elseif('t'==$this->u){//导师
                $this->display('login_t');
            }else{
                $this->error('地址错误');
            }




        }else{

            if('s'==$this->u){//学生
                $this->display('login_s');
            }elseif('t'==$this->u){//导师
                $this->display('login_t');
            }else{
                $this->error('地址错误');
            }
        }
    }
    public function verify(){
        $verify = new \Think\Verify();
        $verify->entry(1);
    }
}