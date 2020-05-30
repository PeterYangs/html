<?php
namespace app\admin\controller;
use app\common\Page;
use app\home\model\AdminModel;
use think\Db;
use think\Exception;
use think\Validate;

/**
 * 管理员控制器
 * Create By Peter
 * Class Admin
 * @package app\admin\controller
 */
class Admin extends Base{


    /**
     * 管理员列表
     * Create by Peter
     * @return \think\response\View
     * @throws \think\exception\DbException
     */
    function admin_list(){

        //这里主要是把用户组名称搞出来有点麻烦
        $admin=Db::table('admin')
            ->alias('a')
            ->join('auth_group_access ac','a.id=ac.uid','left')
            ->join('auth_group g','ac.group_id=g.id','left')
            ->field('a.*,g.title,ac.group_id as gid')
            ->paginate(10);



        $this->assign('admin',$admin);



        return view('admin/admin_list');
        }

    /**
     * 管理员编辑
     * Create by Peter
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
        function admin_edit(){

            $id=input('id');

            if($id){

              $admin=Db::table('admin')
                  ->alias('a')
                  ->field('a.*,g.group_id')
                  ->join('auth_group_access g','a.id=g.uid','left')
                  ->where('a.id=:id')
                  ->bind(array('id'=>array($id,1)))
                  ->find();

              $this->assign('admin',$admin);

//              print_r($admin);

            }

            $group=Db::table('auth_group')->select();

            $this->assign('group',$group);



            remember_list_url();

            return view('admin/admin_edit');
        }

    /**
     * 管理员添加修改
     * Create by Peter
     */
        function admin_update(){
            $post=input('');
            $id=$post['id'];

            $data=array(
                'username'=>$post['username'],
                'password'=>md5($post['password']),
                'email'=>$post['email']
            );
            $re=false;
            if($id){

                unset($data['username']);
                if(!$post['password']){
                    //没有密码就不更新
                    unset($data['password']);
                }
//                Db::transaction(function () use ($re,$id,$data){

                    Db::startTrans();
                    try{
                        Db::table('admin')->where('id=:id')->bind(array('id'=>array($id,1)))->update($data);
                        $re=Db::table('auth_group_access')->where('uid=:id')->bind(array('id'=>array($id,1)))->update(array('group_id'=>$post['group_id']));
                        Db::commit();
                    }catch (Exception $e){

                        Db::rollback();

                        $this->error($e);
                    }





            }else{

                $data['createtime']=date("Y-m-d H:i:s");
//                $data['lastlogin']=date("Y-m-d H:i:s");


                $id=Db::table('admin')->insertGetId($data);

                $re=Db::table('auth_group_access')->insert(array(
                    'uid'=>$id,
                    'group_id'=>$post['group_id']
                ));



            }

            if($re!==false){


                redirect_list();
            }


        }

    /**
     * 管理员删除
     * Create by Peter
     * @throws Exception
     * @throws \think\exception\PDOException
     */
        function admin_delete(){

            $id=input('id');

            Db::table('admin')->delete($id);
            Db::table('auth_group_access')->where('uid=:id')->bind(array('id'=>array($id,1)))->delete();

            redirect_list($_SERVER['HTTP_REFERER']);




        }




    /**
     * 权限码列表
     * Create by Peter
     */
        function rule_list(){

            $rule=Db::table('auth_rule')->order('id','desc')->paginate(10);


            $this->assign('rule',$rule);




            return view('admin/rule_list');
        }


    /**
     * 权限码编辑
     * Create by Peter
     */
        function rule_edit(){

            $id=input('id');

            if($id){
                $rule=Db::table('auth_rule')->where('id',$id)->find();

                $name=explode('/',$rule['name']);
                $rule['controller']=$name[0];
                $rule['action']=$name[1];

//                print_r($rule);

                $this->assign('rule',$rule);



//                print_r($re);

            }



            $controllers=get_all_controller('admin');

            $this->assign('controllers',$controllers);

            remember_list_url();

            return view('admin/rule_edit');
        }


