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

class StudentController extends TeacherController{
    public function index(){
        Log::record('Teacher StudentController',Log::DEBUG);

        $user = session('user_auth');

        $list = M('Student')->where(array(
            'tid'=>$user['uid']
        ))->select();

        $this->assign('list_data',$list);

        $this->display();
    }
    public function add(){
        if(IS_POST){
            $tid = session('user_auth')['uid'];
            $model = D('Admin/Student');
            $data = $model->update(array(
                'tid'=>$tid
            ));
            if($data===false){//失败
                $this->error($model->getError());
            }else{//成功
                $this->success('学生添加成功！',U('index'));
            }
        }else{
            $this->display();
        }
    }
}