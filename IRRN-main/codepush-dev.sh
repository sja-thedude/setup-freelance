echo 
echo Deploy to IOS Dev
appcenter codepush release-react -a dkhactam/ItsReady-IOS -d Dev -t '*' --disable-duplicate-release-error

echo 
echo Deploy to ANDROID Dev
appcenter codepush release-react -a dkhactam/ItsReady-Android -d Dev -t '*' --disable-duplicate-release-error
