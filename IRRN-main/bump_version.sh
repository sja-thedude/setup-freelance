#!/bin/bash

GREEN='\033[0;32m'
RED='\033[0;31m'
BLACK='\033[0;30m'

envFileList=(".env.dev" ".env.template.dev" ".env.group.dev" ".env.stag" ".env.template.stag" ".env.group.stag")

function bump() {

    VERSION_KEY=$4
    ENV_FILE=$3
    BUMP_TYPE=$2
    FLATFORM=$1

    # Read the current value from the .env file
    version=$(grep '^'$VERSION_KEY'=' "$ENV_FILE" | cut -d'=' -f2)

    # Remove string quotes
    current_version=`sed -e 's/^"//' -e 's/"$//' <<<"$version"`

    # Split the version number into components
    IFS='.' read -ra parts <<< "$current_version"

    major=${parts[0]}
    minor=${parts[1]}
    patch=${parts[2]}

    case "$BUMP_TYPE" in
    '3')
        patch=$((patch + 1))
        ;;
    "2")
        minor=$((minor + 1))
        patch=0
        ;;
    "1")
        major=$((major + 1))
        patch=0
        minor=0
        ;;
    esac

    # Join the components back into a version number
    new_version="${major}.${minor}.${patch}"

    # Update the .env file with the new value
    sed -i '' -e "s/^"$VERSION_KEY"=.*/"$VERSION_KEY"="\"$new_version\""/" "$ENV_FILE"

    echo -e "New version [$ENV_FILE] [$FLATFORM]: $current_version => ${GREEN} $new_version ${BLACK}"
}

echo " "
echo "__________INCREASE VERSION__________"
echo " "

read -r -p "$@ Select Flatform (1 - ANDROID 2 - IOS 3 - ALL): " flatform
read -r -p "$@ Select Bump Type (1 - MAJOR 2 - MINOR 3 - PATCH): " type

if [ "$type" == "1" ] || [ "$type" == "2" ] || [ "$type" == "3" ]; then 
    if [ "$flatform" == "1" ]; then
        for file in ${envFileList[@]}; do
            bump "ANDROID" "$type" "$file" "ENV_ANDROID_VERSION_NAME"
        done
    else
        if [ "$flatform" == "2" ]; then
                for file in ${envFileList[@]}; do
                    bump "IOS" "$type" "$file" "ENV_IOS_VERSION_NAME"
                done
            else 
                if [ "$flatform" == "3" ]; then
                    for file in ${envFileList[@]}; do
                        bump "ANDROID" "$type" "$file" "ENV_ANDROID_VERSION_NAME"
                        bump "IOS" "$type" "$file" "ENV_IOS_VERSION_NAME"
                    done
                else 
                    echo " "
                    echo "${RED} => INVALID FLATFORM ${BLACK}"
                    echo "${RED} => CANCEL ${BLACK}"
                    echo " "
                fi
        fi
    fi
else
    echo " "
    echo -e "${RED} => INVALID BUMP TYPE ${BLACK}"
    echo -e "${RED} => CANCEL ${BLACK}"
    echo -e " "
fi