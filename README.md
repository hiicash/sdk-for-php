# HiiCash sdk for php

如果你想使用本项目请使用 composer 安装

```$xslt
$ composer require hiicash/sdk-for-php
```
或者在你的项目跟目录编辑 ```composer.json```

```$xslt
"require": {
    "hiicash/sdk-for-php": "^0.0.1"
}
```
更新
```$xslt
$ composer update
```

## 使用参考
```$xslt
<?php

require '../vendor/autoload.php';

$mchNo = '123456'; // 商户号
$appId = '01hanykjnwebz1a9dnkjfgehtj'; // App ID
$secretKey = 'ErEOQtYoVd76uca4gbDNPiUbNEJP6Y6usdNrKAetxBQQhin07H7dhBfKOiSYypxg'; // 应用私钥
$client = new \HiiCash\Order\Client($mchNo, $appId, $secretKey);

$data = [
    'mchOrderNo' => '20160427210604000490',  // 商户订单号
    'wayCode' => 'BINANCE', // 支付方式
    'amount' => 100, // 支付金额
    'currency' => 'cny', // 货币代码
    'subject' => 'mate 60', // 商品标题
    'body' => [["title" => "华为手机 Mate60 Pro+", "price" => 100, "qt" => 1]],  // 商品描述
];

$ret = $client->create($data);    // 创建订单
print_r($ret);                    // 返回数据

```

### 对应方法
* $client->create($data) 创建订单
* $client->query($data) 查询订单
* $client->close($data) 关闭订单
* $client->check_notify($data, $headers) 验证 notify 数据

> `$data` 为对应 api 的请求数组数据；   
> `$headers` 为回调服务器推送的 header 头信息。
___

> 建议阅读 `example` 内 demo 进行编码使用。