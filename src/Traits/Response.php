<?php


namespace Taoran\Laravel\Traits;


trait Response
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
    public function responseJson(array $data = [], int $code = 200)
    {
        //响应数据
        $responseData = [
            'errmsg' => $this->code[$code] ?? '',
            'errno' => $code,
            'data' => empty($data) ? null : $data,
            'runtime' => '0.1 ms'
        ];

        //debug
        //!config('app.debug') ? false : $responseData['run_time'] = get_run_time() . ' ms';

        return response()->json($responseData, $code, [], JSON_UNESCAPED_UNICODE);
    }
}
