#!/bin/bash
set -euo pipefail
IFS=$'\n\t'

echo -e "\e[96m STEP 1: Create .env \e[39m"
cp -i .env.example .env

echo -e "\e[96m STEP 2: Install composer \e[39m"
composer install

echo -e "\e[96m STEP 3: Generate key \e[39m"
php artisan key:generate

echo -e "\e[96m STEP 4: Migration with seeder \e[39m"
php artisan migrate --seed

echo -e "\e[96m STEP 5: Install node_modules \e[39m"
npm install

echo -e "\e[96m STEP 6: Install bower \e[39m"
npm install -g bower

echo -e "\e[96m STEP 7: Install gulp \e[39m"
npm install -g gulp

echo -e "\e[96m STEP 8: Install apidoc \e[39m"
npm install -g apidoc

echo -e "\e[96m STEP 9: Bower install \e[39m"
bower install --allow-root

echo -e "\e[96m STEP 10: Gulp langjs \e[39m"
gulp langjs

echo -e "\e[96m STEP 11: Gulp \e[39m"
gulp

echo -e "\e[96m STEP 12: Gulp fonts \e[39m"
gulp fonts

echo -e "\e[96m STEP 13: Gulp images \e[39m"
gulp images

echo -e "\e[96m STEP 14: Generate doc for API \e[39m"
npm run doc

echo -e "\e[92m SUCCESS: Setup successful. \e[39m"
