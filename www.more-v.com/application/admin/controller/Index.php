<?php
namespace app\admin\controller;
//use think\Controller;
use app\home\model\DateModel;
use app\home\model\EnumerateModel;
use app\home\model\UserHitModel;

class Index extends Base{


    /**
     * Create by Peter
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function index(){

        $user_hit=new UserHitModel();
//
//        //本周信息
//        $data=$user_hit->whereTime('create_time','week')->select();
////
//
////
//        if($data){
//
//            $data=$this->groupByDay($data);
//
//
//
//
//            $this->assign('hit_data',json_encode($data));
//
//        }



//
        return view('index/index');

    }


    /**
     * 本周访问折线图的数据
     * Create by Peter
     * @param $data
     * @return array
     */
    private function groupByDay($data){

        $week=[0,0,0,0,0,0,0];



        foreach ($data as $key=>$value){

            $day=date('w',$value->getData('create_time'));

            $week[$day]=isset($week[$day])?++$week[$day]:1;

        }

        //把星期天放到最后
        $sunday=$week[0];

        unset($week[0]);

        $week[]=$sunday;

        $week=array_values($week);


        return $week;

    }






}