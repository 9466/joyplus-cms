<?php
/**
 * Created by PhpStorm.
 * User: yang
 * Date: 14-2-13
 * Time: 下午3:32
 */

Class SendBeanstalkMessage{


//发送任务
    public static function sendMessage($body){

        if(Yii::app()->params['USE_BEANSTALK'] === '0'){
            return false;
        }

        require_once 'Beanstalk.php';

        //实例化beanstalk
        $beanstalk = new Socket_Beanstalk(array(
            'persistent' => false, //是否长连接
            'host' => Yii::app()->params['BEANSTALK_SERVER'],
            'port' => Yii::app()->params['BEANSTALK_PORT'],  //端口号默认11300
            'timeout' => 3    //连接超时时间
        ));

        if (!$beanstalk->connect()) {
            exit(current($beanstalk->errors()));
        }
        //选择使用的tube
        $beanstalk->useTube(Yii::app()->params['TUBE_PLAY_HISTORY']);
        //往tube中增加数据
        $put = $beanstalk->put(
            23, // 任务的优先级.
            0,  // 不等待直接放到ready队列中.
            60, // 处理任务的时间.
            $body // 任务内容
        );

        if (!$put) {
            return false;   //('commit job fail');
        }

        $beanstalk->disconnect();
    }



    //处理消息
    public  function  handleMessage(){
        require_once 'Beanstalk.php';
        //实例化beanstalk
        $beanstalk = new Socket_Beanstalk(array(
            'persistent' => false, //是否长连接
            'host' => 'ip地址',
            'port' => 11600,  //端口号默认11300
            'timeout' => 3    //连接超时时间
        ));

        if (!$beanstalk->connect()) {
            exit(current($beanstalk->errors()));
        }
        //查看beanstalkd状态
        //var_dump($beanstalk->stats());

        //查看有多少个tube
        //var_dump($beanstalk->listTubes());

        $beanstalk->useTube('test');

        //设置要监听的tube
        $beanstalk->watch('test');

        //取消对默认tube的监听，可以省略
        $beanstalk->ignore('default');

        //查看监听的tube列表
        //var_dump($beanstalk->listTubesWatched());

        //查看test的tube当前的状态
        //var_dump($beanstalk->statsTube('test'));


        while (true) {
            //获取任务，此为阻塞获取，直到获取有用的任务为止
            $job = $beanstalk->reserve(); //返回格式array('id' => 123, 'body' => 'hello, beanstalk')

            //处理任务
            $result = doJob($job['body']);

            if ($result) {
                //删除任务
                $beanstalk->delete($job['id']);
            } else {
                //休眠任务
                $beanstalk->bury($job['id'],'');
            }
            //跳出无限循环
            if (file_exists('shutdown')) {
                file_put_contents('shutdown', 'beanstalkd在'.date('Y-m-d H:i:s').'关闭');
                break;
            }
        }
        $beanstalk->disconnect();
    }
}