<?php
/**
 * Created by aaron <2590419211@qq.com>.
 * User: john
 * Date: 2017/3/30
 * Time: 15:32
 */

//创建一个客户端 使用异步的方式。
$cli = new swoole_http_client('www-tangwt.dev.lanyi99.cn', 80);

$cli->setHeaders([
    'Host' => "www-tangwt.dev.lanyi99.cn",
    "User-Agent" => 'Chrome/49.0.2587.3',
    'Accept' => 'text/html,application/xhtml+xml,application/xml',
    'Accept-Encoding' => 'gzip',
]);

$cli->get('/api_v1/token/checktoken', function ($cli) {
    echo "Length: " . strlen($cli->body) . "\n";
    echo $cli->body;
});

echo 2222;