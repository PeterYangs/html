<?php
namespace app\admin\controller;

use app\home\model\BaomingModel;
use app\home\model\ClientModel;
use app\home\model\CollectModel;
use app\home\model\SellerModel;

class Seller extends Base{

    /**
     * 供应商列表
     * Create by Peter
     * @return \think\response\View
     * @throws \think\exception\DbException
     */
        function seller_list(){


            $seller=new SellerModel();

            $re=$seller->order('id','desc')
                ->with('country,admin,collect,lvs,collect.admin')
                ->paginate(10);


            $this->assign('seller',$re);



//            print_sql($re);

//            dump();

//            foreach ($re as $v){
//
////                print_r($v->toArray());
//
//            }
//
//
//            exit();

//
//            $c=new CollectModel();
//
//
//            $res=$c->with('admin')->select();
//
//
////            print_sql($res);
//            dump($re);
//            print_sql($re);
//
//            exit();

            return view('seller/seller_list');
        }


    /**
     * 供应商编辑页面
     * Create by Peter
     * @return \think\response\View
     * @throws \think\exception\DbException
     */
        function seller_edit(){


            if(input('id')){


                $this->assign('seller',SellerModel::get(input('id')));



            }


            remember_list_url();

            return view('seller/seller_edit');
        }


    /**
     * 供应商的修改
     * Create by Peter
     */
        function seller_update(){



            $id=input('id');

            $seller=new SellerModel();

                         //根据id判断是更新还是添加
            $re=$seller->isUpdate(valueIsSet($id))->allowField(true)->save(input());


            redirect(redirect_list());


        }


    /**
     * 删除
     * Create by Peter
     */
        function seller_delete(){


            SellerModel::destroy(input('id'));


            redirect(redirect_list());

        }



        function client_list(){


            $client=new ClientModel();


//            deal_join($client,['admin']);
//
//            $client->field('client.*,admin.username');


            $re=$client->order('id','desc')->with('admin')->paginate(10);



            $this->assign('client',$re);


            return view('seller/client_list');
        }


    /**
     *
     * Create by Peter
     * @return \think\response\View
     * @throws \Exception
     * @throws \think\exception\DbException
     */
        function client_edit(){

            $id=input('id');


            if($id){

                $data=ClientModel::get($id,'admin');

                $data=getFieldForOne($data,[
                    ['admin.username'=>'admin_username'],
//                    ['address_id'=>'address_list']
                ]);

                $this->assign('client',$data);

            }



            remember_list_url();

            return view('seller/client_edit');
        }



        function client_update(){


//            print_r(input());
//
//            exit();

            $id=input('id');


            $client=new ClientModel();


            $client->allowField(true)->isUpdate(valueIsSet($id))->save(input());


            redirect_list();

        }


    /**
     * 超体报名管理
     * Create by Peter
     * @return \think\response\View
     * @throws \think\exception\DbException
     */
        function baoming_list(){

            $b=new BaomingModel();

            $re=$b->order('id','desc')->paginate(10);


            $page=$re->render();

            $this->assign('b',json_encode($re));

            $this->assign('page',$page);


            return view('seller/baoming_list');
        }


}