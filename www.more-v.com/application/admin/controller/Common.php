<?php
namespace app\admin\controller;
//use app\home\model\SellerModel;
use app\home\model\AddressModel;
use app\home\model\AdminModel;
use app\home\model\CategoryModel;
use app\home\model\CollectModel;
use app\home\model\DateModel;
use app\home\model\EmailModel;
use app\home\model\FeedbackModel;
use app\home\model\GoodsModel;
use app\home\model\MessageModel;
use app\home\model\SampleOutDetailModel;
use think\Controller;
use think\Db;
use think\Exception;
use think\Loader;
use think\Request;

//本控制器绕过权限管理
class Common extends Controller{


    /**
     * 初始化方法
     * Create by Peter
     * @throws \think\exception\DbException
     */
    function _initialize(){

        Request::instance()->bind('admin',$admin_info=AdminModel::get(getCookieS('admin_id')));

//        AdminModel::

    }


    /**
     * 获取action列表
     * Create by Peter
     */
    function getAction(){
        $controller=input('post.controller');

        if(!$controller)return false;


        $action=get_all_action("admin",$controller);


        print_r(json_encode($action));




    }


    /**
     * 判断权限是否存在
     * Create by Peter
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function is_rule_find(){
        $name=input('name');

        $re=Db::table('auth_rule')->where('name=:name')->bind(array('name'=>$name))->find();

        if($re){

            echo 1;

        }else{

            echo 2;
        }

//            echo $name;


    }

//
//    /**
//     * 一个页面多个分页
//     * Create by Peter
//     * @return \think\response\View
//     */
//    function test(){
//
//        $page=(!empty(input('page'))?input('page'):0);
//        $p=(!empty(input('p'))?input('p'):0);
//
//        $admin=Db::table('admin')->paginate(2,false,array(
//            'query'=>array('p'=>input('p'))//保留的get值
//
//        ));
//        $rule=Db::table('auth_rule')->paginate(1,false,array(
//            'var_page'=>'p',
//            'query'=>array('page'=>input('page'))
//
//        ));
//
//
//
//        $this->assign('admin',$admin);
//        $this->assign('rule',$rule);
//
//
//        return view('common/test');
//
//    }

    /**
     * 文件上传（ajax）
     * Create by Peter
     */
    function upload_file(){


//        if($_FILES['error']==1)  exit(jsonCode(2,'上传超过最大限制'));


        foreach ($_FILES as $k){

            if($k['error']==1) exit(jsonCode(2,'上传超过最大限制'));

        }


        $f=request()->file();
//
//
//        print_r($_FILES);
//
//        exit();

        if($f){

            foreach ($f as $key=>$value){

                $info=$value->validate(['size'=>2097152,'ext'=>'jpg,png,gif,html,txt'])->move(ROOT_PATH . 'public/'  . 'uploads');


//                print_r($info);
//
//                exit();

                if($info) {

//                    echo $info->getExtension() . "<br/>";
                    // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
//                    echo $info->getSaveName() . "<br/>";
                    // 输出 42a79759f284b767dfcb2a0197904287.jpg
//                    echo $info->getFilename();

                    echo json_encode(array(
                        'code'=>1,
                        'file'=>$info->getSaveName(),
                        'name'=>$info->getFilename()
                    ));


                }else{

//                   echo $value->getError();

                   echo json_encode(array(
                       'code'=>2,
                       'msg'=>$value->getError()
                   ));

                }
            }

        }



    }


    /**
     * 系统内存使用情况(百分比)
     * Create by Peter
     */
    function systemMemoryUse(){

//       echo PHP_OS;
//
//       exit();

       if(PHP_OS!="Linux")   exit(0);

        $fp = popen('top -b -n 2 | grep -E "^(Cpu|Mem|Tasks)"',"r");//获取某一时刻系统cpu和内存使用情况
        $rs = "";
        while(!feof($fp)){
            $rs .= fread($fp,1024);
        }
        pclose($fp);
        $sys_info = explode("\n",$rs);
        $tast_info = explode(",",$sys_info[3]);//进程 数组
        $cpu_info = explode(",",$sys_info[4]);  //CPU占有量  数组
        $mem_info = explode(",",$sys_info[5]); //内存占有量 数组


        $mem_total = trim(trim($mem_info[0],'Mem: '),'k total');
        $mem_used = trim($mem_info[1],'k used');
        $mem_usage = (int)round(100*intval($mem_used)/intval($mem_total),2);  //百分比


        //正在运行的进程数
        $tast_running=trim(trim($tast_info[1],'running'));
        //CPU占有量
        $cpu_usage=(trim(trim($cpu_info[0],'Cpu(s):'),'%us')*100)."%";//百分比


        echo  jsonCode(1,'success',array(
            'mem_usage'=>$mem_usage,
            'tast_running'=>$tast_running,
            'cpu_usage'=>$cpu_usage
        ));



    }


