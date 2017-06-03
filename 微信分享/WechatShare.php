<?php

/**
 * 微信分享类
 * 非 thinkphp 请自行修改配置及缓存方式
 */
class WechatShare
{
    // 微信的 appid && appSecret
    protected $appid;
    protected $appSecret;

    public function __construct ()
    {
        $this->appid = '';
        $this->appSecret = '';
    }

    /**
     * 分享类
     * @Author   LiuJian
     * @DateTime 2017-06-03
     * @return   array          签名、时间戳、随机字符串等
     */
    public function weChatShare ()
    {
        $data = [];
        $data['timestamp'] = time();
        $data['url'] = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $data['noncestr'] = 'q_a';

        $accessToken = $this->getData('access_token');
        if (!$accessToken) {
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appid.'&secret='.$this->appSecret;
            $res = $this->getCurl($url);
            $accessToken = json_decode($res)->access_token;
            $this->setData('access_token', $accessToken, 7200);
        }

        $data['jsapi_ticket'] = $this->getData('ticket');
        if (!$data['jsapi_ticket']) {
            $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$accessToken.'&type=jsapi';
            $res = $this->getCurl($url);
            $data['jsapi_ticket'] = json_decode($res)->ticket;
            $this->setData('ticket', $data['jsapi_ticket'], 7200);
        }

        $str = $this->sortData($data);
        $weChatInfo = [
            'signature' => sha1($str),
            'time' => $data['timestamp'],
            'noncestr' => $data['noncestr'],
        ];

        return $weChatInfo;
    }

    public function sortData ($data)
    {
        $str = '';

        ksort($data);
        foreach ($data as $k => $v) {
            $str .= $k . '=' . $v . '&';
        }

        return rtrim($str, '&');
    }

    protected function setData ($name, $data, $time)
    {
        S($name, $data, $time);
    }

    protected function getData ($name)
    {
        return S($name);
    }

    protected function getCurl ($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
        $output = curl_exec($ch);

        curl_close($ch);

        return $output;
    }
}



