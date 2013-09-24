<?php
require_once ("ContentManager.php");
require_once ("ContentManager.php");
class LetvContent extends Content{
//3gphd ,3gp
	const BASE_URL="http://m.letv.com/playvideo.php?id={id}&mmsid={mmsid}";
	
	private $contentparmStart="vid:";
  	private $contentparaend=",";
  	private $contentparmStart2="mmsid:";
  	private $contentparaend2=",";
  	private $notfound='/wap/wrong';
  	private $p_code="UTF-8";

    public function parseAndroidVideoUrl($url,$p_coding="UTF-8",$p_script){    	
  		//$content = getPageWindow($url, $this->p_code);
  		return "";
  	//	writetofile('stds.log',$content);var_dump($url);
//  		return $this->parseAndroidVideoUrlByContent($content, $p_coding,$p_script);
  	}
  	public function parseAndroidVideoUrlByContent($content, $p_coding,$p_script){
  		return "";
//  		$vid = getBody($content,$this->contentparmStart,$this->contentparaend); 
//	    $mmsid = getBody($content,$this->contentparmStart2,$this->contentparaend2); 
//	   // var_dump($mmsid); var_dump($vid);
//  		return $this->getAndroidVideoUrl($vid,$mmsid);
  	}
  	
  	private function getAndroidVideoUrl($vid,$mmsid){
  		$videoAddressUrl="";
  	  if(isset($vid) && !is_null($vid)){
  		  $url = replaceStr(LetvContent::BASE_URL,"{id}",$vid);
  		  //check gaoqing
  		  $hdurl = replaceStr($url,"{mmsid}",$mmsid);
//  		  var_dump($hdurl);
  		  //$location = getLocation($hdurl);
  		  
//  		  writetofile("daa.txt", $hdurl);
  		 // if(!isN($location) && strpos($location, $this->notfound) ===false){
  		  	$videoAddressUrl=$videoAddressUrl.MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.$hdurl;			 
  		 // }  		    		  
  		}
  		return $videoAddressUrl;
  	}
    public function checkHtmlCanPlay($url,$p_coding){
  		$content = getPage($url, $this->p_code);
  		return false;
  	}
    public function parseIOSVideoUrl($url,$p_coding,$p_script){
  		$content = getPageWindow($url, $this->p_code);
  		return $this->parseIOSVideoUrlByContent($content, $p_coding,$p_script);
  	}
  	
