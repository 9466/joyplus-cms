<?php
/**
 * Created by PhpStorm.
 * User: yangliu
 * Date: 14-2-18
 * Time: 下午3:11
 */
require_once ("../admin_conn.php");
require_once ("collect_fun.php");
require_once("api_collect_youku_cj.php");
headAdminCollect ("优酷视频采集");

define("VIDEOS","https://openapi.youku.com/v2/shows/videos.json?client_id=&show_id={id}&page=1&count={count}");
define("SHOWINFO","https://openapi.youku.com/v2/shows/show.json?client_id=&show_id={id}");

$action = be("get","action");

$contentparmStart="showid_en=\"";
$contentparaend="\";";

if(isset($action) && $action ==='collectSimpl'){
    $type_id = be("all","type_id");
    $web_url = be("all","web_url");
    $site_url = be("all","site_url");


    $id ='';
    if(!isN($web_url)){
       $content = getPage($web_url,'utf-8');
       $id = getBody($content,$contentparmStart,$contentparaend);
    }else if(!isN($site_url) && preg_match('/id_z(\w.+?).html/',$site_url)){
        $ids = preg_match_all('/id_z(\w.+?).html/',$site_url,$match);
        $id = $match[1][0];
    }else{
         errmsg ("采集提示","采集信息不能为空");
    }
    $content = getYoukuInfo($id,$type_id);

    $pid = '';
    $category = "电影";
    switch($type_id){
        case "1":      //电影
            $pid = "177";
            $category = "电影";
            break;
        case "2":      //电视剧
            $pid = "178";
            $category = "电视剧";
            break;
        case "3":      //综艺
            $pid = "179";
            $category = "综艺";
            break;
        case "131":    //动漫
            $pid = "180";
            $category = "动漫";
            break;
    }
    if($id === '' || $pid === ''){
        errmsg ("采集提示","采集信息获取错误");
    }

    if($category !== $content->category){
        errmsg ("采集提示","错误：非所选视频类型");
    }

    ProcessVideos($content,$type_id,$pid);
    showmsg ("优酷视频采集完成！","collect_vod_youku.php");
    function  ProcessVideos($content,$type_id,$pid){
        global $db;
        $p_playtype = 'youku';

        $titlecode=$content->name;
        $sql="select m_id,m_name,m_type,m_area,m_playfrom,m_starring,m_directed,m_pic,m_content,m_year,m_addtime,m_urltest,m_zt,m_pid,m_typeid,m_hits,m_playserver,m_state from {pre}cj_vod where m_pid='".$pid."' and m_name='".$titlecode."'  and m_playfrom='".$p_playtype."'  order by m_id desc";
        $rowvod=$db->getRow($sql);


        if ($rowvod) {
            $movieid=$rowvod["m_id"];
            $lzcode =$content->episode_updated;
            if($type_id ==="3"){
                $remarkscode = $content->episode_updated;
            }else{
                $remarkscode= $content->episode_count;
            }
            $sql = "update {pre}cj_vod set m_addtime='".date('Y-m-d H:i:s',time())."',m_zt='0',m_typeid='".$type_id."',m_playserver='0',m_state='".$lzcode."',m_remarks='".$remarkscode."' where m_id=".$movieid;
            $db->query($sql);
        }else{
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
        getYoukuUrl($content->id,$content->episode_count,$movieid,$type_id,$pid);

    }


}
?>
<form action="collect_vod_youku.php?action=collectSimpl" method="post" id="form1" name="form1">
<table class="tb">
    <tr>
      <td width="20%" >项目：</td>
      <td>
          <select id="type_id" name="type_id">
              <option value="1">优酷电影</option>
              <option value="2">优酷电视剧</option>
              <option value="3">优酷综艺</option>
              <option value="131">优酷动漫</option>
          </select>
</td>
</tr>
<tr>
    <td width="20%" >视频单集网页地址：</td>
    <td>
        <INPUT id="site_url" name="web_url" size="100" value="" >
    </td>
</tr>

<tr>
    <td width="20%" >视频详情介绍网页地址：</td>
    <td>
        <INPUT id="name" name="site_url" size="100" value="" >
    </td>
</tr>



<tr>
    <td  colspan="2"  ><input type="submit" class="btn" id="btnNext1" name="btnNext" value="下一步"></td>

</tr>
</table>
</form>
