<?php
namespace app\home\model;
use think\Model;
class SellMaterialModel extends Model{

    protected $table="sell_material";
    protected $autoWriteTimestamp=true;


    /**
     * 图片转json
     * Create by Peter
     * @param $value
     * @return string
     */
    function setGoodsImgAttr($value){


        return json_encode($value);
    }


    /**
     * json转数组
     * Create by Peter
     * @param $value
     * @return mixed
     */
    function getGoodsImgAttr($value){

        return json_decode($value,true);
    }



    function sell(){


        return $this->belongsTo('SellModel','sell_id','id');
    }

    /**
     * 是否免费样品
     * Create by Peter
     * @param $value
     * @return mixed
     */
    function getFreeSamplesAttr($value){

        $arr=[1=>'Yes',2=>'No'];

        return $arr[$value];
    }



    function getDateAttr($value,$data){


        return date("M.dS.Y",$data['create_time']);
    }


    function user(){


        return $this->belongsTo('UserModel','user_id','id');
    }

}

