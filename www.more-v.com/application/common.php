<?php
//错误级别
error_reporting(E_ALL & ~E_NOTICE);
define("KEYS", 'morev');
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Cookie;
// 应用公共文件
use PHPMailer\PHPMailer\PHPMailer;
//use PHPMailer\PHPMailer\Exception;

//date_default_timezone_set('PRC');

/**
 * 获取错误信息的error（cookie）,使用一次就删除
 * Create by Peter
 */
function getErrorMsg(){

    $error=Cookie::get('login_error');

    Cookie::delete('login_error');

    return $error;
}


/**
 * 设置加密cookie
 * Create by Peter
 * @param $key
 * @param $value
 * @param $ex
 */
function setCookieS($key, $value, $ex)
{


    $str = authcode($value, 'ENCODE', KEYS, 0);


//    setcookie($key, $str, $ex, '/');

    Cookie::set($key,$str,$ex);


}

/**
 *
 * 解密cookie
 * Create by Peter
 * @param $key
 * @return bool|string
 */
function getCookieS($key)
{

//    $c = $_COOKIE[$key];

    $c=Cookie::get($key);


    $str = authcode($c, 'DECODE', KEYS, 0);


    return $str;


}

/**
 * 加密字符串
 * Create by Peter
 * @param $value
 * @return bool|string
 */
function setDataS($value){

    $str=authcode($value,'ENCODE',KEYS);


    return $str;
}


function getDataS($value){

    $str=authcode($value,'DECODE',KEYS);

    return $str;
}



/**
 * Create by Peter
 * @param $string            加密内容
 * @param string $operation 加密还是解密   加密（ENCODE） 解密（DECODE）
 * @param string $key 秘钥，相当于appsecret
 * @param int $expiry 有效时间，0为永久，单位为秒
 * @return bool|string
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
{
    // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
    $ckey_length = 4;

    // 密匙
    $key = md5($key ? $key : $GLOBALS['discuz_auth_key']);

    // 密匙a会参与加解密
    $keya = md5(substr($key, 0, 16));
    // 密匙b会用来做数据完整性验证
    $keyb = md5(substr($key, 16, 16));
    // 密匙c用于变化生成的密文
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) :
        substr(md5(microtime()), -$ckey_length)) : '';
    // 参与运算的密匙
    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);
    // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，
//解密时会通过这个密匙验证数据完整性
    // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) :
        sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    // 产生密匙簿
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    // 核心加解密部分
    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        // 从密匙簿得出密匙进行异或，再转成字符
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if ($operation == 'DECODE') {
        // 验证数据有效性，请看未加密明文的格式
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) &&
            substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)
        ) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
        // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}


/**
 * 获取所有方法
 * Create by Peter
 * @param $module
 * @param $controller
 * @return array
 */
function get_all_action($module,$controller){

    $data=get_class_methods("app\\{$module}\\controller\\{$controller}");

    //过滤
    $fi=array("_initialize","__construct",'_empty');


//      $d=array();

    foreach ($data as $key=>$value){

        if(in_array($value,$fi)){

            unset($data[$key]);

//          }else {
//
//              $d[$key]=$value;
        }

    }


    return $data;


}

/**
 * 获取所有控制器，默认是后台模块
 * Create by Peter
 * @param string $module
 * @return array
 */
function get_all_controller($module='admin'){

    $data=glob("../application/{$module}/controller/*.php");

    //过滤
    $fir=array('Api','Base',"Login","Error","Common");

    $file=array();
    foreach ($data as $key=>$value){

        $file[$key]=basename($value,".php");

        //过滤
        if(in_array($file[$key],$fir)){

            unset($file[$key]);
        }



    }





    return $file;

}

/**
 * 记住列表页面的地址（一般在编辑页面使用）
 * Create by Peter
 */
function remember_list_url($url=''){
        if(!$url)$url=$_SERVER["HTTP_REFERER"];

        Cookie::set('list_url',$url,0);

}

/**
 * 跳转到列表页面
 * Create by Peter
 */
