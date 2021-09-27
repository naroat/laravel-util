<?php
namespace Taoran\Laravel\Jwt;

use Taoran\Laravel\Exception\ApiException;
use Illuminate\Support\Arr;
use \Firebase\JWT\JWT;
use Carbon\Carbon;

class JwtAuth
{
    private static $expires = 60;                   //设置单次过期时间(分钟)
    private static $maxExpires = 60 * 24 * 30;      //最大过期时间(分钟),app使用
    private static $seesionKey = '';                //会话key

    public static $encodeData = array();
    public static $sessionData = array();

    private static $type = 'token';                 //类型: token or session
    private static $sessionPrefix = 'session';

    private static $token;                          //获取到的token

    public function __construct()
    {
        self::$sessionPrefix = config('session.cookie');
    }

    /**
     * 验证jwt
     * @param string $token
     * @param object $request
     * @return bool
     */
    public function check($request, $token = '')
    {

        //尝试获取token
        $token = $this->tryToGetToken($request, $token);
        if (!$token) {
            return false;
        }

        //session类型,直接获取token作为key
        if (self::$type == 'session') {
            $session_key = \Crypt::decrypt($token);
            if (!$session_key) {
                return false;
            }
            self::$seesionKey = $session_key;
        } else {
            try {
                self::$encodeData = (array)JWT::decode($token, config('app.key'), array('HS256'));
            } catch (\Exception $e) {
                return false;
            }

            self::$seesionKey = self::$encodeData['session_key'];
        }

        //通过sessionkey获取数据
        $data = \Cache::get(self::$sessionPrefix . ':' . self::$seesionKey);
        if (empty($data)) {
            return false;
        }

        if (self::$type == 'token') {
            //验证过期时间
            if ($data['expires_time'] < time()) {
                return false;
            }

            //过期时间
            $expires_time = $data['device'] == 'web' ? self::$expires : self::$maxExpires;

            $time = time();
            $data['expires_time'] = $time + $expires_time * 60;
            $data['refresh_time'] = $time;

            //拥有sk字段的,需验证签名
            if (!empty($data['sk'])) {
                if (!$this->checkSign($data['sk'])) {
                    return false;
                }
            }

            \Cache::put(self::$sessionPrefix . ':' . self::$seesionKey, $data, Carbon::now()->addMinutes($expires_time));
        } else {
            $expires_time = config('session.lifetime');
            $time = time();
            $data['expires_time'] = $time + ($expires_time * 60);
            $data['device'] = 'web_session';
            self::$maxExpires = $expires_time;
            \Cache::put(self::$sessionPrefix . ':' . self::$seesionKey, $data, Carbon::now()->addMinutes($expires_time));
        }

        self::$sessionData = $data;

        return true;
    }

    /**
     * 获取token
     * @param $request
     * @param $token
     * @return bool
     */
    protected function tryToGetToken($request, $token)
    {

        if (empty($token)) {
            //获取header内容
            $token = $this->getTokenByHeader();
        }

        if (empty($token)) {
            //获取url上token字段上的内容
            $token = $this->getTokenByUrl();
        }

        if (empty($token)) {
            //获取cookie上的内容
            $token = $this->getTokenByCookie();
            self::$type = 'session';
        }

        self::$token = $token;

        return $token;
    }

    /**
     * 通过Header获取token
     * @return array|string
     */
    public function getTokenByHeader()
    {
        return request()->header('Authorization');
    }

    /**
     * 通过Url获取token
     * @return mixed
     */
    public function getTokenByUrl()
    {
        return request()->query->get('token');
    }

    /**
     * 通过cookie获取token
     * @return mixed
     */
    public function getTokenByCookie()
    {
        return request()->cookies->get(config('session.cookie'));
    }

    /**
     * 生成token
     * @param array $session_data
     * @return string
     */
    public function createToken(array $session_data = array(), $device = '')
    {
        load_helper('Password');
        $session_key = create_guid();

        $time = time();
        $expires_time = $time + (self::$expires * 60);
        $secret_key = '';
        $is_sign = false;

        if (empty($device)) {
            $device = 'web';

            //判断设备类型
            $is_app = boolval(request()->header('X-ISAPP'));
            if ($is_app) {
                $device = 'app';
            }

            $is_sign = config('app.is_web_sign', false);
        }

        if ($device == 'app' || $is_sign) {
            $expires_time = $time + (self::$maxExpires * 60);
            $secret_key = create_guid();
        }

        $session_data['device'] = $device;
        $session_data['create_at'] = $time;      //创建时间
        $session_data['expires_time'] = $expires_time;  //过期时间
        $session_data['sk'] = $secret_key;

        $data = array(
            'session_key' => $session_key
        );

        //过期时间
        $session_expires_time = $device == 'web' ? self::$expires : self::$maxExpires;

        \Cache::add(self::$sessionPrefix . ':' . $session_key, $session_data, Carbon::now()->addMinutes($session_expires_time));
        return [
            'token' => JWT::encode($data, config('app.key')),
            'sk' => $secret_key,
            'is_sign' => intval($is_sign),
            'expires_time' => $session_expires_time
        ];
    }

