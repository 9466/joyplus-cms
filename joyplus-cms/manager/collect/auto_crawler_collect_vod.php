<?php
require_once ("../admin_conn.php");
require_once ("collect_fun.php");
require_once ("MovieType.php");
require_once ("NBaidu.php");
require_once ("tools/ContentManager.php");
require_once ("collect_vod.php");
require_once ("../score/AutoDouBanParseScore.php");


$host=$_SERVER['HTTP_HOST'];
 $crontab = be("all", "crontab");
crawler($crontab);

exit(-1);

function crawler($crontab){	
     global $db;
     writetofile("crawler_collect.sql", 'crawler start: crontab: ' .$crontab); 
	
    $sql = "SELECT * FROM mac_cj_zhuiju  where status=0 and crontab_desc like'%".$crontab."%' GROUP BY m_urltest order by m_urltest ";
     writetofile("crawler_collect.sql", 'crawler start: sql: ' .$sql); 
    $rs = $db->query($sql);
    parseVodPad($rs);
    unset($rs);
	

	 writetofile("crawler_collect.sql", 'crawler stop.');
	 collect($crontab);
}
 
function collect($crontab){
	writetofile("crawler_collect.sql", 'collect start.');
	global $db;
	$time=date("Y-m-d");
	$count = $db->getOne("SELECT count(*) FROM mac_cj_zhuiju  where status=0 and crontab_desc like'%".$crontab."%'  ");
	$sql="select a.* from {pre}cj_vod as a , mac_cj_zhuiju as b where a.m_id= b.m_id and b.crontab_desc like'%".$crontab."%' and a.m_typeid>0 and a.m_name IS NOT NULL AND a.m_addtime like'%".$time."%' and a.m_name != '' and a.m_playfrom not in ('tudou','kankan','cntv','wasu')";
    
	MovieInflow($sql,$count,true);
	writetofile("crawler_collect.sql", 'collect stop.');
}

    function parseVodPad($rs){
        while ($row =@mysql_fetch_array($rs))	{
    		$m_urltest=$row["m_urltest"];
			$m_pid=$row["m_pid"];
			$m_typeid=$row["m_typeid"];
			$m_playfrom=$row["m_playfrom"];
			$m_id=$row["m_id"];
            $id ='';
            if(!isN($m_urltest) && preg_match('/movie\/(\w.+?).htm/',$m_urltest)){
                preg_match_all('/movie\/(\w.+?).htm/',$m_urltest,$match);
                $id = $match[1][0];
            }else if(!isN($m_urltest) && preg_match('/tv\/(\w.+?).htm/',$m_urltest)){
                preg_match_all('/tv\/(\w.+?).htm/',$m_urltest,$match);
                $id = $match[1][0];
            }else if(!isN($m_urltest) && preg_match('/show\/(\w.+?).htm/',$m_urltest)){
                preg_match_all('/show\/(\w.+?).htm/',$m_urltest,$match);
                $id = $match[1][0];
            }else if(!isN($m_urltest) && preg_match('/comic\/(\w.+?).htm/',$m_urltest)){
                preg_match_all('/comic\/(\w.+?).htm/',$m_urltest,$match);
                $id = $match[1][0];
            }
            cjBaiduView($m_id,$id,$m_pid,$m_typeid,$m_playfrom);
	        unset($rs);
        }
    }



