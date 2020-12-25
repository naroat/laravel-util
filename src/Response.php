<?php


namespace Taoran\Laravel;


class Response
{
    /**
     * 错误msg
     *
     * @var string
     */
    public $errmsg = '';

    /**
     * 异常信息
     *
     * @var null
     */
    public $exception = null;

    /**
     * 响应
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(array $data = [], int $code = 200)
    {
        //响应数据
        $responseData = [
            'errmsg' => $this->errmsg,
            'errno' => $code,
            'data' => empty($data) ? null : $data,
            'runtime' => (microtime(true) - LARAVEL_START) . ' ms'
        ];

        if (!empty($this->exception)) {
            $responseData['debug'] = $this->exception;
        }

        return response()->json($responseData, $code, [], JSON_UNESCAPED_UNICODE);
    }
}
