NAME := rosenstrauch/swyfler
VERSION := $(or $(VERSION),$(VERSION),'latest')
PLATFORM := $(shell uname -s)

build: clean composer_install

deploy:
	cd my-playbook && ansible-playbook -i hosts.yml deploy.yml --extra-vars "ansible_host=${DEPLOY_TARGET_HOST} ansible_user=${DEPLOY_TARGET_USER} ansistrano_deploy_to=${DEPLOY_TARGET_PATH}"


clean:
	rm -rf {dist,vendor}
	rm -f composer.lock
	composer clear-cache


composer_install:
	cd app && composer install --no-dev --prefer-dist --no-interaction

make_frontend: npm install && npm run build