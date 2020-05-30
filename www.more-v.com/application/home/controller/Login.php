<?php

namespace app\home\controller;


use app\home\model\CodeModel;
use app\home\model\UserModel;
use think\Controller;
use think\Cookie;
use think\Loader;
use think\Queue;

class Login extends Controller
{


    function login()
    {


        return view('login/login');
    }


    /**
     * 注册检查
     * Create by Peter
     */
    function register_check()
    {


        $post = input();

        $code = $post['code'];


        if (!captcha_check($code)) $this->error('Verification code error！');

        $password = $this->create_password();


        $post['password'] = $password;

        $user = new UserModel();

        $re = $user->validate('User.register')->allowField('company,name,email,web,password')->save($post);


        if ($re) {

            //添加邮件队列任务（异步）
            pushEmailTask($post['email'],'Auto Reply: Successful Registration',$this->email($password,$post['name']));


            $this->success('Please get your password through email', 'index/login', '', 5);

        } else {


            $this->error($user->getError());
        }


    }


    /**
     * 登录检查
     * Create by Peter
     */
    function login_check()
    {

        $user = new UserModel();


        $email = input('email');

        $password = input('password');

        $re = $user->where('email', $email)
            ->where('password', md5($password))
            ->find();


        if ($re) {

            $this->login_cookie($re);

            $this->redirect('index/index');
        } else {

            $this->error('wrong user name or password');
        }


    }

    /**
     * 设置登录成功后的cookie
     * Create by Peter
     * @param $data 用户信息
     */
    private function login_cookie($data)
    {


//        setCookieS($key,$value,$ex);

        setCookieS('user_id', $data['id'], time()+3600*24*7);
        setCookieS('user_name', $data['name'], time()+3600*24*7);


    }


    /**
     * 生成一个随机码
     * Create by Peter
     * @param int $length
     * @param bool $isUpper  是否支持大小写
     * @return string
     */
    private function create_password($length = 6,$isUpper = true)
    {

        $str = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890";

        $password = "";

        for ($i = 0; $i < $length; $i++) {

            $s = substr($str, mt_rand(0, strlen($str) - 1), 1);

            $password .= $s;

        }




        return ($isUpper)?$password:strtolower($password);

    }


    /**
     * 一个使用了队列的 action
     */
    private function actionWithHelloJob($task_id)
    {

        // 1.当前任务将由哪个类来负责处理。
        //   当轮到该任务时，系统将生成一个该类的实例，并调用其 fire 方法
        $jobHandlerClassName = 'app\home\job\Hello';
        // 2.当前任务归属的队列名称，如果为新队列，会自动创建
        $jobQueueName = "helloJobQueue";
        // 3.当前任务所需的业务数据 . 不能为 resource 类型，其他类型最终将转化为json形式的字符串
        //   ( jobData 为对象时，需要在先在此处手动序列化，否则只存储其public属性的键值对)
        $jobData = ['task_id' => $task_id];
        // 4.将该任务推送到消息队列，等待对应的消费者去执行
        $isPushed = Queue::push($jobHandlerClassName, $jobData, $jobQueueName);
        // database 驱动时，返回值为 1|false  ;   redis 驱动时，返回值为 随机字符串|false
        if ($isPushed !== false) {

            return true;
//            echo date('Y-m-d H:i:s') . " a new Hello Job is Pushed to the MQ"."<br>";
        } else {

            return false;
//            echo 'Oops, something went wrong.';
        }
    }

    /**
     * 发送注册密码的模板
     * Create by Peter
     * @param $password
     * @param $username
     * @return mixed
     */
     private function email($password,$username){


        $this->assign('password',$password);

        $this->assign('username',$username);

        return $this->fetch('login/email');
    }


    /**
     * 发送验证码的模板
     * Create by Peter
     * @param $code
     * @param $email
     * @return mixed
     */
    private function code($code,$email){

        $this->assign('code',$code);

        $this->assign('email',$email);


        return $this->fetch('login/code');

    }


    /**
     * 忘记密码修改
     * Create by Peter
     */
    function forget_password(){


        $code=input('code');

        $db=new CodeModel();


        $re=$db->where([
            'email'=>['eq',input('email')],
            'expired'=>['>',time()],

        ])->order('id','desc')->find();

        //如果验证码存在并且和输入的一致且未过期
        if($re&&trim($re['code'])==trim($code)){

            $user=new UserModel();

            $res=$user->validate('User.forget_password')->allowField('password')->save(input(),['email'=>input('email')]);


            if($res!==false){

                echo jsonCode(1,'success','');

            }else{

                echo jsonCode(3,$user->getError(),'');
            }




        }else{


        echo  jsonCode(2,'验证错误或已过期','');
        }





    }

    /**
     * 获取验证码
     * Create by Peter
     */
    function get_code(){

        $email=input('email');


        if(!$this->hasEmail($email)) exit(jsonCode(2,'邮箱不存在',''));

        //访问限制，2s一次
        access_restrictions('forget_password_'.$email,2);



        //生成一个验证码先
        $code=$this->create_password(4,false);



        $code_model=new CodeModel();

        //写入验证码表
        $re=$code_model->save([
            'code'=>$code,
            'email'=>$email,
            'expired'=>time()+60*5

        ]);
        //推送给队列
        pushEmailTask($email,'Auto Reply: Forgot your password?',$this->code($code,$email));



        if ($re){


           echo jsonCode(1,'success','');
        }else{

          echo  jsonCode(2,'fail','');

        }





    }


    /**
     * 邮箱是否存在
     * Create by Peter
     * @param $email
     * @return boolean
     */
    protected function hasEmail($email){

        $user=Loader::model('app\home\model\UserModel');


        $re=$user->where('email',$email)->find();


        if($re)  return true;


        return false;



    }

    /**
     *
     * Create by Peter
     */
    function logout(){

        Cookie::delete('user_id');
        Cookie::delete('user_name');


        redirect_last();


    }

}
