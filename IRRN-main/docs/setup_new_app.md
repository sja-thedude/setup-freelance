#

## Setup New App Instructions

Suppose you want to setup a new app called **Ready Freddy**, package name is **com.itsready.readyfreddy**

### Create .evn file

In the root project folder, create a file named `.env.template.readyfreddy` (or `.env.group.readyfreddy if you are setting up group app`) with the following contents (Replace the values with your corresponding values):

> // Deep Link Scheme
> ENV_APP_SCHEME="" // `itsready + app_token`
>
> // App Configs
> ENV_APP_NAME="Ready Freddy" // your `app name`
> ENV_ANDROID_APP_ID="com.itsready.readyfreddy" // your app `package name`
> ENV_ANDROID_VERSION_NAME="1.0.0"
> ENV_ANDROID_VERSION_CODE="1"
> ENV_IOS_APP_ID="com.itsready.readyfreddy" // your app `package name`
> ENV_IOS_VERSION_NAME="1.0.0"
> ENV_IOS_VERSION_CODE="1"
> ENV_IOS_STORE_URL=" "
> ENV_ANDROID_STORE_URL=" "
>
> // App Template Configs
> ENV_TEMPLATE_APP=" " // `true` if you are setting up `template app` otherwise `" "`
> ENV_WORKSPACE_TOKEN=" " // `app_token` if you are setting up `template app` otherwise `" "`
>
> // App Group Configs
> ENV_GROUP_APP="true" // `true` if you are setting up `group app` otherwise `" "`
> ENV_GROUP_TOKEN=" "  // `app_token` if you are setting up `group app` otherwise `" "`
>
> // Login Facebook Configs
> ENV_FACEBOOK_APP_ID="514303633447261"
> ENV_FACEBOOK_CLIENT_TOKEN="c26abeaf9302df9dcfb75087c6b6c664"
>
> // Login Google Configs
> ENV_WEB_CLIENT_ID="" // the value of `client_id` field with `client_type = 3` from `google-service.json`
> ENV_REVERSED_CLIENT_ID="" // the value of `REVERSED_CLIENT_ID` field from `GoogleServices-Info.plist`
>
> // API URLs
> ENV_API_URL="<https://itsready.be/api/v1>"
>
> // Google map API key
> ENV_GG_MAP_API_KEY="AIzaSyBUpejEqMdNQhT67RL0mu4TaH5-xwFkgWw"
>

---

### Setup new Android app

#### Create android app on FireBase Console

1. Open [It's Ready FireBase Console](./setup_new_app.md](https://console.firebase.google.com/u/0/project/it-s-ready-prod-3-cf685/overview))
2. Click `+` and select `Android`
3. In the `Android package name` field, enter `com.itsready.readyfreddy`
4. In the `App nickname` field, enter `Ready Freddy`
5. Click `Register app`
6. Click `Download google-services.json`

#### Create android app product flavor

1. Open project folder in VSCode
2. Create new folder named `readyFreddy` in `android/app/src`
3. Copy `google-services.json` file downloaded in the section above and paste it into the folder you just created
4. Add new `productFlavor`:
   4.1. Open `android/app/build.gradle`
   4.2. In `productFlavors` section, create new flavor named `readyFreddy` (same as name of the folder you created in step 2)
   ![alt text](image/new_android_flavor.png "App Build Target")

---

### Setup new IOS app

#### Create IOS app on FireBase Console

1. Open [It's Ready FireBase Console](./setup_new_app.md](https://console.firebase.google.com/u/0/project/it-s-ready-prod-3-cf685/overview))
2. Click `+` and select `Apple`
3. In the `Apple bundle ID` field, enter `com.itsready.readyfreddy`
4. In the `App nickname` field, enter `Ready Freddy`
5. Click `Register app`
6. Click `Download GoogleServices-info.plist`

#### Create IOS Build Target

1. Open `ios/ItsReady.xcworkspace` in Xcode
2. Right-click on `ItsReady` target and select `Duplicate`
   ![Duplicate target](image/dupplicate_ios_target.png "App Build Target")
3. Once you’ve done that, you can see the new target has been created together with an associated Info.plist file
   ![Duplicate target](image/dupplicate_ios_target2.png "App Build Target")
4. Change the name of the created Target
   We will need to first rename the scheme by tapping on the “Manage Schemes…” option.
    ![Duplicate target](image/edit_scheme.png "App Build Target")
   Tap on the scheme name and change to `ItsReady_Template_ReadyFreddy` (or `ItsReady_Group_ReadyFreddy` if you are setting up group app)
   ![Duplicate target](image/change_target_name.png "App Build Target")
   Next, change the name of the newly created Target to the same name as well which is “ItsReady_Template_ReadyFreddy” in this case.
   ![Duplicate target](image/change_target_name2.png "App Build Target")
5. Delete newly created Info.plist file
    ![Duplicate target](image/del_plist_file.png "App Build Target")
6. Link newly created Target to main Info.plist file
   Select created `Target > Build Setting`, scroll to `Packaging` section, edit value of `Info.plist File` field to `ItsReady/ItsReady-Info.plist`
   ![Duplicate target](image/link_plist_to_target2.png "App Build Target")
7. Edit Target Build Pre-action
   Select "Edit Scheme"
    ![Duplicate target](image/edit_scheme2.png "App Build Target")
   Select `ItsReady_template_ReadyFreddy`
    ![Duplicate target](image/edit_scheme3.png "App Build Target")
   Select `Build > Pre-actions > New Run Scrips Action`
    ![Duplicate target](image/edit_scheme4.png "App Build Target")
   Fill in the form the content below:

   ```bash
   cp "${PROJECT_DIR}/../.env.template.readyfreddy" "${PROJECT_DIR}/../.env"
   ```

   And in `Provide build setting from` field, select `ItsReady_template_ReadyFreddy`
   ![Duplicate target](image/edit_scheme5.png "App Build Target")

   Continue creating a new `Run Scrip` by click `+` and select `New Run Script Action`.
   Fill in the form the content below:

   ```bash
   "${SRCROOT}/../node_modules/react-native-config/ios/ReactNativeConfig/BuildXCConfig.rb" "${SRCROOT}/.." "${SRCROOT}/tmp.xcconfig"
   ```

   And in Provide build setting from field, select `ItsReady_template_ReadyFreddy`
   ![Duplicate target](image/edit_scheme5.png "App Build Target")
   Click `Close`.
8.  Create new XCode group named `ItsReady_Template_ReadyFreddy`
   ![Duplicate target](image/create_group.png "App Build Target")
   Right-click on the group and select `Add files to "ItsReady"...`
   ![Duplicate target](image/add_file_to_itsready.png "App Build Target")
   Select the GoogleServices-info.plist file downloaded in the section above and make sure `Copy items if needed` option is checked. Then click `Add`
   ![Duplicate target](image/add_file_to_itsready2.png "App Build Target")
9.  Update Podfile
   Open `ios/Podfile` and insert above content at end of the file

    ```bash
        target 'ItsReady_template_ReadyFreddy' do
            available_pods
        end
    ```
