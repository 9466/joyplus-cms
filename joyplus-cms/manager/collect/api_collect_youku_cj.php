<?php
/**
 * Created by PhpStorm.
 * User: yangliu
 * Date: 14-2-17
 * Time: 上午10:27
 */

require_once ("../admin_conn.php");
require_once ("collect_fun.php");
require_once ("tools/ContentManager.php");
define("CATEGORY","https://openapi.youku.com/v2/shows/by_category.json?client_id=715115c589f8533a&category={category}&release_year=&page={page}&count=20&paid=0");
define("VIDEOS","https://openapi.youku.com/v2/shows/videos.json?client_id=715115c589f8533a&show_id={id}&page=1&count={count}");
define("SHOWINFO","https://openapi.youku.com/v2/shows/show.json?client_id=715115c589f8533a&show_id={id}");



$action = be("all", "a_p");
echo $action;

$action = explode("_",$action);
$type = $action[0];
$page = $action[1];

switch($type){
    case "1":      //电影
        for($i = 1;$i<=$page;$i++){
            CjYoukuVideos("电影",'1',$i,"177");
        }
        break;
    case "2":      //电视剧
        for($i = 1;$i<=$page;$i++){
            CjYoukuVideos("电视剧",'2',$i,"178");
        }
        break;
    case "3":      //综艺
        for($i = 1;$i<=$page;$i++){
             CjYoukuVideos("综艺",'3',$i,"179");
        }
        break;
    case "131":    //动漫
        for($i = 1;$i<=$page;$i++){
            CjYoukuVideos("动漫",'131',$i,"180");
        }
        break;
}

function CjYoukuVideos($category,$type_id,$page,$pid){

    $url = replaceStr(CATEGORY,"{category}",$category);
    $url = replaceStr($url,"{page}",$page);
    $playUrlsContent = getPageInfo($url);
    $category_content = $playUrlsContent->shows;

    if(is_array($category_content)){
     foreach($category_content as $siteObject){
         ProcessVideos($siteObject,$type_id,$pid);

     }
    }
}

function  ProcessVideos($siteObject,$type_id,$pid){
    global $db;
    $p_playtype = 'youku';

    $titlecode=$siteObject->name;
    $sql="select m_id,m_name,m_type,m_area,m_playfrom,m_starring,m_directed,m_pic,m_content,m_year,m_addtime,m_urltest,m_zt,m_pid,m_typeid,m_hits,m_playserver,m_state from {pre}cj_vod where m_pid='".$pid."' and m_name='".$titlecode."'  and m_playfrom='".$p_playtype."'  order by m_id desc";
    $rowvod=$db->getRow($sql);


    if ($rowvod) {
        $movieid=$rowvod["m_id"];
        $lzcode =$siteObject->episode_updated;
        if($type_id ==="3"){
            $remarks = $siteObject->episode_updated;
        }else{
            $remarks= $siteObject->episode_count;
        }
        $sql = "update {pre}cj_vod set m_addtime='".date('Y-m-d H:i:s',time())."',m_zt='0',m_typeid='".$type_id."',m_playserver='0',m_state='".$lzcode."',m_remarks='".$remarks."' where m_id=".$movieid;
        $db->query($sql);
    }
    else{
        $content = getYoukuInfo($siteObject->id,$type_id);
        if($type_id ==="3"){
           $remarks = $content->episode_updated;
        }else{
            $remarks= $content->episode_count;
        }
        $m_year ='';
        if ($content->published !== '') {
            $m_year = explode('-',$content->published);
            $m_year = $m_year[0];
        }
        $sql="insert {pre}cj_vod (m_name,m_type,m_area,m_playfrom,m_starring,m_directed,m_content,m_year,m_zt,m_pid,m_typeid,m_hits,m_playserver,m_state,m_addtime,m_language,m_remarks) values( '".$content->name."','".$content->genre."','".$content->area."','".$p_playtype."','".$content->starring."','".$content->director."','".$content->description."','".$m_year."','0','".$pid."','".$type_id."','0','','".$content->episode_updated."','".date('Y-m-d H:i:s',time())."','"."其他"."','".$remarks."')";
        $db->query($sql);
        $movieid= $db->insert_id();
    }
    getYoukuUrl($siteObject->id,$siteObject->episode_count,$movieid,$type_id,$pid);

}


