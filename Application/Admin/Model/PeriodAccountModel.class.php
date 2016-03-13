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
class PeriodAccountModel extends \Think\Model{
    /* 自动验证规则 */
    protected $_validate = array(
        array('username', 'require', '用户名不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('password', 'require', '密码不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
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

        Log::record('将要存入数据库中的论文数据'.json_encode($data),Log::DEBUG);
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
}