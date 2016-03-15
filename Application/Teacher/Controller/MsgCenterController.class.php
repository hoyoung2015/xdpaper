<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/19
 * Time: 14:36
 */
namespace Teacher\Controller;
use \Think\Controller;
use Think\Log;

class MsgCenterController extends TeacherController{
    public function index($read = 100,$row = 20){
        $map = array(
            'tid'=>session('user_auth')['uid'],
        );

        if($read!=100){
            $map['read'] = intval($read);
        }

        $model = M('TeacherMsg');


        $count = $model->where($map)->count();
        // 分页
//        if ($count > $row) {
            $page = new \Think\Page ( $count, $row );
            $page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
            $this->assign('_page',$page->show ());
//        }

        $list = $model->where($map)->order('create_time desc')->limit($page->firstRow,$row)->select();

        //统计已读
        $this->hasRead = $model->where(array(
            'tid'=>session('user_auth')['uid'],
            'read'=>1
        ))->count();
        //未读
        $this->notRead = $model->where(array(
            'tid'=>session('user_auth')['uid'],
            'read'=>0
        ))->count();



        $this->assign('list_data',$list);
        $this->read = $read;
        $this->display();
    }
    public function setRead($ids = null){
        if(IS_POST){
            $res = M('TeacherMsg')->where(array(
                'id'=>array('in',$ids)
            ))->save(array('read'=>1));
            if($res){
                $this->success('标记完成',U('index'));
            }else{
                $this->error('出错了');
            }
        }
    }
    public function setAllRead(){
        $res = M('TeacherMsg')->where(array(
            'read'=>0
        ))->save(array('read'=>1));
        if($res){
            $this->success('标记完成',U('index'));
        }else{
            $this->error('消息都为已读了');
        }
    }
}