#!/bin/bash

SCRIPT=$(readlink -f "$0")
SCRIPTPATH=$(dirname "$SCRIPT")
BASE="$SCRIPTPATH/.."

RED='\e[0;31m'
NC='\e[0m' # No Color

init_error() {
	echo "$(tput setaf 1)Initialization error:\n--> $1$(tput sgr 0)"
	exit 1;
}

createDirectory() {
	echo "---> Creating directory '$1'"
	if [ -d $1 ]; then
		echo "  -> Directory $1 exists, deleting..."
		rm -Rf "$1"
		if [ $? -ne 0 ]; then
			init_error "Cannnot delete directory '$1'"
		fi
	fi
	mkdir -p "$1"
	if [ $? -ne 0 ]; then
		init_error "Cannot create directory '$1'"
	fi
	echo "  -> Directory $1 $(tput setaf 2)created$(tput sgr 0)"
}

#
#	Composer
#

echo ""
echo "$(tput setaf 4)Composer install:$(tput sgr 0)"
cd "$BASE"
composer install

#
#	Create directories
#

echo ""
echo "$(tput setaf 4)Prepare temp directory:$(tput sgr 0)"
createDirectory "$BASE/log"
createDirectory "$BASE/temp"
