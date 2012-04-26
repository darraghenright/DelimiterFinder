#!/bin/sh

#
# Test runner
#

FILE='DelimiterFinderTest.php'

[[ ! -r $FILE ]] && exit 1

phpunit --verbose --colors $FILE

exit $?