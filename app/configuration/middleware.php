<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

// middleware for cors for swaggerui
// https://github.com/tuupola/cors-middleware
$app->add(new \Tuupola\Middleware\Cors([
    "origin" => ["*"],
    "methods" => ["GET", "POST", "PUT", "PATCH", "DELETE", "CONNECT", "DEBUG", "DONE", "HEAD", "HTTP","QUIC", "REST", "SESSION", "SHOULD", "SPDY", "TRACE", "TRACK"],
  "headers.allow" => ["Content-Type", "Authorization", "api_key"],
    "headers.expose" => [],
    "credentials" => false,
    "cache" => 0,
  'logger' => $container['logger'],

]));


//$app->add(new \Slim\Middleware\HttpBasicAuthentication([
//  "users" => [
//    "admin" => getenv("ADMIN_PASSWORD")
//  ],
//  "path" => "/admin",
//  "secure" => true,
//  "relaxed" => ["localhost", "opencounter-slim-codenv-webserver"],
//]));