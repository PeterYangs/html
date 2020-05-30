<?php
/**
 *
 * 这是一个消费者类，用于处理 helloJobQueue 队列中的任务
 */
namespace app\home\job;

use app\home\model\EmailTaskModel;
use think\Loader;
use think\queue\Job;

class Hello {

    /**
     * fire方法是消息队列默认调用的方法
     * @param Job            $job      当前的任务对象
     * @param array|mixed    $data     发布任务时自定义的数据
     */
    public function fire(Job $job,$data){

        $isJobDone = $this->doHelloJob($data);

        if ($isJobDone) {
            //如果任务执行成功， 记得删除任务
            $job->delete();
            print("<info>Hello Job has been done and deleted"."</info>\n");
        }else{
            if ($job->attempts() > 3) {
                //通过这个方法可以检查这个任务已经重试了几次了
                print("<warn>Hello Job has been retried more than 3 times!"."</warn>\n");
                $job->delete();
                // 也可以重新发布这个任务
                //print("<info>Hello Job will be availabe again after 2s."."</info>\n");
                //$job->release(2); //$delay为延迟时间，表示该任务延迟2秒后再执行
            }
        }
    }

    /**
     * 根据消息中的数据进行实际的业务处理
     * @param array|mixed    $data     发布任务时自定义的数据
     * @return boolean                 任务执行的结果
     */
    private function doHelloJob($data) {
        // 根据消息中的数据进行实际的业务处理...



//        return true;

        $task_id=$data['task_id'];

//        $email=new EmailTaskModel();

//        echo $task_id;

//        $re=EmailTaskModel::get($task_id);

        $email=Loader::model('app\home\model\EmailTaskModel');


        $re=$email->get($task_id);

//        print_r($re);








        if($re){

            $res=mailer($re->email,$re->title,$re->content);

            if($res){

                return true;
            }

            return false;
        }

//        print_r($re);

//        if($re){
//
//            mailer();
//
//        }


//        print_r($data);


//        return true;

//        print("<info>Hello Job Started. job Data is: ".var_export($data,true)."</info> \n");
//        print("<info>Hello Job is Fired at " . date('Y-m-d H:i:s') ."</info> \n");
//        print("<info>Hello Job is Done!"."</info> \n");

//        return true;
    }
}