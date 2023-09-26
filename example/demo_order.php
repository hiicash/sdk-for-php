<?php

require __DIR__.'/../vendor/autoload.php';

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
    'body' => '华为手机 Mate60 Pro+', // 商品描述
];

$ret = $client->create($data);    // 创建订单
print_r($ret);                    // 返回数据