<?php

require 'vendor/autoload.php';

use igorw\turing as t;

// universal turing machine
// based on Paul Rendell's Game of Life UTM
// http://rendell-attic.org/gol/utm/utmprog.htm

$accept_states = [0];

// quintuple
// state, read condition, write value, move direction, new state
$rules = [
    [ 1, '0', 'A', 'r',  2],
    [ 1, '1', 'B', 'r',  3],
    [ 1, 'A', 'A', 'r',  1],
    [ 1, 'B', 'B', 'r',  1],
    [ 1, 'D', 'D', 'r',  1],
    [ 1, 'X', 'X', 'r',  1],
    [ 2, 'B', 'M', 'r', 11],
    [ 2, 'A', 'A', 'l',  0],
    [ 2, 'D', 'D', 'r',  1],
    [ 2, 'X', 'X', 'r',  1],
    [ 2, '0', 'A', 'l',  4],
    [ 2, '1', 'B', 'l',  5],
    [ 3, '0', 'A', 'l',  6],
    [ 3, '1', 'B', 'l',  7],
    [ 3, 'A', 'A', 'l',  3],
    [ 3, 'B', 'B', 'l',  3],
    [ 3, 'D', 'D', 'l',  3],
    [ 3, 'M', '1', 'l',  3],
    [ 3, 'X', 'M', 'r', 12],
    [ 4, '0', 'A', 'l',  8],
    [ 4, '1', 'A', 'l',  8],
    [ 4, 'A', 'A', 'l',  4],
    [ 4, 'B', 'B', 'l',  4],
    [ 4, 'D', 'D', 'l',  4],
    [ 4, 'X', 'X', 'l',  4],
    [ 4, 'M', 'B', 'r',  2],
    [ 5, '0', '0', 'r',  8],
    [ 5, '1', '0', 'r',  8],
    [ 5, 'A', 'A', 'l',  5],
    [ 5, 'B', 'B', 'l',  5],
    [ 5, 'D', 'D', 'l',  5],
    [ 5, 'X', 'X', 'l',  5],
    [ 6, '0', 'B', 'l',  8],
    [ 6, '1', 'B', 'l',  8],
    [ 6, 'A', 'A', 'l',  6],
    [ 6, 'B', 'B', 'l',  6],
    [ 6, 'D', 'D', 'l',  6],
    [ 6, 'X', 'X', 'l',  6],
    [ 7, '0', '1', 'r',  8],
    [ 7, '1', '1', 'r',  8],
    [ 7, 'A', 'A', 'l',  7],
    [ 7, 'B', 'B', 'l',  7],
    [ 7, 'D', 'D', 'l',  7],
    [ 7, 'X', 'X', 'l',  7],
    [ 8, '0', '0', 'r', 10],
    [ 8, '1', '1', 'r',  9],
    [ 8, 'A', '0', 'r', 10],
    [ 8, 'B', '1', 'r',  9],
    [ 9, '0', 'A', 'r',  9],
    [ 9, '1', 'B', 'r',  9],
    [ 9, 'A', 'A', 'r',  9],
    [ 9, 'B', 'B', 'r',  9],
    [ 9, 'C', 'D', 'r', 10],
    [ 9, 'D', 'D', 'r',  9],
    [ 9, 'X', 'X', 'r',  9],
    [10, '0', 'A', 'l',  3],
    [10, '1', 'M', 'r', 11],
    [10, 'A', 'A', 'r', 10],
    [10, 'B', 'B', 'r', 10],
    [10, 'D', 'D', 'r', 10],
    [10, 'X', 'X', 'r', 10],
    [10, 'M', 'M', 'l', 13],
    [10, 'C', 'C', 'l', 13],
    [11, '0', 'A', 'r', 11],
    [11, '1', 'B', 'r', 11],
    [11, 'A', 'A', 'r', 11],
    [11, 'B', 'B', 'r', 11],
    [11, 'X', 'X', 'r', 11],
    [11, 'C', 'D', 'r', 11],
    [11, 'D', 'D', 'r', 11],
    [11, 'M', 'X', 'l',  4],
    [12, '0', 'A', 'l',  3],
    [12, '1', 'M', 'r', 12],
    [12, 'A', 'A', 'r', 12],
    [12, 'B', 'B', 'r', 12],
    [12, 'D', 'D', 'r', 12],
    [12, 'C', 'C', 'l', 13],
    [12, 'M', 'M', 'l', 13],
    [13, 'A', '0', 'l', 13],
    [13, 'B', '1', 'l', 13],
    [13, 'D', 'C', 'l', 13],
    [13, 'M', 'M', 'l', 13],
    [13, 'X', 'X', 'l',  1],
];

// The Turing Machine being simulated simply duplicates a string of symbols,
// effectively a unary multiply by 2. The data being operated on is a string
// of three symbols.
//
// https://www.youtube.com/watch?v=1X21HQphy6I

$tape = str_split('0000000001ABABAAXBABDBBX110C11M1000C00M1111C00M1010C11M0010C1M0010C00M');

// full tape before after
// 0000000001ABABAA XBABDBBX110C11M1000C00M1111C00M1010C11M0010C1M0010C00M
// 000BABABABABABAA XBABDBBXBBADBBXBAAADAAXBBBBDAAXBABADBBX0010C1M0010C00M

// T tape before/after
// 000 000000 101010 0
// 000 101010 101010 0

// T description
// the rules of the T machine, encoded onto the tape
// write, direction, next offset read 0, next offset read 1
// offsets are unary
// 1 L 1L 2R
// 1 R 1R 2R
// 1 L 2R 2L
// 1 R 2L 2L
// 1 L H  2R
// 0 L H  1R
// 0 L H  2L

$position = 23;
$state = 1;

return [
    $rules,
    $accept_states,
    new t\Config($tape, $position, $state),
];
