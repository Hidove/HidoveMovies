<?php

namespace app\api\controller;

use think\Controller;
use think\Db;
use think\facade\Request;
use app\api\model\GetList;

class table extends Controller
{
    public function index(){
//        $sort = Request::param('sort');
        $page = Request::param('page');
        $result = GetList::index($page);
        $json = [
            'code' => 1,
            'msg' => 'success',
            'data' => $result,
        ];
        return json($json);
    }
    public function sort(){
        $sort = Request::param('sort');
        $page = Request::param('page');
        $result = GetList::sort($sort,$page);
        $json = [
            'code' => 1,
            'msg' => 'success',
            'data' => $result,
        ];
        return json($json);

    }
    public function search(){
        $wd = Request::param('wd');
        $page = Request::param('page');
            $result = GetList::search($wd,$page);
            $json = [
                'code' => 1,
                'msg' => 'success',
                'data' => $result,
            ];
            return json($json);

    }

}
