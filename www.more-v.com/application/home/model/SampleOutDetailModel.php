<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/25 0025
 * Time: 14:23
 */

namespace app\home\model;


class SampleOutDetailModel extends BaseModel
{

    protected $table='sample_out_detail';
    protected $autoWriteTimestamp=true;



    function sampleDetail(){


        return $this->belongsTo('SampleDetailModel','sample_detail_id','id');
    }



    function getAdmin(){


        return $this->belongsTo('SampleDetailModel','sample_detail_id','id');
    }


    function goods(){


        return $this->belongsTo('GoodsModel','goods_id','id');
    }

}