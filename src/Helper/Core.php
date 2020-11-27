<?php
if (!function_exists('response_json')) {
    function response_json($data = array(), $list = array(), $code = 200)
    {
        $data = [
            'errno' => 0,       //
            'errmsg' => 'ok',
            'data' => empty($data) ? null : $data,
            'runtime' => ''
        ];
        return response()->json($data, $code, $list, JSON_UNESCAPED_UNICODE);
    }
}
if (!function_exists('verify')) {
    function verify($request, $data)
    {
        $validator = Validator::make($request->all(), $data);

        //验证失败
        if ($validator->fails()) throw new \App\Exceptions\ApiException($validator->errors()->first());

        return $request->all();
    }
}

/**
 * orm打印带参数的sql
 * @param $model
 * @return string
 */
if (!function_exists('orm_sql')) {
    function orm_sql($model) {
        $bindings = $model->getBindings();
        $sql = str_replace('?', '%s', $model->toSql());
        $tosql = sprintf($sql, ...$bindings);
        return $tosql;
    }
}
/**
 * env内容转数组
 */
if (!function_exists('env_to_array')) {
    function env_to_array($example_path)
    {
        $example_array = [];
        $example = file($example_path, FILE_IGNORE_NEW_LINES);
        $example = array_filter($example);
        foreach ($example as $val) {
            if ($val[0] == '#') {
                //过滤注释
                unset($val);
                continue;
            }
            $env_name = strstr($val, '=', true);
            $env_value = ltrim(strstr($val, '='), '=');
            $example_array[$env_name] = $env_value;
        }
        return $example_array;
    }
}
/**
 * 数组转env内容
 */
if (!function_exists('array_to_env')) {
    function array_to_env($env_array)
    {
        $content = '';
        foreach ($env_array as $key => $val) {
            $content .= $key . '=' . $val . "\r\n";
        }
        return $content;
    }
}

/**
 * 判断数据库是否存在
 * return true:存在
 */
if (!function_exists('db_exists')) {
    function db_exists($dbname)
    {
        $flag = true;
        $host = config('database.connections.mysql.host');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $dbh = mysqli_connect($host, $username, $password);
        $select_db = mysqli_query($dbh, 'use ' . $dbname);
        if (!$select_db) {
            $flag = false;
        }
        return $flag;
    }
}

/**
 * 删除目录
 */
if (!function_exists('rm_dir')) {
    function rm_dir($path)
    {
        if (!is_dir($path)) {
            return false;
        }
        $dirs = scandir($path);
        foreach ($dirs as $dir) {
            if ($dir == '.' || $dir == '..') {
                continue;
            }
            if (is_dir($path . $dir)) {
                delete_dir($path . $dir);
            } else {
                @unlink($path . $dir);
            }
        }
        @rmdir($path);
    }
}


/**
 * 设置保存的数据
 * @param Object $model
 * @param array $data
 * @return object
 */
if (!function_exists('set_save_data')) {
    function set_save_data(\Illuminate\Database\Eloquent\Model $model, array $data)
    {
        foreach ($data as $key => $v) {
            //是否html内容
            $model->$key = htmlspecialchars($v, ENT_QUOTES);
        }
        return $model;
    }
}