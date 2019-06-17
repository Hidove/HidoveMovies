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
        $result = Hidove::index($page);
        $template =  $result['Hidove']['template'];
        $result['Hidove']['template']= '/templates/'.$result['Hidove']['template'].'/';
        $this->assign([
            'video'    => $result['video'],
            'Hidove'    => $result['Hidove'],
            'Hi'   =>$result['Hi'],
        ]);
        return $this->fetch('templates/'.$template.'/index.html');
    }
    public function search($wd='')
    {
        $result = Hidove::search($wd);
        $template =  $result['Hidove']['template'];
        $result['Hidove']['template']= '/templates/'.$result['Hidove']['template'].'/';
        $this->assign([
            'video'    => $result['video'],
            'Hidove'    => $result['Hidove'],
            'Hi'   =>$result['Hi'],
        ]);
        return $this->fetch('templates/'.$template.'/search.html');
    }
    public function info($id)
    {
            $result = Hidove::info($id);
            foreach ($result['Hidove']['category'] as $key =>$value){
                if($value['id']==$result['video']['tid']){
                    if ($value['shield']=='true'){
                        $this->error('该资源已被屏蔽');
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
        $result = Hidove::sort($id,$page);
        $template =  $result['Hidove']['template'];
        $result['Hidove']['template']= '/templates/'.$result['Hidove']['template'].'/';
        $this->assign([
            'video'    => $result['video'],
            'template' => '/templates/'.$result['Hidove']['template'].'/',
            'Hidove'    => $result['Hidove'],
            'Hi'=> $result['Hi'],
        ]);
        return $this->fetch('templates/'.$template.'/sort.html');
    }

}
