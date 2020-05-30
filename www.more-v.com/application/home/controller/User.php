<?php

namespace app\home\controller;

use app\home\model\EventRegistration;
use app\home\model\SellMaterialModel;
use app\home\model\SellModel;
use app\home\model\UserModel;

class User extends Base
{


    function init()
    {

        //登录判断
        if (!getCookieS('user_id')) $this->redirect('index/login');

    }


    /**
     * 会员信息页面
     * Create by Peter
     * @return \think\response\View
     */
    function sign_up()
    {


        return view('user/sign_up');
    }

    function basic_info()
    {

        $re = UserModel::get(getCookieS('user_id'));


//        print_sql($re);

        $this->assign('user_info', $re);

        return view('user/basic_info');
    }

    function event_series()
    {


        return view('user/event_series');
    }

    function products_info()
    {

        $re=SellMaterialModel::all(['user_id'=>request()->user['id']],'sell');

//        print_sql($re);
//        exit();

        $this->assign('sell_data',$re);

        return view('user/products_info');
    }

    /**
     * 会员信息修改
     * Create by Peter
     */
    function user_update()
    {


        $user = new UserModel();

        $re = $user->allowField('company,name,tel,web')->save(input('post.'), ['id' => getCookieS('user_id')]);


        if ($re != false) {

            $this->redirect('user/sign_up');

        } else {

            $this->error($user->getError());
        }


    }


    /**
     * 密码修改
     * Create by Peter
     */
    function password_edit()
    {


        if (!captcha_check(input('code'))) $this->error('Verification code error！');


        $user = new UserModel();

        //判断旧密码
        $re = $user->where([
            'id' => getCookieS('user_id'),
            'password' => md5(input('orig_password'))

        ])->find();


        if ($re) {

            //修改密码
            $res = $user->validate('User.update')->allowField('password')->save(input(), ['id' => getCookieS('user_id')]);


            if ($res != false) $this->redirect('user/basic_info');


            $this->error($user->getError());

        } else {

            $this->error("Password verification failed");

        }


    }

    /**
     * 会员信息修改
     * Create by Peter
     */
    function info_update()
    {


        $user = new UserModel();


        $re = $user->allowField('sex,address,city,state,zip_code,country_id')
            ->save(input(), ['id' => getCookieS('user_id')]);


        if ($re !== false) {


            redirect_last();

        }


        $this->error($user->getError());


    }

    /**
     * 活动报名
     * Create by Peter
     */
    function event_join(){


//        dump(new EventRegistration());


        $event=new EventRegistration();

        $re=$event->allowField('activity_id,user_id')->validate('User.activity_add')->save([
            'activity_id'=>input('id'),
            'user_id'=>getCookieS('user_id'),
        ]);




        if($re){


//            echo "<script> alert('GG'); </script>";

            redirect_last();

        }else{

           $this->error($event->getError());

        }








    }


    /**
     *产品详情
     * Create by Peter
     */
    function product_detail(){


        $re=SellModel::get(input('id'));


        $rand=SellModel::getRandom();


        $this->assign('rand',$rand);

        $this->assign('sell_data',$re);

        return view('user/product_detail');
    }

    /**
     * 上传产品信息页面
     * Create by Peter
     */
    function pro_upload(){

        $re=SellModel::get(input('id'));

        $this->assign('sell_data',$re);

        return view('user/pro_upload');
    }


    /**
     * 用户上传素材资料
     * Create by Peter
     */
    function user_upload(){



        $db=new SellMaterialModel();


        $post=input('post.');

        $post['user_id']=getCookieS('user_id');

//        $post['']


        //防止更新
        unset($post['id']);

        $re=$db->allowField(true)->save($post);


        if($re) {

//            pushEmailTask();

            $this->redirect('sell/Selling_Leads');

        }


        $this->error($db->getError());


    }



}