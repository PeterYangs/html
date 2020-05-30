<?php
namespace app\home\controller;
//use think\Controller;
use app\home\model\NewsModel;

class News extends Base{

    /**
     * 新闻详情
     * Create by Peter
     * @return \think\response\View
     * @throws \think\exception\DbException
     */
    function news_detail(){

        $id=input('id');


//        echo 1;

        if($id){

            $re=NewsModel::get($id);

//            print_sql($re);

            $this->assign('news',$re);
        }


        return view('news/news_detail');
    }




}