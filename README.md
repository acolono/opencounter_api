[![build status](https://gitlab.acolono.net/open-counter/SlimCounter/badges/develop/build.svg)](https://gitlab.acolono.net/open-counter/SlimCounter/commits/develop)


<div id="table-of-contents">
<h2>Table of Contents</h2>
<div id="text-table-of-contents">
<ul>
<li><a href="#orgheadline1">1. Requirements</a></li>
<li><a href="#orgheadline2">2. Usage</a></li>
</ul>
</div>
</div>

<img src="./img/opencounter-logo.png" alt="OpenCounter Logo" width="150">

<span class="underline">A minimal example for developers to demonstrate</span>

-   REST and API
-   Iteration over incrementation
-   Object oriented
-   Automated software testing (and test driven development)

<span class="underline">A simple useful application/service/backend with usecases that lets the user:</span>

1.  Register a counter with a password
2.  Retrieve the counter value
3.  Change the value by incrementing it by 1 (providing the password)

A counter has a:

-   Name which can be changed
-   Unique ID
-   Value
-   Status (locked, disabled, active)

# Requirements<a id="orgheadline1"></a>

You need a webserver, Php7 and a database. Point the webserver to ./public directory.

# Usage<a id="orgheadline2"></a>

## Production
<span class="underline">Use Composer to install dependencies</span>

If you don't have composer installed on your host but have docker then you can use
<https://github.com/RobLoach/docker-composer>

    composer install --no-dev

<span class="underline">Environment file</span>

You need to create a .env file containing variables. (See example env.dev)

 
<span class="underline">Create the Database</span>
installed via [Phinx](https://phinx.org/)

    bin/phinx migrate -c phinx.php

## Development
<span class="underline">Install Dependencies</span>

    composer install
<span class="underline">Setup environment variables</span>

    cp env.dev .env
<span class="underline">Start Docker containers</span>

    docker-compose -f docker-compose.dev.yml up


<span class="underline">Setup Database</span>

    docker exec -t -i opencounter-slim-codenv-php-fpm php /var/www/opencounter-slim-codenv/bin/phinx migrate -c /var/www/opencounter-slim-codenv/phinx.php

<span class="underline">Run Tests with these commands</span>


```
$ bin/run-tests.sh
```

Probably you will want to use the continuous testing setup which automatically reruns the tests on filesave
with immediate feedback in terminal and browser. For this you will need to run

    $ npm install

In case you dont have node/npm on your host but have docker running then you can use
<https://serversforhackers.com/docker-for-gulp-build-tasks>
(Sorry, put this together into a repo but couldn't share it yet and neither did the OP, feel free to pass me a link to something like this that is ready to use)

This should allow you to run

    $ gulp

