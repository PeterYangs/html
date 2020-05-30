<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/22 0022
 * Time: 14:18
 */

namespace app\home\model;
use think\Model;


class ClientModel extends BaseModel
{
    protected $autoWriteTimestamp=true;
    protected $table='client';




    function admin(){




        return $this->belongsTo('AdminModel','admin_id','id');
    }


    function join_admin(){



        return $this->join('admin','client.admin_id=admin.id','left');
    }


    function setAddressIdAttr($value){

        if(!$value) return '';

        return join(',',$value);

    }

    function getAddressIdAttr($value){

        if(!$value) return [];

        $arr=explode(',',$value);

        $address=new AddressModel();

        foreach ($arr as $k=>$v){


            $address->whereOr('id',$v);

        }


      return  $address->select();


    }


//    function getAdminNameAttr($value,$data){
//
//
////         $re=$this->belongsTo('AdminModel','admin_id','id')->where('id',$data['admin_id'])->find();
////
////         return $re->username;
//
//    }


}