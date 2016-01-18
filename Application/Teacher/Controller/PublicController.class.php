<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/18
 * Time: 16:26
 */
namespace Teacher\Controller;
use Think\Controller;
class PublicController extends Controller{
    public function login(){
        if(IS_POST){

        }else{
            $this->display();
        }
    }
}