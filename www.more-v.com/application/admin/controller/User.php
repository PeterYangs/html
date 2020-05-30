<?php
namespace app\admin\controller;
use app\home\model\SellMaterialModel;
use app\home\model\UserModel;

class User extends Base{

    /**
     * 会员列表
     * Create by Peter
     * @return \think\response\View
     * @throws \think\exception\DbException
     */
    function user_list(){

        //处理地址栏where条件
        $where=deal_url_where();


        $user=new UserModel();

        //自动关联并筛选
        deal_model_where_with_join($user,$where);


        $re=$user->paginate(10);




        $this->assign('user',$re);


        return view('user/user_list');
    }


    /**
     * 会员编辑页面
     * Create by Peter
     * @return \think\response\View
     * @throws \think\exception\DbException
     */
    function user_edit(){


        $id=input('id');


        if($id){

           $re=UserModel::get($id);

           $this->assign('user',$re);



        }




        return view('user/user_edit');
    }


    /**
     * 会员修改
     * Create by Peter
     */
    function user_update(){
        $id=input('id');

        $user=new UserModel();

        $post=input();

        if($id){
            //没写密码就从更新数组中移除
            if(!$post['password']) unset($post['password']);


            $re=$user->validate('User.update')->allowField(true)->save($post,$id);



        }else{


            $re=$user->validate('User')->allowField(true)->save($post);



        }


        if($re!==false){

            $this->success('success','user/user_list');


        }else{


            $this->error($user->getError());
        }



    }


    /**
     * 会员删除
     * Create by Peter
     */
    function user_delete(){

        $id=(int)input('id');

        UserModel::destroy($id);


        $this->redirect('user/user_list');


    }



    function test(){

        $re=mailer('wings.kn@gmail.com','这是一封测试邮件','你好！朋友！<br/>');

        var_dump($re);

    }

    /**
     * 素材列表
     * Create by Peter
     * @return \think\response\View
     * @throws \think\exception\DbException
     */
    function material_list(){


        $db=new SellMaterialModel();
        $re=$db->order('id','desc')->with('sell,user,sell.country')->paginate(10);

        $this->assign('material',$re);


        return view('user/material_list');
    }

    /**
     * 素材编辑
     * Create by Peter
     * @return \think\response\View
     * @throws \think\exception\DbException
     */
    function material_edit(){


        $re=SellMaterialModel::get(input('id'));

        $this->assign('material',$re);

        return view('user/material_edit');
    }


}
