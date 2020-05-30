<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/17 0017
 * Time: 14:26
 */

namespace app\home\model;


use think\Model;

class FeedbackModel extends Model
{
    protected $table='feedback';
    protected $autoWriteTimestamp=true;


    function client(){


      return  $this->belongsTo('ClientModel','client_id','id');
    }



}