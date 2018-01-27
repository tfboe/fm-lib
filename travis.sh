#!/bin/bash

#set environment default values
CODE_COVERAGE="${CODE_COVERAGE:-0}"
SEND_TO_CODECOV="${SEND_TO_CODECOV:-1}"
INTEGRATION="${INTEGRATION:-0}"
UNIT="${UNIT:-0}"

if [ "$UNIT" = '1' ]; then
    if [ "$INTEGRATION" = '1' ]; then
        #move into inner directory
        cd ../fm-lib-test/vendor/tfboe/fm-lib
    fi
    #run unit tests
    echo "Run unit tests"
    vendor/bin/phpunit --stderr --configuration phpunit-unit.xml --coverage-php=unit.cov
    if [ "$INTEGRATION" = '1' ]; then
        #move report to outer directory
        mv unit.cov ../../../
        #move into outer directory
        cd ../../../
    fi
fi

if [ "$INTEGRATION" = '1' ]; then
    echo "Run integration tests"
    vendor/bin/phpunit --stderr --configuration phpunit-integration.xml --coverage-php=integration.cov
fi

if [ "$CODE_COVERAGE" = "1" ]; then
    #merge all reports to one code coverage report
    vendor/bin/phpcov -n merge --clover=coverage.xml .
    if [ "$SEND_TO_CODECOV" = '1' ]; then
        if [ $? -eq 0 ]; then bash <(curl -s https://codecov.io/bash); fi
    else
        #output coverage file
        cat coverage.xml
    fi
fi