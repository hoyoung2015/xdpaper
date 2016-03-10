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
function get_status_name($status_code){
    $arr = array(
        1=>'未投',
        2=>'在审',
        3=>'被拒',
        4=>'录用'
    );
    return $arr[$status_code];
}
