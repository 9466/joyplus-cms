<?php

require_once ("MovieType.php");
require_once ("tools/ContentManager.php");

  global $db;
  
  
  
  $sql = "SELECT webUrls,d_downurl, d_playfrom,d_id FROM {pre}vod";
  $rs = $db->query($sql); 
  parseVodPad($rs);
  function parseVodPad($rs){
    	global $db;
        while ($row = $db ->fetch_array($rs))	{
    		$webUrls=$row["webUrls"];
    		$d_downurl=$row["d_downurl"]; 
    		$d_playfrom=$row["d_playfrom"];
			$d_id=$row["d_id"];	
			if(strpos($d_playfrom, 'letv_v2_fee') ===false && strpos($d_playfrom, 'letv_v2') ===false &&(strpos($d_playfrom, 'le_tv_fee') !==false || strpos($d_playfrom, 'letv') !==false)){
		    UpdateLetvV2VideoUrl($webUrls,$d_downurl,$d_playfrom,$d_id);
			}else{
				continue;
			}
	    }
	    unset($rs);
    }
  
  function UpdateLetvV2VideoUrl($webUrls,$d_downurl,$d_playfrom,$d_id){
  	 global $db;
  	 $from =''; 
  	 $d_from =''; 
  	 $n_from =''; 
  	 $strSet="";
     $playurlarr1 = explode("$$$",$webUrls);
     if(strpos($d_playfrom, 'le_tv_fee') !==false && strpos($d_playfrom, 'letv') !==false){
     	BothLetvV2VideoUrl($webUrls,$d_downurl,$d_playfrom,$d_id);
     }
     else if(strpos($d_playfrom, 'le_tv_fee') !==false){
	   $from='le_tv_fee';
	   $d_from='letv_v2_fee';
	   $n_from=$d_playfrom."$$$".'letv_v2_fee';
	   $n_from=replaceStr($n_from, '$$$$$$', '$$$');
	   $strSet.= " d_playfrom='".$n_from."'";
	 }else if(strpos($d_playfrom, 'letv') !==false){
	   $from='letv';
	   $d_from='letv_v2';
	   $n_from=$d_playfrom."$$$".'letv_v2';
	   $n_from=replaceStr($n_from, '$$$$$$', '$$$');
	   $strSet.= " d_playfrom='".$n_from."'";
	 }
	 
	 $playfromarr = explode("$$$",$d_playfrom);
	 
  $playurl='';
	   for ($i=0;$i<count($playurlarr1);$i++){
            if(!isN($playurlarr1[$i])){
	           $playfrom = $playfromarr[$i];
	           if($playfrom === $from){
                   $playurl =$playurlarr1[$i];
                   $playurl=$webUrls."$$$".$playurl;
		   	       $playurl=replaceStr($playurl, '$$$$$$', '$$$');
		   	       $strSet.= " ,webUrls='".$playurl."'";
                   break;
	           }
            }
	   }
	   
   $videoUrl=$d_from.'$$';
  if (isN($d_downurl)){
	   	  	$d_downurl=$videoUrl;
	   	  }else {
		   	  $d_downurlArray = explode("$$$", $d_downurl);  		   	  
		   	  foreach ($d_downurlArray as $downUrls){
		   	  	$downUrlsArray = explode("$$", $downUrls);
		   	  	if($downUrlsArray[0]===$from){
		   	  		$videoUrl=$d_from.'$$'.$downUrlsArray[1];
		   	  		break;
		   	  	}
		   	  }
		   	  $d_downurl=$d_downurl."$$$".$videoUrl;
		   	  $d_downurl=replaceStr($d_downurl, '$$$$$$', '$$$');
		   	  $strSet.= " ,d_downurl='".$d_downurl."'";
	   	  }
	   	  
   //if(!isN($videoUrl) && strpos($videoUrl, "http") !==false){
		  $strSet.= " ,d_time='".date('Y-m-d H:i:s',time())."'";
	   	  $sql= "update {pre}vod set ".$strSet." where d_id=" .$d_id;
	   	  writetofile('insertLetvV2.log', $sql);
	   	  $db->query($sql);
	 //  }
  }
  function BothLetvV2VideoUrl($webUrls,$d_downurl,$d_playfrom,$d_id){
  	 global $db;
  	 $strSet="";
     $playurlarr1 = explode("$$$",$webUrls);
	   $from_1='le_tv_fee';
	   $from_2='letv';
	   $d_from_1='letv_v2_fee';
	   $d_from_2='letv_v2';
	   $n_from=$d_playfrom."$$$".'letv_v2$$$letv_v2_fee';
	   $n_from=replaceStr($n_from, '$$$$$$', '$$$');
	   $strSet.= " d_playfrom='".$n_from."'";
	 
	 $playfromarr = explode("$$$",$d_playfrom);
	 
  $playurl='';
	   for ($i=0;$i<count($playurlarr1);$i++){
            if(!isN($playurlarr1[$i])){
	           $playfrom = $playfromarr[$i];
	           if($playfrom === $from_1 || $playfrom === $from_2){
	           	if($playurl ===''){
	           		$playurl =$playurlarr1[$i];
	           	}else{
	           		$playurl =$playurl.'$$$'.$playurlarr1[$i];
	           	}
	           }
            }
	   }
	   $playurl=$webUrls."$$$".$playurl;
	   $playurl=replaceStr($playurl, '$$$$$$', '$$$');
	   $strSet.= " ,webUrls='".$playurl."'";
	   
   $videoUrl='';
  if (isN($d_downurl)){
	   	  	$d_downurl=$videoUrl;
	   	  }else {
		   	  $d_downurlArray = explode("$$$", $d_downurl);  		   	  
		   	  foreach ($d_downurlArray as $downUrls){
		   	  	$downUrlsArray = explode("$$", $downUrls);
		   	  	if($downUrlsArray[0]===$from_1){
		   	  		if($videoUrl ===''){
		   	  		    $videoUrl=$d_from_1.'$$'.$downUrlsArray[1];
		   	  		}else{
		   	  			$videoUrl=$videoUrl.'$$$'.$d_from_1.'$$'.$downUrlsArray[1];
		   	  		}
		   	  	}else if($downUrlsArray[0]===$from_2){
		   	  		if($videoUrl ===''){
		   	  		   $videoUrl=$d_from_2.'$$'.$downUrlsArray[1];
		   	  		}else{
		   	  			$videoUrl=$videoUrl.'$$$'.$d_from_2.'$$'.$downUrlsArray[1];
		   	  		}
		   	  	}
		   	  }
		   	  $d_downurl=$d_downurl."$$$".$videoUrl;
		   	  $d_downurl=replaceStr($d_downurl, '$$$$$$', '$$$');
		   	  $strSet.= " ,d_downurl='".$d_downurl."'";
	   	  }
  // if(!isN($videoUrl) && strpos($videoUrl, "http") !==false){
		  $strSet.= " ,d_time='".date('Y-m-d H:i:s',time())."'";
	   	  $sql= "update {pre}vod set ".$strSet." where d_id=" .$d_id;
	   	  writetofile('insertLetvV2.log', $sql);
	   	  $db->query($sql);
	//   }
  }
  
  
  
  ?>