function redirect_list($url=""){
    //没有指向的话
    if(!$url){
        //找cookie里面上一次的跳转地址
        $url=Cookie::get('list_url');
        //cookie里面也没有的话，跳转到上一次的地址
        if(!$url){

            $url=$_SERVER['HTTP_REFERER'];
        }

    }

    echo "<script>location.href='".$url."'</script>";
    exit();

}


/**
 * 跳转上一页
 * Create by Peter
 */
function redirect_last(){

    $url=$_SERVER['HTTP_REFERER'];

    echo "<script>location.href='".$url."'</script>";
}



/**
 * 对当前用户的可见的菜单连接进行筛选
 * Create by Peter
 * @param $menu 完整列表,见配置文件数组
 * @param $auth 拥有的列表，授权类返回的数组
 * @return array
 */
function filter_menu($menu,$auth){

    $new_menu=array();
    foreach ($menu as $key=>$value){


        foreach ($value as $key1=>$value1){

            if(in_array(ucwords($value1),$auth)){

                $new_menu[$key]=[$key1=>$value1];
            }


        }


    }



    return $new_menu;



}


/**
 *打印数据库对象
 * Create by Peter
 * @param $collection 执行sql后的返回值
 * @return mixed      数组
 */
function print_sql($collection){

    if(is_array($collection))return print_r(collection($collection)->toArray());

    if(is_object($collection)) return print_r($collection->toArray());



    return print_r([]);

}

/**
 * 将sql对象转数组
 * Create by Peter
 * @param $collection
 * @return array
 */
function get_sql_array($collection){


    if(is_array($collection))return collection($collection)->toArray();

    if(is_object($collection)) return $collection->toArray();



    return [];


}

/**
 * Create by Peter
 * @param string $to 接收者
 * @param string $title 标题
 * @param string $content 内容
 * @return bool
 */
function mailer($to,$title,$content){

    $mail=new PHPMailer();

//    $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
    try {
        //Server settings
        $mail->SMTPDebug = 0;                                 //开启debug模式
        $mail->isSMTP();                                      //使用SMTP协议
        $mail->Host = 'smtp-mail.outlook.com';                // SMTP服务器地址
        $mail->Port=587;                                      //端口
        $mail->SMTPAuth = true;                               // 开启验证
        $mail->Username = 'morevss@outlook.com';                // SMTP username
        $mail->Password = 'mwsp2017';                           // 密码和邮箱密码一致
        $mail->Timeout=20;                                      //设置超时时间，单位为秒，默认3分钟

        $mail->FromName="yy";
        $mail->From="morevss@outlook.com";

        $mail->CharSet="utf-8";

//            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
//            $mail->Port = 587;                                    // TCP port to connect to

        //Recipients
//        $mail->setFrom('morevss@outlook.com', 'yy');        //发送者信息
        $mail->addAddress($to);     // Add a recipient
//            $mail->addAddress('ellen@example.com');               // Name is optional
//            $mail->addReplyTo('info@example.com', 'Information');
//            $mail->addCC('cc@example.com');
//            $mail->addBCC('bcc@example.com');

        $mail->Encoding = "base64";

        //Attachments
//            $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//        $mail->addAttachment('C:/index/thinkphp_5.0.11_with_extend/public/favicon.ico', 'new.ico');    // Optional name

        //Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $subject = "=?UTF-8?B?".base64_encode($title)."?=";
        $mail->Subject = $subject;
        $mail->Body    = $content;
//        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        return  $mail->send();
//        echo 'Message has been sent';
    } catch (Exception $e) {
//        echo 'Message could not be sent.';
//        echo 'Mailer Error: ' . $mail->ErrorInfo;

        file_put_contents('./uploads/mail_error.txt',$mail->ErrorInfo.PHP_EOL,FILE_APPEND);

    }


}


