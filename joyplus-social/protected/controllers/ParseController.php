<?php

class ParseController extends Controller
{
	
	public $p_videocodeApiUrl=""; //letv/?url=http://www.letv.com/ptv/pplay/85827.html
	
    function actionRealAnalyze(){
        header('Content-type: application/json');

	    if(!IjoyPlusServiceUtils::validateAPPKey()){
  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
		   return ;
		}
		$url= Yii::app()->request->getParam("url");	
        if( !(isset($url) && !is_null($url) && strlen($url) >0) ) {
   			IjoyPlusServiceUtils::exportServiceError(Constants::KEYWORD_IS_NULL);	
   			return ;	   			
   		}

		try{
			$vodtype = $this->getVodtype($url);
			if ($vodtype !== "" && $vodtype !="joyplus"){
		        $content = $this->getContent("http://localhost/".$vodtype.'/?url='. $url, "utf-8");  
		        $content = json_decode($content);
			}
			
		  if(isset($content) ){
		      IjoyPlusServiceUtils::exportEntity(array('results'=>$content));
		    }else {
			  IjoyPlusServiceUtils::exportEntity(array('results'=>array()));
			}
		}catch (Exception $e){
//			var_dump($e);
		  IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);	
		}
	}
	
	function  getVodtype($url){
		$vodtype ="";
		if (strpos($url, "pan.baidu.com") >0){
			$vodtype = "baidu";
		}else{
			$vodtype = "joyplus";
		}
		return $vodtype;
		
	}
	
function getContent($url,$charset){   
	$charset = strtoupper($charset);
	$content = "";
	if(!empty($url)) {
		if( function_exists('curl_init') &&  function_exists('curl_exec')  ){
			$ch = curl_init();
			$timeout = 5;
			curl_setopt($ch, CURLOPT_URL, $url); //
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (iPhone; CPU iPhone OS 5_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9B179 Safari/7534.48.3');
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
			$content = @curl_exec($ch);	
			$httpCode = @curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
			curl_close($ch);
		}else{
			die('当前环境不支持采集，请检查php配置中allow_url_fopen是否为On；');
		}
	}
	return ($content);
}


	
}
