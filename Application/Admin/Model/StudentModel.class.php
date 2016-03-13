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
        array('username', 'require', '登录名为必须', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('username', '/^[a-zA-Z]\w{0,39}$/', '登录名不合法', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('username', '', '登录名已经存在', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH),
        array('nickname', 'require', '姓名不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('nickname', '1,20', '姓名长度不能超过20个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        array('password', '6,12', '密码长度在6到12位之间', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
        array('remark', '0,120', '备注不能超过140个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
        array('email', 'require', '邮件为必须', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('tag', 'require', '标签为必须', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('repassword','password','密码不一致',self::EXISTS_VALIDATE,'confirm'),
        array('email','email','email格式错误'),
    );
    /* 自动完成规则 */
    protected $_auto = array(
        array('status', 1, self::MODEL_INSERT, 'string'),
        array('login', 0, self::MODEL_INSERT, 'string'),
        array('update_time', 'time', self::MODEL_BOTH, 'function'),
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
    );

    public function get($map = array('id'=>0,'tid'=>0)){
        $info = $this->where($map)->find();
        //去掉标签首尾的逗号
        $info['tag'] = substr($info['tag'],1,count($info['tag'])-2);
        return $info;
    }
    /**
     * @param array $input 想要新增加的数据
     * @param array $where 更新条件
     * @return bool|mixed
     */
    public function update($input=array(),$where = array()){
        /* 获取数据对象 */
        $data = $this->create(array_merge($_POST,$input));

        if(empty($data) || $data===false){
            return false;
        }

        Log::record('将要存入数据库中的学生数据'.json_encode($data),Log::DEBUG);
        //补充首尾的逗号
        $data['tag'] = ",".$data['tag'].",";
        /* 添加或新增行为 */
        if(empty($data['id'])){ //新增数据

            $id = $this->add($data); //添加行为

            if(!$id){
                $this->error = $this->getError();
                return false;
            }
        } else { //更新数据
            $status = $this->where(array_merge(array(
                'id'=>$data['id']
            ),$where))->save($data); //更新基础内容
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
                $tag['tag'] = substr($tag['tag'],1,count($tag['tag'])-2);
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
    /**
     * 更新用户信息
     *
     * @param int $uid
     *        	用户id
     * @param string $password
     *        	密码，用来验证
     * @param array $data
     *        	修改的字段数组
     * @return true 修改成功，false 修改失败
     * @author huajie <banhuajie@163.com>
     */
    public function updateUserFields($uid, $password, $data) {
        if (empty ( $uid ) || empty ( $password ) || empty ( $data )) {
            $this->error = '参数错误！';
            return false;
        }

        $map ['id'] = $uid;
        $user = $this->where ( $map )->find ();
        if ($user['password'] !== $password) {
            $this->error = '验证出错：密码不正确！';
            return false;
        }
        $user['update_time'] = time();
        // 更新用户信息
        $data = $this->create ( array_merge($user,$data) );
        if ($data) {
            $res = $this->where ( array (
                'id' => $uid
            ) )->save ( $data );
            return $res;
        }
        return false;
    }
}