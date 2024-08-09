<?php
namespace Karlhsu\Yidun\Service;
class ImageService extends BaseService
{
    const URL = '/v5/image/check';
    const BASE64_URL = '/v5/image/base64Check';
    const VERSION = 'v5.2';
    const TIMEOUT = 10;


    public function __construct($config) {
        $this->config = $config;
    }

    public function getUri()
    {
        return self::API_URL.self::URL;
    }

    public function getBase64Uri()
    {
        return self::API_URL.self::BASE64_URL;
    }

    /**
     * url图片同步校验
     * @see https://support.dun.163.com/documents/588434200783982592?docId=791131792583602176
     * @author KarlHsu
     */
    public function check($params)
    {
        $params['version'] = self::VERSION;
        $params = $this->toUtf8(array_merge($this->getCommonParams(), $params));
        $params["signature"] = $this->gen_signature($this->config['secret_key'], $params);
        $client = new \GuzzleHttp\Client(array_merge([
            'timeout' => self::TIMEOUT,
        ], $this->getCommonOptions()));
        $response = $client->request('POST', $this->getUri(), [
            'body' => http_build_query($params),
        ]);
        $result = json_decode($response->getBody()->getContents(), true);
        if ($this->checkResult($result)) {
            return $result['result'];
        }
        throw new \Exception($result['msg']);
    }

    /**
     * base64图片同步校验
     * @see https://support.dun.163.com/documents/588434200783982592?docId=791131792583602176
     * @author KarlHsu
     */
    public function checkBase64($params)
    {
        $params['version'] = self::VERSION;
        $params = $this->toUtf8(array_merge($this->getCommonParams(), $params));
        $params["signature"] = $this->gen_signature($this->config['secret_key'], $params);
        $client = new \GuzzleHttp\Client(array_merge([
            'timeout' => self::TIMEOUT,
        ], $this->getCommonOptions()));
        $response = $client->request('POST', $this->getBase64Uri(), [
            'body' => http_build_query($params),
        ]);
        $result = json_decode($response->getBody()->getContents(), true);
        if ($this->checkResult($result)) {
            return $result['result'];
        }
        throw new \Exception($result['msg']);
    }
}