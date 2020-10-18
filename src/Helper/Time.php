<?php


/**
 * 获取毫秒级别的时间戳
 */
if (!function_exists('get_msectime')) {
    function get_msectime()
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return (int)$msectime;
    }
}

/**
 * 毫秒转日期
 * @params $msectime int 毫秒时间戳
 */
if (!function_exists('get_msec_to_mescdate')) {
    function get_msec_to_mescdate($msectime, $format = "Y-m-d H:i:s.x")
    {
        $msectime = $msectime * 0.001;
        if (strstr($msectime, '.')) {
            sprintf("%01.3f", $msectime);
            list($usec, $sec) = explode(".", $msectime);
            $sec = str_pad($sec, 3, "0", STR_PAD_RIGHT);
        } else {
            $usec = $msectime;
            $sec = "000";
        }
        $date = date($format, $usec);
        return $mescdate = str_replace('x', $sec, $date);
    }
}

/**
 * 日期转毫秒
 * @params string 日期字符串 eg:2018-09-21 15:30:29.304
 */
if (!function_exists('get_date_to_mesc')) {
    function get_date_to_mesc($mescdate)
    {
        $mescdate_arr = explode(".", $mescdate);
        $usec = $mescdate_arr[0];
        $sec = isset($mescdate_arr[1]) ? $mescdate_arr[1] : 0;
        $date = strtotime($usec);
        $return_data = str_pad($date . $sec, 13, "0", STR_PAD_RIGHT);
        return (int)$return_data;
    }
}