function sendEmailByAdmin($email,$password,$to,$title,$content,&$msg=''){


    $host=getEmailUrl($email)['smtp'];

//    $host='smtp-mail.outlook.com';
//
//
//    print_r($host);



    $mail=new PHPMailer();

//    $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
    try {
        //Server settings
        $mail->SMTPDebug = 0;                                 //开启debug模式
        $mail->isSMTP();                                      //使用SMTP协议
        $mail->Host = $host;                // SMTP服务器地址
        $mail->Port=587;                                      //端口
        $mail->SMTPAuth = true;                               // 开启验证
        $mail->Username = $email;                // SMTP username
        $mail->Password = $password;                           // 密码和邮箱密码一致
        $mail->Timeout=20;                                      //设置超时时间，单位为秒，默认3分钟

        $mail->FromName="yy";
        $mail->From=$email;

        $mail->CharSet="utf-8";

//            $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
//            $mail->Port = 587;                                    // TCP port to connect to

        //Recipients
//        $mail->setFrom('morevss@outlook.com', 'yy');        //发送者信息
        $mail->addAddress($to);     // Add a recipient
//            $mail->addAddress('ellen@example.com');               // Name is optional
//            $mail->addReplyTo('info@example.com', 'Information');
//            $mail->addCC('cc@example.com');
//            $mail->addBCC('bcc@example.com');

        $mail->Encoding = "base64";

        //Attachments
//            $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//        $mail->addAttachment('C:/index/thinkphp_5.0.11_with_extend/public/favicon.ico', 'new.ico');    // Optional name

        //Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $subject = "=?UTF-8?B?".base64_encode($title)."?=";
        $mail->Subject = $subject;
        $mail->Body    = $content;
//        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        return  $mail->send();
//        echo 'Message has been sent';
    } catch (\PHPMailer\PHPMailer\Exception $exception) {
//        echo 'Message could not be sent.';
//        echo 'Mailer Error: ' . $mail->ErrorInfo;

//        echo $mail->err

        $msg=$exception->getMessage();


        file_put_contents('./uploads/mail_error.txt',$mail->ErrorInfo.PHP_EOL,FILE_APPEND);

    }



}












/**
 * 对地址栏的where条件处理，例如返回结果为：
 * 'standard' =>                               key
 *    array (size=3)
 *      0 => string 's' (length=1)             表名
 *      1 => string 'eq' (length=2)            表达式，= > like 什么的
 *      2 => string 'yyyyyyyyy' (length=9)     值
 *      3 => string '3'                        元素id
 * Create by Peter
 * @return array
 */
function deal_url_where(){

    //获取url,所有参数
    $p=input('param.');


    $where=[];



    if($p){
        foreach ($p as $key=>$value){

            //将syh这个字符串转成",转后的整个字符串是标准的json字符串
            $value=str_replace('syh','"',$value);

            //转成数组
            $value=json_decode($value,true);


            $where[$key][]=$value[0];
            $where[$key][]=$value[1];
            $where[$key][]=$value[2];

        }



        return $where;

    }



    return [];

 }

/**
 * 对模型的where条件进行添加，例如:  $model->where('name','like','yy')->where('age','eq','21')
 * Create by Peter
 * @param \think\Model $model
 * @param array $where          例如：'standard' =>                                     key（字段名）
 *                                          array (size=3)
 *                                              0 => string 's' (length=1)             表名
 *                                              1 => string 'eq' (length=2)            表达式，=,>,like 什么的
 *                                              2 => string 'yyyyyyyyy' (length=9)     值
 */
 function deal_model_where( $model,array $where){


     //如果有筛选条件
     if($where){

         foreach ($where as $key=>$value){
             //如果值不存在，就跳过
             if(!$value[2]) continue;
                                                                        //这里手动加个模糊搜索加%
             $model->where($value[0].".".$key,$value[1],($value[1]=='like')?"%".$value[2]."%":$value[2]);

         }

     }


 }


