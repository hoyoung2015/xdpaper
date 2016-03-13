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
    public function index($stag = '',$s = '',$ptag = '',$pe = '',$ps = '',$row = 10){

        Log::record('论文搜索条件$stag>>'.$stag,Log::DEBUG);
        Log::record('论文搜索条件$s>>'.$s,Log::DEBUG);
        Log::record('论文搜索条件$ptag>>'.$ptag,Log::DEBUG);
        Log::record('论文搜索条件$pe>>'.$pe,Log::DEBUG);
        Log::record('论文搜索条件$ps>>'.$ps,Log::DEBUG);
        Log::record('论文搜索条件$row>>'.$row,Log::DEBUG);

        $model = M();

        $sql = <<<sqlBody
SELECT <field> FROM xdpaper.paper as p
inner join student as s on p.sid=s.id
left join periodical as pe on pe.id=p.periodical_id
where 1=1
sqlBody;
        if(!empty($stag)){//学生标签
            $sql = $sql." and s.tag like '%,$stag,%'";
        }
        if(!empty($s)){//学生id
            $sql = $sql." and s.id = $s";
        }
        if(!empty($ptag)){//期刊标签
            $sql = $sql." and pe.tag like '%,$ptag,%'";
        }
        if(!empty($pe)){//期刊id
            $sql = $sql." and pe.id = $pe";
        }
        if(!empty($ps)){//论文状态code
            $sql = $sql." and p.paper_status = $ps";
        }
        //查count
        $count = $model->query(str_replace('<field>','count(*) as total',$sql))[0]['total'];
        $count = intval($count);


        // 分页
        if ($count > $row) {

            $page = new \Think\Page ( $count, $row, array(
                'stag'=>$stag,
                's'=>$s,
                'ptag'=>$ptag,
                'pe'=>$pe
            ));
            $page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
            $this->assign('_page',$page->show ());
            $limit = " limit ".$page->firstRow.",$row";
        }
        $order = " order by p.create_time desc";

        $sql = $sql.$order;

        $limit = " limit $row";
//
        $field = "p.*,s.nickname,s.id as sid,pe.name as pename,pe.web_site";
        $list = $model->query(str_replace('<field>',$field,$sql).$limit);
//        Log::record('===='.vsprintf($sql,array($field)),Log::DEBUG);
        for($i=0;$i<count($list);$i++){
            $list[$i]['paper_status_name'] = get_status_name($list[$i]['paper_status']);
        }

        $this->assign('list_data',$list);
        //查询学生标签
        $this->studentTags = D('Admin/Student')->findGroup();
        //查询学生
        $this->students = M('Student')->where(array(
            'tag'=>array('like',"%$stag%")
        ))->field('id,nickname')->select();
        //查询期刊标签
        $this->periodicalTags = D('Admin/Periodical')->findGroup();
        //查询期刊
        $this->periodicals = M('Periodical')->where(array(
            'tag'=>array('like',"%$ptag%")
        ))->field('id,name')->select();
        //查询投稿状态集合
        $paperStatus = array();
        foreach(C('PSSC') as $key=>$value){
            array_push($paperStatus,array(
               'code'=>$value,
                'key'=>$key,
                'name'=>get_status_name($value)
            ));
        }
        $this->paperStatus = $paperStatus;
        /**
         * 以下是搜索条件
         */
        $this->stag = $stag;//学生标签
        $this->s = $s;//学生id
        $this->ptag = $ptag;//期刊标签
        $this->pe = $pe;//期刊id
        $this->ps = $ps;//投稿状态

        $this->display();
    }

}