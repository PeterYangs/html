<?php
namespace app\home\model;
use think\Model;
use think\response\Json;

class GoodsModel extends BaseModel {
    protected $table="goods";
    protected $autoWriteTimestamp=true;






    function brand(){


        return $this->belongsTo('BrandModel','brand_id','id');
    }


    /**
     * 转成逗号分隔的字符串
     * Create by Peter
     * @param $value
     * @return string
     */
    function setCategoryIdAttr($value){


        if(is_array($value)) return join(',',$value);

        return '';
    }

    /**
     * 获取这个商品的所有分类(用于商品详情)
     * Create by Peter
     * @param $value
     * @param $data
     * @return array|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function getCategoryNameAttr($value,$data){

        if($data['category_id']){

            $category=new CategoryModel();

            $re=$category->where('id','in',$data['category_id'])->field('name,id')->select();


           if($re)  return json_encode(get_sql_array($re));
//           if($re)  return (get_sql_array($re));


           return '';
        }

        return '';
    }


    /**
     * 获取商品分类，用于列表
     * Create by Peter
     * @param $value
     * @param $data
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function getAllCategoryAttr($value,$data){

        if($data['category_id']){

            $category=new CategoryModel();

            $re=$category->where('id','in',$data['category_id'])->field('name,id')->select();


//            print_sql($re);

//            print_r(array_column($re,'name'));

           if ($re) return join( '、',array_column(get_sql_array($re),'name'));


           return '';

        }


        return '';

    }


    function setImgAttr($value){


        if(!$value)  return '';

        return json_encode(str_replace('\\','/',$value));
//        return json_encode($value);


    }


    function getImgAttr($value){

        if($value) return json_decode($value,true);

        return '';

    }


//
//    function category(){
//
//
//        return $this->belongsTo("CategoryModel",'category_id','id');
//    }


}