<?php
/**
 * @Author：www.hidove.cn
 */
namespace app\admin\controller;
use think\Controller;
use think\facade\Request;
use think\facade\Session;
class api extends Controller
{
    protected function initialize()
    {
        //判断是否登录
        $session = Session::get('name');
        if ($session!='admin'){
            $this->error('请登录',url('admin/login/login'));
        }
    }
	public function category($type='index'){
		if(Request::isAjax()){
			//判断接口请求的数据
			if($type=='api'){
				$face = category('admin');
				$json = [
					'code'=>1,
					'msg'=>'success',
					'data'=>$face,
				];
				return json($json);
			}else{
				$face = category('index');
				$json = [
					'code'=>1,
					'msg'=>'success',
					'data'=>$face,
				];
				return json($json);
			}
		}
	}
}
