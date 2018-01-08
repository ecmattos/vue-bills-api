<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__ . '/vendor/autoload.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
$app = new Silex\Application();

###
# Bills Pay - Inicio
###
function getBillsPay()
{
    $json = file_get_contents(__DIR__ . '/bills-pay.json');
    $data = json_decode($json, true);
    return $data['bills'];
}

function findIndexByIdPay($id)
{
    $bills = getBillsPay();
    foreach ($bills as $key => $bill) {
        if ($bill['id'] == $id) {
            return $key;
        }
    }
    return false;
}

function writeBillsPay($bills)
{
    $data = ['bills' => $bills];
    $json = json_encode($data);
    file_put_contents(__DIR__ . '/bills-pay.json', $json);
}

$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

$app->get('api/bills-pay', function () use ($app) {
    $bills = getBillsPay();
    return $app->json($bills);
});

$app->get('api/bills-pay/total', function () use ($app) {
    $bills = getBillsPay();
    $sum=0;
    foreach ($bills as $value) {
        $sum += (float)$value['value'];
    }
    return $app->json(['total' => $sum]);
});

$app->get('api/bills-pay/{id}', function ($id) use ($app) {
    $bills = getBillsPay();
    $bill = $bills[findIndexByIdPay($id)];
    return $app->json($bill);
});

$app->post('api/bills-pay', function (Request $request) use ($app) {
    $bills = getBillsPay();
    $data = $request->request->all();
    $data['id'] = rand(100,100000);
    $bills[] = $data;
    writeBillsPay($bills);
    return $app->json($data);
});

$app->put('api/bills-pay/{id}', function (Request $request, $id) use ($app) {
    $bills = getBillsPay();
    $data = $request->request->all();
    $index = findIndexByIdPay($id);
    $bills[$index] = $data;
    $bills[$index]['id'] = (int)$id;
    writeBillsPay($bills);
    return $app->json($bills[$index]);
});

$app->delete('api/bills-pay/{id}', function ($id) {
    $bills = getBillsPay();
    $index = findIndexByIdPay($id);
    array_splice($bills,$index,1);
    writeBillsPay($bills);
    return new Response("", 204);
});

###
# Bills Pay - Fim
###

###
# Bills Receive - Inicio
###
function getBillsReceive()
{
    $json = file_get_contents(__DIR__ . '/bills-receive.json');
    $data = json_decode($json, true);
    return $data['bills-receive'];
}

function findIndexByIdReceive($id)
{
    $bills = getBillsReceive();
    foreach ($bills as $key => $bill) {
        if ($bill['id'] == $id) {
            return $key;
        }
    }
    return false;
}

function writeBillsReceive($bills)
{
    $data = ['bills-receive' => $bills];
    $json = json_encode($data);
    file_put_contents(__DIR__ . '/bills-receive.json', $json);
}

$app->get('api/bills-receive', function () use ($app) {
    $bills = getBillsReceive();
    return $app->json($bills);
});

$app->get('api/bills-receive/total', function () use ($app) {
    $bills = getBillsReceive();
    $sum=0;
    foreach ($bills as $value) {
        $sum += (float)$value['value'];
    }
    return $app->json(['total' => $sum]);
});

$app->get('api/bills-receive/{id}', function ($id) use ($app) {
    $bills = getBillsReceive();
    $bill = $bills[findIndexByIdReceive($id)];
    return $app->json($bill);
});

$app->post('api/bills-receive', function (Request $request) use ($app) {
    $bills = getBillsReceive();
    $data = $request->request->all();
    $data['id'] = rand(100,100000);
    $bills[] = $data;
    writeBillsReceive($bills);
    return $app->json($data);
});

$app->put('api/bills-receive/{id}', function (Request $request, $id) use ($app) {
    $bills = getBillsReceive();
    $data = $request->request->all();
    $index = findIndexByIdReceive($id);
    $bills[$index] = $data;
    $bills[$index]['id'] = (int)$id;
    writeBillsReceive($bills);
    return $app->json($bills[$index]);
});

$app->delete('api/bills-receive/{id}', function ($id) {
    $bills = getBillsReceive();
    $index = findIndexByIdReceive($id);
    array_splice($bills,$index,1);
    writeBillsReceive($bills);
    return new Response("", 204);
});

###
# Bills Receive - Fim
###


$app->match("{uri}", function($uri){
    return "OK";
})
->assert('uri', '.*')
->method("OPTIONS");


$app->run();