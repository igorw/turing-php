<?php

namespace igorw\turing;

function graphviz_rules(array $rules, array $accept_states)
{
    $template = <<<EOF
digraph turing_machine {
    rankdir=LR;
%s
}

EOF;

    $out = '';

    if ($accept_states) {
        $out .= sprintf("    node [shape = doublecircle]; %s;\n", implode(' ', $accept_states));
    }

    $out .= "    node [shape = circle];\n";

    $self_references = [];

    foreach ($rules as $init_state => $cases) {
        foreach ($cases as $read_cond => $rule) {
            list($write_val, $move_dir, $new_state) = $rule;

            $move_dir = strtoupper($move_dir);

            if ($init_state === $new_state) {
                $self_references[$init_state][] = [$read_cond, $write_val, $move_dir];
                continue;
            }

            $out .= "    \"$init_state\" -> \"$new_state\" [ label = \"$read_cond, $write_val, $move_dir\" ];\n";
        }
    }

    foreach ($self_references as $state => $refs) {
        $out .= "    \"$state\" -> \"$state\" [ label = \"";
        $out .= implode("\n", array_map('igorw\turing\format_ref', $refs));
        $out .= "\" ];\n";
    }

    return sprintf($template, $out);
}

function format_ref(array $ref)
{
    list($read_cond, $write_val, $move_dir) = $ref;

    return "$read_cond, $write_val, $move_dir";
}
