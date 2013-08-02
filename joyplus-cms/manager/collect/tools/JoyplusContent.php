<?php
require_once (dirname(__FILE__)."/ContentManager.php");

class JoyplusContent extends Content{
	
	public  $from;
	private $p_videocodeApiUrlParamstart="\"rid\":\"";
	private $p_videocodeApiUrlParamend="\"";
	private $p_videocodeApiUrl="http://xxxxxx/"; //letv/?url=http://www.letv.com/ptv/pplay/85827.html
	private $p_videourlstart="\"data\":\"";
	private $p_videourlend="\"}";  //http://v.pptv.com/show/rOeRD3fdTYvubNQ.html
	
  	private $p_code="UTF-8";
    public function parseAndroidVideoUrl($url,$p_coding,$p_script){
      return "";
  	}
  	public function parseAndroidVideoUrlByContent($content, $p_coding,$p_script){
  		return "";
  	}
    public function checkHtmlCanPlay($url,$p_coding){
  		$content = getPage($url, $this->p_code);
  		return false;
  	}
  	
    public function parseIOSVideoUrl($url,$p_coding,$p_script){
    	
  		$content = getPage($this->p_videocodeApiUrl.$this->from.'/?url='. $url, "utf-8");  
//  		var_dump($url);		
     writetofile("joyplus.log", $this->p_videocodeApiUrl.$this->from.'/?url='. $url);
  		return $this->parseIOSVideoUrlByContent($content, $p_coding,$p_script);
  	}
  	
  	public function parseIOSVideoUrlByContent($content, $p_coding,$p_script){
  		$videoAddressUrl="";
  		$contentObj= json_decode($content); 
  		if(is_object($contentObj) && property_exists($contentObj, 'down_urls') && property_exists($contentObj->down_urls, 'urls')){  			
  			if(is_array($contentObj->down_urls->urls)){
  				foreach ($contentObj->down_urls->urls as $item){
  					if(!isN( $item->url)){
	  					if(isN($videoAddressUrl)){
	  						$videoAddressUrl=$item->type .MovieType::VIDEO_NAME_URL_SEP. $item->url;
	  					}else {
	  						$videoAddressUrl=$videoAddressUrl. MovieType::VIDEO_SEP_VERSION. $item->type .MovieType::VIDEO_NAME_URL_SEP. $item->url;
	  					}
  					}
  				}
  			}
  		}
  		//var_dump($videoAddressUrl);
		return $videoAddressUrl;
  	}
  }
?>