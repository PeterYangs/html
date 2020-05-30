<?php
namespace app\home\model;
use think\Model;
class DateModel extends Model{


    protected $autoWriteTimestamp=true;

    protected $table='date';

//    protected $readonly='id';




    function setDisplayIdAttr($value){


        if(!$value) return '';
        
        return join(',',$value);
    }



    function setStartAttr($value){

        if(!$value) return 0;

        return strtotime($value);
    }


    function getStartAttr($value){


        return date("Y-m-d",$value);
    }


    function setEndAttr($value,$data){

        if(!$value) return strtotime($data['start']."+ 9hour");

        return strtotime($value."+ 9hour");
    }


    function getEndAttr($value){


        return date("Y-m-d H:m",$value);


    }



    function admin(){


        return $this->belongsTo('AdminModel','admin_id','id');
    }


    function seller(){


        return $this->belongsTo('SellerModel','seller_id','id');
    }

    function types(){


        return $this->belongsTo("EnumerateModel",'type','id');
    }


    function getColorAttr($value,$data){


        $now=time();

        $now=strtotime(date("Y-m-d",$now));

        $start=$data['start'];

        $end=strtotime(date('Y-m-d',$data['end']));

        if($data['status']==1) return "#26C281";            //已完成

        if($now>$end&&$data['status']==2)  return "#D91E18"; //未完成已过期

        return "#3598dc";




//        if($now>=$start)




    }


}
