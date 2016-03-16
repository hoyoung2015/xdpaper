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
 ps.id,ps.periodical_id,ps.remark,submit_date,ps.status,record_json,is_active,paper_id,submit_status,periodical.name as periodical_name,periodical.web_site as periodical_site
 from paper_submit as ps
 inner join periodical on periodical.id=ps.periodical_id
 where paper_id='$paper_id'
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
        ))->find()?0:1;
        if($this->ableToNewSubmitFlag==1){
            //检查是否已被录用
            $this->ableToNewSubmitFlag = ($paper['is_active']==0&&$paper['paper_status']==C('PSSC')['ACCEPT'])?0:1;
        }

        Log::record('ableToNewSubmitFlag>>>>>'.json_encode($paper),Log::DEBUG);

        //取出活跃投稿
        if(count($paperSubmits)>0 && $paperSubmits[0]['is_active']==1){
            //取出第一个
            $this->activePaperSubmit = array_shift($paperSubmits);
        }

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

            //检查次期刊是否已经投过了
            $count = $model->where(array(
                'paper_id'=>$paper_id,
                'periodical_id'=>$_POST['periodical_id']
            ))->count();
            if($count>0){
                $this->error('该期刊已经投过了');
            }
            //生成初始状态
            $record_json_arr = array(
                array(
                    'update_date'=>$_POST['submit_date'],
                    'status_code'=>C('PSSC')['INIT'],
                    'status_name'=>C('PSSC_NAME')['INIT'],
                    'remark'=>$_POST['remark'],
                    'is_active'=>0  //未投是初始状态，都设为0
                )
            );


            $res = $model->addSubmit(array(
                'sid'=>session('user_auth')['uid'],
                'record_json'=>json_encode($record_json_arr),
                'submit_status'=>C('PSSC')['INIT']
            ));
            if($res){
                $user = session('user_auth');

                $periodical = M('Periodical')->field('name')->find($_POST['periodical_id']);

                $content = $user['nickname']."的论文《".$paper['name']."》在".$_POST['submit_date']."进入".$periodical['name']."的 ".get_status_name(C('PSSC')['INIT'])." 状态";
//                $url = U('StudentPaper')
                //发送消息
                D('Admin/TeacherMsg')->receiveMsg($user['tid'],$content,'');



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

            //查询已经投过的期刊id
            $ps = M('PaperSubmit')->field('periodical_id')->where(array(
                'paper_id'=>$paper_id
            ))->select();
            $tmp = array();
            foreach($ps as $v){
                array_push($tmp,$v['periodical_id']);
            }
            $tid = session('user_auth')['tid'];
            $init_code = C('PSSC')['INIT'];
            $review_code = C('PSSC')['REVIEW'];
            $sql = <<<sql
select pe.id,pe.name,if(ps.total,ps.total,0) as total
from periodical as pe
left join (
	select id,periodical_id,submit_status,count(id) as total from paper_submit
    where submit_status in ($init_code,$review_code)
    group by periodical_id
) ps on ps.periodical_id=pe.id
where pe.tid=$tid
sql;

            if(!empty($tmp)){
                $sql .= ' and pe.id not in ('.join(',',$tmp).')';
            }

            $periodical = M()->query($sql);

            $this->assign('periodical',$periodical);

            $this->display();
        }
    }
    public function delTopStatus($submit_id){
        $model = D('Admin/PaperSubmit');
        $res = $model->delTopStatus($submit_id);
        $this->success ( '操作完成' ,U('index',array('paper_id'=>$res['paper_id'])));
    }
    public function updateStatus($submit_id=null){
        $model = D('Admin/PaperSubmit');
        if(IS_POST){


            $paperSubmit = $model->find($_POST['submit_id']);
            $record_arr = json_decode($paperSubmit['record_json'],true);
            $periodical = M('Periodical')->field('name')->find($paperSubmit['periodical_id']);
            //判断submit_date是否未空
            if(empty($_POST['submit_date'])){
                $this->error('更新日期不能为空');
            }

            //将第一个的活跃状态取消
            $record_arr[0]['is_active'] = 0;

            array_unshift($record_arr,array(
                'update_date'=>$_POST['submit_date'],
                'status_code'=>$_POST['status_code'],
                'status_name'=>get_status_name(intval($_POST['status_code'])),
                'remark'=>$_POST['remark'],
                'is_active'=>1  //活跃状态
            ));
            $paperSubmit['submit_status'] = $_POST['status_code'];
            $paperSubmit['record_json'] = json_encode($record_arr);

            $res = $model->updateStatus($paperSubmit);

            $user = session('user_auth');

            Log::record('更新投递状态后的结果>>>>>'.$res,Log::DEBUG);
            if($res){
                $user = session('user_auth');
                //查论文
                $paper = M('Paper')->field('name')->find($paperSubmit['paper_id']);
                $content = $user['nickname']."的论文《".$paper['name']."》在".$_POST['submit_date']."进入".$periodical['name']."的 ".get_status_name($paperSubmit['submit_status'])." 状态";
//                $url = U('StudentPaper')
                //发送消息
                D('Admin/TeacherMsg')->receiveMsg($user['tid'],$content,'');


                $this->success('投递状态更新成功！',U('index',array('paper_id'=>$paperSubmit['paper_id'])));
            }else{
                $this->error($model->getError());
            }
        }else{
            $submit = $model->find($submit_id);
            $this->paper = M('Paper')->find($submit['paper_id']);
            $this->periodical = M('Periodical')->find($submit['periodical_id']);

            $record_arr = json_decode($submit['record_json'],true);

            $current_status_code = $record_arr[0]['status_code'];
            $list = array();
            if($current_status_code==C('PSSC')['INIT']){
                //未投->在审
                array_push($list,array(
                    'status_code'=>C('PSSC')['REVIEW'],
                    'status_name'=>C('PSSC_NAME')['REVIEW']
                ));
                array_push($list,array(
                    'status_code'=>C('PSSC')['REJECT'],
                    'status_name'=>C('PSSC_NAME')['REJECT']
                ));
            }else if($current_status_code==C('PSSC')['REVIEW']){
                //在审->拒绝或者在审->录用
                array_push($list,array(
                    'status_code'=>C('PSSC')['REJECT'],
                    'status_name'=>C('PSSC_NAME')['REJECT']
                ));
                array_push($list,array(
                    'status_code'=>C('PSSC')['ACCEPT'],
                    'status_name'=>C('PSSC_NAME')['ACCEPT']
                ));
            }
            $this->assign('list_data',$list);
            $this->assign('submit',$submit);

            $this->display();
        }
    }
    public function closeSubmit($submit_id){
        $model = D('Admin/PaperSubmit');
        $res = $model->closeSubmit($submit_id);
        if($res){
            $this->success('操作完成',U('index',array('paper_id'=>$res['paper_id'])));
        }else{
            $this->error('出错了');
        }
    }
    public function reviewingPeriod($period_id){
        //查询所属导师的所有学生
        $tid = session('user_auth')['tid'];
        $init_code = C('PSSC')['INIT'];
        $review_code = C('PSSC')['REVIEW'];
        $sql = <<<sql
SELECT p.id as paper_id, p.name as paper_name,p.sid,ps.submit_status,s.nickname,ps.submit_date,ps.periodical_id
FROM paper as p
inner join paper_submit as ps on ps.paper_id=p.id
inner join student as s on p.sid=s.id
where ps.periodical_id=$period_id and ps.submit_status in ($init_code,$review_code)
and s.tid=$tid order by ps.submit_date desc
sql;
        $this->listdata = M()->query($sql);
        $periodical = M('periodical')->field('name')->find($period_id);
        if($periodical){
            $this->assign('period_name',$periodical['name']);
        }
        $this->display();
    }

}