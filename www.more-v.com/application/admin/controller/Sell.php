<?php
namespace app\admin\controller;

use app\home\model\CountryModel;
use app\home\model\SellModel;

class Sell extends Base{


    /**
     * 需求列表
     * Create by Peter
     * @return \think\response\View
     */
    function sell_list(){


        $sell=new SellModel();

//        $re=$sell->order('id','desc')->paginate(10);



        $re=$sell->getSellList();

        $this->assign('sell',$re);

        return view('sell/sell_list');
    }


    /**
     * 需求编辑页面
     * Create by Peter
     * @return \think\response\View
     */
    function sell_edit(){


        $id=input('id');



        if ($id){

            $re=SellModel::get($id);


            $this->assign('sell',$re);

        }
        //获取国家列表
        $re=CountryModel::all();


        $this->assign('country',$re);



        return view('sell/sell_edit');
    }


    /**
     *
     * 需求修改
     * Create by Peter
     */
    function sell_update(){

//        print_r(input());
//
//        exit();


        $id=input('id');

        $sell=new SellModel();


        if ($id){


            $re=$sell->allowField(true)->save(input(),$id);



        }else{

            $re=$sell->allowField(true)->save(input());



        }


        if($re!==false){


            $this->redirect('sell/sell_list');


        }else{


            $this->error($sell->getError());

        }






    }

    /**
     * 需求修改
     * Create by Peter
     */
    function sell_delete(){

        $id=input('id');


        SellModel::destroy($id);

        //跳转回上一页
        redirect_last();

    }


}