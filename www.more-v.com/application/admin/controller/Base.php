<?php
namespace app\admin\controller;
//use Think\Auth;
use app\home\model\AdminModel;
use Auth\Start\Auth;
use think\Controller;
use think\Request;

class Base extends controller{

    /**
     * 后台初始化方法
     * Create by Peter
     * @throws \think\exception\DbException
     */
    function _initialize(){

        //登录判断
        if(!getCookieS('admin_id')){


            $this->redirect('login/login');

            exit();
        }


//        require_once '../Auth5.php';
//        $auth=new Auth();

        $auth=new Auth();

//        print_r($auth);


        //权限的判断
        $re=$auth->check(request()->controller()."/".request()->action(),getCookieS('admin_id'));


//        print_r($re);

        if(!$re){

            if(request()->isAjax()){

                exit(jsonCode(2,'你没有这个权限！'));

            }

            $this->error('你没有这个权限');
            exit();
        }



        //获取菜单完整列表
        $menu=config('menu');



        //如果不是超级管理员
        if(is_array($re)){


            $menu=filter_menu($menu,$re);


        }

//        $show_name='';

        $admin_info=[];

        if(getCookieS('admin_id')) Request::instance()->bind('admin',$admin_info=AdminModel::get(getCookieS('admin_id')));


        $show_name=valueIsSet($admin_info['nickname'])?$admin_info['nickname']:$admin_info['username'];

        Request::instance()->bind('show_name',$show_name);



        //传递数据到整个后台
        $this->assign('menu',$menu);



    }

    public function _empty($name)
    {

        echo "没有这个Action";

    }


}