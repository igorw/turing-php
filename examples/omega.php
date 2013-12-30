<?php

require 'vendor/autoload.php';

use igorw\turing as t;

// infinite loop

$accept_states = [];

// quintuple
// state, read condition, write value, move direction, new state
$rules = [
    [1, '_', '_', 'n', 2],
    [2, '_', '_', 'n', 1],
];

// binary 11
$tape = [];

$position = 0;
$state = 1;

t\run_until(
    $rules,
    $accept_states,
    new t\Config($tape, $position, $state),
    500000
);
