<?php
namespace app\home\model;
use think\Model;
class SellerModel extends Model{

    protected $autoWriteTimestamp=true;

    protected $table='seller';





    function country(){



        return $this->belongsTo('CountryModel','country_id','id');
    }




    function admin(){



        return $this->belongsTo('AdminModel','admin_id','id');
    }



    function lvs(){

        return $this->belongsTo('EnumerateModel','lv','id');
    }


    function collect(){


        return $this->hasMany('CollectModel','seller_id','id');
    }


//    function getShowNicknameAttr($value,$data){
//
//
//
//       if($data['nickname'])  return $this->admin->nickname;
//
//
////       return '';
//    }



}

