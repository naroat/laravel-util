<?php
namespace Taoran\Laravel\Exception;

class Handler
{

    /**
     * http状态表
     * @var array
     */
    public static $httpStatus = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Page Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported'
    );

    /**
     * 执行
     * @param $request
     * @param \Exception $exception
     * @param $obj
     * @return \Illuminate\Http\JsonResponse|void
     */
    public static function handler($request, \Exception $exception, $obj)
    {
        if ($request->header('X-ISAPI') == 1) {
            $reponse = app()->make('taoran_response');
            $status_code = empty($exception->getCode()) ? 500 : $exception->getCode();
            $reponse->errmsg = $exception->getMessage() ?? '';
            //debug模式
            if (config('app.debug') == 'true') {
                $reponse->errmsg = !empty($exception->getMessage()) ? $exception->getMessage() : self::getHttpStatus($status_code);
                $reponse->exception = config('app.debug') == 'true' ? [
                    'type' => get_class($exception),
                    'line' => $exception->getLine(),
                    'file' => $exception->getFile(),
                    'trace' => explode("\n", $exception->getTraceAsString())
                ] : true;
            }

            //响应
            return $reponse->handle([], [], $status_code);
        } else {
            return $obj->renderByParent($request, $exception);
        }
    }

    /**
     * 获取http状态
     * @param $http_code
     * @return mixed
     */
    public static function getHttpStatus($http_code)
    {
        return isset(self::$httpStatus[$http_code]) ? self::$httpStatus[$http_code] : self::$httpStatus[500];
    }
}
