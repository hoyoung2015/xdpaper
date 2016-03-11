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

class StudentPaperController extends TeacherController{
    public function index($tag = '',$nickname = ''){
        $model = M();
        $sql = <<<sql
SELECT p.*,s.nickname,s.id as sid,pe.name as pename,pe.web_site FROM xdpaper.paper as p
inner join student as s on p.sid=s.id
left join periodical as pe on pe.id=p.periodical_id
sql;

        $list = $model->query($sql);

        for($i=0;$i<count($list);$i++){
            $list[$i]['paper_status_name'] = get_status_name($list[$i]['paper_status']);
        }

        $this->assign('list_data',$list);
        $this->display();
    }

}