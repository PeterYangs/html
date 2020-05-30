<?php
namespace app\home\model;
use think\Model;
class UserHitModel extends Model{

    protected $autoWriteTimestamp=true;

    protected $table='user_hit';


    /**
     * 本周访问量
     * Create by Peter
     */
   static function thisWeekCount(){

       echo  self::whereTime('create_time','week')->count();


    }

    /**
     * 总访问人数
     * Create by Peter
     */
    static function allGroupByIp(){


       echo self::group('ip')->count();
    }


    /**
     *本周访问人数
     * Create by Peter
     */
    static function thisWeekGroupByIp(){


        echo self::whereTime('create_time','week')->group('ip')->count();
    }


//    function thisWeek
    

}

