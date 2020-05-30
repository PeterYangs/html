<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/19 0019
 * Time: 17:19
 */

namespace app\home\model;
use think\Model;

class SampleDetailModel extends Model
{

    protected $autoWriteTimestamp=true;
    protected $table='sample_detail';


    function setArriveTimeAttr($val){

        if(!$val) return 0;

        return strtotime($val);



    }

    function setExpiredTimeAttr($val){

        if(!$val) return 0;

        return strtotime($val);



    }


    function getArriveTimeAttr($val){


        return date('Y-m-d',$val);
    }


    function getExpiredTimeAttr($val){


        return date('Y-m-d',$val);
    }



    function warehouses(){


        return $this->belongsTo("EnumerateModel",'warehouse_id','id');
    }


    function goods(){


        return $this->belongsTo("GoodsModel",'goods_id','id');
    }


    function sample(){

        return $this->belongsTo("SampleModel",'sample_id','id');
    }


    function setProductionDateAttr($value){

        if(!$value)  return 0;

        return strtotime($value);
    }


    function getProductionDateAttr($value){

        if(!$value) return "";

        return date("Y-m-d",$value);

    }


//
//    function setNumAttr($val,$data){
//
//
//    }







}