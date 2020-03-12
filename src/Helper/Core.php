<?php
if (!function_exists('response_json')) {
    function response_json($data = array(), $list = array(), $code = 200)
    {
        $data = [
            'status' => true,
            'error_msg' => 'ok',
            'error_code' => '',
            'data' => empty($data) ? null : $data,
            'list' => $list,
        ];
        return response()->json($data, $code, $list, JSON_UNESCAPED_UNICODE);
    }
}
if (!function_exists('verify')) {
    function verify($request, $data)
    {
        $validator = Validator::make($request->all(), $data);

        //验证失败
        if ($validator->fails()) throw new \App\Exceptions\ApiException($validator->errors()->first());

        return $request->all();
    }
}