/**
 * 自动联表并筛选
 * Create by Peter
 * @param \think\Model $model
 * @param array $where          where条件格式同上
 */
 function deal_model_where_with_join(think\Model $model,array $where){

        //获取主表名称
        $master_table=$model->getTable();
        //连接的表数组
        $join_arr=[];

        if($where){


            foreach ($where as $key=>$value){

                //如果值不存在，就跳过
                if(!$value[2]) continue;
                //如果条件是主表
                if($master_table==$value[0]){

                    //直接添加
                    $model->where($value[0].".".$key,$value[1],($value[1]=='like')?"%".$value[2]."%":$value[2]);

                }else{

                    //判断一下这个表是否已经被连接
                    if(!in_array($value[0],$join_arr)){
                        //拼接关联表函数名
                        $join_method='join_'.$value[0];
                        //判断这个模型中的关联函数是否存在
                        if(method_exists($model,$join_method)){
                            //执行
                            $model->$join_method();
                            //将这个关联加入到关联数组当中
                            $join_arr[]=$value[0];

                        }

//                        如果方法不存在，跳过这次循环




                    }



                    //判断一下是否已经关联
                    if(in_array($value[0],$join_arr)) $model->where($value[0].".".$key,$value[1],($value[1]=='like')?"%".$value[2]."%":$value[2]);




                }



            }




        }


 }

/**
 * 模型联表
 * Create by Peter
 * @param \think\Model $model  操作的模型
 * @param array $where         join条件，例：['goods.admin','']支持多个
 */
 function deal_join(think\Model $model,array $where){
     //获取主表名称
     $master_table=$model->getTable();
     //连接的表数组
     $join_arr=[];

     if($where){


         foreach ($where as $key=>$value){

             //如果值不存在，就跳过
             if(!$value) continue;


           $joins= explode('.',$value);

           foreach ($joins as $k=>$v){


//               $table=join("_",$v);

//           $join_arr[]=


               //如果条件是主表
               if($master_table==$v){

                   //直接添加
//                 $model->where($value[0].".".$key,$value[1],($value[1]=='like')?"%".$value[2]."%":$value[2]);

               }else{

                   //判断一下这个表是否已经被连接
                   if(!in_array($v,$join_arr)){
                       //拼接关联表函数名
                       $join_method='join_'.$v;
                       //判断这个模型中的关联函数是否存在
                       if(method_exists($model,$join_method)){
                           //执行
                           $model->$join_method();
                           //将这个关联加入到关联数组当中
                           $join_arr[]=$v;

                       }

//                        如果方法不存在，跳过这次循环




                   }



                   //判断一下是否已经关联
//                 if(in_array($value[0],$join_arr)) $model->where($value[0].".".$key,$value[1],($value[1]=='like')?"%".$value[2]."%":$value[2]);


               }


           }



         }



     }


 }






/**
 * 发布一个邮件任务
 * Create by Peter
 * @param $email
 * @param $title
 * @param $content
 */
 function pushEmailTask($email,$title,$content){

     //添加这个邮件信息
     $task = new app\home\model\EmailTaskModel();

     $task->save([
         'email' => $email,
         'title' => $title,
         'content' => $content,
     ]);
     //获取添加主键id
     $task_id = $task->id;


     //推送这个邮件任务，让队列去执行
//     $this->actionWithHelloJob($task_id);
     addTask($task_id);



 }


