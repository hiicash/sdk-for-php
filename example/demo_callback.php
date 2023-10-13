<?php

require __DIR__ . '/../vendor/autoload.php';

$mchNo = '123456'; // 商户号
$appId = '01hanykjnwebz1a9dnkjfgehtj'; // App ID
$secretKey = 'ErEOQtYoVd76uca4gbDNPiUbNEJP6Y6usdNrKAetxBQQhin07H7dhBfKOiSYypxg'; // 应用私钥
$client = new \HiiCash\Order\Client($mchNo, $appId, $secretKey);

$ret = $client->check_notify($_POST, getallheaders());    // 验证 notify 数据

if ($ret) {
    // 处理订单逻辑

    return $client->notify_msg(); // 返回成功信息
}

return $client->notify_msg('error');