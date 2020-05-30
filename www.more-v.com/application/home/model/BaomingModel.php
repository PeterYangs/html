<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/17 0017
 * Time: 09:26
 */

namespace app\home\model;


use think\Model;

class BaomingModel extends Model
{

    protected $table='baoming';

    protected $autoWriteTimestamp=true;




    function setNeedAttr($value){

        if(!$value) return '';

        if(!is_array($value)) return '';

        return join(',',$value);


    }


}