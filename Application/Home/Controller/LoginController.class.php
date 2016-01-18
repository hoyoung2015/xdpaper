<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/18
 * Time: 16:26
 */
namespace Home\Controller;
use Think\Controller;
class LoginController extends Controller{
    public function index(){
        if(IS_POST){

        }else{
            $u = I('u','s');
            if('s'==$u){//学生
                $this->display('login_s');
            }elseif('t'==$u){//导师
                $this->display('login_t');
            }else{
                $this->error('地址错误');
            }
        }
    }
}