    /**
     * 搜索页面
     * Create by Peter
     * @return \think\response\View
     */
    function search(){

//        $seller=new SellerModel();

        //实例化模型
        $seller=Loader::model("app\home\model\\".input('model'));

        //输入框的值
        $name=input('name');

        //搜索的字段
        $condition=input('condition');
        //排序
        $re=$seller->order($seller->getTable().'.id','desc');

        //where字段
        $whereField=input('whereField');

        /*
         * where条件
         * 1.等于
         * 2.不等于
         * 3.like
         * 4.>
         * 5.<
         */
        $where=input('where');

        //where值
        $search=input('search');


        if($name){

            $re->where($condition,'like','%'.$name.'%');

        }

//        $arr=[
////            'sample_detail.goods'=>[
////
////
////            ]
//            'sample_detail.goods',
//            'sample_detail.sample'
//
//
//        ];
        $join=explode(',',input('join'));


        deal_join($seller,$join);



        $re->field($seller->getTable().".*");


        /*
         * where条件
         * 1.等于
         * 2.不等于
         * 3.like
         */
        if($whereField){


            switch ($where){
                case 1:
                    $re->where($whereField,$search);
                    break;
                case 2:
                    $re->where($whereField,'neq',$search);
                    break;
                case 3:
                    $re->where($whereField,'like','%'.$search.'%');
                    break;
                case 4:
                    $re->where($whereField,'>',$search);
                    break;
                case 5:
                    $re->where($whereField,'<',$search);
                    break;



            }


        }


//        $re->

//        $re->where('sa');

//        $str="id,sampleDetail.goods.name,sampleDetail.arrive_time,sampleDetail.expired_time,sampleDetail.warehouses.name,now";



//        $re->with('sampleDetail.goods')->hasWhere('sampleDetail',['name'=>'123']);


//        $re->join('');


//        $re->has('sampleDetail.goods',['id'=>10]);

//        $goods=new GoodsModel();

//        $re->hasWheres('sampleDetail.goods',['name','蔓越莓']);

//        GoodsModel::hasWhere();

//        CategoryModel::hasWhere('');
//
//        $re=GoodsModel::hasWhere('category',['id'=>10])->select();


//        $re->belongsTo('SampleDetailModel','sample_detail_id','id')->has();


////
///
///
////
//        print_sql($re);
//
//        dump($re);
//
//        exit();



//
        $this->assign('field',explode(',',htmlspecialchars_decode(input('field'))));

        $this->assign('title',explode(',',input('title')));


        $re=$re->paginate(10);




        $this->assign('data',$re);


        return view('layout/search');
    }

    /**
     * 分类页面
     * Create by Peter
     */
    function category(){


        $re=getAllCategory();


        $this->assign('cate',$re);

      echo  $this->fetch('layout/category');
//        return view('layout/category');
    }

    /**
     * 获取日程数据
     * Create by Peter
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function getDateEvent(){

        $date=new DateModel();

        /*
         * 三种访问类型
         * 1.属于私人的，where条件为，where('admin_id',getCookieS('admin_id'))
         * 2.属于可见人员，where条件为，where('privacy',3)->where("FIND_IN_SET(".getCookieS('admin_id').",display_id)")
         *   display_id的储存格式为 1,2,3，所以用find_in_set函数
         * 3.属于公开的，这个没什么好说的
         *
         *
         */

        $re=$date->where('admin_id',getCookieS('admin_id'))
            ->whereOr(function ($query){

                $query->where('privacy',3)->where("FIND_IN_SET(".getCookieS('admin_id').",display_id)");
            })->whereOr('privacy',1)
            ->with(['admin'=>function($query){

                $query->field('id,username,nickname');//关联出发布人的信息，这里也不要泄露密码
            }])
            ->with('seller,seller.lvs')
            ->with('types')
//            ->with('seller.lvs.name')
            ->whereTime('start','month')->select();







        if($re){

            getDataAttrForArray($re,'color');


            $admin=new AdminModel();

            foreach ($re as $key=> $v){


              if($v->display_id){                                         //注意这里，不要把密码泄露了
                  $res=$admin->where('id','in',$v->display_id)->field('id,username,nickname')->select();

                  if($res){

                      $arr=get_sql_array($res);

                      $re[$key]['date']=$arr;

                  }

              }

            }







        }






