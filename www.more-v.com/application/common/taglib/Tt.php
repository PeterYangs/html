<?php
namespace app\common\taglib;

use think\template\TagLib;

class Tt extends TagLib{

    /**
     * 定义标签列表
     */
    protected $tags   =  [
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'close'     => ['attr' => 'time,format', 'close' => 0], //闭合标签，默认为不闭合
        'open'      => ['attr' => 'name,type', 'close' => 1],
        'models'     =>['attr'=>'model,limit,item,order','close' => 1],
        'table'      =>['attr'=>'name,limit,where,order','close'=>1],

    ];

    /**
     * 这是一个闭合标签的简单演示
     */
    public function tagClose($tag)
    {
        $format = empty($tag['format']) ? 'Y-m-d H:i:s' : $tag['format'];
        $time = empty($tag['time']) ? time() : $tag['time'];
        $parse = '<?php ';
        $parse .= 'echo date("' . $format . '",' . $time . ');';
        $parse .= ' ?>';
        return $parse;
    }

    /**
     * 这是一个非闭合标签的简单演示
     */
    public function tagOpen($tag, $content)
    {
        $type = empty($tag['type']) ? 0 : 1; // 这个type目的是为了区分类型，一般来源是数据库
        $name = $tag['name']; // name是必填项，这里不做判断了
        $parse = '<?php ';
        $parse .= '$test_arr=[[1,3,5,7,9],[2,4,6,8,10]];'; // 这里是模拟数据
        $parse .= '$__LIST__ = $test_arr[' . $type . '];';
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="' . $name . '"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }



    function tagModels($tag,$content){


        $model=$tag['model'];

        $limit=isset($tag['limit'])?$tag['limit']:20;

        $item=isset($tag['item'])?$tag['item']:'v';

        $order=isset($tag['order'])?$tag['order']:'id desc';


        $parse="<?php ";

        $parse.='$model= think\Loader::model(\''.$model.'\');';

        $parse.='$re=$model->limit('.$limit.')->order(\''.$order.'\')->select();';

        $parse.='$__LIST__= $re;';

        $parse.="?>";

        $parse .= '{volist name="__LIST__" id="'.$item.'"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;

    }



    function tagTable($tag,$content){


        $name=$tag['name'];//表名

        $limit=isset($tag['limit'])?$tag['limit']:20;//长度


        $where=isset($tag['where'])?$tag['where']:' id is not null';


        $order=isset($tag['order'])?$tag['order']:' id desc';

        $item='v';


        $parse="<?php ";


        $parse.='$re=\think\Db::table(\''.$name.'\')->where(\''.$where.'\')->order(\''.$order.'\')->limit(\''.$limit.'\')->select();';


        $parse.='$__LIST__= $re;';

        $parse.="?>";

        $parse .= '{volist name="__LIST__" id="'.$item.'"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;



    }



}