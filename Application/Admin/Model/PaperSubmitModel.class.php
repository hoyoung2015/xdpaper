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
        //同时更新论文状态
        M('Paper')->where(array(
            'id'=>$data['paper_id'],
            'sid'=>session('user_auth')['uid']
        ))->save(array(
            'paper_status'=>$data['submit_status'],
            'periodical_id'=>$data['periodical_id']
        ));

        if(!$id){
            $this->error = $this->getError();
            return false;
        }
        return true;
    }
    public function updateStatus($input=array()){
        $this->save($input);
        $this->updatePaperStatus($input['paper_id'],$input['submit_status']);
        return true;
    }
    public function delTopStatus($submit_id){
        $submit = $this->find($submit_id);
        $record_arr = json_decode($submit['record_json'],true);//解析json数组
        array_shift($record_arr);//移除第一个
        $submit['record_json'] = json_encode($record_arr);
        //更新PaperSubmit状态
        $submit['submit_status'] = $record_arr[0]['status_code'];
        $this->save($submit);
        //更新Paper状态
        $this->updatePaperStatus($submit['paper_id'],$submit['submit_status']);

        return $submit;
    }
    public function closeSubmit($submit_id){
        //根据id查询PaperSubmit
        $paperSubmit = $this->find($submit_id);
        //解析json
        $record_arr = json_decode($paperSubmit['record_json'],true);
        //获取当前状态
        $finalStatusCode = $record_arr[0]['status_code'];
        $record_arr[0]['is_active'] = 0;//设置第一个状态为不活跃
        $paperSubmit['is_active'] = 0;//关闭paperSubmit活跃状态
        $paperSubmit['record_json'] = json_encode($record_arr);

        Log::record('DATA>>'.json_encode($paperSubmit),Log::DEBUG);

        if($finalStatusCode==C('PSSC')['REJECT']){//被拒
            $this->save($paperSubmit);
            //更新Paper状态为未投
            return $paperSubmit;
        }elseif($finalStatusCode==C('PSSC')['ACCEPT']){//录用
            $this->save($paperSubmit);
            //修改Paper状态，只有在录用情况下才能关闭论文，不再投稿
            M('Paper')->where(array(
                'id'=>$paperSubmit['paper_id']
            ))->save(array(
                'is_active'=>0,
                'periodical_id'=>$paperSubmit['periodical_id']
            ));
            return $paperSubmit;
        }else{//其他状态是不允许关闭的
            $this->error = '非法操作';
            return false;
        }
    }


    private function updatePaperStatus($paperId,$status){
        $tempModel = M('Paper');
        $tempModel->where(array(
            'id'=>$paperId,
            'sid'=>session('user_auth')['uid']
        ))->save(array(
            'paper_status'=>$status
        ));
        Log::record('SQL>>'.$tempModel->getlastSql(),Log::DEBUG);
        return true;
    }

}