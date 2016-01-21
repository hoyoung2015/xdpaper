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
        $map = array();
        Log::record('tag的值'.$tag,Log::DEBUG);
        empty($name) || $map['name'] = array('like', '%'.(string)$name.'%');
        empty($tag) || ($map['tag'] = array('like', '%'.(string)$tag.'%'));

        $row = 10;

        $model = D('Admin/Periodical');
        $list = $model->where($map)->page($page,$row)->select();

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
}