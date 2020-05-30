<?php
namespace app\common\validate;
use think\Validate;
class User extends Validate{


    protected $rule=[
        'email'=>'require|unique:user',  //必填且唯一
        'password'=>'require|confirm',
        'activity_id'=>'unique:event_registration,activity_id^user_id',//活动id和用户id同时满足唯一性


    ];


    protected $message=[
        'email.require'=>'email required',
        'email.unique'=>'The email has been registered',
        'password.confirm'=>'Password and confirmation password are inconsistent',
        'activity_id.unique'=>'You have already participated'



    ];

    //场景
    protected $scene=[
        'update'=>['password'=>'confirm'],//更新操作时，密码不是必填
        'register'=>['email'],
        'activity_add'=>['activity_id'],
        'forget_password'=>['password']


    ];


}


