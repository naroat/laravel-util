<?php

namespace App\Exceptions;

/**
 * Class ApiException
 * @package App\Exceptions
 */
class ApiException extends \Exception
{
    public $sentry = false;

    /**
     * ApiException constructor.
     * @param string $message 错误信息
     * @param string $error_id 错误id
     * @param string $code http状态码
     */
    public function __construct($message = '', $error_id = 'ERROR', $code = 400, $sentry = false)
    {
        parent::__construct($message, $code);
        empty($error_id) || $this->error_id = $error_id;
        $this->sentry = $sentry;
    }

    /**
     * 获取错误id
     * @return string
     */
    public function getErrorId()
    {
        return empty($this->error_id) ? 'ERROR' : $this->error_id;
    }

    /**
     * Report the exception.
     *
     * @param  \Illuminate\Http\Request
     * @return void
     */
    public function render($request)
    {
        if ($this->sentry) {
            report_to_sentry($this);
        }

        $http_code = $this->getCode();

        if ($request->header('X-ISAPI') == 1) {
            $data = ExceptionHandler::formatApiData($this);
            return response()->json($data, $http_code, [], JSON_UNESCAPED_UNICODE);
        } else {
            $trace = explode("\n", $this->getTraceAsString());
            $type = get_class($this);
            $error_html = '<html><header><title>出错了！</title></header><body>';
            $error_html .= "<h1>出错了！</h1>";
            $error_html .= "<h2>错误信息:{$this->getMessage()}</h2>";
            $error_html .= '<div><div>';
            $error_html .= "<h3>file:  {$this->getFile()}</h3>";
            $error_html .= "<h3>line:  {$this->getLine()}</h3>";
            $error_html .= "<h3>type:  {$type}</h3>";
            foreach ($trace as $val) {
                $error_html .= "<p>{$val}</p>";
                $error_html .= "<hr>";
            }
            $error_html .= '</div></div></body></html>';
            echo $error_html;
            exit;
        }
    }

}