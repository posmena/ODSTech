# import packages
cd /var/www/odst-live/_private

php scripts/preProcessFeed.php livingsocial full 1
php scripts/processFeed.php livingsocial full 1
php scripts/postProcessFeed.php livingsocial full 1
