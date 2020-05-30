<?php
namespace app\home\model;
use think\Model;
class EventRegistration extends Model {


    protected $autoWriteTimestamp=true;

    protected $readonly=['user_id','activity_id'];




}