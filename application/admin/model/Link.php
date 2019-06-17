<?php
/**
 * @Author：www.hidove.cn
 */
namespace app\admin\model;

use think\facade\Request;
use think\Model;

class Link extends Model
{
    public function updata($data){
            //获取源配置

            //写入配置
            $res = "<?php\nreturn [\n";
            //排版用的tap键
            $space = '  ';
            foreach ($data as $key =>$value){
                $res = $res .$space."['title' =>'".$value['title']."','url' => '". $value['url']."'],\n";
            }
            $res = $res . '];';
//            return json($res);
            //写入配置
            $result = file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/../config/link.php', $res);
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
