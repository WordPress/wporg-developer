#!/bin/bash

# Import the handbooks. These are on cron events.
for post_type in blocks wpcs rest-api; do
	echo Importing the ${post_type} handbook...

	wp cron event run devhub_${post_type}_import_manifest
	wp cron event run devhub_${post_type}_import_all_markdown
done
