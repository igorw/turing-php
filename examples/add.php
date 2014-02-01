<?php

require 'vendor/autoload.php';

use igorw\turing as t;

// "Addition on Turing Machines" by Jay McCarthy
// binary addition with named states
//   http://jeapostrophe.github.io/2013-10-29-tmadd-post.html

$accept_states = ['halt'];

// quintuple
// state, read condition, write value, move direction, new state
$rules = [
    'check-if-zero' => [
        '0' => ['0', 'r', 'check-if-zero'],
        '1' => ['1', 'l', 'seek-left&sub1'],
        '+' => ['_', 'l', 'seek-left&zero'],
    ],
    'seek-left&sub1' => [
        '0' => ['0', 'l', 'seek-left&sub1'],
        '1' => ['1', 'l', 'seek-left&sub1'],
        '_' => ['_', 'r', 'sub1:ones-complement'],
    ],
    'sub1:ones-complement' => [
        '0' => ['1', 'r', 'sub1:ones-complement'],
        '1' => ['0', 'r', 'sub1:ones-complement'],
        '+' => ['+', 'l', 'sub1:add1:zero-until-0'],
    ],
    'sub1:add1:zero-until-0' => [
        '1' => ['0', 'l', 'sub1:add1:zero-until-0'],
        '0' => ['1', 'r', 'sub1:add1:find-end'],
    ],
    'sub1:add1:find-end' => [
        '0' => ['0', 'r', 'sub1:add1:find-end'],
        '1' => ['1', 'r', 'sub1:add1:find-end'],
        '+' => ['+', 'l', 'sub1:ones-complementR'],
    ],
    'sub1:ones-complementR' => [
        '0' => ['1', 'l', 'sub1:ones-complementR'],
        '1' => ['0', 'l', 'sub1:ones-complementR'],
        '_' => ['_', 'r', 'seek-right&add1'],
    ],
    'seek-right&add1' => [
        '0' => ['0', 'r', 'seek-right&add1'],
        '1' => ['1', 'r', 'seek-right&add1'],
        '+' => ['+', 'r', 'add1:find-end']],
    'add1:find-end' => [
        '0' => ['0', 'r', 'add1:find-end'],
        '1' => ['1', 'r', 'add1:find-end'],
        '_' => ['_', 'l', 'add1:zero-until-0'],
    ],
    'add1:zero-until-0' => [
        '1' => ['0', 'l', 'add1:zero-until-0'],
        '0' => ['1', 'l', 'seek-left&continue'],
    ],
    'seek-left&continue' => [
        '0' => ['0', 'l', 'seek-left&continue'],
        '1' => ['1', 'l', 'seek-left&continue'],
        '+' => ['+', 'l', 'seek-left&continue'],
        '_' => ['_', 'r', 'check-if-zero'],
    ],
    'seek-left&zero' => [
        '0' => ['_', 'l', 'seek-left&zero'],
        '_' => ['_', 'r', 'seek-start'],
    ],
    'seek-start' => [
        '_' => ['_', 'r', 'seek-start'],
        '0' => ['0', 'l', 'move-right-once'],
        '1' => ['1', 'l', 'move-right-once'],
    ],
    'move-right-once' => [
        '_' => ['_', 'r', 'halt'],
    ],
];

// binary representation of 2 + 3
// expected output is 5: 0101
$tape = str_split('0010+0011');

$position = 0;
$state = 'check-if-zero';

return [
    $rules,
    $accept_states,
    new t\Config($tape, $position, $state),
];
