<?php


namespace Taoran\Laravel;


class Response
{
    /**
     * 错误码
     *
     * @var array
     */
    protected $code = [
        200 => 'ok'
    ];

    /**
     * 响应
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(array $data = [], int $code = 200)
    {
        //响应数据
        $responseData = [
            'errmsg' => $this->code[$code] ?? '',
            'errno' => $code,
            'data' => empty($data) ? null : $data,
            'runtime' => (microtime(true) - LARAVEL_START) . ' ms'
        ];

        //debug
        //!config('app.debug') ? false : $responseData['debug'] = '';

        return response()->json($responseData, $code, [], JSON_UNESCAPED_UNICODE);
    }
}
