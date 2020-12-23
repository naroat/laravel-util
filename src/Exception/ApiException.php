<?php

namespace Taoran\Laravel\Exception;

/**
 * Class ApiException
 * @package App\Exceptions
 */
class ApiException extends \Exception
{
    /**
     * ApiException constructor.
     * @param string $message 错误信息
     * @param string $error_id 错误id
     * @param string $code http状态码
     */
    public function __construct($message = '数据库错误！', $error_id = 'ERROR', $code = 400)
    {
        parent::__construct($message, $code);
        empty($error_id) || $this->error_id = $error_id;
    }

    /**
     * 获取错误id
     * @return string
     */
    public function getErrorId()
    {
        return empty($this->error_id) ? 'ERROR' : $this->error_id;
    }
}
