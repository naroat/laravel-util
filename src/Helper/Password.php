<?php

/**
 * 生成全局唯一标识
 */
if (!function_exists('create_guid')) {
    /**
     * @return string
     */
    function create_guid()
    {
        $charid = strtolower(md5(uniqid(mt_rand(), true)));
        $hyphen = chr(45);// "-"
        $guid = substr($charid, 0, 8) . $hyphen . substr($charid, 8, 4) . $hyphen . substr($charid, 12, 4) .
            $hyphen . substr($charid, 16, 4) . $hyphen . substr($charid, 20, 12);
        return $guid;
    }
}