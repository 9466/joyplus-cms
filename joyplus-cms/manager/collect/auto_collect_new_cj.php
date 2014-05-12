<?php
/**
 * Created by PhpStorm.
 * User: yangliu
 * Date: 14-2-28
 * Time: 上午11:22
 */

ob_implicit_flush(true);
ini_set('max_execution_time', '0');
require_once ("../admin_conn.php");
require_once ("collect_fun.php");
require_once ("MovieType.php");
require_once ("NBaidu.php");
require_once ("tools/ContentManager.php");

	$p_id = be("all","p_id");
	/**
     * p_id=1_1-3 pid is 1 and page from 1 to 3
     * p_id=1_1,3,5 pid is 1 and page is 1,3,5
     * http://cmsdev.joyplus.tv/manager/collect/auto_collect_vod_cj.php?p_id=1_1-3
     */

	writetofile("crawel_auto_info.log", $p_id.'{=====}Project===start');

	if (isN($p_id)) { writetofile("crawel_auto_info.log", $p_id.'采集提示","采集项目ID不能为空!'); return; }
	$tem=explode("_", $p_id);
	if(count($tem)==2){
        $p_id=$tem[0];
        $pagenums=$tem[1];
    }else {
        $pagenums="1";
    }
	$db->query ("update {pre}cj_vod_projects set p_time='".date('Y-m-d H:i:s',time())."' where p_id=".$p_id);

	$sql = "select * from {pre}cj_vod_projects where p_id=".$p_id;
	$row= $db->getRow($sql);

	$p_id = $row["p_id"];
	$p_name = $row["p_name"];
	$p_coding = $row["p_coding"];
	$p_playtype = $row["p_playtype"];
	$p_pagetype = $row["p_pagetype"];
	$p_url = $row["p_url"];
	$p_pagebatchurl = $row["p_pagebatchurl"];
	$p_manualurl = $row["p_manualurl"];
	$p_pagebatchid1 = $row["p_pagebatchid1"];  $p_pagebatchid1 = intval($p_pagebatchid1);
	$p_pagebatchid2 = $row["p_pagebatchid2"];  $p_pagebatchid2 = intval($p_pagebatchid2);
	$p_script = $row["p_script"];
	$p_showtype = $row["p_showtype"];
	$p_collecorder = $row["p_collecorder"];
	$p_savefiles = $row["p_savefiles"];
	$p_ontime = $row["p_ontime"];
	$p_listcodestart = $row["p_listcodestart"];
	$p_listcodeend = $row["p_listcodeend"];
	$p_classtype = $row["p_classtype"];
	$p_collect_type = $row["p_collect_type"];
	$p_time = $row["p_time"];
	$p_listlinkstart = $row["p_listlinkstart"];
	$p_listlinkend = $row["p_listlinkend"];
	$p_starringtype = $row["p_starringtype"];
	$p_starringstart = $row["p_starringstart"];
	$p_starringend = $row["p_starringend"];
	$p_titletype = $row["p_titletype"];
	$p_titlestart = $row["p_titlestart"];
	$p_titleend = $row["p_titleend"];
	$p_pictype = $row["p_pictype"];
	$p_picstart = $row["p_picstart"];
	$p_picend = $row["p_picend"];
	$p_timestart = $row["p_timestart"];
	$p_timeend = $row["p_timeend"];
	$p_areastart = $row["p_areastart"];
	$p_areaend = $row["p_areaend"];
	$p_typestart = $row["p_typestart"];
	$p_typeend = $row["p_typeend"];
	$p_contentstart = $row["p_contentstart"];
	$p_contentend = $row["p_contentend"];
	$p_playcodetype = $row["p_playcodetype"];
	$p_playcodestart = $row["p_playcodestart"];
	$p_playcodeend = $row["p_playcodeend"];
	$p_playurlstart = $row["p_playurlstart"];
	$p_playurlend = $row["p_playurlend"];
	$p_playlinktype = $row["p_playlinktype"];
	$p_playlinkstart = $row["p_playlinkstart"];
	$p_playlinkend = $row["p_playlinkend"];
	$p_playspecialtype = $row["p_playspecialtype"];
	$p_playspecialrrul = $row["p_playspecialrrul"];
	$p_playspecialrerul = $row["p_playspecialrerul"];
	$p_server = $row["p_server"];
	$p_hitsstart = $row["p_hitsstart"];
	$p_hitsend = $row["p_hitsend"];
	$p_lzstart = $row["p_lzstart"];
	$p_lzend = $row["p_lzend"];
	$p_colleclinkorder = $row["p_colleclinkorder"];
	$p_lzcodetype = $row["p_lzcodetype"];
	$p_lzcodestart = $row["p_lzcodestart"];
	$p_lzcodeend = $row["p_lzcodeend"];
	$p_languagestart = $row["p_languagestart"];
	$p_languageend = $row["p_languageend"];
	$p_remarksstart = $row["p_remarksstart"];
	$p_remarksend = $row["p_remarksend"];
	$p_directedstart = $row["p_directedstart"];
	$p_directedend = $row["p_directedend"];
	$p_setnametype = $row["p_setnametype"];
	$p_setnamestart = $row["p_setnamestart"];
	$p_setnameend = $row["p_setnameend"];

	$p_videocodeApiUrl= $row["p_videocodeApiUrl"];
	$p_videocodeApiUrlParamstart= $row["p_videocodeApiUrlParamstart"];
	$p_videocodeApiUrlParamend= $row["p_videocodeApiUrlParamend"];
	$p_videourlstart= $row["p_videourlstart"];
	$p_videourlend= $row["p_videourlend"];
    $p_videocodeType= $row["p_videocodeType"];

	$playcodeApiUrl =$row["p_playcodeApiUrl"] ; $playcodeApiUrltype= $row["p_playcodeApiUrltype"] ;
	$p_playcodeApiUrlParamend = $row["p_playcodeApiUrlParamend"] ; $playcodeApiUrlParamstart=  $row["p_playcodeApiUrlParamstart"] ;
	if (isN($playcodeApiUrltype)) { $playcodeApiUrltype = 0;}
    if (isN($p_videocodeType)) { $p_videocodeType = 0;}



	$idarr=array();
	$starringarr=array();
    $linkarr = array();
	$titlearr=array();
	$picarr=array();
	$areaarr=array();
	$yeararr=array();
	$typearr=array();
    $finisharr=array();
	$introarr=array();
    $directorarr=array();
    $durationarr=array();
	$flag=false;
    $count=0;

	$reCollExistMovie = falses;

    if(strpos($pagenums, "-") !==false){
        $nums= explode("-", $pagenums);
        for($i=$nums[0];$i<=$nums[1];$i++){
            writetofile("crawel_auto_info.log", $p_id.'===Current Number{=====}'.$i);
            $strListUrl= replaceStr($p_pagebatchurl,"{ID}",$i);
            writetofile("crawel_auto_info.log", $p_id.'{=====}'.$strListUrl ."{=====}start");
            clearSession();
            cjList();
            writetofile("crawel_auto_info.log", $p_id.'{=====}'.$strListUrl ."{=====}end");
            writetofile("crawel_auto_info.log", $p_id.'===Current Number{=====}'.$i."{=====}end");
            }
        }

