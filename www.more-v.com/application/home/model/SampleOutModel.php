<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/25 0025
 * Time: 14:00
 */

namespace app\home\model;


use think\Model;

class SampleOutModel extends BaseModel
{

    protected $table='sample_out';
    protected $autoWriteTimestamp=true;



    function sampleOutDetail(){


        return $this->hasMany('SampleOutDetailModel','sample_out_id','id');
    }



    function admin(){



        return $this->belongsTo('AdminModel','admin_id','id');
    }



    function client(){



        return $this->belongsTo('ClientModel','client_id','id');
    }



//    function goods(){
//
//
//        return $this->hasManyThrough('GoodsModel','');
//    }

    function sendType(){


        return $this->belongsTo("EnumerateModel",'send_type_id','id');
    }


    function getOutTimeAttr($value){

        if(!$value) return ['time'=>'','is_time'=>false];

//        if($value==1914368096) return ['time'=>'','is_time'=>false];

        $now=time();

        //该通知的时间
        $time=strtotime("+1 week",$value);

                                //出库的时间格式                             是否到了通知的时间
        if($now>=$time) return ['time'=>date('Y-m-d H:i:s',$value),'is_time'=>true];

        return ['time'=>date('Y-m-d H:i:s',$value),'is_time'=>false];


    }


}