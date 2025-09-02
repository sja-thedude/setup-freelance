#!/bin/bash
set -euo pipefail
IFS=$'\n\t'

echo -e "\e[96m STEP 1: Migration \e[39m"
php artisan migrate

echo -e "\e[96m STEP 2: Gulp langjs \e[39m"
gulp langjs

echo -e "\e[96m STEP 3: Gulp fonts \e[39m"
gulp fonts

echo -e "\e[96m STEP 4: Gulp images \e[39m"
gulp images

echo -e "\e[96m STEP 5: Gulp \e[39m"
gulp

echo -e "\e[96m STEP 6: Restart queue \e[39m"
php artisan queue:restart

#echo -e "\e[96m STEP 7: Clear cache \e[39m"
#php artisan cache:clear

echo -e "\e[92m SUCCESS: Deploy successful. \e[39m"
