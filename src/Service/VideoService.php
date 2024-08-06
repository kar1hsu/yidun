<?php
namespace Karlhsu\Yidun\Service;
class VideoService extends BaseService
{
    const URL = '/v4/videosolution/submit';
    const TIMEOUT = 30;


    public function __construct($config) {
        $this->config = $config;
    }

    public function getUri()
    {
        return self::API_URL.self::URL;
    }

    /**
     * 视频异步检测
     * @see https://support.dun.163.com/documents/594247746924453888?docId=594604048299823104
     * @author KarlHsu
     */
    public function submit($params)
    {
        $params = [
            "callback" => json_encode(array_merge([
                "type" => 'video'
            ], $params['callback'] ?? []), JSON_UNESCAPED_UNICODE),
            "callbackUrl" => $params['callback_url'] ?? $this->config['callback_url']
        ];
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
     * 异步回调
     * @see https://support.dun.163.com/documents/594247746924453888?docId=605248448360099840
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