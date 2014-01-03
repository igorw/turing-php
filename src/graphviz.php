<?php

namespace igorw\turing;

function graphviz_rules(array $rules, array $accept_states)
{
    $template = <<<EOF
digraph finite_state_machine {
    rankdir=LR;
%s
}

EOF;

    $out = '';

    if ($accept_states) {
        $out .= sprintf("    node [shape = doublecircle]; %s;\n", implode(' ', $accept_states));
    }

    $out .= "    node [shape = circle];\n";

    foreach ($rules as $rule) {
        list($init_state, $read_cond, $write_val, $move_dir, $new_state) = $rule;

        $out .= "    $init_state -> $new_state [ label = \"$read_cond, $write_val, $move_dir\" ];\n";
    }

    return sprintf($template, $out);
}
