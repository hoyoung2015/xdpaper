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
    public function index($tag = '',$nickname = ''){
        $page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据
        $map = array(
            'tid'=>session('user_auth')['uid'],
        );
        Log::record('tag的值'.$tag,Log::DEBUG);
        empty($nickname) || $map['nickname'] = array('like', '%'.(string)$nickname.'%');
        empty($tag) || ($map['tag'] = array('like', '%,'.(string)$tag.',%'));

        $row = 10;

        $model = D('Admin/Student');
        $list = $model->where($map)->page($page,$row)->select();

        //去掉标签首尾的逗号
        foreach($list as &$pe){
            $pe['tag'] = substr($pe['tag'],1,count($pe['tag'])-2);
        }

        $count = $model->where($map)->count();
        // 分页
        if ($count > $row) {
            $page = new \Think\Page ( $count, $row );
            $page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
            $this->assign('_page',$page->show ());
        }

        $this->tags = $model->findGroup();
        $this->assign('list_data',$list);

        $this->display();
    }
    public function add(){
        $model = D('Admin/Student');
        if(IS_POST){
            $tid = session('user_auth')['uid'];

            $data = $model->update(array(
                'tid'=>$tid
            ));
            if($data===false){//失败
                $this->error($model->getError());
            }else{//成功
                $this->success('学生添加成功！',U('index'));
            }
        }else{
            $this->tags = implode(',',$model->findGroup());
            $this->display();
        }
    }
    public function edit($sid=''){
        $model = D('Admin/Student');
        if(IS_POST){
            $tid = session('user_auth')['uid'];
            $data = $model->update(array(),array(
                'tid'=>$tid
            ));
            if($data===false){//失败
                $this->error($model->getError());
            }else{//成功
                $this->success('学生信息修改成功！',U('index'));
            }
        }else{
            $this->info = $model->get(array(
                'id'=>$sid,
                'tid'=>session('user_auth')['uid']
            ));
            $this->tags = implode(',',$model->findGroup());
            $this->display('add');
        }
    }
    public function del($ids = null){

        /*$map = array(
            'tid'=>session('user_auth')['uid'],
            'id'=>$sid
        );

        $model = D('Admin/Student');
        if(D('Admin/Student')->del($map)){
            $this->success('学生删除成功！',U('index'));
        }else{
            $this->error($model->getError());
        }*/
        parent::common_del(M('Student'),array(
            'tid'=>session('user_auth')['uid']
        ),$ids);
    }
    public function chstatus($ids = null,$status = null){
        ! empty ( $ids ) || $ids = I ( 'id' );
        ! empty ( $ids ) || $ids = array_filter ( array_unique ( ( array ) I ( 'ids', 0 ) ) );
        ! empty ( $ids ) || $this->error ( '请选择要操作的数据!' );

        M('Student')->where(array(
            'id'=>array('in',$ids),
            'tid'=>session('user_auth')['uid']
        ))->save(array(
            'status'=>$status
        ));
        $this->success ( '操作成功' );
    }
}