    /**
     * 权限吗添加修改
     * Create by Peter
     */
        function rule_update(){


            $post=input('');

            $id=$post['id'];
            $title=$post['title'];
            $name=$post['controller']."/".$post['action'];

//            echo $id;
//            exit();


            if($id){
                $re=Db::table('auth_rule')->where('id=:id')->bind(array('id'=>array($id,1)))->update(array(
                    'name'=>$name,
                    'title'=>$title
                ));


            }else{

                $is_find=Db::table('auth_rule')->where('name=:name')->bind(array('name'=>$name))->find();
                if($is_find){

                    $this->error("Rule already exists");

                }




                $re=Db::table('auth_rule')->insert(array(
                    'title'=>$title,
                    'name'=>$name
                ));



            }

            if($re!==false){

                redirect_list();

            }



        }




    /**
     * 删除权限
     * Create by Peter
     */
        function rule_delete(){

            $id=input('id');

            Db::table('auth_rule')->delete($id);


            redirect_list($_SERVER['HTTP_REFERER']);

//            echo $id;


        }

    /**
     * 用户组列表
     * Create by Peter
     */
        function group_list(){

            $group=Db::table('auth_group')->paginate(10);


            $this->assign('group',$group);




            return view('admin/group_list');
        }


    /**
     * 用户组编辑页面
     * Create by Peter
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
        function group_edit(){

            $id=input('id');

            if($id){

                $group=Db::table('auth_group')->where('id=:id')->bind(array('id'=>array($id,1)))->find();
                //转json
                $group['rule']=json_encode(explode(',',$group['rules']));


                $this->assign('group',$group);

            }

            $rule=Db::table('auth_rule')->select();


            $this->assign('rule',$rule);




            remember_list_url();

            return view('admin/group_edit');
        }

    /**
     * 用户组添加修改
     * Create by Peter
     * @throws Exception
     * @throws \think\exception\PDOException
     */
        function group_update(){
            $post=input();
            $id=$post['id'];
            $rule=$post['rule'];
            if (!$rule) {

                $this->error('Please fill in the complete information');
                exit();
            }
            $rule=join(',',$rule);
            $title=$post['title'];

//            print_r($id);
//            exit();

            if($id){

                $re=Db::table('auth_group')->where('id',$id)->update(array(
                    'title'=>$title,
                    'rules'=>$rule

                ));


//                print_r($re);


            }else{

                $re=Db::table('auth_group')->insert(array(
                    'rules'=>$rule,
                    'title'=>$title
                ));



            }

            if($re){

            redirect_list();

            }else{

//                echo 333;


            }

        }

    /**
     * 删除用户组
     * Create by Peter
     * @throws Exception
     * @throws \think\exception\PDOException
     */
        function group_delete(){
            $id=input('id');
            Db::table('auth_group')->delete($id);

            redirect_list($_SERVER['HTTP_REFERER']);


        }


    /**
     * 系统邮件列表
     * Create by Peter
     * @return \think\response\View
     */
        function system_email_list(){


            $limit=6;

            $mail_arr=getMailList((int)input('page'),$limit);

//            $page=new Page(10,6,input('page'),request()->url(true),3);


            $page = \think\paginator\driver\Bootstrap::make($mail_arr['item'], $limit, (valueIsSet(input('page'))?1:input('page')), $mail_arr['count'], false,
                [
                'var_page' => 'page',
                'path'     => request()->baseUrl(),
                'query'    => [],
                'fragment' => '',
            ] );



//            var_dump($page->render());


            $this->assign('page',$page->render());

            $this->assign('mail',$mail_arr['item']);


            return view('admin/system_email_list');
        }

    /**
     * 邮件详情页
     * Create by Peter
     * @return \think\response\View
     */
        function system_email_edit(){

            $id=input('id');

            $mail=getMailById($id);


            $this->assign('mail',$mail);




//            echo $mail->textHtml;

            return view('admin/system_email_edit');

        }

    /**
     * 用户中心页面
     * Create by Peter
     * @return \think\response\View
     */
        function profile(){



            return view('admin/profile');
        }


    /**
     * 用户信息修改
     * Create by Peter
     */
        function profile_update(){


            $admin=new AdminModel();


            $rule=[
                'email'=>'unique:admin'

            ];

            if(input('email')&&input('email_password')){

                $re=connectEmailBox(input('email'),input('email_password'),$msg);


                if(!$re) exit(jsonCode(2,$msg));
            }


            $re=$admin->allowField('nickname,email,mobile,email_password')->validate($rule)->save(input(),['id'=>request()->admin->id]);



            if($re!==false) exit(jsonCode(1,'修改成功'));



            exit(jsonCode(3,$admin->getError()));


        }







}
