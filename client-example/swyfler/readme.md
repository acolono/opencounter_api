
place settings file and adjust configuration

    cp app/config/settings.php.dist app/config/settings.php

Start the application using PHP's built-in web server.


   $ cd public
   $ php -S localhost:8000

or better yet

    docker-compose up


to build run

    make build

or just run the real command:

  cd app && composer install --no-dev --prefer-dist --no-interaction

manual deployment via ansistrano:

set the following env vars:

    DEPLOY_TARGET_HOST
    DEPLOY_TARGET_PATH
    DEPLOY_TARGET_USER


and run either

    make deploy

or directly:


    cd my-playbook && ansible-playbook -i hosts.yml deploy.yml --extra-vars "ansible_host=${DEPLOY_TARGET_HOST} ansible_user=${DEPLOY_TARGET_USER} ansistrano_deploy_to=${DEPLOY_TARGET_PATH}"


for automated deployment see [gitlab ci](./.gitlab-ci.yml)


task runner can be used to put the css and js into place via any of these equivalent command aliases:

    make frontend

which maps to:

    npm install && npm run build

which maps to

    gulp
