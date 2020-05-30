<?php
namespace app\admin\controller;
use app\home\model\ContactUs;
class Message extends Base {


    /**
     * 联系我们列表
     * Create by Peter
     */
    function message_list(){
        //联系我们模型
        $c=new ContactUs();

        $re=$c->order('id','desc')->paginate(10);



        $this->assign('message',$re);


        return view('message/message_list');

//        print_sql($re);


    }



}
