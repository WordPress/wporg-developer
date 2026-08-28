#!/bin/bash

set -e

wp theme activate wporg-developer-2023

wp rewrite structure '/%year%/%monthnum%/%postname%/'
wp rewrite flush
