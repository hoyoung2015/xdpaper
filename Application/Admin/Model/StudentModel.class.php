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
class StudentModel extends \Think\Model{
    /* 自动验证规则 */
    protected $_validate = array(
        array('username', 'require', '登录名为必须', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('username', '/^[a-zA-Z]\w{0,39}$/', '登录名不合法', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('username', '', '登录名已经存在', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH),
        array('nickname', 'require', '姓名不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('nickname', '1,20', '姓名长度不能超过20个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        array('password', '6,12', '密码长度在6到12位之间', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        array('remark', '0,120', '备注不能超过140个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
        array('email', 'require', '邮件为必须', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('tag', 'require', '标签为必须', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('repassword','password','密码不一致',self::MUST_VALIDATE,'confirm'),
        array('email','email','email格式错误'),
    );
    /* 自动完成规则 */
    protected $_auto = array(
        array('status', 1, self::MODEL_INSERT, 'string'),
        array('login', 0, self::MODEL_INSERT, 'string'),
        array('update_time', 'time', self::MODEL_BOTH, 'function'),
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
    );
    public function update($input=array()){
        /* 获取数据对象 */
        $data = $this->create(array_merge($_POST,$input));

        if(empty($data) || $data===false){
            return false;
        }

        Log::record('将要存入数据库中的学生数据'.json_encode($data),Log::DEBUG);
        /* 添加或新增行为 */
        if(empty($data['id'])){ //新增数据

            $id = $this->add(); //添加行为

            if(!$id){
                $this->error = $this->getError();
                return false;
            }
        } else { //更新数据
            $status = $this->where(array(
                'tid'=>$data['tid'],
                'id'=>$data['id']
                ))->save(); //更新基础内容
            if(false === $status){
                $this->error = '更新行为出错！';
                return false;
            }
        }
        //内容添加或更新完成
        return $data;

    }
    public function findGroup(){
        $tags = $this->distinct(true)->field('tag')->select();
        Log::record('原始标签：'.json_encode($tags),Log::DEBUG);

        $tagsOutput = array();
        foreach($tags as $tag){
            if(!empty($tag['tag'])){

                $tagsOutput = array_values_merge($tagsOutput,explode(',',$tag['tag']));
            }
        }
        Log::record('处理后的标签：'.json_encode($tagsOutput),Log::DEBUG);
        sort($tagsOutput);
        return $tagsOutput;
    }
    public function del($where = array()){
        $data = $this->where($where)->delete();
        if($data){
            return true;
        }else{
            return false;
        }
    }
}