exit();



function cjList()
{
    global $db,$p_collect_type,$strListUrl,$p_coding,$count;
    global $p_playtype,$p_id,$idarr,$starringarr,$linkarr,$titlearr,$picarr,$areaarr,$yeararr,$typearr,$finisharr,$introarr,$directorarr,$durationarr;
    $strListCode = getPage($strListUrl,$p_coding);
    writetofile("crawel_auto_info.log", $p_id.'{=====}'.$strListUrl ."{=====}List===start");
    if($p_playtype ==='baidu' ){
        if( isN($_SESSION["strListCodeCut"] )){
            $_SESSION["strListCodeCut"] = $strListCode;
        }
        else{
            $strListCodeCut = $_SESSION["strListCodeCut"];
        }
        $baiduList = NBaidu::parseListByContent($strListCode, $p_coding, '');

        $idarr = $baiduList['idarr'];
        $linkarr = $baiduList['linkarr'];
        $starringarr = $baiduList['starringarr'];
        $titlearr = $baiduList['titlearr'];
        $picarr =$baiduList['picarr'];
        $areaarr =$baiduList['areaarr'];
        $yeararr =$baiduList['yeararr'];
        $typearr =$baiduList['typearr'];
        $finisharr =$baiduList['finisharr'];
        $introarr =$baiduList['introarr'];
        $directorarr =$baiduList['directorarr'];
        $durationarr =$baiduList['durationarr'];

    }


        for ($i=0 ;$i<count($idarr);$i++){
            $idMo=$idarr[$i];
            if($p_playtype ==='baidu'){
                $count++;
                 cjBaiduView($idMo,$i);
            }else {
                 cjView($idMo,$i);
            }

        }
    clearSession();

}


