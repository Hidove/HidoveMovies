<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use \think\facade\Cache;
use think\facade\Config;
// 应用公共文件
function HidoveCurlGet($url){
	// 创建一个新 cURL 资源
	$curl = curl_init();
	// 设置URL和相应的选项
	// 需要获取的 URL 地址
	curl_setopt($curl, CURLOPT_URL,$url); 
	#启用时会将头文件的信息作为数据流输出。
	curl_setopt($curl, CURLOPT_HEADER, false);
	#在尝试连接时等待的秒数。设置为 0，则无限等待。
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
	#允许 cURL 函数执行的最长秒数。
	curl_setopt($curl, CURLOPT_TIMEOUT, 10);
	#关闭ssl
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    #TRUE 将 curl_exec获取的信息以字符串返回，而不是直接输出。
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
	// 抓取 URL 并把它传递给浏览器
	$res = curl_exec($curl);
	// 关闭 cURL 资源，并且释放系统资源
	curl_close($curl);
	return $res;


}
function collect($type='json',$ac='videolist',$category='',$id='',$wd = '',$page='',$limit = ''){
    	//&h=&t=&ids=&wd=白色&type=1&mid=1&param=&page=1&limit=
    	$api = Config('Hidove.api');
    	//获取排列顺序
    	$order = Config('Hidove.order');
    	$url=$api."?ac=$ac&t=$category&ids=$id&pg=$page&wd=$wd&limit=$limit";
		$data = HidoveCurlGet($url);
		$data = simplexml_load_string($data,'SimpleXMLElement', LIBXML_NOCDATA);
		if ($type == 'json') {
			return json($data);
		}
		$data= json_encode($data);//将对象转换个JSON
		$array=json_decode($data,true);//将json转换成数组
		return($array);
}
/**
 * 获取接口地址的信息
 * [apiInfo description]
 * @param  string $type [description]
 * @return [type]       [description]
 */
function apiInfo($type='json',$url='apiInfo'){
	//<ty id="1">电影片</ty><ty id="2">连续剧</ty><ty id="3">综艺片</ty><ty id="4">动漫片</ty><ty id="5">动作片</ty><ty id="6">喜剧片</ty><ty id="7">爱情片</ty><ty id="8">科幻片</ty><ty id="9">恐怖片</ty><ty id="10">剧情片</ty><ty id="11">战争片</ty><ty id="12">国产剧</ty><ty id="13">香港剧</ty><ty id="14">韩国剧</ty><ty id="15">欧美剧</ty><ty id="16">福利片</ty><ty id="17">伦理片</ty><ty id="18">音乐片</ty><ty id="19">台湾剧</ty><ty id="20">日本剧</ty><ty id="21">海外剧</ty>
    # 获取接口缓存
    $data =  Cache::tag('apiInfo')->get($url);
    if ($data){
        return ($data);
    }
    if ($url=='apiInfo'){
        $api = Config('Hidove.api');
    }else{
        $api = $url;
    }
//    dump($api);
	$data = HidoveCurlGet($api);
	preg_match('~pagecount="(\d+)"~', $data,$pagecount);
	preg_match('~recordcount="(\d+)"~', $data,$recordcount);
	preg_match_all('~<ty id="(\d+)">(.+?)</ty>~', $data, $category,PREG_SET_ORDER);
	$result['pagecount'] = $pagecount[1];
	$result['recordcount'] = $recordcount[1];
	foreach ($category as $key => $value) {
		unset($value[0]);
		$result['category'][$key]['id'] = $value[1];
		$result['category'][$key]['title'] = $value[2];
	}
	# 缓存结果
    Cache::tag('apiInfo')->set($url,$result,3600);
	if ($type=='json') {
		return json($result);
	}
	return $result;

}
function Hidove($type='index'){
    if ($type=='admin'){
        $Hidove =  Config::get('Hidove.');

        $Hidove['template']='/static/';
        return $Hidove;
    }else{
        $Hidove = Config::get('Hidove.');
        $apiInfo = apiInfo('array');
//        $Hidove['category'] = $apiInfo['category'];
        $Hidove['category'] = category();
        return $Hidove;
    }
}

/**
 * 
 * @param string $type
 * @return mixed
 */
function category($type='index'){
    if ($type=='admin'){
		//获取接口中的分类
        return apiInfo()['category'];
    }
	//获取配置中的分类
    return Config::get('category.');
}