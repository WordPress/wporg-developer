#!/bin/bash

# Imports every WordPress source file whose DocBlocks contain interactive
# snippet fences into wp-parser reference posts. Parsing only that subset
# keeps the sweep fast while producing the same snippet pages as a full parse.

set -e

ROOT=$(pwd)
SRC=$(mktemp -d)
SUBSET=$(mktemp -d)

wp core download --path="$SRC" --skip-content --force

cd "$SRC"

find wp-includes wp-admin -name '*.php' -exec grep -l '```php interactive' {} + | while read -r file; do
	install -D "$file" "$SUBSET/$file"
done

cd "$ROOT"

wp plugin deactivate posts-to-posts 2>/dev/null || true
wp plugin activate phpdoc-parser

wp parser create "$SUBSET" --user=1 --quick

wp plugin deactivate phpdoc-parser
wp plugin activate posts-to-posts

wp rewrite flush

rm -rf "$SRC" "$SUBSET"
