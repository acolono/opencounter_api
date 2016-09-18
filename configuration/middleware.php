<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

// middleware for cors for swaggerui
// https://github.com/tuupola/cors-middleware
$app->add(new \Tuupola\Middleware\Cors([
    "origin" => ["*"],
    "methods" => ["GET", "POST", "PUT", "PATCH", "DELETE", "CONNECT", "DEBUG", "DONE", "HEAD", "HTTP","QUIC", "REST", "SESSION", "SHOULD", "SPDY", "TRACE", "TRACK"],
    "headers.allow" => ["Content-Type"],
    "headers.expose" => [],
    "credentials" => false,
    "cache" => 0,
]));