<?php
namespace app\admin\controller;
use app\home\model\NewsModel;
class News extends Base{

    /**
     * 新闻列表
     * Create by Peter
     * @return \think\response\View
     * @throws \think\exception\DbException
     */
    function news_list(){


            $news=new NewsModel();

            $re=$news->order('sort','asc')->paginate(10);


            $this->assign('news',$re);

        return view('news/news_list');
    }


    /**
     * 新闻编辑
     * Create by Peter
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function news_edit(){
        $id=input('id');
        $news=new NewsModel();

        if($id){


            $re=$news->find($id);


//            print_sql($re);

            $this->assign('news',$re);

        }



        return view('news/news_edit');

    }

    /**
     * 新闻修改
     * Create by Peter
     */
    function news_update(){

        $id=input('id');

//        print_r($id);
//        exit();

        $news=new NewsModel();

        if($id){

            //这里有一个小bug，第二个参数无用，第一个参数已经传了id了
            $re=$news->save(input(),$id);


            if($re!==false){

                $this->success('success','news/news_list');

            }else{

                $this->error('fail');
            }


        }else{

            $re=$news->save(input());


            if($re){


                $this->success('success','news/news_list');
            }else{

                $this->error('fail');
            }

        }




    }

    /**
     * 新闻删除
     * Create by Peter
     */
    function news_delete(){

        $id=input('id');

        NewsModel::destroy($id);


        $this->redirect('news/news_list');


    }


    /**
     * 更改新闻排序
     * Create by Peter
     * 2019/06/14 16:20:44
     * Email:904801074@qq.com
     */
    function newsChangeSort(){


        $sort=input('sort');
        $id=input('id');

        $news=new NewsModel();


        $news->save([
            'sort'=>$sort
        ],['id'=>$id]);



        exit(jsonCode(1,'success'));


    }


}
