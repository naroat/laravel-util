<?php


namespace Taoran\Laravel\Jwt;


use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\ValidationData;

class JwtAuth
{
    // 加密后的token
    private $token;
    // 解析JWT得到的token
    private $decodeToken;
    // 用户ID
    private $uid;
    // jwt密钥
    private $secrect = '';

    // jwt参数
    private $iss = '';// 该JWT的签发者
    private $aud = '';// 配置访问群体
    private $id = '';//配置ID

    public function __construct() {}

    private function __clone() {}

    /**
     * 获取token
     *
     * @return array
     */
    public function getToken()
    {
        return $this->token;
    }


    /**
     * 验证
     *
     * @return bool
     */
    public function check()
    {
        //接收token
        $this->token = $this->tryToGetToken();
        if (!$this->token) {
            return false;
        }

        //验证令牌
        if (!$this->verify()) {
            return false;
        }

        //验证过期时间
        if ($this->validate()['exp'] < time()) {
            return false;
        }

        return true;
    }

    /**
     * 接收token
     * @return string
     */
    public function tryToGetToken()
    {
        if (empty($this->token)) {
            //头信息中获取
            $this->token = $this->getTokenByHeader();
        }

        if (empty($this->token)) {
            //参数中获取token
            $this->token = $this->getTokenByUrl();

        }

        return (string)$this->token;
    }

    /**
     * 头信息中获取token
     *
     * @return array|string|null
     */
    public function getTokenByHeader()
    {
        return request()->header('Authorization');
    }

    /**
     * 参数中获取token
     *
     * @return array|string|null
     */
    public function getTokenByUrl()
    {
        return request()->get('token');
    }

    /**
     * 加密jwt
     */
    public function encode()
    {

        $time = time();
        $this->uid = create_guid();
        $this->token = (new Builder())
            ->setIssuer($this->iss)// Configures the issuer (iss claim)
            ->setAudience($this->aud)// 配置访问群体
            ->setId($this->id, true)// 配置id（jti声明），作为头项进行复制
            ->setIssuedAt($time)// 配置令牌的颁发时间（iat声明）
            ->setNotBefore($time + 1)// 配置令牌可以使用的时间（单位:分钟）1分钟
            ->setExpiration($time + 60)// 配置令牌的过期时间 (单位:秒) 60秒
            ->set('uid', $this->uid)// 配置一个名为“uid”的新声明
            ->sign(new Sha256(), $this->secrect)// 使用secrect作为密钥创建签名
            ->getToken(); // 检索生成的令牌

        return $this->token;
    }

    /**
     * 解密token
     */
    public function decode()
    {

        if (!$this->decodeToken) {
            $this->decodeToken = (new Parser())->parse((string)$this->token);
            $this->uid = $this->decodeToken->getClaim('uid');
        }
        return $this->decodeToken;

    }


    /**
     * 验证令牌是否超过有效期
     */
    public function validate()
    {
        $data = new ValidationData();
        $data->setAudience($this->aud);
        $data->setIssuer($this->iss);
        $data->setId($this->id);
        # 返回状态以及过期时间、uid
        $res['status'] = $this->decode()->validate($data);
        $res['exp'] = $this->decode()->getClaim('exp');
        $res['uid'] = $this->decode()->getClaim('uid');
        return $res;
    }

    /**
     * 验证令牌在生成后是否被修改
     * @return bool
     */
    public function verify()
    {
        $res = $this->decode()->verify(new Sha256(), $this->secrect);
        return $res;
    }

    /**
     * 路由-返回init信息
     * @return array
     */
    public function routeInit()
    {
        $token = $this->encode();
        return array(
            'result' => true,
            'token' => (string)$token
        );
    }

}

