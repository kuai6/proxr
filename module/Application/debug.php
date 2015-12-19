<?php

/**
 * Dump without exit
 *
 * @param $arg
 * @param int $depth
 */
function d($arg, $depth = 2)
{
    print_r(sprintf('<pre style=\"background: #f4f4f4; padding: 10px; border: 1px solid #ccc;\">%s</pre>',
        \Doctrine\Common\Util\Debug::dump($arg, $depth, true, false)
    ));
}

/**
 * Dump with exit;
 * @param $arg
 * @param int $depth
 */
function dd($arg, $depth = 2)
{
    d($arg, $depth);
    die();
}
