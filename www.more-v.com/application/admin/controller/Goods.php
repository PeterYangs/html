<?php
namespace app\admin\controller;

use app\home\model\CategoryModel;
use app\home\model\EnumerateModel;
use app\home\model\GoodsModel;

class Goods extends Base{






    function category_list(){


        $re=getAllCategory();

//        print_r($re);
//
//        exit();

        $this->assign('cate',$re);
//        print_r($re);

        return view('goods/category_list');
    }


    function enumerate_list(){

        $re=getAllCategory(0,'','\app\home\model\EnumerateModel');


        $this->assign('cate',$re);


        return view('goods/enumerate_list');
    }



    function category_update(){

        $id=input('id');


        $cate=new CategoryModel();


        $re=$cate->isUpdate(valueIsSet($id))->allowField(true)->save(input());



        if($re!==false) exit( jsonCode(1,'操作成功'));


        exit(jsonCode(2,'操作失败'));


    }


    function enumerate_update(){
        $id=input('id');

        $cate=new EnumerateModel();


        $re=$cate->isUpdate(valueIsSet($id))->allowField(true)->save(input());



        if($re!==false) exit( jsonCode(1,'操作成功'));


        exit(jsonCode(2,'操作失败'));

    }


    /**
     * Create by Peter
     * @throws \think\exception\DbException
     */
    function category_delete(){




        $re=CategoryModel::get(['parent_id'=>input('id')]);


        if($re)  exit(jsonCode(2,'此分类下有子分类，删除失败！'));


        $res=CategoryModel::destroy(input('id'));


        if($res) exit(jsonCode(1,'删除成功'));


        exit(jsonCode(3,'删除失败'));


    }


    /**
     * Create by Peter
     * @throws \think\exception\DbException
     */
    function enumerate_delete(){

        $re=EnumerateModel::get(['parent_id'=>input('id')]);

        if($re)  exit(jsonCode(2,'此分类下有子分类，删除失败！'));


        $res=EnumerateModel::destroy(input('id'));


        if($res) exit(jsonCode(1,'删除成功'));


        exit(jsonCode(3,'删除失败'));


    }





    /**
     * 商品列表
     * Create by Peter
     * @return \think\response\View
     * @throws \think\exception\DbException
     */
    function goods_list(){


        $goods=new GoodsModel();


        $re=$goods->order('id','desc')->with('brand,brand.seller,brand.seller.admin')->paginate(10);


        $this->assign('goods',$re);


        return view('goods/goods_list');
    }


    /**
     * Create by Peter
     * @return \think\response\View
     * @throws \think\exception\DbException
     */
    function goods_edit(){

        $id=input('id');

        if($id) $this->assign('goods',GoodsModel::get($id));

//         print_r(GoodsModel::get($id));

        remember_list_url();

        return view('goods/goods_edit');
    }



    function goods_update(){


//        print_r(input());
//
//        exit();


        $id=input('id');


        $goods=new GoodsModel();




        $goods->isUpdate(valueIsSet($id))->allowField(true)->save(input());


        redirect_list();


    }



    function goods_delete(){

        $re=GoodsModel::destroy(input('id'));

        if($re) exit(jsonCode(1,'删除成功'));

        exit(jsonCode(2,'删除失败'));


//        if($re) echo jsonCode(2,'删除失败');

    }

}
