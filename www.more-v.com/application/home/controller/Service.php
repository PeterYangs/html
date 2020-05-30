<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/12
 * Time: 15:11
 */

namespace app\home\controller;


class Service extends Base{

    function client(){
        return view('service/client');
    }

    function import(){
        return view('service/import');
    }

    function third_party(){
        return view('service/third_party');
    }

}