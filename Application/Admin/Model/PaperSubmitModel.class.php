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
class PaperSubmitModel extends \Think\Model{
    /* 自动验证规则 */
    protected $_validate = array(
        array('periodical_id', 'require', '请选择期刊', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('paper_id', 'require', '没有论文id', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('submit_date', 'require', '请选择投稿日期', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
    );
    /* 自动完成规则 */
    protected $_auto = array(
        array('status', 1, self::MODEL_INSERT, 'string'),
        array('is_active', 1, self::MODEL_INSERT, 'string'),
        array('update_time', 'time', self::MODEL_BOTH, 'function'),
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
    );
    /**
     * 第一次投稿
     * @param array $input
     * @return bool
     */
    public function addSubmit($input=array()){
        /* 获取数据对象 */

        $data = $this->create(array_merge($_POST,$input));
        Log::record('新投递的数据'.json_encode($data),Log::DEBUG);
        if(empty($data) || $data===false){
            return false;
        }
        $id = $this->add($data);
        if(!$id){
            $this->error = $this->getError();
            return false;
        }
        return true;
    }
    public function updateStatus($input=array()){

    }
}