<?php
namespace app\home\model;
use think\Model;
class AdminModel extends BaseModel {

    protected $table='admin';

    protected $autoWriteTimestamp=true;



        function getShowNicknameAttr($value,$data){


          if($data['nickname'])  return "(".$data['nickname'].")";

          return '';
        }




        function getUsernameAttr($value,$data){

            if($data['nickname']) return $data['nickname'];


            return $value;
        }



        function getRealUsernameAttr($value,$data){



              return $data['username'];
        }


}