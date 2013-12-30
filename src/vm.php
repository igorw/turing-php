<?php

namespace igorw\turing;

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

function step(array $rules, Config $config)
{
    $tape = $config->tape;
    $position = $config->position;

    $read_val = isset($tape[$position]) ? $tape[$position] : '_';
    $matched_rule = match_rule($rules, $config->state, $read_val);

    list($init_state, $read_cond, $write_val, $move_dir, $new_state) = $matched_rule;

    $tape[$position] = $write_val;

    if ('l' === $move_dir) {
        $position--;
        if ($position < 0) {
            $position++;
            array_unshift($tape, '_');
        }
    } else if ('r' === $move_dir) {
        $position++;
        if ($position >= count($tape)) {
            array_push($tape, '_');
        }
    }

    return new Config(
        $tape,
        $position,
        $new_state,
        $config->steps + 1
    );
}

function run(array $rules, array $accept_states, Config $config)
{
    while (!in_array($config->state, $accept_states)) {
        $config = step($rules, $config);
    }

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
        sprintf("Steps: %s\n", $config->steps),
    ]);
}
