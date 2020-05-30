<?php
namespace app\admin\controller;
use app\home\model\EmailModel;
use think\Request;

class Email extends Base{


    /**
     * 收件箱列表(只是布局，并没有邮件的数据)
     * Create by Peter
     * @return \think\response\View
     */
    function inbox_list(Request $request){


        return view('email/inbox_list');
    }


    /**
     * ajax分页获取邮件数据，把数据传回列表
     * Create by Peter
     * @param Request $request
     */
    function emailAjax(Request $request){


        $limit=6;

        $re=getAdminEmailList($request->admin->email,$request->admin->email_password,input('page'),$limit);

        $page = \think\paginator\driver\Bootstrap::make($re['item'], $limit, empty(input('page'))?1:input('page'), $re['count'], false,
            [
                'var_page' => 'page',
                'path'     => 'javascript:AjaxPage([PAGE]);',
                'query'    => [],
                'fragment' => '',
            ] );

        $this->assign('email',$re['item']);


        $this->assign('page',$page);





       echo $this->fetch('email/emailAjax');

    }




    /**
     * 邮件详情（页面，并没有数据）
     * Create by Peter
     * @param Request $request
     * @return \think\response\View
     */
    function email_edit(Request $request){



//        $re=getAdminEmailDetail($request->admin->email,$request->admin->email_password,input('id'));
//
//
//        $this->assign('email',$re);


        return view('email/email_edit');
    }


    /**
     * 邮件详情
     * Create by Peter
     * @param Request $request
     */
    function email_content(Request $request){

        $re=getAdminEmailDetail($request->admin->email,$request->admin->email_password,input('id'));


      echo  $re->textHtml;
    }



    /**
     * 写邮件页面
     * Create by Peter
     * @return \think\response\View
     */
    function write_email(){





        return view('email/write_email');
    }

    /**
     * 邮件发送
     * Create by Peter
     * @param Request $request
     */
    function email_send(Request $request){



        $re=sendEmailByAdmin($request->admin->email,$request->admin->email_password,input('to'),input('title'),input('content'),$msg);


        if(!$re) {

            exit(jsonCode(2,$msg));
        }


        $email=new EmailModel();

        $email->save([
            'admin_id'=>$request->admin->id,
            'title'=>input('title'),
            'from'=>$request->admin->email,
            'to'=>input('to'),
            'content'=>input('content'),
        ]);


        exit(jsonCode(1,'发送成功'));




    }








    /**
     * 已发送列表
     * Create by Peter
     * @param Request $request
     * @return \think\response\View
     * @throws \think\exception\DbException
     */
    function email_sent(Request $request){


            $email=new EmailModel();


            $re=$email->order('id','desc')->where('admin_id',$request->admin->id)->paginate(10);


            $this->assign('email',$re);


            return view('email/email_sent');
        }


    /**
     * 已发送详情页面
     * Create by Peter
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
        function sent_edit(Request $request){

            $email=new EmailModel();

            $re=$email->where('admin_id',$request->admin->id)->where('id',input('id'))->find();


            $this->assign('mail',$re);

            return view('email/sent_edit');


        }


}

