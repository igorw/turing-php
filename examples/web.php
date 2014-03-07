<?php

require 'vendor/autoload.php';

use igorw\turing as t;

// standalone web script
// makes use of sapi, so run as webserver
//
// example:
//   php -Slocalhost:8082 examples/web.php
//   open http://localhost:8082
//   open http://localhost:8082/hi

$accept_states = [13];

$rules = [
    -9 => [
        'req_method'    => ['req_method', 'r',  -9],
        'G'             => ['G', 'r',  -9],
        'E'             => ['E', 'r',  -9],
        'T'             => ['T', 'r',  -9],
        'req_uri'       => ['req_uri', 'r',  -8],
    ],
    -8 => ['/' => ['/', 'r',  -7]],
    -7 => ['h' => ['h', 'r',  -6]],
    -6 => [
        'i' => ['i', 'r',  -6],
        'req_body' => ['req_body', 'r',  -5],
    ],
    -5 => ['rep_body' => ['rep_body', 'r',  0]],
     0 => ['_' => ['h', 'r',  1]],
     1 => ['_' => ['e', 'r',  2]],
     2 => ['_' => ['l', 'r',  3]],
     3 => ['_' => ['l', 'r',  4]],
     4 => ['_' => ['o', 'r',  5]],
     5 => ['_' => [',', 'r',  6]],
     6 => ['_' => [' ', 'r',  7]],
     7 => ['_' => ['w', 'r',  8]],
     8 => ['_' => ['o', 'r',  9]],
     9 => ['_' => ['r', 'r', 10]],
    10 => ['_' => ['l', 'r', 11]],
    11 => ['_' => ['d', 'r', 12]],
    12 => ['_' => ['!', 'r', 13]],
];

$tape = array_merge(
    ['req_method'],
    split_or_empty_array($_SERVER['REQUEST_METHOD']),
    ['req_uri'],
    split_or_empty_array($_SERVER['REQUEST_URI']),
    ['req_body'],
    split_or_empty_array(file_get_contents('php://input')),
    ['rep_body']
);

$position = 0;
$state = -9;

$config = new t\Config($tape, $position, $state);

try {
    $result = t\run(
        $rules,
        $accept_states,
        $config
    );
} catch (t\NoSuchRuleException $e) {
    http_response_code(400);
    echo "<h1>Rule did not match, and it's your fault.</h1>";
    echo "<pre>$e</pre>";
    exit;
}

$output = false;
foreach ($result->tape as $cell) {
    if ($cell === 'rep_body') {
        $output = true;
        continue;
    }

    if ($output && $cell !== '_') {
        echo $cell;
    }
}

function split_or_empty_array($input) {
    return strlen($input) ? str_split($input) : [];
}
