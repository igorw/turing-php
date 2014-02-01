<?php

require 'vendor/autoload.php';

use igorw\turing as t;

// binary increment algorithm based on Tom Stuart's "Understanding
// Computation"

$accept_states = [3];

// quintuple
// state, read condition, write value, move direction, new state
$rules = [
    1 => ['0' => ['1', 'r', 2],
          '_' => ['1', 'r', 2],
          '1' => ['0', 'l', 1]],
    2 => ['0' => ['0', 'r', 2],
          '1' => ['1', 'r', 2],
          '_' => ['_', 'l', 3]],
];

// binary 11
$tape = ['1', '0', '1', '1'];

$position = 3;
$state = 1;

return [
    $rules,
    $accept_states,
    new t\Config($tape, $position, $state),
];
