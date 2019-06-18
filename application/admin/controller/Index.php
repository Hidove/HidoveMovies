<?php
/**
 * @Author：www.hidove.cn
 */
namespace app\admin\controller;
use app\admin\model\Link;
use think\App;
use think\facade\Cache;
use think\facade\Config;
use think\Controller;
use think\facade\Request;
use think\facade\Session;
class index extends Controller
{
    protected function initialize()
    {
        //判断是否登录
        $session = Session::get('name');
        if ($session!='admin'){
            $this->error('请登录',url('admin/login/login'));
        }
    }
    public function index(){
        $Hidove = Hidove('admin');
        $this->assign([
            'Hidove' =>$Hidove,
            'video' =>'',
        ]);
    return $this->fetch();
}
    public function set($data=''){
        $Hidove = Hidove('admin');
        $this->assign([
            'Hidove' =>$Hidove,
            'video' =>'',
        ]);
        return $this->fetch();
    }
    public function updateSet(){
            if (Request::isAjax()) {
                //获取数据
                $data = Request::param();
                //获取源配置
                $Hidove = Hidove();
                if (empty($data['password'])){
                    unset($data['password']);
                }
                unset($Hidove['category']);
                $res = "<?php\nreturn [\n";
                foreach ($Hidove as $key => $value) {
                    //判断接受数据配置是否存在该配置项，不存在不修改
                    if (!isset($data[$key])) {
                        $res = $res . "'$key' => '$value',\n";
                    } else {
                        //存在，修改它
                        if ($key=='foot-code'){
                            $temp = addslashes(htmlentities($data[$key]));
                        }elseif($key=='password'){
                            $temp = md5(md5($data[$key].'Hidove'));
                        }else{
                            $temp = $data[$key];

                        }
                        $res = $res . "'$key' => '$temp',\n";
//                        $res = $res . "'$key' => '$data[$key]',\n";
                    }
                }
                $res = $res . '];';
                //写入配置
                $result = file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/../config/Hidove.php', $res);
                if ($result) {
                    return [
                        'code' => 1,
                        'msg' => 'success',
                    ];
                }
                return [
                    'code' => 0,
                    'msg' => 'error',
                ];
            }
    }
    public function cache(){
        $Hidove = Hidove('admin');
        $this->assign([
            'Hidove' =>$Hidove,
        ]);
        return $this->fetch();
    }
    public function clearCache(){
        if(Request::isAjax()){
            $data = Request::param();
            Cache::clear('apiInfo');
//            cache::clear();
            foreach ($data as $value){
                $result = Cache::clear($value);
            }
            if ($result){
                return json([
                    'code'=>1,
                    'msg'=>'success',
                ]);
            }
            return json([
                'code'=>0,
                'msg'=>'error',
            ]);
        }
    }
    public function api()
    {
        $Hidove = Hidove('admin');
        $this->assign([
            'Hidove' =>$Hidove,
        ]);
    return $this->fetch();
    }

    /**
     * 分类页面
     * @return mixed
     */
    public function category(){

        $category = category('index');
//        dump($category);
        $Hidove = Hidove('admin');
        $apiInfo = apiInfo('array');
//        dump($apiInfo);die();
        $this->assign([
            'Hidove' =>$Hidove,
            'category' =>$category,
            'apiInfo' =>$apiInfo,
        ]);
        return $this->fetch();
    }
    public function updateCategory(){
        if (Request::isAjax()) {
            //获取数据
            $data = Request::param();
            //获取源配置
            $category = category();
//            防止重复
            foreach ($data as $key => $value){
                if ( isset($value['shield']) ){
                    # 防止重复, 进行初始化，将屏蔽的分类全部去除
                    foreach ($category as &$vo){
                        $vo['shield'] = 'false';
                    }
                    break;
                }
            }
            foreach ($data as $key => $value){
                foreach ($category as $i =>&$vo){
                    if ( $vo['title'] == $value['title'] ){
                        if ( isset($value['id']) ){
                            $vo['id'] = $value['id'];
                        }
                        if ( isset($value['shield']) ){
                            # 防止重复
                            $vo['shield'] = $value['shield'];
                        }
                    }
                }
            }
            //写入配置
            $res = "<?php\nreturn [\n";
            //排版用的tap键
            $space = '  ';
            foreach ($category as $key =>$value){
                $res = $res .$space."['title' =>'".$value['title']."','id' => '". $value['id'] ."','shield' => '".$value['shield']."'],\n";
            }
            $res = $res . '];';
//            return json($res);
            //写入配置
            $result = file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/../config/category.php', $res);
            if ($result) {
                return json([
                    'code' => 1,
                    'msg' => 'success',
                ]);
            }
            return json([
                'code' => 0,
                'msg' => 'error',
            ]);
        }
    }

    /**
     * 屏蔽页面
     */
    public function shield()
    {
        $category = category('index');
        $Hidove = Hidove('admin');
        $apiInfo = apiInfo('array');
        $this->assign([
            'Hidove' =>$Hidove,
            'category' =>$category,
            'apiInfo' =>$apiInfo,
        ]);
        return $this->fetch();
    }
    public function code(){
        $category = category('admin');
        $Hidove = Hidove('admin');
        $this->assign([
            'Hidove' =>$Hidove,
            'category' =>$category,
        ]);
        return $this->fetch();
    }
	
		
    public function link(){
        if(Request::isAjax()){
            $data = Request::param();
            Link::updata($data);
        }
        $category = category('admin');
        $Hidove = Hidove('admin');
        $this->assign([
            'Hidove' =>$Hidove,
            'category' =>$category,
        ]);
        return $this->fetch();
    }
}
