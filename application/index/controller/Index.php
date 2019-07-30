<?php
/**
 * @Author：www.hidove.cn
 */
namespace app\index\controller;
use think\Controller;
use app\index\model\Hidove;
class Index extends Controller
{
    public function index($page=1)
    {
        $Hidove = Hidove::index();
        $info['page'] =$page;
        $template =  $Hidove['template'];
        $Hidove['template']= '/templates/'.$Hidove['template'].'/';
        $this->assign([
            'Hidove'    => $Hidove,
            'info'   =>$info,
        ]);
        return $this->fetch('templates/'.$template.'/index.html');
    }
    public function search($wd='',$page=1)
    {
        $info['page'] =$page;
        $info['keyword'] =$wd;
        $Hidove = Hidove::search($wd,$page);
        $template =  $Hidove['template'];
        $Hidove['template']= '/templates/'.$Hidove['template'].'/';
        $this->assign([
//            'video'    => $result['video'],
            'Hidove'    => $Hidove,
            'info'   =>$info,
        ]);
        return $this->fetch('templates/'.$template.'/search.html');
    }
    public function info($id)
    {
            $result = Hidove::info($id);
            if ($result['video']['status']=='error'){
                $this->error('该资源存在异常',url('index/index/index'));
            }
            foreach ($result['Hidove']['category'] as $key =>$value){
                if($value['id']==$result['video']['tid']){
                    if ($value['shield']=='true'){
                        $this->error('该资源已被屏蔽',url('index/index/index'));
                    }else{
                        break;
                    }
                 }
            }
            $template =  $result['Hidove']['template'];
            $result['Hidove']['template']= '/templates/'.$result['Hidove']['template'].'/';
            $this->assign([
                'video'    => $result['video'],
                'Hidove'    => $result['Hidove'],
            ]);
      return $this->fetch('templates/'.$template.'/info.html');
    }
    public function sort($id=1,$page=1)
    {
        $Hidove = Hidove::sort($id,$page);
        $info['page'] =$page;
        foreach ($Hidove['category'] as $key =>$value){
            if ($value['id']==$id){
                $info['sort'] =$value;
            }
        }

        $template =  $Hidove['template'];
        $Hidove['template']= '/templates/'.$Hidove['template'].'/';
        $this->assign([
            'template' => '/templates/'.$Hidove['template'].'/',
            'Hidove'    => $Hidove,
            'info'   =>$info,
        ]);
        return $this->fetch('templates/'.$template.'/sort.html');
    }

}
