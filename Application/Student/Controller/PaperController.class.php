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
class PaperController extends StudentController{
    public function index($name = ''){
        $page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据
        $map = array(
//            'tid'=>session('user_auth')['uid']
        );
        empty($name) || $map['name'] = array('like', '%'.(string)$name.'%');

        $row = 10;

        $model = D('Admin/Paper');
        $list = $model->where($map)->page($page,$row)->select();

        $count = $model->where($map)->count();
        // 分页
        if ($count > $row) {
            $page = new \Think\Page ( $count, $row );
            $page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
            $this->assign('_page',$page->show ());
        }

        $this->assign('list_data',$list);
        $this->assign('row',$row);

        $this->display();
    }
    public function add(){
        $model = D('Admin/Paper');
        if(IS_POST){
            $data = $model->update(array(
                'sid'=>session('user_auth')['uid']
            ));
            if($data===false){//失败
                $this->error($model->getError());
            }else{//成功
                $this->success('论文添加成功！',U('index'));
            }
        }else{
            $this->display();
        }
    }
    public function del($id = null){
        $model = D('Admin/Paper');
        if ($model->delPaper($id,session('user_auth')['uid'])) {
            $this->success ( '删除成功' );
        } else {
            $this->error ( '删除失败！' );
        }
    }
    public function edit($id=''){
        $model = D('Admin/Paper');
        if(IS_POST){
            $data = $model->update(array(),array(
                'sid'=>session('user_auth')['uid']//传入学生id，防止跨域修改
            ));
            if($data===false){//失败
                $this->error($model->getError());
            }else{//成功
                $this->success('期刊信息修改成功！',U('index'));
            }
        }else{
            $this->info = $model->where(array(
                'id'=>$id,
                'sid'=>session('user_auth')['uid']
            ))->find();
            $this->display('add');
        }
    }
    public function submitCtrl($id = null){

        $this->display();
    }
    public function newSubmit(){
        if(IS_POST){

        }else{
            //查询期刊
            $this->periodical = M('Periodical')->select();

            $this->display();
        }
    }
}