<?php

namespace HiiCash\Order;

class Client
{
    private $publishUrl = 'https://hiicash.oss-ap-northeast-1.aliyuncs.com/gateway.txt';
    private $apiUrl;
    private $mchNo;
    private $appId;
    private $secretKey;

    public function __construct(
        string $mchNo,
        string $appId,
        string $secretKey,
        string $apiUrl = null
    ) {
        $this->mchNo = $mchNo;
        $this->appId = $appId;
        $this->secretKey = $secretKey;
        $this->apiUrl = $apiUrl;
        $this->checkApiUrl();
    }

    private function checkApiUrl()
    {
        if ($this->apiUrl === null) {
            $this->apiUrl = file_get_contents($this->publishUrl);
        }
    }

    /*
     * 创建订单
     * @return json
     */
    public function create(array $data)
    {
        // hook data
        if (isset($data['body'])) {
            if (isset($data['body']['title'])) {
                $data['body'] = [$data['body']];
            }
            $data['body'] = json_encode($data['body'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        return $this->merge('POST', 'pay/order/create', $data);
    }

    /*
     * 查询订单
     * @return mixed
     */
    public function query(array $data)
    {
        return $this->merge('POST', 'pay/order/query', $data);
    }

    /*
     * 关闭订单
     * @return json
     */
    public function close(array $data)
    {
        return $this->merge('POST', 'pay/order/close', $data);
    }

    /*
     * 验证 notify 数据
     * @return Boolean
     */
    public function check_notify(array $data, array $headers): bool
    {
        return ($this->sign($data, $headers) === $this->header($headers, 'HiicashPay-Signature'));
    }

    public function notify_msg($code = 'success', $msg = '')
    {
        return json_encode(['returnCode' => $code, 'returnMsg' => $msg],
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    protected function get_millisecond(): int
    {
        list($mse, $sec) = explode(' ', microtime());

        return (int) sprintf('%.0f', ((float) $mse + (float) $sec) * 1000);
    }

    protected function header($headers, $key)
    {
        return $headers[$key] ?? $headers[strtolower($key)];
    }

    protected function sign(array $data, array $headers): string
    {
        $body = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $payload = $this->header($headers, 'HiicashPay-Timestamp') . "\n" .
            $this->header($headers, 'HiicashPay-Nonce') . "\n" . $body . "\n";

        return $this->generate_signature($payload);
    }

    protected function generate_signature(string $payload): string
    {
        // 使用 HMAC-SHA512 算法生成签名
        $hash = hash_hmac("sha512", $payload, $this->secretKey, true);

        // 将签名转换为大写字符串
        return strtoupper(bin2hex($hash));
    }

    protected function merge(string $method, string $uri, array $data = [])
    {
        $data['mchNo'] = $data['mchNo'] ?? $this->mchNo;
        $data['appId'] = $data['appId'] ?? $this->appId;
        $data['version'] = '1.0';

        $headers = [
            'HiicashPay-Timestamp' => $this->get_millisecond(),
            'HiicashPay-Nonce' => md5(time()),
            'HiicashPay-AppId' => $this->appId
        ];
        $headers['HiicashPay-Signature'] = $this->sign($data, $headers);
        $httpHeaders = [];
        foreach ($headers as $key => $header) {
            $httpHeaders[] = $key . ': ' . $header;
        }
        $jsonStr = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if ($method === 'POST') {
            return $this->http_post_json($uri, $jsonStr, $httpHeaders);
        }

        return false;
    }

    protected function http_post_json(string $uri, string $jsonStr, array $headers, array $options = [])
    {
        $ch = curl_init($this->apiUrl . $uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);

        $headers[] = 'Content-Type: application/json; charset=utf-8';
        $headers[] = 'Content-Length: ' . strlen($jsonStr);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if (! empty($options)) {
            curl_setopt_array($ch, $options);
        }
        $execute = curl_exec($ch);
        curl_close($ch);

        if ($execute) {
            return json_decode($execute, true);
        }

        return false;
    }
}