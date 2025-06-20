<?php

use Swoole\Http\{Request, Response, Server};
use Swoole\Coroutine\Http\Client;

$servidor = new Server('0.0.0.0', 8080);

// $servidor->on('request', function ($request, $response) {    
//     $response->header('Content-Type', 'text/html; charset=utf8');
//     $response->end(print_r($request->header, true));
// });

$servidor->on('request', function ($request, $response) {  

    $channel = new chan(2);

    go(function () use ($channel) {
        $cliente = new Client('localhost', 8001);
        $cliente->get('/app/servidor.php');

        $conteudo = $cliente->getBody();
        $channel->push($conteudo);
    });

    go(function () use ($channel) {
        $conteudo = file_get_contents('arquivo.txt');
        $channel->push($conteudo);
    });

    go(function () use ($channel, &$response) {
        $primeiraResposta = $channel->pop();
        $segundaResposta = $channel->pop();

        $response->end($primeiraResposta . $segundaResposta);
    });
});


$servidor->start();