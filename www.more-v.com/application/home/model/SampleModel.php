<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/19 0019
 * Time: 14:27
 */

namespace app\home\model;
use think\Model;

class SampleModel extends Model
{

    protected $autoWriteTimestamp=true;
    protected $table='sample';



    function sampleDetail(){


        return $this->hasMany('SampleDetailModel','sample_id','id');
    }



    function admin(){


        return $this->belongsTo("AdminModel",'admin_id','id');
    }


}