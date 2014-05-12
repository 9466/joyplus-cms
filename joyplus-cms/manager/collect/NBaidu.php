<?php
require_once ("../admin_conn.php");
require_once ("collect_fun.php");
class NBaidu{
	const contentparmListStart="({"; //
	const contentparaListend="})";
    const remarkstart = '&nbsp;共';
    const remarkstop = '集';
    const remarkstart1 = "共<span";
    const remarkstop1 = "span>集";

	static function parseListByContent($content,$p_code,$type){
		$contentSt= "{".getBody($content, NBaidu::contentparmListStart, NBaidu::contentparaListend).'}';

		$content=json_decode($contentSt);
		if(is_object($content)&& property_exists($content, 'videoshow')&& property_exists($content->videoshow, 'videos')){
			$videos=$content->videoshow->videos;
			if(is_array($videos)){
				$linkarr= array();
				$starringarrcode= array();
				$titlearrcode= array();
				$picarrcode= array();
				$areaarrcode= array();
				$typearrcode= array();
				$yeararrcode= array();
				$idarrcode= array();
				$finisharrcode= array();
				$introarrcode= array();
				$directorcode= array();
				$durationcode= array();
				foreach ($videos as $video){

					if(property_exists($video, 'url')){
						$linkarr[]=$video->url;
					}else {
						continue;
					}

					if(property_exists($video, 'title')){
						$titlearrcode[]=$video->title;
					}else {
						$titlearrcode[]='';
					}

					if(property_exists($video, 'date')){
						$yeararrcode[]=$video->date;
					}else {
						$yeararrcode[]='';
					}
					if(property_exists($video, 'intro')){
                        $introarrcode[]=$video->intro;
					}else {
                        $introarrcode[]='';
					}
					if(property_exists($video, 'finish')){
                        $finisharrcode[]=$video->finish;
					}else {
                        $finisharrcode[]='';
					}
					if(property_exists($video, 'duration')){
                        $durationcode[]=$video->duration;
					}else {
                        $durationcode[]='';
					}

					if(property_exists($video, 'actor')){
						$starringarrcode[]=NBaidu::parseArrayObjectToString($video->actor,'name');
					}else {
						$starringarrcode[]='';
					}
					if(property_exists($video, 'director')){
						$directorcode[]=NBaidu::parseArrayObjectToString($video->director,'name');
					}else {
                        $directorcode[]='';
					}

					if(property_exists($video, 'area')){
						$areaarrcode[]=NBaidu::parseArrayObjectToString($video->area,'name');
					}else {
						$areaarrcode[]='';
					}

					if(property_exists($video, 'type')){
						$typearrcode[]=NBaidu::parseArrayObjectToString($video->type,'name');
					}else {
						$typearrcode[]='';
					}
					if(property_exists($video, 'id')){
						$idarrcode[]=$video->id;
					}else {
                        $idarrcode[]='';
					}


				}
					
				return array(
                   'idarr'=>$idarrcode,
  	 			   'linkarr'=>$linkarr,
  	 			   'typearr'=>$typearrcode,
  	 			   'areaarr'=>$areaarrcode,
  	 			   'starringarr'=>$starringarrcode,
  	 			   'yeararr'=>$yeararrcode,
  	 			   'titlearr'=>$titlearrcode,
  	 			   'picarr'=>$picarrcode,
  	 			   'finisharr'=>$finisharrcode,
  	 			   'introarr'=>$introarrcode,
  	 			   'directorarr'=>$directorcode,
  	 			   'durationarr'=>$durationcode,
				);
			}
		}
	}

	static function parseArrayObjectToString($array,$key){
		$temp= array();
		if(is_array($array)){
			foreach ($array as $item){
				//	  			var_dump($item);
				if(property_exists($item, $key)){
					$temp[]=$item->$key;
				}
			}
			return implode(" ", $temp);
		}
		return "";
	}




