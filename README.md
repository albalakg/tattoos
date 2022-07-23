# Step 1
# ------
# change .env-example to .env

# Step 2
# ------
# create database with the db name as in the .env file

# Step 3
# ------
# install of the application dependencies
# run composer install

# Step 4
# ------
# generate a unique key for the application
# that key is used for encryptions
# run php artisan key:generate

# Step 5
# ------
# migrates the tables to the database
# run php artisan migrate

# Step 6
# ------
# creates default records for the application
# run php artisan db:seed

# Step 7
# ------
# inserts unique keys for the authentication 
# run php artisan passport:install

# Step 8
# ------
# runs the application
# run php artisan serve



# A user to login
# email     = demo@goldens.com
# password  = goldensDemoPa55