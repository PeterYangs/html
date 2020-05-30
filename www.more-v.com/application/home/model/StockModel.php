<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/20 0020
 * Time: 11:28
 */

namespace app\home\model;
use think\Model;

class StockModel extends Model
{
    protected $table='stock';
    protected $autoWriteTimestamp=true;



    function sampleDetail(){



        return $this->belongsTo('SampleDetailModel','sample_detail_id','id');
    }




    function join_sample_detail(){



        return $this->join('sample_detail','stock.sample_detail_id = sample_detail.id','left');
//            ->join('goods','goods.id = sample_detail.goods_id','left');
    }


    function join_goods(){


        return $this->join('goods','goods.id = sample_detail.goods_id','left');
    }


    function join_sample(){

        return $this->join('sample','sample_detail.sample_id=sample.id','left');

    }




//    function t(){
//
//        return $this->hasManyThrough('GoodsModel','SampleDetailModel');
//    }


}