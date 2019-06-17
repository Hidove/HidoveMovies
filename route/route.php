<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
Route::pattern([
    'sort' => '\d+',
    'id'   => '\d+',
]);
Route::rule('sort/:id/[:page]','index/index/sort');
Route::rule('info/:id','index/index/info');
Route::rule('search','index/index/search');
Route::rule('index','index/index/index');
Route::rule('admin/cache','admin/index/cache');
Route::rule('admin/set','admin/index/set');
Route::rule('admin/api','admin/index/api');
Route::rule('admin/code','admin/index/code');
Route::rule('admin/index$','admin/index/index');
Route::rule('admin/login$','admin/login/index');
Route::rule('api/category','admin/api/category');