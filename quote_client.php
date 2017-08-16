<?php
/**
 * Created by aaron <2590419211@qq.com>.
 * User: john
 * Date: 2017/3/30
 * Time: 15:32
 */
$host = "quote.qihuo18.com";
$client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
//设置事件回调函数
$client->on("connect", function ($cli) use ($host) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!"§$%&/()=[]{}';
    $useChars = [];
    // select some random chars:
    for ($i = 0; $i < 16; $i++) {
        $useChars[] = $characters[mt_rand(0, strlen($characters)-1)];
    }
    // add spaces and numbers:
    if (true === true) {
        array_push($useChars, ' ', ' ', ' ', ' ', ' ', ' ');
    }
    if (false === true) {
        array_push($useChars, rand(0, 9), rand(0, 9), rand(0, 9));
    }
    shuffle($useChars);
    $randomString = trim(implode('', $useChars));
    $randomString = substr($randomString, 0, 16);
    $key = base64_encode($randomString);
    $path = "/socket.io/?EIO=3&transport=websocket";
    $port = 8000;
    $header = "GET " . $path . " HTTP/1.1\r\n";
    $header.= "Host: " . $host . ":" . $port . "\r\n";
    $header.= "Upgrade: websocket\r\n";
    $header.= "Connection: Upgrade\r\n";
    $header.= "Sec-WebSocket-Key: " . $key . "\r\n";
    $header.= "Sec-WebSocket-Version: 13\r\n\r\n";
    $cli->send($header);
    swoole_timer_tick(20000, function ($timer_id) use ($cli) {
        $cli->send(hybi10Encode("2"));
    });
});
function hybi10Encode($payload, $type = 'text', $masked = true)
{
        $frameHead = [];
        $frame = '';
        $payloadLength = strlen($payload);

        var_dump($payload);
    switch ($type) {
        case 'text':
            // first byte indicates FIN, Text-Frame (10000001):
            $frameHead[0] = 129;
            break;

        case 'close':
            // first byte indicates FIN, Close Frame(10001000):
            $frameHead[0] = 136;
            break;

        case 'ping':
            // first byte indicates FIN, Ping frame (10001001):
            $frameHead[0] = 137;
            break;

        case 'pong':
            // first byte indicates FIN, Pong frame (10001010):
            $frameHead[0] = 138;
            break;
    }

        // set mask and payload length (using 1, 3 or 9 bytes)
    if ($payloadLength > 65535) {
        $payloadLengthBin = str_split(sprintf('%064b', $payloadLength), 8);
        $frameHead[1] = ($masked === true) ? 255 : 127;
        for ($i = 0; $i < 8; $i++) {
            $frameHead[$i + 2] = bindec($payloadLengthBin[$i]);
        }

        // most significant bit MUST be 0 (close connection if frame too big)
        if ($frameHead[2] > 127) {
            $this->close(1004);
            return false;
        }
    } elseif ($payloadLength > 125) {
        $payloadLengthBin = str_split(sprintf('%016b', $payloadLength), 8);
        $frameHead[1] = ($masked === true) ? 254 : 126;
        $frameHead[2] = bindec($payloadLengthBin[0]);
        $frameHead[3] = bindec($payloadLengthBin[1]);
    } else {
        $frameHead[1] = ($masked === true) ? $payloadLength + 128 : $payloadLength;
    }

        // convert frame-head to string:
    foreach (array_keys($frameHead) as $i) {
        $frameHead[$i] = chr($frameHead[$i]);
    }

    if ($masked === true) {
        // generate a random mask:
        $mask = [];
        for ($i = 0; $i < 4; $i++) {
            $mask[$i] = chr(rand(0, 255));
        }

        $frameHead = array_merge($frameHead, $mask);
    }
        $frame = implode('', $frameHead);
        // append payload to frame:
    for ($i = 0; $i < $payloadLength; $i++) {
        $frame .= ($masked === true) ? $payload[$i] ^ $mask[$i % 4] : $payload[$i];
    }

        return $frame;
}
$client->on("receive", function ($cli, $data) {
//
    echo "Received: " . $data . "\n";
    if (substr($data, 0, 4) == "HTTP") {
        $_data[0] = "sub";
        $_data[1]['symbol'][] = "RB1709";
        $cli->send(hybi10Encode("40/socket.io/"));
    }
    if (substr($data, 2, 2) == '40') {
        $_data[0] = "sub";
        $_data[1]['symbol'][] = "RB1709";
        $cli->send(hybi10Encode('42/socket.io/,["sub","{\"symbol\":[\"RB1709\"]}"]'));
    }
});
$client->on("error", function ($cli) {
    echo "Connect failed\n";
});
$client->on("close", function ($cli) {
    echo "Connection close\n";
});
//发起网络连接 192.168.99.222:8288/index/
swoole_async_dns_lookup($host, function ($host, $ip) use ($client) {
    $client->connect($ip, 8000);
});
echo 222;
