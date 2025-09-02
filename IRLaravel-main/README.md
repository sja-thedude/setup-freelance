# INSTALLATION

- Step 1: Download source code
    ```bash
    git clone git@gitlab.com:vitex.asia/it-ready.git
    ```

- Step 2: Check structure of ``storage`` directory and create new if doesn't exist
    - app697-connectict
        - storage
            - app
                - public
                    - default
            - debugbar
            - framework
                - cache
                - sessions
                - views
            - logs

- Step 3: If you have problem with folder permissions. You can check at * [here](https://laracasts.com/discuss/channels/general-discussion/laravel-framework-file-permission-security)

- Step 4: Create .env
    ```bash
    cp -i .env.example .env
    ```
  Update database connection
  ```bash
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=homestead
    DB_USERNAME=homestead
    DB_PASSWORD=secret
  ```
  
- Step 5: Install composer
  ```bash
  composer install
  ```
  
- Step 6: Generate key
  ```bash
  php artisan key:generate
  ```

- Step 7: Migration with seeder
  ```bash
  php artisan migrate --seed
  ```
  
- Step 8: Install node_modules
    ```bash
    npm install
    ```

- Step 9: Install bower
    ```bash
    npm install -g bower
    ```

- Step 10: Install gulp
    ```bash
    npm install -g gulp
    ```

- Step 11: Install apidoc
    ```bash
    npm install -g apidoc
    ```

- Step 12: Bower install packages
    ```bash
    bower install --allow-root
    ```

- Step 13: Run gulp
    ```bash
    gulp fonts
    gulp langjs
    gulp images
    gulp
    ```

- Step 14: Run doc
    ```bash
    npm run doc
    ```
  
- Step 15: Create folder 'public/uploads' if it doesn't exist

# DESCRIPTION FORMAT OF MERGE REQUEST (FROM DEVELOP TO STAGING OR PRODUCTION)

- What is needed to upgrade the current code to a new version?
- What is the impact?
- What with current users using the site/application

This are things i always check before releasing a new agenda version. I think we should do the same for all the other projects.

1. Maybe add something like this in the pull requests
- [X] git pull origin master
- [X] artisan migrations
- [X] composer install
- [X] gulp

2. Extra scripts
- [ ] run script x from y

3. Test
- [ ] Test X because this was the biggest change

# NOTE
- We needed the package libpng-dev to be installed on the system.
- Else you get errors like:
    - pngquant pre-build test failed
    - no nasm (Netwide Assembler) found
    - Error: pngquant failed to build, make sure that libpng-dev is installed
