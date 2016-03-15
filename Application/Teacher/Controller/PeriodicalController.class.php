<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/21
 * Time: 13:19
 */
namespace Teacher\Controller;
use \Think\Controller;
use Think\Log;
class PeriodicalController extends TeacherController{
    public function index($tag = '',$name = ''){
        $page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据
        $map = array(
            'tid'=>session('user_auth')['uid']
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
        $model = D('Admin/Periodical');
        if(IS_POST){
            $data = $model->update(array(
                'tid'=>session('user_auth')['uid']
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
                'tid'=>session('user_auth')['uid']
            ));
            if($data===false){//失败
                $this->error($model->getError());
            }else{//成功
                $this->success('期刊信息修改成功！',U('index'));
            }
        }else{
            $this->info = $this->info = $model->get($id);
            $this->tags = implode(',',$model->findGroup());
            $this->display('add');
        }
    }
    public function del($ids = null){
        Log::record('删除的ids'.json_encode($ids),Log::DEBUG);

        if($ids==null){
            $ids = array(I ( 'id' ));
        }

        $delete_ids = array();
        //删除依赖检查
        foreach($ids as $id){
            $count = M('PaperSubmit')->where("periodical_id=$id")->count();

            if($count<1){//没有被引用

                array_push($delete_ids,$id);
            }
        }
        Log::record('待删除的id'.json_encode($delete_ids),Log::DEBUG);

        if(empty($delete_ids)){

            $this->error('期刊被引用');
        }
        Log::record('$count==>>>>>>>>>>>>>>>>>>.'.'--------------',Log::DEBUG);
        $res = M('Periodical')->where(array(
            'id'=>array('IN',$delete_ids),
            'tid'=>session('user_auth')['uid']
        ))->delete();

        if($res){
            $this->success('操作完成！',U('index'));
        }else{
            $this->error('系统错误');
        }

    }
}