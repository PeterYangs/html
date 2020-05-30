<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
//isCN();
//exit();
//echo $_SERVER['HTTP_ACCEPT_LANGUAGE'];
//if(isCN()){
//    header('Location:'."http://".'more-v.net');
//    exit();
//}
// [ 应用入口文件 ]

// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');
// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';


//function isCN()
//{
//    if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
//        $lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
//        $lang = substr($lang, 0, 5);
//        if (preg_match("/zh-cn/i", $lang)) {
//            $lang = "简体中文";
//
//            return true;
//        } elseif (preg_match("/zh/i", $lang)) {
//            $lang = "繁体中文";
//
//            return true;
//        } else {
//            $lang = "English";
//
//            return false;
//        }
////        return $lang;
//    } else {
//        return false;
//    }
//}