<?php
namespace app\home\model;
use think\Model;
class Activity extends Model{

    protected $autoWriteTimestamp=true;


    /**
     * 时间字符串转时间戳
     * Create by Peter
     * @param $value
     * @return false|int
     */
    function setActivityTimeAttr($value){


        return strtotime($value);
    }


    /**
     * 反之
     * Create by Peter
     * @param $value
     * @return false|string
     */
    function getActivityTimeAttr($value){


        if(!$value) return "";

        return date('Y-m-d',$value);
    }


    /**
     * 用户中心的时间显示
     * Create by Peter
     * @param $value
     * @param $data
     * @return false|string
     */
    function getATimeAttr($value,$data){
        //Aug.29th/2017
//        print_r($data);
        if(!$data['activity_time']) return "";



        return date("M.jS/Y",$data['activity_time']);
    }


    /**
     * 活动是否过期
     * Create by Peter
     * @param $value
     * @param $data
     * @return string    1是已过期，2是已报名，3是可报名
     */
    function getIsFinishedAttr($value,$data){

        $now=time();

        $activity_time=$data['activity_time'];

        if($now>$activity_time) return "Finished";


        return "Unfinished";

    }


    function getIsJoinAttr($value,$data){

        $activity_id=$data['id'];

        $activity_time=$data['activity_time'];//活动截止时间

        $now=time();



        if($now>$activity_time)  return 1;



        $re=EventRegistration::get([
            'activity_id'=>$activity_id,
            'user_id'=>getCookieS('user_id')
        ]);


        if($re) return 2;


        return 3;



    }


    function eventRegistration(){



        return $this->hasMany('EventRegistration','activity_id','id');

    }


    /**
     * 正确的url格式
     * Create by Peter
     * @param $value
     * @param $data
     * @return string
     */
    function getRealUrlAttr($value,$data){

        if(!$data['url']) return "javascript:void(0)";

        if(preg_match('/^http:\/\//',$data['url'])) return $data['url'];

        return "http://".$data['url'];


    }



}