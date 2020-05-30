<?php
namespace app\home\model;
use think\Model;
class BrandModel extends Model{

    protected $autoWriteTimestamp=true;

    protected $table='brand';



    function seller(){


        return $this->belongsTo('SellerModel','seller_id','id');
    }


}