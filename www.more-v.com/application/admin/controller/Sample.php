<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/19 0019
 * Time: 10:46
 */

namespace app\admin\controller;
//use think\Controller;

use app\home\model\MessageModel;
use app\home\model\SampleDetailModel;
use app\home\model\SampleModel;
use app\home\model\SampleOutDetailModel;
use app\home\model\SampleOutModel;
use app\home\model\StockModel;
use think\Db;
use think\Request;

class Sample extends Base
{
    /**
     * 库存列表
     * Create by Peter
     * @return \think\response\View
     * @throws \think\exception\DbException
     */
    function storage_list(){


        $stock=new StockModel();

        $re=$stock->with(['sampleDetail'=>['goods','warehouses','sample.admin']])
            ->order('id','desc')->paginate(10);


        $this->assign('stock',$re);


        return view('sample/storage_list');
    }


    /**
     * 入库编辑
     * Create by Peter
     * @return \think\response\View
     * @throws \think\exception\DbException
     */
    function storage_edit(){




        $this->assign('ck',json_encode(getEnumerateById(10)));


        return view('sample/storage_edit');
    }


    /**
     * 商品入库
     * Create by Peter
     */
    function storage_update(){

        $post=input();

        $items=getOneItemArr($post['items']);


        //入库记录表
        $sample=new SampleModel();



            Db::startTrans();

            try {
                //添加操作人员和费用
                $sample->save([
                    'admin_id' => request()->admin->id,
                    'spend' => $post['spend']
                ]);




                foreach ($items as $key=>$value){

                    //写入 人库存详情
                    $re=$sample->sampleDetail()->save($value);

                    //写入库存表
                    $stock=new StockModel();
                    //添加操作
                    $stock->isUpdate(false)->save([
                        'now'=>$value['num'],
                        'sample_detail_id'=>$re->id,
                    ]);

                }





                Db::commit();
                exit(jsonCode(1, '入库成功！'));

            }catch (\Exception $e){

                Db::rollback();
                exit(jsonCode(2,$e->getMessage()));

            }





    }

    /**
     *入库记录
     * Create by Peter
     * @return \think\response\View
     * @throws \think\exception\DbException
     */
    function warehousing_record(){


        $sample=new SampleModel();


        $re=$sample->with('admin')->with(['sampleDetail'=>['goods','warehouses']])->paginate(10);



        $this->assign('sample',$re);

        return view('sample/warehousing_record');
    }


    /**
     * 样品出库页面
     * Create by Peter
     * @return \think\response\View
     */
    function storage_out(){




        return view('sample/storage_out');
    }


    /**
     * 出库申请
     * Create by Peter
     */
    function storage_out_update(){

//        print_r(input());
//        exit();

        $sample_out=new SampleOutModel();

        $sample_out_detail=new SampleOutDetailModel();

        $stock=new StockModel();

        $post=input();

        Db::startTrans();


        try{


        //转成单一数组
        $arr=getOneItemArr($post['items']);

        //判断库存是否充足
        foreach ($arr as $key=>$value){


          $res=$stock->where('id',$value['stock_id'])->find();


          if(!$res) exit(jsonCode(2,'入库信息不存在'));

          if($res->now-$value['num']<0) exit(jsonCode(2,'商品:'.$res->sampleDetail->goods->name.'(批次:'.$res->sampleDetail->arrive_time.')库存不足，请注意查看！'));


        }



        //记录在出库表
        $sample_out->save([
            'client_id'=>input('client_id'),
            'admin_id'=>request()->admin->id,
            'contact_name'=>$post['contact_name'],//联系人
            'p'=>$post['p'],
            'c'=>$post['c'],
            'a'=>$post['a'],
            'detail'=>$post['detail'],
            'mobile'=>$post['mobile']
        ]);

        //数组添加出库表id
        $arr=addItemCommonData($arr,'sample_out_id',$sample_out->id);

        //存进出库详情
        $sample_out_detail->saveAll($arr);


        Db::commit();

        echo jsonCode(1,'成功');


        }catch (\Exception $e){

            echo jsonCode(2,$e->getMessage());

            Db::rollback();

        }


    }

