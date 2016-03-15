<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/19
 * Time: 15:23
 */
function format_list($list){

}
function is_teacher_login(){
    $userAuth = session('user_auth');

    if(empty($userAuth)){
        //尚未登录
        return false;
    }
    return $userAuth['uid'];
}
/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function check_verify($code, $id = 1){
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}
