<?php

namespace Karlhsu\Yidun\Service;

class BaseService
{
    const API_URL = "http://as.dun.163.com";

    const INTERNAL_STRING_CHARSET = 'auto';

    const SIGNATURE_METHOD = 'MD5';

    protected array $config;


    /**
     * 将输入数据的编码统一转换成utf8
     * @params 输入的参数
     * @author KarlHsu
     */
    protected function toUtf8($params){
        $utf8s = array();
        foreach ($params as $key => $value) {
            $utf8s[$key] = is_string($value) ? mb_convert_encoding($value, "utf8", self::INTERNAL_STRING_CHARSET) : $value;
        }
        return $utf8s;
    }

    /**
     * 计算参数签名
     * $params 请求参数
     * $secretKey secretKey
     * @author KarlHsu
     */
    protected function gen_signature($secretKey, $params){
        ksort($params);
        $buff="";
        foreach($params as $key=>$value){
            if($value !== null) {
                $buff .=$key;
                $buff .=$value;
            }
        }
        $buff .= $secretKey;
        return md5($buff);
    }

    protected function genOpenApiSignature($secretKey, $params, $header) {
        // 1. 参数名按照ASCII码表升序排序
        $paramNames = array_keys($params);
        sort($paramNames);

        // 从header中取得timestamp和nonce
        $timestamp = $header["X-YD-TIMESTAMP"];
        $nonce = $header["X-YD-NONCE"];

        // 2. 按照排序拼接参数名与参数值
        $paramBuffer = "";
        foreach ($paramNames as $paramName) {
            $paramValue = $params[$paramName];
            $paramBuffer .= $paramName . ($paramValue ?? "");
        }

        // 3. 将secretKey，nonce，timestamp拼接到最后
        $paramBuffer .= $secretKey . $nonce . $timestamp;

        try {
            // 使用SHA-1算法计算散列值
            return sha1($paramBuffer);
        } catch (\Exception $e) {
            // 错误处理
            error_log("[ERROR] not supposed to happen: " . $e->getMessage());
        }
        return "";
    }

    /**
     * 检查结果
     * @author KarlHsu
     */
    public function checkResult($result)
    {
        if ($result['code'] == 200) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 返回
     * @author KarlHsu
     */
    public function returnResult()
    {
        return [
            "code" => 200,
            "msg" => "接收成功"
        ];
    }


    /**
     * 公共参数
     * @author KarlHsu
     */
    public function getCommonParams()
    {
        return [
            "secretId" => $this->config['secret_id'],
            "businessId" => $this->config['business_id'] ?? '',
            "timestamp" => time() * 1000,// time in milliseconds
            "nonce" => sprintf("%d", rand()), // random int
            "signatureMethod" => self::SIGNATURE_METHOD,
        ];
    }

    /**
     * 公共请求
     * @author KarlHsu
     */
    public function getCommonOptions()
    {
        return [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
            ],
        ];
    }

}