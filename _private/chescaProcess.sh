# import packages
cd /var/www/odst-live/_private

# export regions
#db.packageschecksum.distinct('region')

php scripts/scraper.php chesca 1 1 | mail -s 'Chesca Processed' tech@odst.co.uk

echo 'http://www.odst.co.uk/feeds/chesca.zip?type=webgains' > logs/chesca.log
mongoexport -d odstech -c chesca_scrape --csv -f 'product_id','name','deeplink','price','category','deeplink','largeimage','thumbnail','sizes','description','full_merchant_price','stock','delivery_time','delivery_price' -o files/hostedfeeds/chesca/webgains.csv | mail -s "Webgains Chesca Exported" tech@odst.co.uk < logs/chesca.log

./zipFiles.sh