<?php

namespace Taoran\Laravel\Core;

use App\Exceptions\ApiException;


/**
 * 控制器基类
 * Class Controller
 */
trait Controller
{

    /**
     * 应答数据api
     * @param array || Collection || Object $data
     * @param array $list
     * @return \Illuminate\Http\JsonResponse
     */
    public function response($data = array(), $list = array(), $code = '200')
    {
        $data = [
            'status' => true,
            'error_msg' => 'ok',
            'error_code' => '',
            'data' => empty($data) ? null : $data,
            'list' => $list,
//            'request_id' => app()->make('request_id')
        ];

        return response()->json($data, $code, array(), JSON_UNESCAPED_UNICODE);
    }

    /**
     * 应答列表数据api
     * @param array $list
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseList($list = array())
    {
        return $this->response(array(), $list);
    }
}



