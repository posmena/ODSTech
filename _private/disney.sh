# import packages
cd /var/www/odst-live/_private

php scripts/preProcessFeed.php disney full 1
php scripts/processFeed.php disney full 1
php scripts/postProcessFeed.php disney full 1
