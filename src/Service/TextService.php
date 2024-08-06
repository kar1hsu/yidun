<?php
namespace Karlhsu\Yidun\Service;
class TextService extends BaseService
{
    const URL = '/v5/text/check';
    const TIMEOUT = 2;

    public function __construct($config) {
        $config['version'] = self::VERSION;
        $this->config = $config;
    }

    public function getUri()
    {
        return self::API_URL.self::URL;
    }

    /**
     * 文本校验
     * @see https://support.dun.163.com/documents/588434200783982592?docId=791131792583602176
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
}