<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/15 0015
 * Time: 09:53
 */

namespace app\home\model;
use think\Model;


class CollectModel extends Model
{
    protected $table='collect';

    protected $autoWriteTimestamp=true;




    function admin(){


      return  $this->belongsTo("AdminModel",'admin_id','id')->field('id,username,nickname');
    }



}