    /**
     * 出库记录列表
     * Create by Peter
     * @return \think\response\View
     * @throws \Exception
     * @throws \think\exception\DbException
     */
    function storage_out_list(){




        $sample_out=new SampleOutModel();


        $id=input('id');

        if($id) $sample_out->where('id',$id);


//        $sample_out->where('id',5);
//        $re=$sample_out->with('')->order('id','desc')->paginate(3);


        $re=$sample_out->with('sampleOutDetail,client,client.admin,send_type')->with(['sampleOutDetail'=>['goods','sampleDetail.warehouses']])->with(['admin'=>function($query){
//            $query->field('create_time');


        }])->order('id','desc')->paginate(3);





        $page=$re->render();

        $re=getFieldForArray($re,[
            ['admin.username'=>'admin_username'],
            ['client.name'=>'client_name'],
            ['client.admin.username'=>'client_admin_username']
        ]);


//        $re=$sample_out->with('client,client.admin')->with(['admin'=>function($query){}])->order('id','desc')->paginate(3);


//        $re=load_relation($re,'sampleOutDetail.sampleDetail.warehouses');

//        print_sql($list);
////
//        exit();


        $this->assign('sample',$re);

        $this->assign('page',$page);


        return view('sample/storage_out_list');
    }


    /**
     * 更改出库状态
     * Create by Peter
     */
    function changeStatus(){

        $id=input('id');

        $sample_out=new SampleOutModel();


        $re=$sample_out->save(['status'=>1],['id'=>$id,'status'=>2]);



        if($re) exit(jsonCode(1,'修改成功'));

        exit(jsonCode(2,$sample_out->getError()));




//        echo jsonCode(1,'123',input());





    }

    /**
     * 出库填表
     * Create by Peter
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function sample_form(){


        $id=input('id');

        $sample_out=new SampleOutModel();

        $re=$sample_out->where('id',$id)->with('sampleOutDetail,client,client.admin')->with(['sampleOutDetail'=>['goods','sample_detail','sample_detail.warehouses']])->find();


        $e=getEnumerateById(13);


        $this->assign('e',json_encode($e));


//        print_sql($re);


        $this->assign('sample',$re);



        return view('sample/sample_form');
    }

    /**
     * 出库
     * Create by Peter
     */
    function out(Request $request){

        $post=input();

        $id=$post['id'];

        $sample_out=new SampleOutModel();

//        $sample_out_detail=new SampleOutDetailModel();


        $stock=new StockModel();

        //转成单一数组
        $arr=getOneItemArr($post['items']);


        Db::startTrans();

        try {

            $goods_data="";//出库商品信息,用于消息提示

            //判断库存是否充足
            foreach ($arr as $key => $value) {


                $res = $stock->where('id', $value['stock_id'])->find();


                if (!$res) exit(jsonCode(2, '入库信息不存在'));

                if ($res->now - $value['num'] < 0) exit(jsonCode(2, '商品:' . $res->sampleDetail->goods->name . '(批次:' . $res->sampleDetail->arrive_time . ')库存不足，请注意查看！'));


                $goods_data.='商品:' . $res->sampleDetail->goods->name . '(批次:' . $res->sampleDetail->arrive_time . ",数量：".$value['num']. ')'."\n";

            }

            //减库存
            foreach ($arr as $key => $value) {

                $stock = new StockModel();
                $stock->where('id', $value['stock_id'])->where('now', '>', 0)->setDec('now', $value['num']);


            }

            //存运货信息
            $ress=$sample_out->save([
                'send_type_id' => $post['send_type_id'],
                'order_no' => $post['order_no'],
//                'contact_name' => $post['contact_name'],
//                'mobile' => $post['mobile'],
                'status' => 3,//更改出库状态
                'money'=>$post['money'],
                'out_time'=>time()
            ], ['id' => $id,'status'=>1]);


            if(!$ress) throw new \Exception("请勿重复提交出库！");



            $message=new MessageModel();

            //出库提醒
            $message->save([
                'admin_id'=>$request->admin->id,
                'type'=>1,
                'title'=>'出库成功',
                'content'=>$goods_data,
            ]);

            $message=new MessageModel();


            $message->save([
                'admin_id'=>$request->admin->id,
                'type'=>1,
                'title'=>'样品反馈',
                'content'=>'样品反馈提醒<a href="'.$request->root()."/".$request->module().'/sample/storage_out_list/id/'.$id.'" target="_blank">详情</a>',
                'tip_time'=>strtotime('+1 week')
            ]);





            echo jsonCode(1, '132');

            Db::commit();

        }catch (\Exception $e){

            Db::rollback();

            echo jsonCode(2,$e->getMessage());

        }

    }



}