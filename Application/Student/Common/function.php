<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/19
 * Time: 10:34
 */
function is_student_login(){
    $userAuth = session('user_auth');

    if(empty($userAuth)){
        //尚未登录
        return false;
    }
    return $userAuth['uid'];
}