	static function parseMovieInfoById($id,$type){

		if($type ===1 || $type ==='1') {
			$url='http://v.baidu.com/movie_intro/?dtype=playUrl&service=json&id='.$id;
		}else if($type ===2 || $type ==='2'){
			$url='http://v.baidu.com/tv_intro/?dtype=tvPlayUrl&service=json&id='.$id;
		}else if($type ===3 || $type ==='3'){
			$url='http://v.baidu.com/show_intro/?dtype=tvshowPlayUrl&service=json&frp=browse&year=2014&id='.$id;
		}else if($type ===131 || $type ==='131'){
			$url='http://v.baidu.com/comic_intro/?dtype=comicPlayUrl&service=json&id='.$id;
		}

		writetofile("baiducontent.log","request url:".$url);

		$playUrlsContent = getPage($url, 'gb2312');
		writetofile("baiducontent.log","request content:".$playUrlsContent);

		$content=json_decode($playUrlsContent);

		if($type ===1 || $type ==='1') {
				$info=NBaidu::parseArrayMovie($content);
		}else if($type ===2 || $type ==='2'){
				$info=NBaidu::parseArrayTV($content);
		}else if($type ===3 || $type ==='3'){
				$info =NBaidu::parseArrayShow($content);
		}else if($type ===131 || $type ==='131'){
				$info=NBaidu::parseArrayComic($content);
		}
		return $info;
	}

	static function parseArrayMovie($content){
        $info= new VInfo();
		$sites = array();
		if (is_array($content)){
			foreach ($content as $siteObject){
				$site = array();
				if (property_exists($siteObject, 'site') ){
					$site['site_url']=$siteObject->site;
				}
				if (property_exists($siteObject, 'name') ){
					$site['site_name']=NBaidu::getSite($siteObject->name);
				}


                $info->max_episode='1';
				if (property_exists($siteObject, 'link') ){
					$episodes = array();
					$episodes[]=array(
			          'episode' => '1',
			          'url' => $siteObject->link
					);
					if(strpos($siteObject->link, "baidu.com") !==false){
						continue;
					}
					$site['episodes']=$episodes;
				}
                if(!isN($site['site_name']) && $site['site_name'] !==null){
                    $sites[]=$site;
                }
			}
		}
        $info->sites = $sites;
		return $info;
	}
	static function obj2arr($array) {
		if (is_object ( $array )) {
			$array = ( array ) $array;
		}
		if (is_array ( $array )) {
			foreach ( $array as $key => $value ) {
				$array [$key] = NBaidu::obj2arr ( $value );
			}
		}
		return $array;
	}
	static function parseArrayTV($sitesObject){
        $info = new VInfo();
		$sites = array();
		if (is_array($sitesObject)){
			$sitesArray =$sitesObject;
			foreach($sitesArray as $siteObject){
				$site = array();
				if(is_object($siteObject)){
					if (property_exists($siteObject, 'site_info') ){
						$site_info = $siteObject->site_info;
						if (is_object($site_info) && property_exists($site_info, 'name') ){
							$site['site_name']=NBaidu::getSite($site_info->name);
						}
						if (is_object($site_info) && property_exists($site_info, 'site') ){
							$site['site_url']=$site_info->site;
						}
					}


                    $info->max_episode='';
					if (property_exists($siteObject, 'episodes') ){
						$episodesArray= $siteObject->episodes;
						if(is_array($episodesArray)){
							$episodes = array();
							foreach ($episodesArray as $item) {
								if (property_exists($item, 'single_title') ){
									$episode['name']=$item->single_title;
								}
								if (property_exists($item, 'url') ){
									$episode['url']=$item->url;
								}
								if(strpos($item->url, "baidu.com") !==false){
									continue;
								}
								if (property_exists($item, 'episode') ){
									$episode['episode']=$item->episode;
                                    $info->max_episode =$item->episode;
								}
								$episodes[]=$episode;
							}
							$site['episodes']=$episodes;
						}
					}
                    if(!isN($site['site_name']) && $site['site_name'] !==null){
                        $sites[]=$site;
                    }
				}
					
			}
		}
        $info->sites = $sites;
        return $info;
	}

