<?php
/**
 * Created by PhpStorm.
 * User: yangliu
 * Date: 14-2-18
 * Time: 下午2:02
 */


Class IpUtils{

    public static function getIp(){
            $ip = $_SERVER['REMOTE_ADDR'];

            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }

            return $ip;
        }
}
?>