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
class PeriodicalModel extends \Think\Model{
    /* 自动验证规则 */
    protected $_validate = array(
        array('name', 'require', '期刊名为必须', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('name', '', '期刊名已经存在', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH),
        array('domain', 'require', '领域为必须', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('tag', 'require', '级别为必须', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),

        array('web_site', 'require', '网址为必须', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('web_site', 'url', '网址为格式错误', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
    );
    /* 自动完成规则 */
    protected $_auto = array(
        array('status', 1, self::MODEL_INSERT, 'string'),
        array('update_time', 'time', self::MODEL_BOTH, 'function'),
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
    );

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
        /* 添加或新增行为 */
        if(empty($data['id'])){ //新增数据

            $id = $this->add(); //添加行为

            if(!$id){
                $this->error = $this->getError();
                return false;
            }
        } else { //更新数据
            $status = $this->where(array_merge(array(
                'id'=>$data['id']
            ),$where))->save(); //更新基础内容
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
        Log::record('期刊原始标签：'.json_encode($tags),Log::DEBUG);

        $tagsOutput = array();
        foreach($tags as $tag){
            if(!empty($tag['tag'])){

                $tagsOutput = array_values_merge($tagsOutput,explode(',',$tag['tag']));
            }
        }

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