	static function parseArrayComic($sitesArray){
        $info = new VInfo();
		$sites = array();
		if (is_object($sitesArray)){
			foreach($sitesArray as $siteObject){
				$site = array();
				if(is_object($siteObject)){
					if (property_exists($siteObject, 'site_info') ){
						$site_info = $siteObject->site_info;
						if (is_object($site_info) && property_exists($site_info, 'name') ){
							$site['site_name']=NBaidu::getSite($site_info->name);
						}
						if (is_object($site_info) && property_exists($site_info, 'site') ){
							$site['site_url']=$site_info->site;
						}
					}


                    $info->max_episode='';
					if (property_exists($siteObject, 'episodes') ){
						$episodesArray= $siteObject->episodes;
						if(is_array($episodesArray)){
							$episodes = array();
							foreach ($episodesArray as $item) {
								if (property_exists($item, 'single_title') ){
									$episode['name']=$item->single_title;
								}
								if (property_exists($item, 'url') ){
									$episode['url']=$item->url;
								}
								if(strpos($item->url, "baidu.com") !==false){
									continue;
								}
								if (property_exists($item, 'episode') ){
									$episode['episode']=$item->episode;
                                    $info->max_episode =$item->episode;
								}
								$episodes[]=$episode;
							}
							$site['episodes']=$episodes;
						}
					}
                    if(!isN($site['site_name']) && $site['site_name'] !==null){
                        $sites[]=$site;
                    }
				}

			}
		}
        $info->sites = $sites;
        return $info;
	}

	static function parseArrayShow($sitesObject){
        $info = new VInfo();
        $sites = array();
        if (is_array($sitesObject)){
            $sitesArray =$sitesObject;
            foreach($sitesArray as $siteObject){
                $site = array();
                if(is_object($siteObject)){
                    if (property_exists($siteObject, 'site_info') ){
                        $site_info = $siteObject->site_info;
                        if (is_object($site_info) && property_exists($site_info, 'name') ){
                            $site['site_name']=NBaidu::getSite($site_info->name);
                        }
                        if (is_object($site_info) && property_exists($site_info, 'site') ){
                            $site['site_url']=$site_info->site;
                        }
                    }

                    $info->max_episode='';
                    if (property_exists($siteObject, 'episodes') ){
                        $episodesArray= $siteObject->episodes;
                        if(is_array($episodesArray)){
                            $episodes = array();
                            foreach ($episodesArray as $item) {
                                if (property_exists($item, 'single_title') ){
                                    $episode['name']=$item->single_title;
                                }
                                if (property_exists($item, 'url') ){
                                    $episode['url']=$item->url;
                                }
                                if(strpos($item->url, "baidu.com") !==false){
                                    continue;
                                }
                                if (property_exists($item, 'episode') ){
                                    $episode['episode']=$item->episode;
                                    if($info->max_episode === ""){
                                        $info->max_episode =$item->episode;
                                    }
                                }
                                $episodes[]=$episode;
                            }
                            $site['episodes']=$episodes;
                        }
                    }
                    if(!isN($site['site_name']) && $site['site_name'] !==null){
                        $sites[]=$site;
                    }
                }

            }
        }
        $info->sites = $sites;
        return $info;
	}

	static function parseArrayToString($array){
		if(is_array($array)){
			return implode(",", $array);
		}
		return "";
	}
	static function getSite($sitename){

		$PLAY_FROMS= array(
//	  	   '爱奇艺'=>'qiyi',
			'搜狐'=>'sohu',
			'优酷'=>'youku',
			'风行网'=>'fengxing',
			'乐视'=>'letv',
            '腾讯'=>'qq',
//		    'iqiyi.com'=>'qiyi',
			'sohu.com'=>'sohu',
			'youku.com'=>'youku',
			'funshion.com'=>'fengxing',
			'letv.com'=>'letv',
			'qq.com'=>'qq',
		) ;
		try{
			return $PLAY_FROMS[$sitename];
		}catch (Exception $e){
			return null;
		}
	}


    static function getRemarks($id,$type){
        switch($type){
            case "2":
                $url ="http://v.baidu.com/htvplaysingles/?id=".$id;
                break;
            case "131":
                $url = "http://v.baidu.com/hcomicsingles/?id=".$id;
                break;
            default:
                $url = "http://v.baidu.com/htvplaysingles/?id=".$id;
                break;
        }

        $remarkcode = array();
        $strViewCode = getPage($url,"gb2312");
        $content=json_decode($strViewCode);
        if(property_exists($content,"res_num")){
            $remarkcode["lzcode"] = $content->res_num;

        }

        if(property_exists($content,"max_episode")){
            $remarkcode["remarkcode"] = $content->max_episode;
        }
        return $remarkcode;
    }


}

class VInfo{
	public $max_episode;
	public $sites; // site_url"/site_name
}

?>