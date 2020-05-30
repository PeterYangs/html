<?php
namespace app\home\model;
use think\Model;

class SellModel extends Model{

    protected $table="sell";
    protected $autoWriteTimestamp=true;


    /**
     * 时间字符串转数字
     * Create by Peter
     * @param $value
     * @return false|int
     */
    function setValidityAttr($value){



        return strtotime($value);
    }


    /**
     * 数字转时间格式
     * Create by Peter
     * @param $value
     * @return false|string
     */
    function getValidityAttr($value){


        return date('Y-m-d',$value);
    }


    /**
     * 获取国家的名字（这个字段是不存在的，是对place的id名转的）
     * 值得说明的是，其实获取器的效率并不是很高，通过trace调试发现，
     * 当模型调用place_name属性时，会触发这个读取器，也就是说，当你
     * 获取一个长度为10需求列表时，会有一个sql语句加上10个获取名字的
     * sql语句，这个读取器的效率没有联表查询效率高，慎用，尤其是获取列表
     * 的时候
     * Create by Peter
     * @param $value
     * @param $data
     * @return mixed
     */
    function getPlaceNameAttr($value,$data){


        $re=CountryModel::get($data['place']);

        return $re['name'];
    }

    /**
     * 获取需求列表，比上面那个修改器效率高一点
     * Create by Peter
     * @param array $where       where条件  例如：'standard' =>                             key（字段名）
     *                                              array (size=3)
     *                                              0 => string 's' (length=1)             表名
     *                                              1 => string 'eq' (length=2)            表达式，=,>,like 什么的
     *                                              2 => string 'yyyyyyyyy' (length=9)     值
     * @return \think\Paginator
     */
    function getSellList(array $where=[]){


        $this->alias('s')
            ->join('country c','s.place = c.id','left')
            ->order('id','desc')
            ->field('s.*,c.name as c_name');

            //添加where 条件
            deal_model_where($this,$where);




            $res=$this->paginate(10);

        return $res;
    }



    function getSellListGroupByPlace(array $where=[]){

        $this->alias('s')
            ->join('country c','s.place = c.id','left')
            ->order('id','desc')
            ->field('s.*,c.name as c_name')
            ->group('place')
            ->where('place','neq',0);


        deal_model_where($this,$where);


        return $this->select();



    }




    /**
     * 获取剩余时间
     * Create by Peter
     * @param $value
     * @param $data
     * @return  array
     */
    function getIsExpiredAttr($value,$data){
        //截止时间
        $validity=$data['validity'];

        $now=time();

        $day=ceil(($validity-$now)/86400);


        if($day<0){



            return [
                'html'=>"<span style='color: red'>已过期</span>",
                'res'=>true,
                'class'=>'unsale',
                'str'=>'已下架'
            ];

        }else{

            return [
                'html'=>"<span>还剩<span style='color: green'>{$day}天</span></span>",
                'res'=>false,
                'class'=>'sale',
                'str'=>'查看详情'

            ];

        }



    }



    function country(){



       return $this->belongsTo('CountryModel','place','id');
    }


    /**
     * 转json格式
     * Create by Peter
     * @param $value
     * @return string
     */
    function setGoodsImgAttr($value){





        return json_encode($value);
    }


    /**
     * 转数组
     * Create by Peter
     * @param $value
     * @return mixed
     */
    function getGoodsImgAttr($value){



        return json_decode($value);
    }


    /**
     * 获取随机推荐
     * Create by Peter
     * @return false|\PDOStatement|string|\think\Collection
     */
   static function getRandom(){


     return   self::order('rand()')->limit(6)->select();

    }



}

