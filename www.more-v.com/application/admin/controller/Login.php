<?php
namespace app\admin\controller;
use think\Controller;
use think\Cookie;
use think\Db;
use think\Request;

class Login extends Controller{



    function login(){

//        var_dump( md5("1111"));

        return view('login/login');
    }


    /**
     * 验证账号密码
     * Create by Peter
     */
    function check_login(){



        $post=input('post.');

        $username=$post['username'];

        //访问限制，每个账号三秒一次
        access_restrictions('admin_login_'.$username,3);


        $password=$post['password'];

        $re=Db::table('admin')->where('username=:username and password=:password')->bind(array(
            'username'=>$username,
            'password'=>md5($password)
        ))->find();


        if($re){

            setCookieS("admin_id",$re['id'],0);
            setCookieS('admin_name',$re['username'],0);


            $this->redirect("index/index");


        }else{

            Cookie::set('login_error',1,0);

            $this->redirect('login/login');

        }


    }

    /**
     * 登出
     * Create by Peter
     */
    function logout(){
        Cookie::clear('usa_');

        $this->redirect('login/login');
    }


}
