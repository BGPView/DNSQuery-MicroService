<?php

$esHosts = ['localhost:9200'];


use Elasticsearch\ClientBuilder;

require 'vendor/autoload.php';
require 'functions.php';

$client = ClientBuilder::create()->setHosts($esHosts)->build();

$prefix = $_GET['prefix'];
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 1;

$searchParams = [
    'index' => 'main_index_dns_1466675086',
    'type' => 'dns_records',
    'body' => [
        'size' => $limit,
        'from' => $limit * ($page - 1),
        'filter' => [
            'range' => [
                'ip_dec' => getPrefixRange($prefix),
            ],
        ],
    ]
];

$searchResults = $client->search($searchParams);

$domains = [];
foreach ($searchResults['hits']['hits'] as $searchResult) {
    $domains[$searchResult['_source']['entry']][] = $searchResult['_source']['input'];
}

$data = [
    'query_time' =>'',
    'total' => $searchResults['hits']['total'],
    'domains' => $domains,
];

header('Content-Type: application/json');
echo json_encode($data);
die();
