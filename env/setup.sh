#!/bin/bash

wp theme activate wporg-developer

wp rewrite structure '/%year%/%monthnum%/%postname%/'
wp rewrite flush

wp post create --post_type=page --post_title=Home --post_status=publish --post_name=home --page_template=page-home-landing.php
wp post create --post_type=page --post_title=Reference --post_status=publish --post_name=reference --page_template=page-reference-landing.php
wp post create --post_type=page --post_title=Dashicons --post_status=publish --post_name=dashicons --page_template=page-dashicons.php

wp option update page_on_front `wp post list --post_type=page --name=home --format=ids`
wp option update show_on_front page