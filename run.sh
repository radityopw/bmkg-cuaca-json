#! /bin/bash
cd "$(dirname "$0")"

git pull

rm -r xml/*
rm -r cuaca/*
php -f convert.php
git add .
git commit -m "generate file json cuaca"
git push

