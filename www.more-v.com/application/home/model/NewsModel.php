<?php
namespace app\home\model;
use think\Model;
class NewsModel extends Model{

    protected $autoWriteTimestamp=true;

    protected $table='news';


    /**
     * 获取编辑器的内容,转成html标签
     * Create by Peter
     * @param $value
     * @return string
     */
    function getContentAttr($value){


        return htmlspecialchars_decode($value);
    }


    /**
     * 时间格式转化，用于底部新闻时间
     * Create by Peter
     * @param $value
     * @param $data
     * @return false|string
     */
    function getNewsDateAttr($value,$data){



        return date("M d,Y",$data['create_time']);

    }


//    function get



    function setPhotoAttr($value){

        if(!is_array($value)) return [];


        return json_encode(str_replace('\\','/',$value));
    }



}