function getYoukuInfo($id,$type_id){
    $url = replaceStr(SHOWINFO,"{id}",$id);
    $playUrlsInfo = getPageInfo($url);
    $director = '';
    $starring = '';
    if($type_id === "1" || $type_id === "2"){
        if($playUrlsInfo->attr->director !== null){
            foreach($playUrlsInfo->attr->director as $d_item){
                if(isN($director)){
                    $director = $d_item->name;
                }else{
                    $director = $director."/".$d_item->name;
                }
            }
        }
        if($playUrlsInfo->attr->performer !== null){
        foreach($playUrlsInfo->attr->performer as $s_item){
            if(isN($starring)){
                $starring = $s_item->name;
            }else{
                $starring = $starring."/".$s_item->name;
            }
        }
        }
    }else if($type_id === "3"){
        $director = $playUrlsInfo->attr->tv_station->name;
        if($playUrlsInfo->attr->host !== null){
        foreach($playUrlsInfo->attr->host as $s_item){
            if(isN($starring)){
                $starring = $s_item->name;
            }else{
                $starring = $starring."/".$s_item->name;
            }
        }
        }
    }else if($type_id === "131"){
        if($playUrlsInfo->attr->director !== null){
        foreach($playUrlsInfo->attr->director as $d_item){
            if(isN($director)){
                $director = $d_item->name;
            }else{
                $director = $director."/".$d_item->name;
            }
        }
        }
        if($playUrlsInfo->attr->voice !== null){
        foreach($playUrlsInfo->attr->voice as $s_item){
            if(isN($starring)){
                $starring = $s_item->name;
            }else{
                $starring = $starring."/".$s_item->name;
            }
        }
        }
    }

    $playUrlsInfo->director = $director;
    $playUrlsInfo->starring = $starring;
    return $playUrlsInfo;
}


function getYoukuUrl($id,$count,$movieid,$type_id,$pid){
    global $db;
    if($type_id ==="3"){
        $count = "100&orderby=videoseq-desc";
    }else if ($type_id ==="1"){
        $count = 1;
    }else if($count>100){
        $count=100;
    }
    $url = replaceStr(VIDEOS,"{id}",$id);
    $url = replaceStr($url,"{count}",$count);
    $urlContent = getPageInfo($url);
    $webUrls = $urlContent->videos;
    foreach ($webUrls as $webUrlItem){
        $contentObject =ContentProviderFactory::getContentProvider("youku");
        $androidUrl = $contentObject->parseAndroidVideoUrl($webUrlItem->link, "", "");
        $videoAddressUrl = $contentObject->parseIOSVideoUrl($webUrlItem->link, "", "");
        if($type_id === "3"){
            $name = $webUrlItem->stage.$webUrlItem->title;
        }else{
            $name = $webUrlItem->stage;
        }

        $sql="SELECT {pre}cj_vod_url.u_id FROM ({pre}cj_vod_url INNER JOIN {pre}cj_vod ON {pre}cj_vod_url.u_movieid = {pre}cj_vod.m_id)  where {pre}cj_vod_url.name='" . $name . "' and {pre}cj_vod.m_pid=" . $pid . " and {pre}cj_vod.m_id=" . $movieid;
        $rowurl = $db->getRow($sql);
        if (empty($rowurl)) {
            $db->query("insert into {pre}cj_vod_url(u_movieid,u_weburl,iso_video_url,name,android_vedio_url) values('".$movieid."','".$webUrlItem->link."','".$videoAddressUrl."','".$name."' ,'".$androidUrl."' )");
        }
    }
}

function getPageInfo($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $html_content = curl_exec($ch);
    curl_close($ch);
    $html_content = json_decode($html_content);
    return $html_content;
}