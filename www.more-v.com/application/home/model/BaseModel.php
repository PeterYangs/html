<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/25 0025
 * Time: 10:03
 */

namespace app\home\model;
use think\Model;


class BaseModel extends Model
{

    protected static $isWhere=false;



    function initialize()
    {

//        static $kk=false;
//
//        if(!$kk) {
//            $this->where('id', 'neq', 0);
//
//          $kk= true;
//        }
    }


    static function init()
    {
//        parent::init();

//        static::where('id','neq',0);

//        self::where('id','neq',0);

    }

}