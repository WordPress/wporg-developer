#!/bin/bash

wp theme activate wporg-developer-2023

wp rewrite structure '/%year%/%monthnum%/%postname%/'
wp rewrite flush

# Create the pages required.
wp post create --post_type=page --post_title=Dashicons --post_status=publish --post_name=dashicons --page_template=page-dashicons.php

# Set the homepage to be the home page.
wp option update page_on_front `wp post list --post_type=page --name=home --format=ids`
wp option update show_on_front page

# Run all cron tasks (Including Handbook imports from GitHub)
wp cron event run --all
