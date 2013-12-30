<?php

require 'vendor/autoload.php';

use igorw\turing as t;

// turing's original binary algorithm for calculating one third (1/3)
// prepend `0.` to the result and interpret the rest as a binary
// fraction
//
// there is a blank square between every digit. this is turing's
// convention for metadata. no metadata is stored in this case, so
// the blank squares can just be ignored.
//
// also note that the computation never halts. calculating 1/3 will
// go on forever, as it cannot be represented fully in binary.

$accept_states = [];

// quintuple
// state, read condition, write value, move direction, new state
$rules = [
    ['b', '_', '0', 'r', 'c'],
    ['c', '_', '_', 'r', 'e'],
    ['e', '_', '1', 'r', 'f'],
    ['f', '_', '_', 'r', 'b'],
];

$tape = [];

$position = 0;
$state = 'b';

t\run_debug(
    $rules,
    $accept_states,
    new t\Config($tape, $position, $state)
);
