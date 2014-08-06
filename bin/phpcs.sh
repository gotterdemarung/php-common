#!/bin/sh

cd "$(dirname "$0")/../"
vendor/bin/phpcs --standard=PSR2 src/