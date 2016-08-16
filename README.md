<div id="table-of-contents">
<h2>Table of Contents</h2>
<div id="text-table-of-contents">
<ul>
<li><a href="#sec-1">1. you need a webserver and a database</a></li>
<li><a href="#sec-2">2. Installation of dependencies</a>
<ul>
<li><a href="#sec-2-1">2.1. Create the Database</a></li>
</ul>
</li>
<li><a href="#sec-3">3. Contributing</a></li>
</ul>
</div>
</div>

<img src="./img/opencounter-logo.png" alt="OpenCounter Logo" width="150">

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

# you need a webserver and a database<a id="sec-1" name="sec-1"></a>

point webserver to public directory

# Installation of dependencies<a id="sec-2" name="sec-2"></a>

Use Composer to install dependencies

    "require": {
        "acolono/opencounter-api": "*"
    },
        "repositories": [
            {
                "type": "vcs",
                "url": "https://github.com/acolono/opencounter-api"
            }
        ],

## Create the Database<a id="sec-2-1" name="sec-2-1"></a>

installed via phinx

    php vendor/bin/phinx migrate

# Contributing<a id="sec-3" name="sec-3"></a>

To develop opencounter use the OpenCounterDocker