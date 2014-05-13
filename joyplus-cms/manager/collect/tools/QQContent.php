<?php
require_once (dirname(__FILE__)."/ContentManager.php");

class QQContent extends Content{

	private $qq_url = "http://vv.video.qq.com/geturl?vid={vid}&otype=xml";
    private $vidstart = "vid";
    private $vidend = "";
    private $curl_loops = 0;//避免死了循环必备
    private $curl_max_loops = 3;

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
        $vidcontent = $this->curl_get_file_contents($url,"");
        if(!isN($vidcontent) && preg_match('/vid:"(\w+)"/',$vidcontent)){
            preg_match_all('/vid:"(\w+)"/',$vidcontent,$match);
            $videoUrlParam = $match[1][0];
        }
        if(isset($videoUrlParam) && $videoUrlParam){
            $c_url = replaceStr($this->qq_url,"{vid}",$videoUrlParam);
        }else{
            return "";
        }
        $content = simplexml_load_file($c_url);
		return $this->parseIOSVideoUrlByContent($content, $p_coding,$p_script);
	}

	public function parseIOSVideoUrlByContent($content, $p_coding,$p_script){
        try{
            $mp4_url = 'mp4' .MovieType::VIDEO_NAME_URL_SEP.$content->vd->vi->url;
            foreach($content->vd->vi->urlbk->ui as $ui_item){
                $mp4_url .= MovieType::VIDEO_SEP_VERSION.'mp4' .MovieType::VIDEO_NAME_URL_SEP.$ui_item->url;
            }
            return $mp4_url;
        }catch(Exception $e){
            return "";
        }

    }

    function curl_get_file_contents($url, $referer='') {
        $useragent = "Mozilla/5.0 (iPhone; CPU iPhone OS 5_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9B179 Safari/7534.48.3";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_REFERER, $referer);
        $data = curl_exec($ch);
        $ret = $data;
        list($header, $data) = explode("\r\n\r\n", $data, 2);
        $last_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        preg_match('/http-equiv="refresh" content="0; url=(.*?)"/', $data, $matches);
        if ($matches) {
            $new_url = "http://v.qq.com".$matches[1];
            $new_url = stripslashes($new_url);
            return $this->curl_get_file_contents($new_url, $last_url);
        } else {
            return $data;
        }
    }
}
?>
