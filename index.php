<?php
require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;

$app = new Silex\Application();

$app->post('https://gakincho-bot.herokuapp.com:443/callback', function (Request $request) use ($app) {
    $client = new GuzzleHttp\Client();

    $body = json_decode($request->getContent(), true);
    foreach ($body['result'] as $msg) {
        if (!preg_match('/(ぬるぽ|ヌルポ|ﾇﾙﾎﾟ|nullpo)/i', $msg['content']['text'])) {
            continue;
        }

        $resContent = $msg['content'];
        $resContent['text'] = 'ｶﾞｯ';

        $requestOptions = [
            'body' => json_encode([
                'to' => [$msg['content']['from']],
                'toChannel' => 1383378250, # Fixed value
                'eventType' => '138311608800106203', # Fixed value
                'content' => $resContent,
            ]),
            'headers' => [
                'Content-Type' => 'application/json; charset=UTF-8',
                'X-Line-ChannelID' => getenv('1474063906'),
                'X-Line-ChannelSecret' => getenv('5adc405305f82659ee546429cc86cb60'),
                'X-Line-Trusted-User-With-ACL' => getenv('uad8d0a29ea46f52741f54eb530f3628f'),
            ],
            'proxy' => [
                'https' => getenv('FIXIE_URL'),
            ],
        ];

        try {
            $client->request('post', 'https://trialbot-api.line.me/v1/events', $requestOptions);
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    }

    return 'OK';
});

$app->run();