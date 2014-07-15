<?php
/**
 * auth 授权 php版本
 * @author  caolong@feinno.com
 * @date    2014-07-15
 * @version 1.0
 * demo使用:
$options = array(
    'appKey'=>'',                   //从融云开发者平台申请的 AppKey
    'appSecret'=>'',                //从融云开发者平台申请的 AppSecret
    'userId'=>'',                   //用户 Id
    'deviceId'=>'',                 //设备标示
    'format'=>'json',               //返回格式 仅限于 json 或者 xml
    'name'=>'',                     //用户名称，最大长度 128 字节
    'portraitUri'=>''               //用户头像 URL，最大长度 1024 字节
);
$p = new AuthService($options);
$ret = $p->request();
print_r($ret);
 */

class AuthService{
    private $format = 'json';   //返回格式，仅限于 json 或者 xml
    private $appKey;            //从融云开发者平台申请的 AppKey
    private $appSecret;         //从融云开发者平台申请的 AppSecret
    private $userId;            //用户 Id，最大长度 32 字节，来自开发者自己的应用，必须保证全平台内不重复，重复的用户 Id 将被当作是同一个用户
    private $deviceId;          //设备id 唯一标示
    private $name;              //用户名称，最大长度 128 字节
    private $portraitUri;       //用户头像 URL，最大长度 1024 字节
    private $url = 'http://auth.cn.rong.io';

    /**
     * 初始化
     * @param $appKey
     * @param $appSecret
     * @param $userId
     * @param $deviceId
     * @param string $format
     * @param string $name
     * @param string $portraitUri
     */
    public function __construct($data = array()){
        $this->setOptions($data);
    }

    /**
     * 设置私有属性
     * @param $data
     */
    public function setOptions($data){
        $ref = new ReflectionClass('AuthService');
        $properties = array_keys($ref->getDefaultProperties());
        if(!empty($data) && is_array($data)) {
            foreach($data as $key=>$val) {
                if(in_array($key,$properties)) {
                    $this->$key = $val;
                }
            }
        }
    }

    /**
     * 发送请求
     */
    public function request(){
        $url = $this->url.'/reg.'.$this->format;
        $params = array(
            'userId'=>$this->userId,
            'format'=>$this->format,
            'deviceId'=>$this->deviceId,
            'name'=>$this->name,
            'portraitUri'=>$this->portraitUri,
        );
        $httpHeader = array(
            'appKey:'.$this->appKey,
            'appSecret:'.$this->appSecret
        );
        return $this->curl($url,$params,$httpHeader);
    }

    /**
     * curl发送请求
     * @param $url              请求地址
     * @param $params           请求参数
     * @param $httpHeader       httpheader数据
     * @param string $method    请求方法
     * @return bool|mixed
     */
    private  function curl($url,$params,$httpHeader,$method = 'post') {
        $ch = curl_init();
        if ('GET' == strtoupper($method)) {
            curl_setopt($ch, CURLOPT_URL, "$url?".http_build_query($params));
        }else{
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
        }
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch,CURLOPT_DNS_USE_GLOBAL_CACHE, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $ret = curl_exec($ch);
       if (false === $ret) {
            $err =  curl_errno($ch);
		    curl_close($ch);
            $r = array(
                'code'=>$err,
                'token'=>0,
                'userId'=>0,
            );
            self::formatResponseData($r,$this->format);
            return false;
        }
        curl_close($ch);
        return $ret;
    }


    /**
     * 格式化数据
     * @param unknown $controller
     */
    public static function formatResponseData($arr,$format= 'json'){
        switch($format){
            case 'xml':
                print_r(self::arrToXml($arr,true));
                break;
            case 'json':
            default:
                print_r(json_encode($arr));
                break;
        }
    }

    //数组toXML
    public static function arrToXml($data,$flag=true,$key='',$type=0){
        $xml = '';
        $flag && $xml .= "<result>\n";
        foreach ($data as $k=>$v){
            if(is_array($v)){
                    $xml .= (is_numeric($k)?"<item>":"<{$k}>")."\n".self::arrToXml($v,false,$k,$type).(is_numeric($k)?"</item>":"</{$k}>")."\n";
            }else{
                $xml .= "<{$k}>".(is_numeric($v)?$v:"<![CDATA[{$v}]]>")."</{$k}>\n";
            }
        }
        $flag && $xml .= '</result>';
        return $xml;
    }

}

$options = array(
    'appKey'=>'e0x9wycfx7flq',                   //从融云开发者平台申请的 AppKey
    'appSecret'=>'Hxv5X26zBs',                //从融云开发者平台申请的 AppSecret
    'userId'=>'1122',                   //用户 Id
    'deviceId'=>'2233',                 //设备标示
    'format'=>'json',               //返回格式 仅限于 json 或者 xml
    'name'=>'',                     //用户名称，最大长度 128 字节
    'portraitUri'=>''               //用户头像 URL，最大长度 1024 字节
);
$p = new AuthService($options);
$ret = $p->request();
print_r($ret);