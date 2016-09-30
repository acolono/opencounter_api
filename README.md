<div id="table-of-contents">
<h2>Table of Contents</h2>
<div id="text-table-of-contents">
<ul>
<li><a href="#orgheadline1">1. you need a webserver and a database</a></li>
<li><a href="#orgheadline2">2. Usage</a></li>
<li><a href="#orgheadline3">3. Contributing</a></li>
<li><a href="#orgheadline4">4. more docs (assuming you are viewing this in the running codenv)</a></li>
</ul>
</div>
</div>

<img src="./img/opencounter-logo.png" alt="OpenCounter Logo" width="150">
<img src="![img](//acolono.gitlab.net/opencounter_api/project/badges/master/build.svg)" alt="OpenCounter master Build status" width="150">
<img src="![img](//acolono.gitlab.net/opencounter_api/project/badges/master/coverage.svg)" alt="OpenCounter master Coverage" width="150">

<span class="underline">a minimal example for developers to demonstrate</span>

-   rest and api
-   to demonstrate iteration over incrementation
-   objectoriented
-   automated software testing (and test driven dev)

<span class="underline">A simple useful application/service/backend with usecases that lets the user:</span>

1.  register a counter with a password
2.  retrieve the counter value
3.  change the value by incrementing it by 1 (providing the password)

a counter has a:

-   name which can be changed
-   unique id
-   value
-   status (locked, disabled, active)

# you need a webserver and a database<a id="orgheadline1"></a>

point webserver to ./public directory

# Usage<a id="orgheadline2"></a>

<span class="underline">Use Composer to install dependencies</span>

if you dont have composer installed on your host but have docker then you can use
<https://github.com/RobLoach/docker-composer>

    composer install

<span class="underline">Environment file</span>

you need to create a public/.env file containing

    DISPLAY_ERRORS=1
    LOG_LEVEL=debug
    PRODUCTION=0

<span class="underline">Create the Database</span>
installed via [Phinx](https://phinx.org/)

    # using codenv to run bin/phinx migrate
    docker exec -t -i opencounter-slim-codenv-php-fpm php /var/www/opencounter-slim-codenv/bin/phinx migrate -c /var/www/opencounter-slim-codenv/phinx.yml

<span class="underline">Run Tests</span>

run tests with these commands

    ```
    $ bin/behat
    $ bin/phpspec
    ```

probably you will want to use the continuous testing setup which automatically reruns the tests on filesave
with immediate feedback in terminal and browser, for this you will need to run

    $ npm install

if you dont have node/npm on your host but have docker running then you can use
<https://serversforhackers.com/docker-for-gulp-build-tasks>
(sorry, did put this together into a repo but couldnt share it yet and neither did the OP, feel free to pass me a link to something like this that is ready to use)

this should allow you to run

    $ gulp

# Contributing<a id="orgheadline3"></a>

To develop opencounter use [github:acolono/opencounter<sub>slim</sub><sub>codenv</sub>](https://github.com/acolono/opencounter_slim_codenv)
