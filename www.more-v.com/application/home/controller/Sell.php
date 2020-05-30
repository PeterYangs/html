<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/10
 * Time: 15:25
 */

namespace app\home\controller;


use app\home\model\SellModel;

class Sell extends Base{

    /**
     * 需求页面
     * Create by Peter
     * @return \think\response\View
     */
    function Selling_Leads(){



        //获取地址栏的where条件
        $where=deal_url_where();


        $sell=new SellModel();

        //需求列表
        $re=$sell->getSellList($where);

        //规格列表
        $group_name=$sell->where('standard','<>','')->group('standard')->select();


        //获取国家列表
        $rea=$sell->getSellListGroupByPlace();



        $this->assign('country',$rea);


        $this->assign('group_name',$group_name);

        $this->assign('sell',$re);

        return view('sell/Selling_Leads');

    }

}