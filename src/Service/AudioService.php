<?php
namespace Karlhsu\Yidun\Service;
class AudioService extends BaseService
{
    const URL = '/v2/audio/check';
    const VERSION = 'v2';
    const SUBMIT_URL = '/v4/audio/submit';
    const SUBMIT_VERSION = 'v4.1';
    const TIMEOUT = 10;


    public function __construct($config) {
        $this->config = $config;
    }

    public function getUri()
    {
        return self::API_URL.self::URL;
    }

    public function getSubmitUri()
    {
        return self::API_URL.self::SUBMIT_URL;
    }

    /**
     * 音频同步检测
     * @see https://support.dun.163.com/documents/588434426518708224?docId=588884842603749376
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
     * 音频同步检测
     * @see https://support.dun.163.com/documents/588434426518708224?docId=588884892356583424
     * @author KarlHsu
     */
    public function submit($params)
    {
        $params['version'] = self::SUBMIT_VERSION;
        $params = $this->toUtf8(array_merge($this->getCommonParams(), $params));
        $params["signature"] = $this->gen_signature($this->config['secret_key'], $params);
        $client = new \GuzzleHttp\Client(array_merge([
            'timeout' => self::TIMEOUT,
        ], $this->getCommonOptions()));
        $response = $client->request('POST', $this->getSubmitUri(), [
            'body' => http_build_query($params),
        ]);
        $result = json_decode($response->getBody()->getContents(), true);
        if ($this->checkResult($result)) {
            return $result['result'];
        }
        throw new \Exception($result['msg']);
    }

    /**
     * 异步回调
     * @see https://support.dun.163.com/documents/588434426518708224?docId=589589116186927104
     * @author KarlHsu
     */
    public function callback($params)
    {
        $result = json_decode($params, true);
        if ($this->checkResult($result)) {
            return $result['result'];
        }
        throw new \Exception($result['msg']);
    }

}