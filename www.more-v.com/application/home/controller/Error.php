<?php
namespace app\home\controller;
use think\Controller;
class Error extends Controller{



    public function _empty($name)
    {

        return view('index/errors');

    }




}