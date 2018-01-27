#!/bin/bash

#set environment default values
CODE_COVERAGE="${CODE_COVERAGE:-0}"
SEND_TO_CODECOV="${SEND_TO_CODECOV:-1}"

if [ "$CODE_COVERAGE" = '1' ]; then
    # first generate unit tests
    cd ../fm-lib-test/vendor/tfboe/fm-lib
    echo "Run unit test"
    vendor/bin/phpunit --stderr --configuration phpunit-unit.xml --coverage-php=unit.cov

    # now run integration tests
    cd ../../../
    echo "Run integration tests"
    vendor/bin/phpunit --stderr --configuration phpunit-integration.xml --coverage-php=integration.cov
    mv vendor/tfboe/fm-lib/unit.cov .
    vendor/bin/phpcov -n merge --clover=coverage.xml .
    if [ "$SEND_TO_CODECOV" = '1' ]; then
        if [ $? -eq 0 ]; then bash <(curl -s https://codecov.io/bash); fi
    else
        cat unit.cov
        cat integration.cov
        cat coverage.xml
    fi
else
    vendor/bin/phpunit --stderr --configuration phpunit-unit.xml
fi