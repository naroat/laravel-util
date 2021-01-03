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

/**
 * 登录密码加密
 */
if (!function_exists('encrypt_password')) {
    /**
     * @param string $password 密码
     * @param string $salt 扰乱码
     * @return string
     */
    function encrypt_password($password, $salt)
    {
        return md5(sha1($password . $salt));
    }
}