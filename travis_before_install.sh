#!/bin/bash

#set environment default values
CODE_COVERAGE="${CODE_COVERAGE:-0}"

if [ "$CODE_COVERAGE" = '1' ]; then
    # install mailcatcher
    sudo apt-get update && sudo apt-get install -y g++ make libsqlite3-dev ruby ruby-all-dev && gem install mailcatcher
fi