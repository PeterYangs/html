<?php
namespace app\home\controller;
use app\home\model\Activity;
use app\home\model\AdminModel;
use app\home\model\BaomingModel;
use app\home\model\ContactUs;
use app\home\model\NewsModel;
use app\home\model\UserModel;
use Auth\Start\Auth;
use think\Controller;
class Index extends Base {

    /**
     * 首页
     * Create by Peter
     * @return \think\response\View
     */
    function index(){

        $ac=new Activity();

        $re=$ac->order('id','desc')->limit(8)->select();

        //随机一个活动
        $ran=$re[mt_rand(0,count($re)-1)];


        $this->assign('activity',$re);


        $this->assign('ran',$ran);

        return view('index/index');
    }


    /**
     * 联系我们提交
     * Create by Peter
     */
    function submit_msg(){

        $post=input();


        $c=new ContactUs();

        $re=$c->save($post);



        if($re){


            $this->success('success!');
        }

        $this->error('error!');






    }

    /**
     * 活动页面
     * Create by Peter
     */
    function events(){

        $ac=new Activity();
        $re=$ac->order('id','desc')->limit(6)->select();


        $this->assign('activity',$re);

        return view('index/events');
    }


    /**
     *
     * Create by Peter
     */

    function service(){

        return view("index/service");
    }


    function more_v(){

        return view('index/more_v');

    }

    /**
     *events_detail
     * Create by Peter
     */
    function events_detail(){

        return view('index/Events_detail');

    }


    function loveBox(){



        return view('index/loveBox');
    }


    function smartTool(){

        return view('index/smartTool');

    }


    function activity(){


        return view('index/activity');
    }


    function aboutUs(){


        return view('index/aboutUs');
    }


    /**
     * 新闻页面
     * Create by Peter
     * @return \think\response\View
     */
    function News(){

        $news=new NewsModel();


        $re=$news->order('id','desc')->paginate(6);

//        print_sql($re);

        $this->assign('news',$re);


        return view('index/News');
    }



    function login(){
//        remember_list_url();


        return view('index/login');

    }

    function register(){
        return view('index/register');
    }


    function errors(){

        return view('index/errors');
    }

    function news_one(){
        return view('index/news_one');
    }
    function news_two(){
        return view('index/news_two');
    }
    function news_three(){
        return view('index/news_three');
    }



    function contact(){
        return view('index/Contact');
    }





    function mailer_test(){
        getMailList();

    }


    function auth_test(){

        $auth=new Auth();


        $re=$auth->check('Index/index',4);


        dump($re);


    }


    function f(){

        $admin=new UserModel();

        $re=$admin->with('country')->paginate(10);


//        $admin->getTable();

//        echo $re->country->name;


        $res=getFieldForArray($re,[
            ['country.name'=>'country_name'],
            ['country.zh_name'=>'country_zh_name']
        ]);

        print_sql($res);


    }


    function baoming(){
        // 指定允许其他域名访问
        header('Access-Control-Allow-Origin:*');
         // 响应类型
        header('Access-Control-Allow-Methods:POST');
        // 响应头设置
        header('Access-Control-Allow-Headers:x-requested-with,content-type');

//        print_r($_POST);

//        print_r(input());

        $bao=new BaomingModel();


        $re=$bao->allowField(true)->save(input());


        if($re) exit(jsonCode(1,'success'));


        echo jsonCode(2,$bao->getError());

    }


}