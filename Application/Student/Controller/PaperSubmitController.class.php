<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/21
 * Time: 13:19
 */
namespace Student\Controller;
use \Think\Controller;
use Think\Log;
class PaperSubmitController extends StudentController{
    public function index($paper_id = null){
        if(!$paper_id){
            $this->error('错误的操作');
        }
        //检查是不是本人的期刊
        $paper = $this->checkPaperAuth($paper_id);
        if(!$paper){
            $this->error('非法的操作');
        }
        //查询多条提交记录
        $model = M();
        $sql = <<<sql
        select
 ps.id,ps.periodical_id,ps.remark,submit_date,ps.status,record_json,is_active,paper_id,submit_status,periodical.name as periodical_name
 from paper_submit as ps
 inner join periodical on periodical.id=ps.periodical_id
 order by ps.create_time desc
sql;
        $paperSubmits = $model->query($sql);


        for($i=0;$i<count($paperSubmits);$i++){
            //解析json字符串格式的记录
            $paperSubmits[$i]['record_arr'] = json_decode($paperSubmits[$i]['record_json'],true);
            $paperSubmits[$i]['record_json'] = null;
            //理论上只有第一个在投的可以修改
            if($i==0){

            }
        }
//        p($paperSubmits);die();

        /**
         * 判断是否可以新投稿
         * 如果有正在活跃的投稿
         * 那么不能投稿
         */
        $this->ableToNewSubmitFlag = M('PaperSubmit')->where(array(
            'sid'=>session('user_auth')['uid'],
            'paper_id'=>$paper_id,
            'is_active'=>1
        ))->find()?1:0;

        Log::record('ableToNewSubmitFlag>>>>>'.$this->ableToNewSubmitFlag,Log::DEBUG);


        $this->paper = $paper;
        $this->paperSubmits = $paperSubmits;
        $this->display();
    }
    public function newSubmit($paper_id){
        if(IS_POST){
            //检查paper_id是不是登陆者的
            $paper = $this->checkPaperAuth($_POST['paper_id']);

            if(!$paper){
                $this->error('非法的操作');
            }

            $model = D('Admin/PaperSubmit');

            //生成初始状态
            $record_json_arr = array(
                array(
                    'update_date'=>$_POST['submit_date'],
                    'status_code'=>1,
                    'status_name'=>'未投',
                    'remark'=>'这是备注',
                    'is_active'=>0  //未投是初始状态，都设为0
                )
            );


            $res = $model->addSubmit(array(
                'sid'=>session('user_auth')['uid'],
                'record_json'=>json_encode($record_json_arr),
                'submit_status'=>1
            ));
            if($res){
                $this->success('新投递成功！',U('index',array('paper_id'=>$paper['id'])));
            }else{
                $this->error($model->getError());
            }

        }else{
            //检查是不是本人的期刊
            $paper = $this->checkPaperAuth($paper_id);
            if(!$paper){
                $this->error('非法的操作');
            }
            $this->paper = $paper;
            //查询期刊
            $this->periodical = M('Periodical')->select();

            $this->display();
        }
    }
    public function delTopStatus($submit_id){
        $model = M('PaperSubmit');
        $submit = $model->where(array(
            'id'=>$submit_id
        ))->find();
        $record_arr = json_decode($submit['record_json'],true);
        /**
         * 只有一个的话是未投状态，不能删
         */

        array_shift($record_arr);
        $submit['record_json'] = json_encode($record_arr);
        $res = $model->save($submit);
        Log::record('删除后保存的结果>>>>>'.$res,Log::DEBUG);

        $this->success ( '删除成功' );
    }
    public function updateStatus($submit_id=null){
        if(IS_POST){



            $model = D('PaperSubmit');
            $paperSubmit = $model->find($_POST['submit_id']);
            $record_arr = json_decode($paperSubmit['record_json'],true);

            //将第一个的活跃状态取消
            $record_arr[0]['is_active'] = 0;

            array_unshift($record_arr,array(
                'update_date'=>$_POST['submit_date'],
                'status_code'=>$_POST['status_code'],
                'status_name'=>get_status_name($_POST['status_code']),
                'remark'=>$_POST['remark'],
                'is_active'=>1  //活跃状态
            ));
            $paperSubmit['record_json'] = json_encode($record_arr);
            $res = $model->save($paperSubmit);

            Log::record('更新投递状态后的结果>>>>>'.$res,Log::DEBUG);
            if($res){
                $this->success('投递状态更新成功！',U('index',array('paper_id'=>$paperSubmit['paper_id'])));
            }else{
                $this->error($model->getError());
            }
        }else{
            $model = M('PaperSubmit');
            $submit = $model->find($submit_id);
            $this->paper = M('Paper')->find($submit['paper_id']);
            $this->periodical = M('Periodical')->find($submit['periodical_id']);

            $record_arr = json_decode($submit['record_json'],true);

            $current_status_code = $record_arr[0]['status_code'];
            $list = array();
            if($current_status_code==1){
                //未投->在审
                array_push($list,array(
                    'status_code'=>2,
                    'status_name'=>'在审'
                ));
            }else if($current_status_code==2){
                //在审->拒绝或者在审->录用
                array_push($list,array(
                    'status_code'=>3,
                    'status_name'=>'被拒'
                ));
                array_push($list,array(
                    'status_code'=>4,
                    'status_name'=>'录用'
                ));
            }
            $this->assign('list_data',$list);
            $this->assign('submit',$submit);

            $this->display();
        }
    }
}