    /**
     * 设置数据
     * @param $key
     * @param $value
     * @return bool
     */
    public function set($key, $value = '')
    {
        //获取最新数据
        $data = \Cache::get(self::$sessionPrefix . ':' . self::$seesionKey);
        if (empty($data)) {
            return false;
        }

        $keys = is_array($key) ? $key : [$key => $value];

        foreach ($keys as $key => $value) {
            Arr::set($data, $key, $value);
        }

        //过期时间
        $expires_time = $data['device'] == 'web' ? self::$expires : self::$maxExpires;

        $time = time();
        $data['expires_time'] = $time + ($expires_time * 60);
        $data['refresh_time'] = $time;
        \Cache::put(self::$sessionPrefix . ':' . self::$seesionKey, $data, Carbon::now()->addMinutes($expires_time));
        self::$sessionData = $data;
        return true;
    }

    /**
     * 获取数据
     * @param $key
     * @return array | bool
     */
    public function get($key = '', $default = '')
    {
        $data = self::$sessionData;
        if (empty($data)) {
            return false;
        }

        if (empty($key)) {
            return $data;
        }

        return Arr::get($data, $key, $default);
    }

    /**
     * 删除数据
     * @param $key
     * @return array | bool
     */
    public function delete($key)
    {
        //获取最新数据
        $data = \Cache::get(self::$sessionPrefix . ':' . self::$seesionKey);
        if (empty($data)) {
            return false;
        }

        if (!empty($key)) {
            Arr::forget($data, $key);

            //过期时间
            $expires_time = $data['device'] == 'web' ? self::$expires : self::$maxExpires;

            $time = time();
            $data['expires_time'] = $time + ($expires_time * 60);
            $data['refresh_time'] = $time;

            \Cache::put(self::$sessionPrefix . ':' . self::$seesionKey, $data, Carbon::now()->addMinutes($expires_time));
            self::$sessionData = $data;
        }

        return true;
    }

    /**
     *  销毁
     */
    public function destroy()
    {
        \Cache::forget(self::$sessionPrefix . ':' . self::$seesionKey);
        self::$sessionData = array();
    }

    /**
     * 验证签名
     * @param $secret_key
     * @return bool
     * @throws ApiException
     */
    public function checkSign($secret_key)
    {
        //验证时间
        $timestmps = intval(request()->header('X-Hztimestmps'));
        $maxtime = (time() + 60 * 30) * 1000;
        $mintime = (time() - 60 * 30) * 1000;
        if ($timestmps > $maxtime || $timestmps < $mintime) {
            throw new ApiException('签名错误,请确保手机时间准确!', 'SGIN_ERROR');
        }

        $method = strtoupper(request()->method());  //请求方式
        $host_and_path = request()->getHttpHost() . request()->getPathInfo();  //获取主机
        $params = request()->input();

        ksort($params);
        $params_str = $this->toUrlParams($params);

        //拼接签名字符串(请求方式/秘钥/时间戳/主机路径?请求参数)
        $src_str = $method . '/' . $secret_key . '/' . $timestmps . '/' . $host_and_path . '?' . $params_str;
        $signStr = base64_encode(md5($src_str));

        //对比签名是否正确
        if ($signStr != request()->header('X-Signingkey')) {
            throw new ApiException('签名错误,请联系网站管理员!', 'SGIN_ERROR');
        }

        return true;
    }


    /**
     * 格式化参数格式化成url参数
     */
    public function toUrlParams($values)
    {
        $buff = "";
        foreach ($values as $k => $v) {
            if ($v == '' || is_array($v) || is_object($v)) {
                continue;
            }

            $buff .= $k . "=" . $v . "&";
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 设置过期时间
     * @param $minute
     */
    public function setExpires($minute)
    {
        self::$expires = $minute;
    }

    /**
     * 获取token
     * @param $minute
     */
    public function getToken()
    {
        return self::$token;
    }

    /**
     * 路由-返回init信息
     * @return array
     */
    public function routeInit()
    {
        $token = self::createToken();
        return array(
            'result' => true,
            'token' => $token['token'],
            'sk' => $token['sk'],
            'is_sign' => $token['is_sign'],
            'expires_time' => time() + $token['expires_time'] * 60
        );
    }

}