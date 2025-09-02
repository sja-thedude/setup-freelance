echo 
echo Deploy to IOS Staging
appcenter codepush release-react -a dkhactam/ItsReady-IOS -d Staging -t '*' --disable-duplicate-release-error

echo 
echo Deploy to ANDROID Staging
appcenter codepush release-react -a dkhactam/ItsReady-Android -d Staging -t '*' --disable-duplicate-release-error
