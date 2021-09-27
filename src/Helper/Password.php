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
 * 创建一个密码
 *
 * @param string $password 密码
 * @param string $salt 扰乱码
 * @return string
 */
if (!function_exists('create_password')) {
    function create_password($password, &$salt)
    {
        $salt = \Illuminate\Support\Str::random(5);
        return md5(sha1($password . $salt));
    }
}

/**
 * 判断密码是否相等
 *
 * @param string $encrypted_password 已加密密码
 * @param string $password 要比对的密码
 * @param string $salt 扰乱码
 * @return string
 */
if (!function_exists('eq_password')) {
    function eq_password($encrypted_password, $password, $salt)
    {
        if ($encrypted_password != md5(sha1($password . $salt))) {
            return false;
        }
        return true;
    }
}
