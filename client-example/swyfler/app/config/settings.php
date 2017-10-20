<?php

$config = [
  'clientId' => (string)getenv('CLIENT_ID')?: 'abc',
  'userId' => (string)getenv('USER_ID')?: 'user',
  'clientSecret' => (string)getenv('CLIENT_SECRET')?: 'abc',
  'counterId' => (string)getenv('COUNTER_ID')?: '54853a23-adb1-464b-b008-8dc899435ead',
  'redirectUri' => (string)getenv('REDIRECT_URI')?: 'http://localhost:8123/index.php',
  'baseUrl' => (string)getenv('BASE_URL') ?: 'http://opencounter-slim-codenv-webserver:8080/',
  'apiHost' => (string)getenv('API_HOST') ?: 'http://opencounter-slim-codenv-webserver:8080/api',
];
