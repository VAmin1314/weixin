<?php
class WechatCash
{
    // key
    protected $key = 'Hangzhouyouwomeishiwangluoweixin';

    // 提现接口
    protected $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';

    // 证书地址
    protected $sslCert = "./cert/apiclient_cert.pem";
    protected $sslKey = "./cert/apiclient_key.pem";

    public function __construct ()
    {
        //
    }

    /**
     * 提现
     * @Author   LiuJian
     * @DateTime 2017-03-29
     * @return   [type]     [description]
     */
    public function cash ()
    {
        $data = [
            'mch_appid' => 'wx6276649e045d0022',
            'mchid' => '1452850622',
            'nonce_str' => 'ssssssssss',
            'openid' => 'o2n7KwMc7DzTCKGtiszjfsH2_ssI',
            'amount' => '11',
            'spbill_create_ip' => '10.0.0.76',
            'partner_trade_no' => '11111',
            'desc' => '用户提现',
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

        return var_dump($this->getResult($this->url, $data));
    }

    /**
     * curl 方法
     * @Author   LiuJian
     * @DateTime 2017-03-29
     * @param    [type]     $url  [description]
     * @param    [type]     $data [description]
     * @return   [type]           [description]
     */
    public function getResult ($url = null, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        // 证书
        curl_setopt($ch, CURLOPT_SSLCERT, $this->sslCert);
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
        foreach ($array as $k => $v) {
            $str .= $k . "=" . $v . "&";
        }

        $str = $str."key={$this->key}";
        $result = strtoupper(md5($str));

        return $result;
    }
}

$cash = new WechatCash();
$cash->cash();




