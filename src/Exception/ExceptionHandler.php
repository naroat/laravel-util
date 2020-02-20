<?php
namespace App\Exceptions;

use Psr\Log\LoggerInterface;

class ExceptionHandler
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

        if ($exception instanceof ApiException) {
            return $exception->render($request);
        }

        if ($request->header('X-ISAPI') == 1) {
            if (method_exists($exception, 'getStatusCode')) {
                $status_code = $exception->getStatusCode();
            } else {
                $status_code = 500;
            }

            $error_msg = config('app.debug') && !empty($exception->getMessage()) ? $exception->getMessage() : self::getHttpStatus($status_code);

            //如果是致命异常且是debug模式
            if ($exception instanceof \Symfony\Component\Debug\Exception\FatalErrorException && config('app.debug')) {
                $error_msg = $exception->getMessage();
            }

            return response()->json(self::getJsonData($exception, $status_code, $error_msg), $status_code, [], JSON_UNESCAPED_UNICODE);
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

    /**
     * 获取返回数据
     * @param \Exception $e
     * @param $http_code
     * @param $error_msg
     * @param string $error_code
     * @return array
     */
    public static function getJsonData(\Exception $e, $http_code, $error_msg, $error_code = '400')
    {
        $data = [
            'status' => false,
            'error_msg' => $error_msg,
            'error_code' => $error_code,
            'http_code' => $http_code,
            'data' => [],
            'list' => [],
            'request_id' => app()->make('request_id')
        ];

        config('app.debug') == 'true' ? $data['debug'] = [
            'type' => get_class($e),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'trace' => explode("\n", $e->getTraceAsString())
        ] : true;

        return $data;
    }

    /**
     * api抛错数据
     * @param \App\Exceptions\ApiException $e
     * @return array
     */
    public static function formatApiData(\App\Exceptions\ApiException $e)
    {
        $error_code = $e->getErrorId();
        $error_msg = $e->getMessage();
        $http_code = $e->getCode();

        $data = self::getJsonData($e, $http_code, $error_msg, $error_code);

        return $data;
    }

    /**
     * report
     * @param \Exception $exception
     * @throws \Exception
     */
    public static function report(\Exception $exception)
    {
        //报告异常到sentry
        if (!function_exists('report_to_sentry')) {
            throw $exception;
        }

        report_to_sentry($exception);

        try {
            $logger = app()->make(LoggerInterface::class);
        } catch (\Exception $ex) {
            throw $exception; // throw the original exception
        }

        $logger->error(
            $exception->getMessage(),
            [
                'exception' => $exception,
                'request' => request() . "\n\n" . 'request_id:' . app()->make('request_id') . "\n",
                'get_params' => request()->query->all(),
                'post_params' => request()->request->all(),
            ]
        );
    }
}