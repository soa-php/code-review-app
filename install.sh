#!/usr/bin/env bash

cd MergePullRequestPm
./run.sh composer install

cd ../Payment/
./run.sh composer install

cd ../PullRequest
./run.sh composer install