#!/bin/bash
set -eu
FILES=$(find . -not -path "./vendor/*" | grep "\.php$")
php-cs-fixer fix --config=/opt/php_cs/.php_cs.dist --diff --using-cache=no $FILES