        if(!$re)  exit(json_encode(['data'=>[]]));


        return json_encode([
            'me'=>getCookieS('admin_id'),
            'data'=>$re

        ]);

    }

    /**
     * 日程数据的添加修改
     * Create by Peter
     */
    function date_update(){

        $date=new DateModel();

        $data=input();

        $data['admin_id']=getCookieS('admin_id');

        $id=input('id');


        try {
            //更新
            if ($id) {

                $isMe=$date->where('id',$id)->find();

                if($isMe->admin_id!=getCookieS('admin_id')) exit(jsonCode(2,'这不是你发布的任务，你无法修改！'));


                $re = $date->allowField(true)->save($data, ['id' => $id]);

                if ($re !== false) return exit(jsonCode(1, 'success'));

                exit(jsonCode(2, $date->getError()));


            } else {


                $re = $date->allowField(true)->save($data);

                if ($re) exit(jsonCode(1, 'success'));

                exit(jsonCode(2, $date->getError()));


            }

        }catch (\Exception $e){

            exit(exit(jsonCode(2, $e->getMessage())));

        }


    }

    /**
     *
     * Create by Peter
     * @throws \think\exception\DbException
     */
    function date_delete(){

        //日程任务id
        $id=(int)(input('id'));

        $res=DateModel::get(['id'=>$id]);

        if($res){

            if($res->admin_id==getCookieS('admin_id')){

                $re=DateModel::destroy($id);

                if($re) exit(jsonCode(1,'删除成功'));

                exit(jsonCode(2,'删除失败'));

            }

            exit(jsonCode(3,'这不是你发布的任务，你无法删除！'));
        }

        exit(jsonCode(3,'此任务不存在！'));




    }

    /**
     * 主页面的任务状态更改和消息添加
     * Create by Peter
     * @param Request $request
     */
    function emailSendIndex(Request $request){


//        print_r(input());
//
//        exit();


//        //发送邮件
//        $re=sendEmailByAdmin($request->admin->email,$request->admin->email_password,input('to'),input('title'),input('content'),$msg);
//
//
//        if(!$re) {
//
//            exit(jsonCode(2,$msg));
//        }
//
//
//        $email=new EmailModel();
//
//        //添加到已发送列表
//        $email->save([
//            'admin_id'=>$request->admin->id,
//            'title'=>input('title'),
//            'from'=>$request->admin->email,
//            'to'=>input('to'),
//            'content'=>input('content'),
//        ]);


        Db::startTrans();


        try {
            $dateModel = new DateModel();

            //更改任务状态为已完成
            $dateModel->save(['status' => 1], ['id' => input('date_id'),'status'=>2]);



            $re=$dateModel->find(['id'=>input('date_id')]);

//            print_sql($re);

            //把任务完成的消息添加到消息表
            $message=new MessageModel();

            //公共消息
            if($re->privacy==1){

                $message->save([
                    'admin_id'=>$request->admin->id,
                    'title'=>input('title'),
                    'content' => input('content'),
                    'type'=>1//公共消息
                ]);



                    //指定消息
            }elseif ($re->privacy==3&&$re->display_id){



                $data=explodeWithKey(',',$re->display_id,'to_id');




                $data=addItemCommonArrayData($data,[
                    'admin_id'=>$request->admin->id,
                    'title'=>input('title'),
                    'content' => input('content'),
                    'type'=>2//普通信息
                ]);


                $message->saveAll($data);


            }



            $collect = new CollectModel();

            //添加到汇总信息
            $collect->save([
                'admin_id' => $request->admin->id,
                'title' => input('title'),
                'content' => input('content'),
                'seller_id' => input('seller_id')
            ]);





            Db::commit();

            echo jsonCode(1, '发送成功', ['id' => input('date_id')]);

        }catch (\Exception $e){

            Db::rollback();

        }

    }

    /**
     * 获取物流信息，阿里的接口
     * Create by Peter
     */
    function get_express_delivery(){


//        echo 111;
//
//        exit();

        $post=input();

        $host = "http://wdexpress.market.alicloudapi.com";
        $path = "/gxali";
        $method = "GET";
        $appcode = "cc3e21f962654bfd8ef747e436d006ad";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);

        $n=$post['n'];
        $t=$post['t'];

        if(!$n||!$t) exit();

        $querys = "n={$n}&t={$t}";
        $bodys = "";
        $url = $host . $path . "?" . $querys;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($curl, CURLOPT_HEADER, true);
        if (1 == strpos("$".$host, "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }

        $re=curl_exec($curl);

//        echo json_encode(explode("\r\n",$re));


        print_r($re);

//        var_dump(curl_exec($curl));


    }


    /**
     * 获取出库的商品详情
     * Create by Peter
     * @throws \Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function get_storage_out_detail(){

        $id=input('id');


        $model=new SampleOutDetailModel();


        $re=$model->where('sample_out_id',$id)->with('goods,sampleDetail,sampleDetail.warehouses')->select();

        //绑定到主数组
        $re=getFieldForArray($re,[
            ['goods.name'=>'goods_name'],
            ['sample_detail.arrive_time'=>'arrive_time'],
            ['sample_detail.expired_time'=>'expired_time'],
            ['sample_detail.warehouses.name'=>'warehouses_name'],
            ['sample_detail.is_port'=>'is_port'],

        ]);


//        print_sql($re);

        echo jsonCode(1,'',$re);



    }


    /**
     * 获取消息
     * Create by Peter
     * @param Request $request
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function getMessage(Request $request){

        $message=new MessageModel();

        $now=time();

//        $message->where('tip_time','LT',$now);

        $re=$message->where(function ($query) use ($request,$now){
            $query->where('type',2)->where('to_id',$request->admin->id)->where('status',2);//普通消息未读
        })
            ->where('tip_time','LT',$now)
            ->whereOr(function ($query) use ($request,$now){
                $query->where('type',1)
//                    ->where('admin_id','neq',$request->admin->id)
                    ->where("!FIND_IN_SET(".$request->admin->id.",read_list_id)");//系统消息，并且本账户不在已读列表
                })
//            ->where('status',2)
            ->order('id','desc')
            ->select();


        $re=get_sql_array($re);

        $arr=[];

        foreach ($re as $key=>$value){

            if(!$value['tip_time']){
                $arr[]=$value;
                continue;
            }

            if($now>=$value['tip_time']) $arr[]=$value;

        }

//        print_sql($re);

//        if($re)

        echo jsonCode(1,'success',$arr);
//        echo jsonCode(1,'success',['sql'=>$message->getLastSql()]);



    }

    /**
     * 更改消息状态为已读
     * Create by Peter
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function changeMessageStatus(Request $request){

        $id=input('id');

        $message=new MessageModel();

        $re=$message->where('id',$id)->find();
        $list="";
        $status=2;
        //公共消息
        if($re->type==1){


            if(!$re->read_list_id) {
                $list=$request->admin->id;
            }else{

                $list.=",".$request->admin->id;
            }



//            $message->save('');
        //指定消息
        }elseif ($re->type==2){


            $status=1;


        }
//        $message=new MessageModel();

        $res=$message->isUpdate(true)->save([
            'read_list_id'=>$list,
            'status'=>$status
        ],['id'=>$id]);


        if($res!==false)  exit(jsonCode(1,'success',$re));


        echo    jsonCode(2,'success',$message->getError());





    }

    /**
     * 添加修改地址
     * Create by Peter
     */
    function update_address(){


//        print_r(input());

        $post=input();

        $id=$post['id'];

        $address=new AddressModel();


        if($id){

            $re=$address->allowField(true)->save($post,['id'=>$post['id']]);

        }else{

            $re=$address->allowField(true)->save($post);

        }


        if($re!==false) exit(jsonCode(1,'success',$address));


        exit(jsonCode(2,$address->getError()));




    }

    /**
     * 设置反馈信息
     * Create by Peter
     * @param Request $request
     */
    function set_feedback(Request $request){


//        print_r(input());

        $post=input();

        $feedback=new FeedbackModel();


        $feedback->save([
            'goods_id'=>$post['goods_id'],
            'content'=>$post['content'],
            'admin_id'=>$request->admin->id,
            'client_id'=>$post['client_id']
        ]);


        $sample=new SampleOutDetailModel();
        //更改成已反馈
        $sample->save(['is_send'=>1],['id'=>$post['id']]);


        echo jsonCode(1,'success');

    }

    /**
     * 获取反馈信息
     * Create by Peter
     * @throws \Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function get_feedback(){

        $f=new FeedbackModel();


        $re=$f->where('goods_id',input('id'))->with('client')->select();


        $re=getFieldForArray($re,[
            ['client.name'=>'client_name']
        ]);



        echo jsonCode(1,'success',$re);


    }



}
