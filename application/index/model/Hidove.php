<?php
/**
 * @Author：www.hidove.cn
 */
namespace app\index\model;

use think\facade\Cache;
use think\Model;

class Hidove extends Model
{
    public static function index()
    {
        $Hidove = Hidove();
        return $Hidove;
    }
    public static function sort($id,$page){
        //获取源配置

        $Hidove = Hidove();
        return $Hidove;
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
            if(count($videoToArr)!=count($videoToArr,1)){
                //为多维数组，即播放组为空
                $video['status'] = "error";
            }else{
                $video['status'] = "success";
                foreach ($videoToArr as $key =>$value){
                    $videoTmp = explode('#', $value);
                    foreach ($videoTmp as $i=> $vo) {
                        $video['url'][$key][$i] = explode('$', $vo);
                    }
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
        return $Hidove;
    }
}
