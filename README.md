# Step 1
# ------
# change .env-example to .env

# Step 2
# ------
# go to branch development

# Step 3
# ------
# install of the application dependencies
# run composer install

# Step 4
# ------
# install of the application dependencies
# run composer install

# Step 5
# ------
# generate a unique key for the application
# that key is used for encryptions
# run php artisan key:generate

# Step 6
# ------
# migrates the tables to the database
# run php artisan migrate

# Step 7
# ------
# creates default records for the application
# run php artisan db:seed

# Step 8
# ------
# inserts unique keys for the authentication 
# run php artisan passport:install

# Step 9
# ------
# runs the application
# run php artisan serve

# How to deploy to production
# ------
# create a pull request from "develop" -> "main" and merge it
# it will deploy it automatically from the "main" branch right after



# A user to login
# email     = demo@goldens.com
# password  = goldensDemoPa55