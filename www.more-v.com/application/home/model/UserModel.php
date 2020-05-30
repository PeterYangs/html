<?php
namespace app\home\model;
use think\Model;
class UserModel extends Model{

    protected $table="user";
    protected $autoWriteTimestamp=true;
    protected $readonly=['email'];  //只读字段，不能被修改


    /**
     * 密码md5加密
     * Create by Peter
     * @param $value
     * @return string
     */
    function setPasswordAttr($value){



        return md5($value);
    }


    /**
     * 地址数组转json
     * Create by Peter
     * @param $value
     * @return string
     */
    function setAddressAttr($value){



        return json_encode($value);
    }


    /**
     * 地址json转数组
     * Create by Peter
     * @param $value
     * @return mixed
     */
    function getAddressAttr($value){



        return json_decode($value,true);
    }


    function eventRegistration(){



        return $this->hasMany('EventRegistration','user_id','id');
//        return $this->hasManyThrough('EventRegistration','user_id','id');

    }



    function country(){


        return $this->belongsTo('CountryModel','country_id','id');

    }

    /**
     * 关联其他表
     * Create by Peter
     * @param $type
     */
    function join_event_registration($type="inner"){

        $this->join('event_registration','user.id=event_registration.user_id',$type);
    }


}