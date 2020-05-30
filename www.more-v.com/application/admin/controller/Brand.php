<?php
namespace app\admin\controller;

use app\home\model\BrandModel;

class Brand extends Base{


    /**
     * 品牌列表
     * Create by Peter
     * @return \think\response\View
     */
    function brand_list(){


        $brand=new BrandModel();


        $re=$brand->order('id','desc')->with('seller,seller.admin')->paginate(10);


        $this->assign('brand',$re);



        return view('brand/brand_list');
    }



    function brand_edit(){


        $id=input('id');


        if($id) $this->assign('brand',BrandModel::get($id));


        remember_list_url();

        return view('brand/brand_edit');
    }



    function brand_update(){

        $id=input('id');


        $brand=new BrandModel();


        $brand->allowField(true)->isUpdate(valueIsSet($id))->save(input());


        redirect(redirect_list());

//        print_r(input());

    }


    function brand_delete(){


        BrandModel::destroy(input('id'));


//        redirect(redirect_list());


        redirect_last();
    }



}