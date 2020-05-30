<?php
namespace app\home\controller;
use app\home\model\NewsModel;
use app\home\model\UserModel;
use think\Controller;
use think\Loader;
use think\Request;

class Base extends Controller{


    /**
     * 初始化方法
     * Create by Peter
     */
    function _initialize(){


        $this->init();

        if(getCookieS('user_id')) Request::instance()->bind('user',$this->getUserInfo());


    }






    public function _empty($name)
    {


        return view('index/errors');



    }

    /**
     * 初始化方法
     * Create by Peter
     */
    function init(){


        $this->hit();
    }


    /**
     * 获取登录用户信息
     * Create by Peter
     */
    private function getUserInfo(){

        return  UserModel::get(getCookieS('user_id'));

    }

    /**
     * 点击记录
     * Create by Peter
     */
    private function hit(){


        $request = request();

        $user_hit=Loader::model('UserHitModel');


        $user_hit->save([
            'user_id'=>getCookieS('user_id'),
            'module'=>$request->module(),
            'controller'=>$request->controller(),
            'action'=>$request->action(),
            'param'=>json_encode($request->param()),
            'url'=>$request->url(true),
            'ip'=>$request->ip()

        ]);




    }



}
