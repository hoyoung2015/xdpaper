<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/18
 * Time: 13:41
 */
namespace Admin\Controller;
use Think\Log;
class TeacherController extends AdminController{
    public function index(){
        $nickname       =   I('nickname');
        //map是查询条件
        $map['status']  =   array('egt',0);
        $map['nickname']    =   array('like', '%'.(string)$nickname.'%');
        $list   = $this->lists('Teacher', $map);
        int_to_string($list);
        $this->assign('_list', $list);


        $this->meta_title = '导师信息';
        $this->display();
    }

    /**
     * 添加导师
     * @param string $username
     * @param string $password
     * @param string $repassword
     * @param string $email
     */
    public function add($username = '', $password = '', $repassword = '', $email = ''){
        if(IS_POST){

            /* 检测密码 */
            if($password != $repassword){
                $this->error('密码和重复密码不一致！');
            }
            $model = D('Teacher');
            $rs = $model->update();
            if($rs === false){ //注册失败，显示错误信息
                $this->error($model->getError());
            } else { //注册成功
                $this->success('导师添加成功！',U('index'));
            }
        } else {
            $this->meta_title = '新增导师';
            $this->display();
        }
    }
    /**
     * 会员状态修改
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function changeStatus($method=null){
        $id = array_unique((array)I('id',0));
        $id = is_array($id) ? implode(',',$id) : $id;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $map['id'] =   array('in',$id);
        switch ( strtolower($method) ){
            case 'forbidteacher':
                $this->forbid('Teacher', $map );
                break;
            case 'resumeteacher':
                $this->resume('Teacher', $map );
                break;
            case 'deleteteacher':
                $this->delete('Teacher', $map );
                break;
            default:
                $this->error('参数非法');
        }
    }
}