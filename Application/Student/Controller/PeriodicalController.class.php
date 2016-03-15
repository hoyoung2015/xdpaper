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
class PeriodicalController extends StudentController{
    public function index($tag = '',$name = ''){
        $page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据
        $map = array(
            'tid'=>session('user_auth')['tid'] //注意，这里是tid，学生在登陆的时候session也记录了导师信息
        );
        Log::record('tag的值'.$tag,Log::DEBUG);
        empty($name) || $map['name'] = array('like', '%'.(string)$name.'%');
        empty($tag) || ($map['tag'] = array('like', '%,'.(string)$tag.',%'));

        $row = 10;

        $model = D('Admin/Periodical');
        $list = $model->where($map)->page($page,$row)->select();
        //去掉标签首尾的逗号
        foreach($list as &$pe){
            $pe['tag'] = substr($pe['tag'],1,count($pe['tag'])-2);
        }

        $count = $model->where($map)->count();
        // 分页
        $page = new \Think\Page ( $count, $row );
        $page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
        $this->assign('_page',$page->show ());

        $this->tags = $model->findGroup();
        $this->assign('list_data',$list);
        $this->assign('row',$row);

        //清除session
        session('able_to_set_account',null);

        $this->display();
    }
    public function add(){
        $model = D('Admin/Periodical');
        if(IS_POST){
            $data = $model->update(array(
                'tid'=>session('user_auth')['tid']
            ));
            if($data===false){//失败
                $this->error($model->getError());
            }else{//成功
                $this->success('期刊添加成功！',U('index'));
            }
        }else{
            $this->tags = implode(',',$model->findGroup());
            $this->display();
        }
    }
    public function edit($id=''){
        $model = D('Admin/Periodical');
        if(IS_POST){
            $data = $model->update(array(),array(
                'tid'=>session('user_auth')['tid']
            ));
            if($data===false){//失败
                $this->error($model->getError());
            }else{//成功
                $this->success('期刊信息修改成功！',U('index'));
            }
        }else{
            $this->info = $model->get($id);
            $this->tags = implode(',',$model->findGroup());
            $this->display('add');
        }
    }
    public function checkLoginPassd($period_id = null){
        if(IS_POST){
            $password = I('post.password');
            if(empty($password)){
                $this->error('密码不能为空');
            }

            $stu = M('Student')->field('password')->find(session('user_auth')['uid']);
            if(empty($stu)){
                $this->error('非法操作');
            }
            if($stu['password']!=$password){
                $this->error('密码不正确');
            }
            $period_id = I('post.period_id');
            //session中保存验证通过的信息
            session('able_to_set_account',1);
            $this->success('验证通过',U('setAccount',array('period_id'=>$period_id)));
        }else{
            $this->assign('period_id',$period_id);
            $this->display();
        }
    }
    public function setAccount($period_id=null){
        //检查是否验证通过有设置权限
        $able_to_set_account = session('able_to_set_account');
        if($able_to_set_account!=1){
            $this->error('您没有验证登陆密码',U('index'));
        }
Log::record('检查是否验证通过有设置权限'.session('able_to_set_account'),Log::DEBUG);

        if(IS_POST){

            $model = D('Admin/PeriodAccount');
            $data = $model->update(array(
                'sid'=>session('user_auth')['uid']
            ),array(
                'sid'=>session('user_auth')['uid']
            ));
            if($data===false){//失败
                $this->error($model->getError());
            }else{//成功
                $this->success('设置完成');
            }
        }else{
            //检查是否验证通过

            //查期刊
            $period = M('Periodical')->find($period_id);

            //查账户
            $info = M('PeriodAccount')->where(array(
                'period_id'=>$period_id,//期刊id
                'sid'=>session('user_auth')['uid']//学生id
            ))->find();
            $this->assign('period',$period);
            $this->assign('info',$info);
            $this->display();
        }
    }
}