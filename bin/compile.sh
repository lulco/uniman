#!/bin/bash

SCRIPT=$(readlink -f "$0")
SCRIPTPATH=$(dirname "$SCRIPT")
BASE="$SCRIPTPATH/.."

RED='\e[0;31m'
NC='\e[0m' # No Color

#
#	Composer uninstall dev dependencies
#

echo ""
echo "$(tput setaf 4)Composer install no dev:$(tput sgr 0)"
composer install --no-dev

#
#	Compilation
#

echo ""
echo "$(tput setaf 4)Compilation:$(tput sgr 0)"
rm -rf "$BASE/build/"
php $BASE/bin/compile
rm -rf "$BASE/build/index.phar"


#
#	Composer install dev dependencies
#

echo ""
echo "$(tput setaf 4)Composer install:$(tput sgr 0)"
composer install
