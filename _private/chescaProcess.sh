# import packages
cd /var/www/odst-live/_private

# export regions
#db.packageschecksum.distinct('region')

php scripts/scraper.php chesca 1 1 | mail -s 'Chesca Processed' tech@odst.co.uk

echo 'http://www.odst.co.uk/feeds/chesca.zip?type=webgains' > logs/chesca.log
mongoexport -d odstech -c chesca_scrape --csv -f 'id','title','link','price','category','image_link','thumbnail','sizes','full_merchant_price','brand','availability','delivery_time','delivery_cost','material','description' -o files/hostedfeeds/chesca/webgains.csv | mail -s "Webgains Chesca Exported" tech@odst.co.uk < logs/chesca.log

echo 'http://www.odst.co.uk/feeds/chesca.zip?type=froogle' > logs/chesca.log
mongoexport -d odstech -c chesca_scrape --csv -f 'id','title','link','price','condition','shipping','brand','image_link','product_type','availability','description' -o files/hostedfeeds/chesca/froogle.csv | mail -s "Froogle Chesca Exported" tech@odst.co.uk < logs/chesca.log

./zipFiles.sh

php scripts/xmlGenerator.php chesca froogle | mail -s "Google XML Chesca Exported"