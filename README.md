# lambda-php

Lambda calculus interpreter in PHP.

## Lambda calculus

Lambda calculus is a very minimal programming language that was invented in
1936 by Alonzo Church. It is the functional equivalent of the Turing Machine.

Lambda calculus has only three concepts: Function definitions, lexically
scoped variables, function application.

An example term would be the identity function:

    λx.x

The first part `λx` defines a function that takes an `x`, the `.` signifies
that the part that follows is the function body. The body just returns `x`.

In PHP, you would write the same thing as follows:

    function ($x) {
        return $x;
    }

You can nest function definitions. Here is a function returning a function:

    λx.λy.x

And you can also *apply* a function to an argument, which just means calling
the function.

    λf.λg.f g

Which is the short hand (left-associative) form of writing

    λf.λg.(f g)

Nested calls like:

    λf.λg.λh.f g h

Are interpreted as:

    λf.λg.λh.((f g) h)

If you want to change the grouping to be right-associative, you need to
explicitly group them in parentheses:

    λf.λg.λh.(f (g h))

Interestingly, lambda calculus is turing complete. Using just these three
concepts you can represent *any* computation.

Check out the links at the bottom for more details on how to do stuff in
lambda calculus.

## Interpreter

This project consists of a lambda calculus expression parser using
[dissect](https://github.com/jakubledl/dissect), and an *eval-apply*
interpreter based on [Matt Might's implementation in
scheme](http://matt.might.net/articles/implementing-a-programming-language/).

The interpreter is *call-by-value* which means that recursive calls need to be
wrapped in a function to prevent them from being evaluated eagerly.

For examples of how to do numbers (church encoding), booleans, arithmetic,
boolean logic, looping (recursion), etc. look at `example.php`.

## REPL

This project ships with a read-eval-print-loop that you can use to evaluate
lambda calculus expressions:

    $ php repl.php

By default, it is in *int-mode*, expecting the result of the expression to be
a church-encoded number. Example:

    $ php repl.php
    i> λf.λx.f (f (f x))
    3

You can switch to *bool-mode* by sending the `b` command:

    $ php repl.php
    i> b
    b> λx.λy.x
    true

Or `r` for raw mode:

    $ php repl.php
    i> r
    r> λx.x
    λx.x

## WIP

A few things are still a work in progress:

* **Krivine machine:** This alternate interpreter would allow call-by-need
  and indexing into de-bruijn indices, which is needed by...

* **Binary lambda calculus:** Allows encoding lambda calculus programs in
  binary form which produces extremely small programs. This also defines an
  I/O mechanism.

## References

* [Matt Might: 7 lines of code, 3 minutes](http://matt.might.net/articles/implementing-a-programming-language/)
* [Tom Stuart: Programming with Nothing](http://codon.com/programming-with-nothing)
* [Jean-Louis Krivine: A call-by-name lambda-calculus machine](http://www.pps.univ-paris-diderot.fr/~krivine/articles/lazymach.pdf)
* [Rémi Douence, Pascal Fradet: The Next 700 Krivine Machines](http://pop-art.inrialpes.fr/~fradet/PDFs/HOSC07.pdf)
* [Xavier Leroy: The Zinc Experiment](http://citeseerx.ist.psu.edu/viewdoc/summary?doi=10.1.1.43.6772)
* [John Tromp: Binary Lambda Calculus and Combinatory Logic](http://homepages.cwi.nl/~tromp/cl/LC.pdf)
* [John Tromp: Binary Lambda Calculus interpreter for IOCCC](http://www.ioccc.org/2012/tromp/hint.html)
* [Erkki Lindpere: Parsing Lambda Calculus in Scala](http://zeroturnaround.com/rebellabs/parsing-lambda-calculus-in-scala/)
* [Binary Lambda Calculus in Python](https://github.com/sdiehl/bnlc)
* [Krivine Machine in Scheme](https://github.com/ympbyc/Carrot)
* [Algorithmic Information Theory in Haskell](https://github.com/tromp/AIT)
* [Lambda Calculus - Wikipedia](http://en.wikipedia.org/wiki/Lambda_calculus)
* [Binary Lambda Calculus - Wikipedia](http://en.wikipedia.org/wiki/Binary_lambda_calculus)
* [De Bruijn index - Wikipedia](http://en.wikipedia.org/wiki/De_Bruijn_index)

## Thanks to

* [@ympbyc](https://twitter.com/ympbyc)
* [@smdiehl](https://twitter.com/smdiehl)
