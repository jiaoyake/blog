<?php
namespace app\admin\controller;

use think\Controller;
use think\facade\Session;

class Common extends Controller
{
    public function initialize(){
        if (!session('username')) {
            $this->error('请先登录！',url('Index/Index/index'));
        }
    }

    //检查没有登录的
    public function Logined(){
        if (!Session::get('id')){
            $this->error('你还没有登录，请登录。','Index\Index/index');
        }
    }

}
