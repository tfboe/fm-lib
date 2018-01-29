#!/bin/bash
set  -e
#set environment default values
INTEGRATION="${INTEGRATION:-0}"

if [ "$INTEGRATION" = '1' ]; then
    # install mailcatcher
    sudo apt-get update && sudo apt-get install -y g++ make libsqlite3-dev ruby ruby-all-dev && gem install mailcatcher
fi