<?php

// turing machine, roughly based on Tom Stuart's "Understanding Computation"

namespace igorw\turing;

// the configuration holds the entire state of the machine
// it is to be treated as an immutable value object
class Config
{
    public $tape;
    public $state;
    public $steps;

    function __construct(Tape $tape, $state, $steps = 0)
    {
        $this->tape = $tape;
        $this->state = $state;
        $this->steps = $steps;
    }
}

class Tape
{
    public $tape;
    public $position;

    function __construct(array $tape, $position)
    {
        $this->tape = $tape;
        $this->position = $position;
    }

    function read()
    {
        return isset($this->tape[$this->position]) ? $this->tape[$this->position] : '_';
    }

    function write($value)
    {
        $tape = $this->tape;

        $tape[$this->position] = $value;

        return new Tape($tape, $this->position);
    }

    function shift_left()
    {
        $tape = $this->tape;
        $position = $this->position;

        $position--;

        if ($position < 0) {
            $position++;
            array_unshift($tape, '_');
        }

        return new Tape($tape, $position);
    }

    function shift_right()
    {
        $tape = $this->tape;
        $position = $this->position;

        $position++;

        if ($position >= count($tape)) {
            array_push($tape, '_');
        }

        return new Tape($tape, $position);
    }
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
    $read_val = $config->tape->read();
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

    list($init_state, $read_cond, $write_val, $move_dir, $new_state) = $rule;

    $tape = $tape->write($write_val);

    if ('l' === $move_dir) {
        $tape = $tape->shift_left();
    } else if ('r' === $move_dir) {
        $tape = $tape->shift_right();
    }

    return new Config(
        $tape,
        $new_state,
        $config->steps + 1
    );
}

// run through a set of rules until an accept state is reached
// this may never halt

/** @api */
function run(array $rules, array $accept_states, Config $config, callable $on_step = null)
{
    while (!in_array($config->state, $accept_states)) {
        if ($on_step) {
            $on_step($config);
        }

        $rule = match($rules, $config);
        $config = step($rule, $config);
    }

    return $config;
}

// same as run, but prints the current state at every step along the way

/** @api */
function run_debug(array $rules, array $accept_states, Config $config)
{
    $config = run($rules, $accept_states, $config, function (Config $config) {
        echo format_config($config);
        echo "--------\n";
    });

    echo format_config_steps($config);

    return $config;
}

// same as run, but aborts after N steps
// this is useful to debug never-halting machines

/** @api */
function run_until(array $rules, array $accept_states, Config $config, $max_steps = 100000)
{
    return run($rules, $accept_states, $config, function (Config $config) use ($max_steps) {
        if ($config->steps > $max_steps) {
            throw new \RuntimeException("Exceeded maximum steps of '$max_steps'.");
        }
    });
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
                        format_cell($config->tape->position),
                        $config->tape->tape,
                        range(0, count($config->tape->tape)-1))),
                '_')),
        sprintf("Position: %s\n", $config->tape->position),
        sprintf("State: %s\n", $config->state),
    ]);
}

function format_config_steps(Config $config)
{
    return format_config($config).
        sprintf("Steps: %s\n", $config->steps);
}
