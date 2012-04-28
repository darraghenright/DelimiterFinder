#!/bin/sh

#
# Test runner
#

FILE='DelimiterFinderTest.php'

[[ ! -r $FILE ]] && exit 1

clear

phpunit --verbose --colors $FILE

exit $?