function cjBaiduView($m_id,$id,$p_id,$p_collect_type,$m_playfrom){

    if (isN($id) || $id ==="") {
        writetofile("auto_crawel_error.log", $id);
        return;
    }else{

        $info = NBaidu::parseMovieInfoById($id,$p_collect_type);


        //视频连载备注信息
        if ($p_collect_type ==='2' || $p_collect_type ==='131'){
            $lzcode = $info->max_episode;
            $remarksarr = NBaidu::getRemarks($id,$p_collect_type);
            $remarkscode ='';
            if($remarksarr["remarkcode"] !=="" && $remarksarr["remarkcode"] !=="0"){
                $remarkscode = $remarksarr["remarkcode"];
            }

        }else{
            $lzcode = $info->max_episode;
            $remarkscode = $lzcode;
        }

        $lzcode = trim($lzcode);
        try{
            $lzcode = intval($lzcode);
        }catch(Exception $e){
            $lzcode=0;
        }

        $weburl=$info->sites;

        if (count($weburl) <=0) {
            echo "<tr><td colspan=\"2\" align=\"center\"><span style=\"color:red\">第条数据采集结果----获取播放列表链接时出错</td></tr><tr><td width=\"20%\" >来源：</td><td ></td></tr><tr><td width=\"20%\" >名称：</td><td > 连载:".$lzcode." 备注:".$remarkscode."</span></br></td></tr>";
            writetofile("crawel_error.log", $p_collect_type.'{=====}{=====}');
            return;
        }else{
            echo "<tr><td colspan=\"2\" align=\"center\">第条数据采集结果</td></tr><tr><td width=\"20%\" >来源：</td><td ></td></tr><tr><td width=\"20%\" >名称：</td><td > 连载:".$lzcode." 备注:".$remarkscode."</br></td></tr>";
            foreach ($weburl as $weburlitem){
                $p_playtypebaiduweb = $weburlitem['site_name'];
                $baiduwebUrls=$weburlitem['episodes'];
                if($m_playfrom === $p_playtypebaiduweb){
                    updateVod($m_id,$baiduwebUrls,$p_id,$p_collect_type,$p_playtypebaiduweb,$lzcode,$remarkscode);
                }
            }

        }
    }
}

function updateVod($m_id,$baiduwebUrls,$p_id,$m_typeid,$p_playtype,$lzcode,$remarkscode){
    global $db;

    $sql="select m_id from {pre}cj_vod where m_id=".$m_id;
    $rowvod=$db->getRow($sql);
    if(!$rowvod){
        return;
    }


    $sql = "update {pre}cj_vod  set m_playfrom='".$p_playtype."', m_addtime='".date('Y-m-d H:i:s',time())."', m_zt='0', m_pid='".$p_id."', m_typeid='".$m_typeid."', m_state='".$lzcode."', m_remarks='".$remarkscode."' where m_id=".$m_id;
    writetofile("sql.txt", $sql);
    $db->query($sql);

    foreach ($baiduwebUrls as $baiduweburl){
        if(array_key_exists('url', $baiduweburl)){
            $WebTestx = $baiduweburl['url'];
        }else {
            continue;
        }

        if(array_key_exists('img_url', $baiduweburl)){
            $picurl = $baiduweburl['img_url'];
        }else {
            $picurl='';
        }

        writetofile("crawel_auto_info.log", $p_id.'{=====}'.$WebTestx ."{=====}ViewList===start");

        $url = "";
        if(array_key_exists('episode', $baiduweburl)){
            $setname= $baiduweburl['episode'];
        }else {
            $setname='';
        }

        if($m_typeid ==3){
            if(array_key_exists('name', $baiduweburl)){
                $setname=$setname. ' '.$baiduweburl['name'];
            }

        }
        $setname=trim($setname);

        $sql="SELECT {pre}cj_vod_url.u_url,{pre}cj_vod_url.u_id FROM ({pre}cj_vod_url INNER JOIN {pre}cj_vod ON {pre}cj_vod_url.u_movieid = {pre}cj_vod.m_id)  where {pre}cj_vod_url.name='" . $setname . "' and {pre}cj_vod.m_pid=" . $p_id . " and {pre}cj_vod.m_id=" . $m_id;

        $rowurl = $db->getRow($sql);

        if (empty($rowurl)) {
            $contentObject =ContentProviderFactory::getContentProvider($p_playtype);
            $androidUrl = $contentObject->parseAndroidVideoUrl($WebTestx, '', '');
            $videoAddressUrl = $contentObject->parseIOSVideoUrl($WebTestx, '', '');
            writetofile("android_log.txt", $WebTestx.'{===}'.$androidUrl .'{===}'.$videoAddressUrl );

            writetofile("sql.txt","insert into {pre}cj_vod_url(u_url,u_movieid,u_weburl,iso_video_url,name,android_vedio_url) values('".$url."','".$m_id."','".$WebTestx."','".$videoAddressUrl."','".$setname."' ,'".$androidUrl."' )");
            $db->query("insert into {pre}cj_vod_url(pic,u_url,u_movieid,u_weburl,iso_video_url,name,android_vedio_url) values('".$picurl."','".$url."','".$m_id."','".$WebTestx."','".$videoAddressUrl."','".$setname."' ,'".$androidUrl."' )");
        }
    }

}
    
  
	
	

?>