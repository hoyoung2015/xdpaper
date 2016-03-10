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
class TeacherModel extends \Think\Model{
    /* 自动验证规则 */
    protected $_validate = array(
        array('username', 'require', '用户名为必须', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('username', '/^[a-zA-Z]\w{0,39}$/', '用户名不合法', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('username', '', '用户名已经存在', self::MUST_VALIDATE, 'unique', self::MODEL_INSERT),
        array('nickname', 'require', '标题不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('nickname', '1,20', '标题长度不能超过20个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        array('remark', '1,140', '行为描述不能超过140个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
        array('email', 'require', '邮件为必须', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('email','email','email格式错误',self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
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
        Log::record('craete前的数据'.$_POST['nickname'],Log::DEBUG);
        $data = $this->create(array_merge($_POST,$input));
        Log::record('中文json编码测试'.json_encode(array('name'=>'你好')),Log::DEBUG);
        Log::record('存入数据库的数据'.$data['nickname'],Log::DEBUG);
        if(empty($data) || $data===false){
            return false;
        }
        /* 添加或新增行为 */
        if(empty($data['id'])){ //新增数据

            $id = $this->add(); //添加行为

            if(!$id){
                $this->error = $this->getError();
                return false;
            }
        } else { //更新数据
            $status = $this->save(); //更新基础内容
            if(false === $status){
                $this->error = '更新行为出错！';
                return false;
            }
        }
        //内容添加或更新完成
        return $data;

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
    /**
     * 验证用户密码
     *
     * @param int $uid
     *        	用户id
     * @param string $password_in
     *        	密码
     * @return true 验证成功，false 验证失败
     * @author huajie <banhuajie@163.com>
     */
    protected function verifyUser($uid, $password_in) {
        // $password = $this->getFieldById ( $uid, 'password' );
        $map ['id'] = $uid;
        $password = $this->where ( $map )->getField ( 'password' );
        if ($password_in === $password) {
            return true;
        }
        return false;
    }
}