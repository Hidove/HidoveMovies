<?php

namespace app\api\model;
use think\facade\Cache;
use think\Model;

class GetList extends Model
{
    public static function index($page)
    {
        $Hidove = Hidove();
        //获取倒序
        //        $order = Config('Hidove.order');
        $order = $Hidove['order'];
        //判断是否使用缓存
        if ($Hidove['cache-index']=='true'){
            //使用缓存
            # 获取缓存
            $data = Cache::tag('index')->get($page);
//            echo $page;
            $dataType = '服务器缓存';
            if (!$data){
                $dataType = '中心服务器';
                //判断是否倒序
                if ($order=='true') {
                    //获取目标接口分类信息
                    $apiInfo = apiInfo('array');
//                    $page2 = $apiInfo['pagecount']-1;
                    $page2 = $apiInfo['pagecount'] - $page;
                    //发起请求
                    $data = collect('array','videolist','','','',$page2);
                    $data['list']['@attributes']['pagecount'] = $data['list']['@attributes']['pagecount']-1;//倒序第一页资源未满所以去除
                }else{
                    $data = collect('array','videolist','','','',$page);
                }
                # 判断获取数据是否成功，否则不进行缓存
                if ($data){
                    # 缓存到本地
                    Cache::tag('index')->set($page,$data,$Hidove['cache-index-time']);
                }
//                dump($data);die();

            }
        }else{
            //不使用缓存
            $dataType = '中心服务器';
            if ($order=='true') {
                //获取目标接口分类信息
                $apiInfo = apiInfo('array');
                $page2 = $apiInfo['pagecount']-$page;
                //发起请求
                $data = collect('array','videolist','','','',$page2);
                $data['list']['@attributes']['pagecount'] = $data['list']['@attributes']['pagecount']-1;//倒序第一页资源未满所以去除
            }else{
                $data = collect('array','videolist','','','',$page);
            }
        }
        if (empty($data['list']['video'])){
            $video=[];
        }else{
            $video = $data['list']['video'];
        }
        $pageInfo=$data['list']['@attributes'];
        $info = [
            'category' => [],
            // 'page'=>$pageInfo['page'],
            'page'=>$page,
            'pagecount' => $pageInfo['pagecount'],
            'pagesize' => $pageInfo['pagesize'],
            'recordcount' => $pageInfo['recordcount'],
            'dataType' => $dataType,
        ];
        $result = [
            'video'=>$video,
//            'Hidove'=>$Hidove,
            'info'=> $info,
        ];
        return $result;
    }
    public static function sort($id,$page){
        //获取源配置
        $Hidove = Hidove();
        //获取倒序
//        $order = Config('Hidove.order');
        $order = $Hidove['order'];
        //判断是否使用缓存
        if ($Hidove['cache-sort']=='true'){
            # 获取缓存
            $data = Cache::tag('sort')->get($id.'page-'.$page);
            $dataType = '服务器缓存';
            if (!$data){//缓存不存在
                $dataType = '中心服务器';
                //判断是否倒序
                if($order=='true'){
                    //倒序
                    $apiInfo = apiInfo('array',$Hidove['api']."?t=$id");
                    $page2 = $apiInfo['pagecount'] - $page;
                    $data = collect('array','videolist',$id,'','',$page2);
                    //倒序后第一页可能未满，所以去除
                    $data['list']['@attributes']['pagecount'] = $data['list']['@attributes']['pagecount']-1;
                }else{
                    //未倒序
                    $data = collect('array','videolist',$id,'','',$page);
                }
                # 判断数据是否获取成功
                if ($data){
                    # 将数据缓存下来
                    Cache::tag('sort')->set($id.'page-'.$page,$data,$Hidove['cache-sort-time']);
                }
            }
        }else{
            //判断是否倒序
            if($order=='true'){
                //倒序
                $apiInfo = apiInfo('array',$Hidove['api']."?t=$id");
                $page2 = $apiInfo['pagecount']-$page;
                $data = collect('array','videolist',$id,'','',$page2);
                //倒序后第一页可能未满，所以去除
                $data['list']['@attributes']['pagecount']=$data['list']['@attributes']['pagecount']-1;
            }else{
                //未倒序
                $data = collect('array','videolist',$id,'','',$page);
            }
            $dataType = '中心服务器';
        }
        if (empty($data['list']['video'])){
            $video=[];
        }else{
            $video = $data['list']['video'];
        }
        $pageInfo = $data['list']['@attributes'];
        $info = [
            'category' => [],
//            'page'=>$pageInfo['page'],
            'page'=>$page,
            'pagecount' => $pageInfo['pagecount'],
            'pagesize' => $pageInfo['pagesize'],
            'recordcount' => $pageInfo['recordcount'],
            # 缓存类型
            'dataType' =>$dataType,
        ];
        foreach ($Hidove['category'] as $key =>$value){
            if ($value['id']==$id){
                $info['category']['title']=$value['title'];
                $info['category']['id']=$id;
                break;
            }
        }
        $result = [
            'info'=>$info,
//            'Hidove'=>$Hidove,
            'video'=>$video,
        ];
        return $result;
    }
    public static function info($id){
        //?ac=videolist&ids=60534
        //获取源配置
        $Hidove = Hidove();
        //判断是否使用缓存
        if ($Hidove['cache-info']=='true'){
            # 获取缓存
            $data = Cache::tag('info')->get($id);
            $dataType = '服务器缓存';
            if (!$data){
                $dataType = '中心服务器';
                $data = collect('array','videolist','',$id);
                # 判断数据是否获取成功
                if ($data){
                    # 将数据缓存下来
                    Cache::tag('info')->set($id,$data,$Hidove['cache-info-time']);
                }
            }
        }else{
            $data = collect('array','videolist','',$id);
            $dataType = '中心服务器';
        }

        if (!empty($data['list']['video'])){
            foreach ($data['list']['video'] as $key=>&$value){
                !empty($value)?:$value='未知';
            }
            unset($value);//释放引用
        }
        $video=$data['list']['video'];
        if (empty($video['dl']['dd'])){
            $video['url']=['error'];
        }else{
            //判断播放组是否为多个，即数组
            if ( !is_array($video['dl']['dd']) ){
                # 不是数组 转成数组
                $videoToArr[] = $video['dl']['dd'];
            }else{
                //是数组，直接赋值
                $videoToArr = $video['dl']['dd'];
            }
            //循环播放器组
            foreach ($videoToArr as $key =>$value){
                $videoTmp = explode('#', $value);
                foreach ($videoTmp as $i=> $vo) {
                    $video['url'][$key][$i] = explode('$', $vo);
                }
            }
        }
        $result = [
            'video'=>$video,
            'Hidove'=> $Hidove,
        ];
        return $result;
    }
    public static function search($wd,$page){
        //获取源配置
        $Hidove = Hidove();
        //判断是否使用缓存
        if ($Hidove['cache-search']=='true'){
            # 获取缓存
            $data = Cache::tag('search')->get($wd.'-'.$page);
            $dataType = '服务器缓存';
            if (!$data){
                $data = collect('array','list','','',$wd,$page);
                $dataType = '中心服务器';
                # 判断获取数据是否成功，否则不进行缓存
                if ($data){
                    # 缓存到本地
                    Cache::tag('search')->set($wd.'-'.$page,$data,$Hidove['cache-search-time']);
                }
            }
        }else{
            $data = collect('array','list','','',$wd,$page);
            $dataType = '中心服务器';
        }
        if (empty($data['list']['video'])){
            $video=[];
        }else{
            if(count($data['list']['video'],1)==count($data['list']['video'])){
                //是一维数组
                $video[] = $data['list']['video'];
            }else{
                //是多维数组
                $video = $data['list']['video'];
            }
        }

        $pageInfo=$data['list']['@attributes'];
        $info = [
            'category' => [],
            'page'=>$pageInfo['page'],
            'pagecount' => $pageInfo['pagecount'],
            'pagesize' => $pageInfo['pagesize'],
            'recordcount' => $pageInfo['recordcount'],
            'dataType' => $dataType,
            'keyword' =>$wd,
        ];
        $result = [
            'video'=>$video,
//            'Hidove'=> $Hidove,
            'info'=>$info,
        ];
        return $result;
    }

}
