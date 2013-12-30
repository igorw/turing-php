<?php

// turing machine, roughly based on Tom Stuart's "Understanding Computation"

namespace igorw\turing;

// the configuration holds the entire state of the machine
// it is to be treated as an immutable value object
class Config
{
    public $tape;
    public $position;
    public $state;
    public $steps;

    function __construct(array $tape, $position, $state, $steps = 0)
    {
        $this->tape = $tape;
        $this->position = $position;
        $this->state = $state;
        $this->steps = $steps;
    }
}

function read_tape(array $tape, $position)
{
    return isset($tape[$position]) ? $tape[$position] : '_';
}

function shift_tape_left(array $tape, $position)
{
    $position--;

    if ($position < 0) {
        $position++;
        array_unshift($tape, '_');
    }

    return [$tape, $position];
}

function shift_tape_right(array $tape, $position)
{
    $position++;

    if ($position >= count($tape)) {
        array_push($tape, '_');
    }

    return [$tape, $position];
}

function match_rule(array $rules, $state, $read_val)
{
    foreach ($rules as $rule) {
        list($init_state, $read_cond, $write_val, $move_dir, $new_state) = $rule;

        if ($init_state === $state && $read_val === $read_cond) {
            return $rule;
        }
    }

    throw new \RuntimeException(sprintf('No rule matched state %s, value %s.', $state, $read_val));
}

// lookup the rule in the rules table that corresponds to
// the current state and value under the head
//
// $rules is an array of quintuples
//  [state, read condition, write value, move direction, new state]
function match(array $rules, Config $config)
{
    $read_val = read_tape($config->tape, $config->position);
    return match_rule($rules, $config->state, $read_val);
}

// perform one computational step
//
//  * write value from rule
//  * adjust head position
//  * update state
//
// returns the new configuration
function step(array $rule, Config $config)
{
    $tape = $config->tape;
    $position = $config->position;

    list($init_state, $read_cond, $write_val, $move_dir, $new_state) = $rule;

    $tape[$position] = $write_val;

    if ('l' === $move_dir) {
        list($tape, $position) = shift_tape_left($tape, $position);
    } else if ('r' === $move_dir) {
        list($tape, $position) = shift_tape_right($tape, $position);
    }

    return new Config(
        $tape,
        $position,
        $new_state,
        $config->steps + 1
    );
}

// run through a set of rules until an accept state is reached
// this may never halt

/** @api */
function run(array $rules, array $accept_states, Config $config)
{
    while (!in_array($config->state, $accept_states)) {
        $rule = match($rules, $config);
        $config = step($rule, $config);
    }

    return $config;
}

// same as run, but prints the current state at every step along the way

/** @api */
function run_debug(array $rules, array $accept_states, Config $config)
{
    while (!in_array($config->state, $accept_states)) {
        echo format_config($config);
        echo "--------\n";
        $rule = match($rules, $config);
        $config = step($rule, $config);
    }

    echo format_config_steps($config);

    return $config;
}

function format_cell($position)
{
    return function ($cell, $cell_pos) use ($position) {
        return ($position === $cell_pos) ? "($cell)" : $cell;
    };
}

function format_config(Config $config)
{
    return implode('', [
        sprintf("Tape: %s\n",
            trim(
                implode('',
                    array_map(
                        format_cell($config->position),
                        $config->tape,
                        range(0, count($config->tape)-1))),
                '_')),
        sprintf("Position: %s\n", $config->position),
        sprintf("State: %s\n", $config->state),
    ]);
}

function format_config_steps(Config $config)
{
    return format_config($config).
        sprintf("Steps: %s\n", $config->steps);
}
