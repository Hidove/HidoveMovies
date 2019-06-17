<?php
/**
 * @Author：www.hidove.cn
 */
namespace app\admin\controller;

use think\Controller;
use think\facade\Request;
use think\facade\Session;

class Login extends Controller
{
    public function index(){
        $this->error('请登录',url('admin/login/login'));
    }
    public function login()
    {
        $Hidove = Hidove('admin');
        if(Request::isAjax()){
            $data = Request::param();
            $data['password']=md5(md5($data['password'].'Hidove'));
            if($data['username']==$Hidove['username']){
                if ($data['password']==$Hidove['password']){
                    Session::set('name','admin');
                    return json([
                        'code'=>'1',
                        'msg'=>'登录成功',
                    ]);
//                    $this->success('登陆成功',url('admin/index/index'));
                }else{
                    return json([
                        'code'=>'0',
                        'msg'=>'密码错误',
                    ]);
                }
            }else{
                return json([
                    'code'=>'0',
                    'msg'=>'用户名错误',
                ]);
            }

        }
        $this->assign([
            'Hidove' =>$Hidove,
        ]);
        return $this->fetch();
    }
    public function loginOut()
    {
        Session::clear();
        $this->success('退出成功',url('index/index/index'));
    }
}
