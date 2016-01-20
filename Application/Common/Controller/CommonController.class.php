<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/20
 * Time: 15:26
 */
namespace Common\Controller;
use \Think\Controller;

class CommonController extends Controller{
    public function common_del($model = null,$map = array(), $ids = null) {
        ! empty ( $ids ) || $ids = I ( 'id' );
        ! empty ( $ids ) || $ids = array_filter ( array_unique ( ( array ) I ( 'ids', 0 ) ) );
        ! empty ( $ids ) || $this->error ( '请选择要操作的数据!' );

        $map ['id'] = array ('in',$ids);
        if ($model->where ( $map )->delete ()) {
            // 清空缓存
            method_exists ( $model, 'clear' ) && $model->clear ( $ids, 'del' );

            $this->success ( '删除成功' );
        } else {
            $this->error ( '删除失败！' );
        }
    }

}