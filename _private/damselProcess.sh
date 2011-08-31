# import packages
cd /var/www/odst-live/_private

# export regions
#db.packageschecksum.distinct('region')

php scripts/scraper.php damsel 1 1 | mail -s 'Damsel Processed' tech@odst.co.uk

echo 'http://www.odst.co.uk/feeds/damsel.zip?type=webgains' > logs/damsel.log
mongoexport -d odstech -c damsel_scrape --csv -f 'id','title','link','price','category','image_link','thumbnail','sizes','full_merchant_price','brand','availability','delivery_time','delivery_cost','material','description' -o files/hostedfeeds/damsel/webgains.csv | mail -s "Webgains Damsel Exported" tech@odst.co.uk < logs/damsel.log

echo 'http://www.odst.co.uk/feeds/damsel.zip?type=froogle' > logs/damsel.log
mongoexport -d odstech -c damsel_scrape --csv -f 'id','title','link','price','condition','shipping','brand','image_link','category','quantity','availability','description' -o files/hostedfeeds/damsel/froogle.csv | mail -s "Froogle Damsel Exported" tech@odst.co.uk < logs/damsel.log

./zipFiles.sh