  	private $p_videourlstart="vid:";
	private $p_videourlend=",//视频ID";  //http://v.pptv.com/show/rOeRD3fdTYvubNQ.html
  	public function parseIOSVideoUrlByContent($content, $p_coding,$p_script){
  	   $videoUrlParam = getBody($content,$this->p_videourlstart,$this->p_videourlend);	
  	   
  	   $url=replaceStr("http://www.letv.com/v_xml/{VID}.xml", '{VID}', $videoUrlParam);
  	  
  	   $content = getPageWindow($url, $this->p_code);
  	   $content = getBody($content,'<playurl><![CDATA[',']]></playurl>');
  	    
  	    $contentObj= json_decode($content);
  	    if(is_object($contentObj) && property_exists($contentObj, 'dispatch')){
  	    	$contentObj= $contentObj->dispatch;
  	    	if(is_object($contentObj)){
  	    		$videoAddressUrl4="";
  		        $videoAddressUrl1="";
		  		$videoAddressUrl2="";
		  		$videoAddressUrl3="";
		  		//var_dump($contentObj);
		  		$contentObj=ContentProviderFactory::obj2arr($contentObj);		  		
		  		if( array_key_exists('720p', $contentObj)){
		  			$urlArray=$contentObj['720p'];
		  			if(is_array($urlArray)){
		  				$tempUrl =$urlArray[0];
		  				if(strpos($tempUrl, 'tss=ios') !==false){
		  				  	$tempUrl=$this->getUrl($tempUrl);
		  				}else {
		  					$tempUrl=replaceStr($tempUrl, 'tss=no', 'tss=ios');
		  				}
		  				$videoAddressUrl4=MovieType::TOP_CLEAR  .MovieType::VIDEO_NAME_URL_SEP.$tempUrl;		  			  
		  			}
		  		}
  	    	    if( array_key_exists('1300', $contentObj)){
		  			$urlArray=$contentObj['1300'];
		  			if(is_array($urlArray)){
		  				$tempUrl =$urlArray[0];
		  				if(strpos($tempUrl, 'tss=ios') !==false){
		  				  	$tempUrl=$this->getUrl($tempUrl);
		  				}else {
		  					$tempUrl=replaceStr($tempUrl, 'tss=no', 'tss=ios');
		  				}
		  				$videoAddressUrl1=MovieType::HIGH_CLEAR  .MovieType::VIDEO_NAME_URL_SEP.$tempUrl;		  			  
		  			}
		  		}
		  		
  	    	    if( array_key_exists('1000', $contentObj)){
		  			$urlArray=$contentObj['1000'];
		  			if(is_array($urlArray)){
		  				$tempUrl =$urlArray[0];
		  				if(strpos($tempUrl, 'tss=ios') !==false){
		  				  	$tempUrl=$this->getUrl($tempUrl);
		  				}else {
		  					$tempUrl=replaceStr($tempUrl, 'tss=no', 'tss=ios');
		  				}
		  				$videoAddressUrl2=MovieType::HIGH_CLEAR  .MovieType::VIDEO_NAME_URL_SEP.$tempUrl;		  			  
		  			}
		  		}
  	    	
  	    	    if( array_key_exists('350', $contentObj)){
		  			$urlArray=$contentObj['350'];
		  			if(is_array($urlArray)){
		  				$tempUrl =$urlArray[0];
		  				if(strpos($tempUrl, 'tss=ios') !==false){
		  				  	$tempUrl=$this->getUrl($tempUrl);
		  				}else {
		  					$tempUrl=replaceStr($tempUrl, 'tss=no', 'tss=ios');
		  				}
		  				$videoAddressUrl3=MovieType::NORMAL  .MovieType::VIDEO_NAME_URL_SEP.$tempUrl;		  			  
		  			}
		  		}
		  		
  	    	$flag=false;
  		     $videoAddressUrl='';
  	    	 if(!isN($videoAddressUrl4)){
	  		  	if($flag){
	  		  		$videoAddressUrl=$videoAddressUrl.MovieType::VIDEO_SEP_VERSION;
	  		  	}
	  		  	$videoAddressUrl=$videoAddressUrl.$videoAddressUrl4;
	  		  	$flag=true;
	  		  }
	  		  if(!isN($videoAddressUrl3)){
	  		  	if($flag){
	  		  		$videoAddressUrl=$videoAddressUrl.MovieType::VIDEO_SEP_VERSION;
	  		  	}
	  		  	$videoAddressUrl=$videoAddressUrl.$videoAddressUrl3;
	  		  	$flag=true;
	  		  }
	  		  if(!isN($videoAddressUrl2)){
	  		  	if($flag){
	  		  		$videoAddressUrl=$videoAddressUrl.MovieType::VIDEO_SEP_VERSION;
	  		  	}
	  		  	$videoAddressUrl=$videoAddressUrl.$videoAddressUrl2;
	  		  	$flag=true;
	  		  }
	  		  if(!isN($videoAddressUrl1)){
	  		  	if($flag){
	  		  		$videoAddressUrl=$videoAddressUrl.MovieType::VIDEO_SEP_VERSION;
	  		  	}
	  		  	$videoAddressUrl=$videoAddressUrl.$videoAddressUrl1;
	  		  	$flag=true;
	  		  }
	  		  return $videoAddressUrl;
  	    	}
  	    }
  	    return '';
  	}
  	
  	function getUrl($url){
  		$content = getPageWindow($url, $this->p_code);
  		 $contentObj= json_decode($content);
  		 if(is_object($contentObj) && property_exists($contentObj, 'location')){
  		   return ($contentObj->location);
  		 }
  	}
  }
?>