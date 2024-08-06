<?php
namespace Karlhsu\Yidun\Service;
class AudioService extends BaseService
{
    const URL = '/v2/audio/check';
    const SUBMIT_URL = '/v2/audio/submit';
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
        return '失败';
    }

    /**
     * 音频同步检测
     * @see https://support.dun.163.com/documents/588434426518708224?docId=588884892356583424
     * @author KarlHsu
     */
    public function submit($params)
    {
        $params = [
            "callback" => json_encode(array_merge([
                "type" => 'audio'
            ], $params['callback'] ?? []), JSON_UNESCAPED_UNICODE),
            "callbackUrl" => $params['callback_url'] ?? $this->config['callback_url']
        ];
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
        return '失败';
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
        return '失败';
    }

}