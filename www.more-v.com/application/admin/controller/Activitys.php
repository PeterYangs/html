<?php
namespace app\admin\controller;
use app\home\model\Activity;

class Activitys extends Base {


    /**
     * 活动列表
     * Create by Peter
     * @return \think\response\View
     */
    function activity_list(){

        $ac=new Activity();

        $re=$ac->order('id','desc')->withCount('eventRegistration')->paginate(10);



        $this->assign('activity',$re);




        return view('activitys/activity_list');
    }


    /**
     * 活动编辑页面
     * Create by Peter
     */
    function activity_edit(){

        $id=input('id');

        $ac=new Activity();

        if($id){

            $re=$ac->find($id);



            $this->assign('activity',$re);
//            print_sql($re);



        }




        return view('activitys/activity_edit');



    }


    /**
     * 活动修改
     * Create by Peter
     */
    function activity_update(){
        $id=input('id');
        $ac=new Activity();


        if($id){

            $re=$ac->save(input(),$id);

            if($re!==false){

                $this->success('success','activitys/activity_list');

            }else{

                $this->error('fail');
            }



        }else{

            $re=$ac->save(input());


            if($re){

                $this->success('success','activitys/activity_list');

            }else{

                $this->error('fail');

            }

        }



    }


    /**
     * 活动删除
     * Create by Peter
     */
    function activity_delete(){
//        $ac=new Activity();

        $id=input('id');

        Activity::destroy($id);


        $this->redirect('activitys/activity_list');



    }






}
