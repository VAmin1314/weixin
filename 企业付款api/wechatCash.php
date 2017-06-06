<?php
namespace App\Libs;

class weChatCash
{
    // key
    protected $key = '';
    protected $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
    protected $mchAppid = '';
    protected $mchid = '';
    // ssl
    protected $sslCurt;
    protected $sslKey;

    public function __construct ()
    {
        $this->sslCurt = storage_path("cret/apiclient_cert.pem");
        $this->sslKey = storage_path("cret/apiclient_key.pem");
    }

    public function start ($data)
    {
        if (empty($data)) {
            return ['status' => 'error', 'message' => '缺少参数'];
        }

        $data = [
            'openid' => $data['openid'],
            'amount' => $data['amount'],
            'partner_trade_no' => $data['partner_trade_no'],
            'desc' => $data['desc'],
            'mch_appid' => $this->mchAppid,
            'mchid' => $this->mchid,
            'nonce_str' => 'woshisuijide'.mt_rand(1000, 99999),
            'spbill_create_ip' => '120.27.50.204',
            'check_name' => 'NO_CHECK',
            're_user_name' => '测试可以不写的'
        ];

        $sign = $this->getSign($data);

        $data = '<xml>
                    <mch_appid>'.$data['mch_appid'].'</mch_appid>
                    <mchid>'.$data['mchid'].'</mchid>
                    <nonce_str>'.$data['nonce_str'].'</nonce_str>
                    <partner_trade_no>'.$data['partner_trade_no'].'</partner_trade_no>
                    <openid>'.$data['openid'].'</openid>
                    <check_name>'.$data['check_name'].'</check_name>
                    <amount>'.$data['amount'].'</amount>
                    <desc>'.$data['desc'].'</desc>
                    <re_user_name>'.$data['re_user_name'].'</re_user_name>
                    <spbill_create_ip>'.$data['spbill_create_ip'].'</spbill_create_ip>
                    <sign>'.$sign.'</sign>
                </xml>';

        $info = $this->getCurl($this->url, $data);

        return $this->xmlToArray($info);
    }

    /**
     * curl 方法
     * @Author   LiuJian
     * @DateTime 2017-03-29
     * @param    [type]     $url  [description]
     * @param    [type]     $data [description]
     * @return   [type]           [description]
     */
    public function getCurl ($url = null, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        // 证书
        curl_setopt($ch, CURLOPT_SSLCERT, $this->sslCurt);
        curl_setopt($ch, CURLOPT_SSLKEY, $this->sslKey);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($ch);
        if (curl_errno($ch)) {
            return 'Errno' . curl_error($ch);
        }

        return $output;
    }

    /**
     * 作用：生成签名
     */
    function getSign($array)
    {
        ksort($array);
        $str = '';

        foreach ($array as $k => $v) {
            $str .= $k . "=" . $v . "&";
        }

        $str = $str."key={$this->key}";
        $result = strtoupper(md5($str));

        return $result;
    }


    /**
     * 将xml数据转换成数组
     *
     * @Author   LiuJian
     * @DateTime 2017-06-06
     * @param    string     $xml xml 数据
     * @return   array          转换的数组
     */
    protected function xmlToArray($xml)
    {
        // 将XML转为array
        $array = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $array = json_decode(json_encode($array), true);

        return $array;
    }
}






