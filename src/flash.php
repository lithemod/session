<?php

namespace Lithe\Middleware\Session;

/**
 * Middleware that provides flash message functionality for the session.
 *
 * @return \Closure Middleware function that handles flash messages.
 */

function flash()
{
    /**
     *
     * @param \Lithe\Http\Request $req
     * @param \Lithe\Http\Response $res
     * @param callable   $next
     */
    return function (\Lithe\Http\Request $req, \Lithe\Http\Response $res, $next) {
        $req->flash = new \Lithe\Support\Session\Flash;
        $next();
    };
}
