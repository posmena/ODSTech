# import packages
cd /Users/bobbeh/Sites/techodst-dev/_private

# export regions
#db.packageschecksum.distinct('region')

php scripts/scraper.php chesca 1 1 | mail -s 'Chesca Processed' tech@odst.co.uk

echo 'http://www.odst.co.uk/feeds/jtspas.zip?type=webgains' > logs/chesca.log
/usr/local/mongodb/bin/mongoexport -d odstech -c chesca_scrape --csv -f 'product_id','name','deeplink','price','category','deeplink','largeimage','thumbnail','sizes','description' -o files/hostedfeeds/chesca/webgains.csv | mail -s "Webgains Chesca Exported" tech@odst.co.uk < logs/chesca.log

./zipFiles.sh