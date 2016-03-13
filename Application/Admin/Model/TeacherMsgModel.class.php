<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/18
 * Time: 14:39
 */
namespace Admin\Model;
use Think\Log;
use Think\Model;
class TeacherMsgModel extends \Think\Model{
    /* 自动验证规则 */
    protected $_validate = array(
//        array('tid', 'require', '用户名不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
//        array('password', 'require', '密码不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );
    /* 自动完成规则 */
    protected $_auto = array(
        array('status', 1, self::MODEL_INSERT, 'string'),
        array('read', 0, self::MODEL_INSERT, 'string'),
        array('update_time', 'time', self::MODEL_BOTH, 'function'),
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
    );

    function receiveMsg($tid='',$content='',$url=''){
        if($this->create(array(
            'tid'=>$tid,
            'content'=>$content,
            'url'=>$url
        ))){
            if($this->add()){
                return true;
            }
        }
        return false;
    }
}