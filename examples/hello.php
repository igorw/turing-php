<?php

require 'vendor/autoload.php';

use igorw\turing as t;

// hello world

$accept_states = [13];

// quintuple
// state, read condition, write value, move direction, new state
$rules = [
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

$tape = [];

$position = 0;
$state = 0;

return [
    $rules,
    $accept_states,
    new t\Config($tape, $position, $state),
];
