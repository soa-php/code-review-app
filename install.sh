#!/usr/bin/env bash

cd MergePullRequestPm
./run.sh composer install

cd ../Payment/
./run.sh composer install

cd ../PullRequest
./run.sh composer install

cd ../Common
./run.sh composer install

docker network inspect common &>/dev/null || docker network create common