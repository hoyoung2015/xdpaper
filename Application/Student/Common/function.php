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

function get_status_color($status_code){
    $pssc  = C('PSSC');
    foreach($pssc as $key=>$value){
        if($value==$status_code){
            return C('PSSC_COLOR')[$key];
        }
    }
    return null;
}
function send_msg_to_tacher($tid='',$content='',$url=''){

    $msgModel = D('Admin/TeacherMsg');
    $msgModel->add(array(

    ));
    return true;
}
