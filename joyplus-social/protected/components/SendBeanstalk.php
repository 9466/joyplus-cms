<?php
/**
 * Created by PhpStorm.
 * User: yang
 * Date: 14-2-13
 * Time: 下午3:32
 */

Class SendBeanstalk{


//发送任务
    public static function sendMessage($body){

        if(Yii::app()->params['USE_BEANSTALK'] === '0'){
            return false;
        }

        //Connect to the queue
        $queue = new Phalcon\Queue\Beanstalk(array(
            'host' => Yii::app()->params['BEANSTALK_SERVER'],
            'port' => Yii::app()->params['BEANSTALK_PORT']
        ));
        $queue->choose(Yii::app()->params['TUBE_PLAY_HISTORY']);
        //Insert the job in the queue
        $queue->put($body);
    }
}