/**
 * 添加一个队列任务
 * Create by Peter
 * @param $task_id
 * @return bool
 */
 function addTask($task_id){

     // 1.当前任务将由哪个类来负责处理。
     //   当轮到该任务时，系统将生成一个该类的实例，并调用其 fire 方法
     $jobHandlerClassName = 'app\home\job\Hello';
     // 2.当前任务归属的队列名称，如果为新队列，会自动创建
     $jobQueueName = "helloJobQueue";
     // 3.当前任务所需的业务数据 . 不能为 resource 类型，其他类型最终将转化为json形式的字符串
     //   ( jobData 为对象时，需要在先在此处手动序列化，否则只存储其public属性的键值对)
     $jobData = ['task_id' => $task_id];
     // 4.将该任务推送到消息队列，等待对应的消费者去执行
     $isPushed = think\Queue::push($jobHandlerClassName, $jobData, $jobQueueName);
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
 *  api返回数据
 * Create by Peter
 * @param $code  int    状态
 * @param $msg string  信息
 * @param array $data  数据
 * @return string
 */
function jsonCode($code,$msg,$data=[]){


    return json_encode([
        'code'=>$code,
        'msg'=>$msg,
        'data'=>$data
    ]);
}


/**
 * 访问限制
 * Create by Peter
 * @param $name    string  限制名称
 * @param $expired  int 限制时间（频率，单位秒）
 * @param $msg   string    信息
 * @return bool
 */
function access_restrictions($name,$expired,$msg="你的访问过于频繁"){

    $acc=new \app\home\model\AccessRestrictionsModel();


    $re=$acc->where([
        'name'=>['eq',$name],
        'expired'=>['>',time()]

    ])->order('id','desc')->find();


    if($re){

        if(request()->isAjax()){

           echo jsonCode(4,$msg,'');

        }else{

            echo $msg;

        }

        exit();


//        return false;
    }else{

        $acc->save([
            'name'=>$name,
            'expired'=>time()+$expired,
            'ip'=>request()->ip(),

        ]);


        return true;

    }



}

/**
 * 系统邮件列表
 * Create by Peter
 * @param int $page
 * @param int $limit
 * @return array
 */
function getMailList($page=0,$limit=6){
    $mailbox = new PhpImap\Mailbox('{imap-mail.outlook.com:993/ssl}INBOX','morevss@outlook.com','mwsp2017');




    $mailsIds = $mailbox->searchMailbox('ALL');


//    foreach ($mailsIds as $v){
//
//
//        $re=$mailbox->getMailHeader($v);
//
//        print_r($re->fromName."<br/>");
//
//    }
//
//
//    exit();


    $mailsIds=array_reverse($mailsIds);



    $mail_arr=[];



    if(!$page) $page=1;

    $offset=($page-1)*$limit;

    for ($i=1;$i<=$limit;$i++){

        $mail=$mailbox->getMail($mailsIds[$offset],false);



        if(!$mail->textHtml) continue;

        $offset++;

        $mail_arr[]=$mail;
    }


    return [
        'item'=>$mail_arr,
        'count'=>count($mailsIds)
        ];



}


/**
 * 获取系统邮件信息
 * Create by Peter
 * @param $id
 * @return \PhpImap\IncomingMail
 */
function getMailById($id){

    $mailbox = new PhpImap\Mailbox('{imap-mail.outlook.com:993/ssl}INBOX','morevss@outlook.com','mwsp2017');


//    $mailsIds = $mailbox->searchMailbox('ALL');

    return  $mailbox->getMail($id);




}

/**
 * 获取邮箱
 * Create by Peter
 * @param $email
 * @param $password
 * @param $msg
 * @return bool|\PhpImap\Mailbox
 */
function getMailBox($email,$password,&$msg=''){

//    $imap_url=[
//        'outlook.com'=>'imap-mail.outlook.com:993/ssl',
//        'qq.com'=>'imap.qq.com:993/ssl',
//        'more-v.net'=>'imap.exmail.qq.com:993/ssl',
////        '163.com'=>'imap.163.com:993/ssl'
//
//    ];
//
//
//    $url=substr($email,strpos($email,'@')+1);
//
//
//    $path=$imap_url[$url];

    $path=getEmailUrl($email)['imap'];

    if(!$path){

        $msg='此imap地址未添加，请联系帅气的后端程序员';

        return false;
    }



    $mailbox=new PhpImap\Mailbox('{'.$path.'}INBOX',$email,$password);



    return $mailbox;


}


function getEmailUrl($email){
    $imap_url=[
        'outlook.com'=>[
            'imap'=>'imap-mail.outlook.com:993/ssl',
            'smtp'=>'smtp-mail.outlook.com'
        ],
        'qq.com'=>[
            'imap'=>'imap.qq.com:993/ssl',
            'smtp'=>'smtp.qq.com'
        ],
        'more-v.net'=>[
            'imap'=>'imap.exmail.qq.com:993/ssl',
            'smtp'=>'smtp.exmail.qq.com'
        ]
    ];


    $url=substr($email,strpos($email,'@')+1);



    return $imap_url[$url];

}




/**
 * 检测是否支持imap
 * Create by Peter
 * @param $email
 * @param $password
 * @param $msg
 * @return bool|\PhpImap\Mailbox
 */
function connectEmailBox($email,$password,&$msg){

    $mailbox=getMailBox($email,$password,$msg);


    if(!$mailbox)  return false;





    try {
        $re = $mailbox->getImapStream();
    }catch (\PhpImap\Exception $e){


        $msg=$e->getMessage();


        return false;
    }



    return $mailbox;


}


/**
 * 获取管理员邮件列表，用于邮件管理
 * Create by Peter
 * @param $email
 * @param $password
 * @param $page
 * @param $limit
 * @return array
 */
function getAdminEmailList($email,$password,$page,$limit){

    $mailbox=getMailBox($email,$password);

    $mailsIds = $mailbox->searchMailbox('ALL');



    $mailsIds=array_reverse($mailsIds);



    $mail_arr=[];



    if(!$page) $page=1;

    $offset=($page-1)*$limit;

    for ($i=1;$i<=$limit;$i++){

        $mail=$mailbox->getMailHeader($mailsIds[$offset]);



//        if(!$mail->fromName) continue;

        $offset++;

        $mail_arr[]=$mail;
    }



    return [
        'item'=>$mail_arr,
        'count'=>count($mailsIds)
    ];


}

/**
 * 获取邮箱详情
 * Create by Peter
 * @param $email
 * @param $password
 * @param $id
 * @return \PhpImap\IncomingMail
 */
function getAdminEmailDetail($email,$password,$id){

    $mailbox=getMailBox($email,$password);


    return  $mailbox->getMail($id);
}








/**
 * 判断值是否存在（用于判断更新还是添加）
 * Create by Peter
 * @param $v
 * @return bool
 */
function valueIsSet($v){


    if($v)  return true;

    return false;

}

/**
 * 获取关联模型数据(用于搜索页面)
 * Create by Peter
 * @param $obj       当前模型对象
 * @param $param     关联模型数据，例：country.name，就是把当前模型关联到country再获取name
 * @return mixed
 */
function getWithData($obj,$param){


    $arr=explode('.',$param);



    foreach ($arr as $v){


        $obj=$obj->$v;


    }


    return $obj;
}

/**
 * 获取地址链接，除了name    用于搜索页面的
 * Create by Peter
 * @return string
 */
function getUrlButName(){

    $url="__SELF__";

    $arr=request()->except('name');


    foreach ($arr as $k=>$v){

        $url.="/".$k."/".$v;

    }


    return $url;

}

/**
 * 获取所有分类（递归,枚举列表分类也是通过这个）
 * Create by Peter
 * @param int $pid
 * @param string $f
 * @param string $model
 * @return array
 */
function getAllCategory($pid=0,$f='',$model='\app\home\model\CategoryModel'){


    static $list=[];

//    $category=new \app\home\model\CategoryModel();

    //单例
    $category=\think\Loader::model($model);
                                        //这个是分类的默认值，是一个空字符串
    $re=$category->where('parent_id',$pid)->where('id','neq',0)->select();




    if($re){



        foreach ($re as $v){


            $list[$v->id]['string']=(($v->lv==1)?'|':'').$f.''.(($v->lv==1)?'':'├');//树状图符号
            $list[$v->id]['name']=$v->name;//分类名称
            $list[$v->id]['id']=$v->id;//id
            $list[$v->id]['lv']=$v->lv;//等级
            $list[$v->id]['pid']=$v->parent_id;//父级id



                                            //两个中文占位符
            getAllCategory($v->id,$f."|"."&#8195;&#8195;",$model);



        }

    }



    return $list;


}

/**
 * 添加关联模型数据
 * Create by Peter
 * @param $re         模型查找后的对象
 * @param $field      字段
 */
function getDataAttrForArray(&$re,$field){


    foreach ($re as $key=>$val){


        $re[$key][$field]=$re->$field;


    }

}



function addModelData(&$re,$field){

    $arr=explode(',',$field);


    foreach ($arr as $key=>$value){

        if(is_array($re)){


            foreach ($re as $k1=>$v1){



            }

        }

//        $value


    }






}



/**
 * 获取枚举列表的数据
 * Create by Peter
 * @param $parent_id
 * @return false|static[]
 * @throws \think\exception\DbException
 */
function getEnumerateById($parent_id){

   $re= \app\home\model\EnumerateModel::all(['parent_id'=>$parent_id]);



   return $re;

}


/**
 * 将多个独立数组
 * Create by Peter
 * @param $item
 * @return array
 */
function getOneItemArr($item){


    if(!$item) return [];

    $items=[];

    foreach ($item as $key=>$value){

//        $items[][$key]

        foreach ($value as $k=>$v){

            $items[$k][$key]=$v;

        }



    }



    return $items;


}

/**
 * 添加公共的数据,用于saveAll
 * Create by Peter
 * @param $items
 * @param $k
 * @param $v
 * @return array
 */
function addItemCommonData($items,$k,$v){

    if(!is_array($items)) return $items;



    foreach ($items as $key=>$value){

        $items[$key][$k]=$v;


    }


    return $items;
}


/**
 * 添加公共的数据,一次性添加多个,用于saveAll
 * Create by Peter
 * @param $items
 * @param $arr
 * @return array
 *
 *
 *
 *例：
 *
 * $data=[['to_id'=>1],['to_id'=>2]];
 *
 *$data=addItemCommonArrayData($data,[
 *    'admin_id'=>$request->admin->id,
 *    'title'=>input('title'),
 *    'content' => input('content'),
 *    'type'=>2//普通信息
 *]);
 */
function addItemCommonArrayData($items,$arr){

    if(!is_array($items)) return $items;

    $temp=$items;

    foreach ($items as $key=>$value){


        foreach ($arr as $k=>$v){


            $temp[$key][$k]=$v;

        }


    }


    return $temp;



}

/**
 * 转数组带key值
 * Create by Peter
 * @param $delimiter
 * @param $string
 * @param $key
 * @return array
 */
function explodeWithKey($delimiter,$string,$key){


    $arr=explode($delimiter,$string);

    $temp=[];

    foreach ($arr as $k=>$v){

        $temp[]=[$key=>$v];

    }

    return $temp;

}



/**
 * 将关联数组的数据绑定到最外层数组（这个给find,get这种用）
 * Create by Peter
 * @param $modelReturnObj   模型查找后返回的对象
 * @param $fieldList        查找的数据和别名 例：[['country.name'=>'country_name'],['country.zh_name'=>'country_zh_name']]
 * @return mixed
 * @throws Exception
 */
function getFieldForOne($modelReturnObj,$fieldList){


//        $table= $modelReturnObj->getTable();

        if(is_array($modelReturnObj)) throw new Exception('不能是数组，数组用getFieldForArray函数');


        foreach ($fieldList as $key=>$value){


            foreach ($value as $k=>$v){

                try {
                    $tem=getObj($modelReturnObj, $k);

                    //设置默认值
                    if(!$tem) $tem="";

                    $modelReturnObj->$v = $tem;
                }catch (\Exception $e){

                    $modelReturnObj->$v="";

                }

            }

        }

        return $modelReturnObj;

}

/**
 * 将关联数组的数据绑定到最外层数组(这个给select，分页用)
 * Create by Peter
 * @param $modelReturnObj    参数同上
 * @param $fieldList
 * @return array
 * @throws Exception
 */
function getFieldForArray($modelReturnObj,$fieldList){


    $arr=[];
    foreach ($modelReturnObj as $key=>$value){


//        print_r($value);

        $obj=getFieldForOne($value,$fieldList);

        $arr[]=$obj;

    }


    return $arr;
}


/**
 * 获取对象里面某个值
 * Create by Peter
 * @param $obj
 * @param $field
 * @return mixed
 */
function getObj($obj,$field){


    $arr=explode('.',$field);


    foreach ($arr as $key=>$value){


       $obj= $obj->$value;
    }



    return $obj;
}



