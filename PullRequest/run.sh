#!/usr/bin/env bash

COMMAND=${1:-}
shift

run_in_docker="docker run -it --rm -v $PWD/..:/srv/app -w /srv/app/PullRequest --user ${UID} php_base:1.0"

case "$COMMAND" in
    composer)
    ${run_in_docker} composer $@
    ;;
    phpunit)
    ${run_in_docker} vendor/phpunit/phpunit/phpunit tests
    ;;
esac
