<?php
/**
 * Created by PhpStorm.
 * User: panus
 * Date: 2016/10/27
 * Time: 11:09
 */

namespace HuoLib\Tool;;


class RSASign
{
    private static $_priKey = 'file://'.__DIR__.'/rsa/rsa_private_key.pem';
    private static $_pubKey = 'file://'.__DIR__.'/rsa/rsa_public_key.pem';
    private static $_passphrase = '';

    //java 默认生成sign 方式
    const SIGN_DEFAULT_JAVA = OPENSSL_ALGO_MD5;

    public static function generateSign($data)
    {
        $stringToBeSigned = '';
        if (is_array($data)) {
            foreach ($data as $k => $v)
            {
                $stringToBeSigned .= "$k=$v";
            }
        } else {
            $stringToBeSigned = $data;
        }

        return md5($stringToBeSigned);
    }

    public static function encryptWithPri($data)
    {
        $res = openssl_get_privatekey(self::$_priKey, self::$_passphrase);
        $bool = openssl_private_encrypt($data, $sign, $res);
        openssl_free_key($res);
        if($bool){
            return base64_encode($sign);
        }else{
            return $data;
        }
    }

    public static function decryptWithPri($sign)
    {
        $sign = base64_decode($sign);
        $res = openssl_get_privatekey(self::$_priKey);
        $bool = openssl_private_decrypt($sign, $data, $res);
        openssl_free_key($res);
        if($bool){
            return $data;
        }else{
            return '';
        }
    }

    /**
     * 私钥解密数据
     *
     * @param $sign
     * @return string
     */
    public function decryptWithPriFenDuan($sign)
    {
        $sign = base64_decode($sign);
        $res = openssl_get_privatekey($this->_priKey);

        $result  = '';
        for($i = 0; $i < strlen($sign)/128; $i++  ) {
            $data = substr($sign, $i * 128, 128);
            openssl_private_decrypt($data, $decrypt, $res);
            $result .= $decrypt;
        }
        openssl_free_key($res);
        return $result;
    }

    public function encryptWithPub($data)
    {
        $res = openssl_get_publickey(self::$_pubKey);
        $bool = openssl_public_encrypt($data, $sign, $res);
        openssl_free_key($res);
        if($bool){
            return base64_encode($sign);
        }else{
            return $data;
        }
    }

    public static function decryptWithPub($sign)
    {
        $sign = base64_decode($sign);
        $res = openssl_get_publickey(self::$_pubKey);
        $bool = openssl_public_decrypt($sign, $data, $res);
        openssl_free_key($res);
        if($bool){
            return $data;
        }else{
            return '';
        }
    }

    public static function signWithPri($data, $alg = OPENSSL_ALGO_SHA1)
    {
        $data = self::generateSign($data);
        $priId = openssl_pkey_get_private(self::$_priKey);
        $bool = openssl_sign($data, $sign, $priId, $alg);
        if($bool){
            return base64_encode($sign);
        }else{
            return $data;
        }
    }

    public static function verifySign($data, $sign, $alg = OPENSSL_ALGO_SHA1)
    {
        $sign = base64_decode($sign);
        $res = openssl_get_publickey(self::$_pubKey);
        $verify = openssl_verify($data, $sign, $res, $alg);
        openssl_free_key($res);
        if($verify == 1){
            return true;
        }else{
            return false;
        }
    }


}