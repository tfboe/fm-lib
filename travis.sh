#!/bin/bash

CODE_COVERAGE="${CODE_COVERAGE:-0}"

if [ "$CODE_COVERAGE" = '1' ]; then
    vendor/bin/phpunit --stderr --configuration phpunit-unit.xml --coverage-clover=coverage.xml
    if [ $? -eq 0 ]; then bash <(curl -s https://codecov.io/bash); fi
else
    vendor/bin/phpunit --stderr --configuration phpunit-unit.xml
fi