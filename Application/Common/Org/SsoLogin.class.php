<?php
//Cookie配置
define('COOKIE_DOMAIN' ,C('COOKIE_DOMAIN')); //Cookie 作用域
define('COOKIE_PATH' , C('COOKIE_PATH')); //Cookie 作用路径
define('COOKIE_PRE' , C('COOKIE_PRE')); //Cookie 前缀，同一域名下安装多套系统时，请修改Cookie前缀
define('COOKIE_TTL' ,C('COOKIE_TTL')); //Cookie 生命周期，0 表示随浏览器进程
define('AUTH_KEY' , C('AUTH_KEY')); //密钥

class SsoLogin {
    /**
     * 设置 cookie
     * @param string $var     变量名
     * @param string $value   变量值
     * @param int $time    过期时间
     */
    public static function set_cookie($var, $value = '', $time = 0) {

        $s = $_SERVER['SERVER_PORT'] == '443' ? 1 : 0;
        $var = COOKIE_PRE.$var;
        $time += time();

        if(is_array($value)){
            $value = json_encode($value);
        }
        return setcookie($var, sso_codec::sys_auth($value, 'ENCODE'), $time, COOKIE_PATH, COOKIE_DOMAIN, $s);
    }

    /**
     * 获取通过 set_cookie 设置的 cookie 变量
     * @param string $var 变量名
     * @param string $default 默认值
     * @return mixed 成功则返回cookie 值，否则返回 false
     */
    public static function get_cookie($var, $default = '') {
        $var = COOKIE_PRE.$var;
        $json =  isset($_COOKIE[$var]) ? sso_codec::sys_auth($_COOKIE[$var], 'DECODE') : $default;
        return json_decode($json,true);
    }


}


class sso_codec {

    public static function sys_auth($string, $operation = 'DECODE', $expiry=86400) {

        if( empty($string) ){
            return '';
        }

        $ckey_length = 4;
        $key = md5(AUTH_KEY);
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);

        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length = strlen($string);

        $result = '';
        $box = range(0, 255);

        $rndkey = array();
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }

        for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if($operation == 'DECODE') {
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc.str_replace('=', '', base64_encode($result));
        }
    }
}