function cjBaiduView($id,$num){
    global $p_id,$idarr,$linkarr,$starringarr,$titlearr,$picarr,$areaarr,$yeararr,$typearr,$p_collect_type,$finisharr,$introarr,$directorarr,$durationarr;
    global $count;

//    echo "<tr><td colspan=\"2\">开始采集：".$id." / '.$linkarr[$num].'</br> </TD></TR>";


    if (isN($id) || $id ==="") {
//        echo "<tr><td colspan=\"2\">在获取内容页时出错：".$id." / '.$linkarr[$num].' </br></TD></TR>";
        writetofile("crawel_error.log", $id.'{=====}'.$linkarr[$num]);
        return;
    }else{

        $info = NBaidu::parseMovieInfoById($id,$p_collect_type);

//        echo "<tr><td colspan=\"2\">在获取内容页时success ：".$id." / '.$linkarr[$num].'</br> </TD></TR>";
        //节目名称，来自列表或者来自内容页
        $titlecode = $titlearr[$num];

//        $titlecode =replaceStr($titlecode, "&nbsp;", ' ');
//        $titlecode = filterScript($titlecode,"gb2312");
//        $titlecode = replaceStr(replaceStr(replaceStr($titlecode,","," "),"'",""),"\"\"","");
//        $titlecode =replaceStr($titlecode, "&nbsp;", ' ');

        $titlecode = trim($titlecode);

        if(isN($titlecode) || $titlecode === ""){
            echo "<tr><td colspan=\"2\" align=\"center\"><span style=\"color:red\">第".($count)."条数据采集结果----获取播放列表链接时出错</td></tr><tr><td width=\"20%\" >来源：</td><td >".$strlink."</td></tr><tr><td width=\"20%\" >名称：</td><td >".$titlecode." 连载:".$lzcode." 备注:".$remarkscode."</span></br></td></tr>";
            writetofile("crawel_title_error.log", $id.'{=====}{=====}');
            return;
        }


        //百度网页地址
        $strlink= $linkarr[$num];

        //是否完结
        $isfinish = $finisharr[$num];

        //视频连载备注信息
        if ($isfinish ==="0" && ($p_collect_type ==='2' || $p_collect_type ==='131')){
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


        //演员信息
        $starringcode = $starringarr[$num];
        $starringcode = trim($starringcode);


        //图片

        //栏目设置
        $typecode = $typearr[$num];
        $typecode = trim($typecode);

        //视频类型
        $m_typeid = $p_collect_type;

        //导演信息

        $directorcode = $directorarr[$num];
        $directorcode = trim($directorcode);

        //语言
        $languagecode='其他';


        //时间
        $timecode = $yeararr[$num];
        $timecode = trim($timecode);

        if(isN($timecode) || $timecode ===""){
            timecode == "未知" ;
        }


        //地区
        $areacode = $areaarr[$num];
        $areacode = trim($areacode);

        if(isN($areacode) || $areacode ===""){
            $areacode = "未知" ;
        }

        //内容
        $contentcode = $introarr[$num];
        $contentcode = trim($contentcode);
        if (isN($contentcode) || $contentcode ===""){
            $contentcode = "未知" ;
        }

        //时长
        $duration = $durationarr[$num];


        $weburl=$info->sites;

        if (count($weburl) <=0) {
            echo "<tr><td colspan=\"2\" align=\"center\"><span style=\"color:red\">第".($count)."条数据采集结果----获取播放列表链接时出错</td></tr><tr><td width=\"20%\" >来源：</td><td >".$strlink."</td></tr><tr><td width=\"20%\" >名称：</td><td >".$titlecode." 连载:".$lzcode." 备注:".$remarkscode."</span></br></td></tr>";
            writetofile("crawel_error.log", $m_typeid.'{=====}'.$strlink.'{=====}');
            return;
        }else{
            echo "<tr><td colspan=\"2\" align=\"center\">第".($count)."条数据采集结果</td></tr><tr><td width=\"20%\" >来源：</td><td >".$strlink."</td></tr><tr><td width=\"20%\" >名称：</td><td >".$titlecode." 连载:".$lzcode." 备注:".$remarkscode."</br></td></tr>";
            $piccode="";
            foreach ($weburl as $weburlitem){
                $p_playtypebaiduweb = $weburlitem['site_name'];
                $baiduwebUrls=$weburlitem['episodes'];
                updateVod($duration,$baiduwebUrls,$p_id,$titlecode,$piccode,$typecode,$areacode,$strlink,$starringcode,$directorcode,$timecode,$p_playtypebaiduweb,$contentcode,$m_typeid,$lzcode,$languagecode,$remarkscode);
            }

        }
    }
}

function updateVod($duration,$baiduwebUrls,$p_id,$titlecode,$piccode,$typecode,$areacode,$strlink,$starringcode,$directedcode,$timecode,$p_playtype,$contentcode,$m_typeid,$lzcode,$languagecode,$remarkscode){
    global $db,$cg,$p_coding,$p_script;
    $sql="select m_id,m_name,m_type,m_area,m_playfrom,m_starring,m_directed,m_pic,m_content,m_year,m_addtime,m_urltest,m_zt,m_pid,m_typeid,m_hits,m_playserver,m_state from {pre}cj_vod where m_pid='".$p_id."' and m_name='".$titlecode."'  and m_playfrom='".$p_playtype."'  order by m_id desc";

    $rowvod=$db->getRow($sql);

    if ($rowvod) {
        $cg=$cg+1;
        $movieid=$rowvod["m_id"];
        if(isN($titlecode)){
            $titlecode = $rowvod["m_name"];
        }

        if(isN($starringcode)){
            $starringcode = $rowvod["m_starring"];
        }

        if(isN($piccode)){
            $piccode = $rowvod["m_pic"];
        }
        $sql = "update {pre}cj_vod  set duraning='".$duration."' , m_pic='".$piccode."', m_type='".$typecode."',m_area='".$areacode."',m_urltest='".$strlink."',m_name='".$titlecode."',m_starring='".$starringcode."',m_directed='".$directedcode."',m_year='".$timecode."',m_playfrom='".$p_playtype."',m_content='".$contentcode."',m_addtime='".date('Y-m-d H:i:s',time())."',m_zt='0',m_pid='".$p_id."',m_typeid='".$m_typeid."',m_playserver='',m_state='".$lzcode."',m_language='".$languagecode."',m_remarks='".$remarkscode."' where m_id=".$rowvod["m_id"];
        writetofile("sql.txt", $sql);
        $db->query($sql);
    }
    else{
        $cg=$cg+1;
        $sql="insert {pre}cj_vod (duraning,m_name,m_type,m_area,m_playfrom,m_starring,m_directed,m_pic,m_content,m_year,m_urltest,m_zt,m_pid,m_typeid,m_hits,m_playserver,m_state,m_addtime,m_language,m_remarks) values('".$duration."', '".$titlecode."','".$typecode."','".$areacode."','".$p_playtype."','".$starringcode."','".$directedcode."','".$piccode."','".$contentcode."','".$timecode."','".$strlink."','0','".$p_id."','".$m_typeid."','0','','".$lzcode."','".date('Y-m-d H:i:s',time())."','".$languagecode."','".$remarkscode."')";
        writetofile("sql.txt", $sql);
        $db->query($sql);
        $movieid= $db->insert_id();
    }
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

        $sql="SELECT {pre}cj_vod_url.u_url,{pre}cj_vod_url.u_id FROM ({pre}cj_vod_url INNER JOIN {pre}cj_vod ON {pre}cj_vod_url.u_movieid = {pre}cj_vod.m_id)  where {pre}cj_vod_url.name='" . $setname . "' and {pre}cj_vod.m_pid=" . $p_id . " and {pre}cj_vod.m_id=" . $movieid;

        $rowurl = $db->getRow($sql);

        if (empty($rowurl)) {
            $contentObject =ContentProviderFactory::getContentProvider($p_playtype);
            $androidUrl = $contentObject->parseAndroidVideoUrl($WebTestx, $p_coding, $p_script);
            $videoAddressUrl = $contentObject->parseIOSVideoUrl($WebTestx, $p_coding, $p_script);
            writetofile("android_log.txt", $WebTestx.'{===}'.$androidUrl .'{===}'.$videoAddressUrl );

            writetofile("sql.txt","insert into {pre}cj_vod_url(u_url,u_movieid,u_weburl,iso_video_url,name,android_vedio_url) values('".$url."','".$movieid."','".$WebTestx."','".$videoAddressUrl."','".$setname."' ,'".$androidUrl."' )");
            $db->query("insert into {pre}cj_vod_url(pic,u_url,u_movieid,u_weburl,iso_video_url,name,android_vedio_url) values('".$picurl."','".$url."','".$movieid."','".$WebTestx."','".$videoAddressUrl."','".$setname."' ,'".$androidUrl."' )");
        }
    }

}

function cjView($strlink,$num)
{
}


?>