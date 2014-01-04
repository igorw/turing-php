<?php

require 'vendor/autoload.php';

use igorw\turing as t;

// 3-state busy beaver
// from wikipedia
//   http://en.wikipedia.org/wiki/Turing_machine_examples

$accept_states = ['D'];

// quintuple
// state, read condition, write value, move direction, new state
$rules = [
    ['A', '_', '1', 'r', 'B'],
    ['A', '1', '1', 'l', 'C'],
    ['B', '_', '1', 'l', 'A'],
    ['B', '1', '1', 'r', 'B'],
    ['C', '_', '1', 'l', 'B'],
    ['C', '1', '1', 'n', 'D'],
];

$tape = [];

$position = 3;
$state = 'A';

return [
    $rules,
    $accept_states,
    new t\Config(new t\Tape($tape, $position), $state),
];
