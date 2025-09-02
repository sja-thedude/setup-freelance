#!/bin/bash

GREEN='\033[0;32m'
RED='\033[0;31m'
BLACK='\033[0;30m'

envFileList=(".env.dev" ".env.template.dev" ".env.group.dev" ".env.stag" ".env.template.stag" ".env.group.stag")

function bump() {

    VERSION_KEY=$3
    ENV_FILE=$2
    FLATFORM=$1

    # Read the current value from the .env file
    version=$(grep '^'$VERSION_KEY'=' "$ENV_FILE" | cut -d'=' -f2)

    # Remove string quotes
    current_version=`sed -e 's/^"//' -e 's/"$//' <<<"$version"`

    new_version="$((current_version + 1))"
    

    # Update the .env file with the new value
    sed -i '' -e "s/^"$VERSION_KEY"=.*/"$VERSION_KEY"="\"$new_version\""/" "$ENV_FILE"

    echo -e "New build [$ENV_FILE] [$FLATFORM]: $current_version => ${GREEN} $new_version ${BLACK}"
}

echo " "
echo "__________INCREASE BUILD NUMBER__________"
echo " "

read -r -p "$@ Select Flatform (1 - ANDROID 2 - IOS 3 - ALL): " flatform

if [ "$flatform" == "1" ]; then
    for file in ${envFileList[@]}; do
        bump "ANDROID" "$file" "ENV_ANDROID_VERSION_CODE"
    done
else
    if [ "$flatform" == "2" ]; then
            for file in ${envFileList[@]}; do
                bump "IOS" "$file" "ENV_IOS_VERSION_CODE"
            done
        else 
            if [ "$flatform" == "3" ]; then
                for file in ${envFileList[@]}; do
                    bump "ANDROID" "$file" "ENV_ANDROID_VERSION_CODE"
                    bump "IOS" "$file" "ENV_IOS_VERSION_CODE"
                done
            else 
                echo " "
                echo "${RED} => INVALID FLATFORM ${BLACK}"
                echo "${RED} => CANCEL ${BLACK}"
                echo " "
            fi
    fi
fi