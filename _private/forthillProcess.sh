# import packages
cd /var/www/odst-live/_private

# export regions
#db.packageschecksum.distinct('region')

echo 'started'
php scripts/processFeed.php forthillhome verbose  | mail -s 'Forthill Processed' tech@odst.co.uk

echo 'downloaded feed'

php scripts/scraper.php forthillhome full verbose | mail -s 'Forthill Brands Scraped' tech@odst.co.uk

echo 'scraped brands'

echo 'http://www.odst.co.uk/feeds/forthill.zip?type=all' > logs/forthill.log
mongoexport -d odstech -c live_forthillhome --csv -f 'productid','productname','price','shipping','description','availability','category','condition','manufacturer','features','deeplink','image_thumbnail','image_large','google_product_type','nextag_deeplink','dooyoo_deeplink','google_deeplink' -o files/hostedfeeds/forthillhome/all.csv | mail -s "Forthill All Exported" tech@odst.co.uk < logs/forthill.log

echo 'http://www.odst.co.uk/feeds/forthill.zip?type=webgains' > logs/forthill.log
mongoexport -d odstech -c live_forthillhome --csv -f 'productid','productname','price','shipping','description','availability','category','condition','manufacturer','features','deeplink','image_thumbnail','image_large','webgains_deeplink' -o files/hostedfeeds/forthillhome/webgains.csv | mail -s "Forthill Webgains Exported" tech@odst.co.uk < logs/forthill.log

echo 'http://www.odst.co.uk/feeds/forthill.zip?type=froogle' > logs/forthill.log
mongoexport -d odstech -c live_forthillhome --csv -f 'id','title','link','price','shipping','description','condition','image_link','product_type','google_product_category','quantity','availability','brand','mpn' -o files/hostedfeeds/forthillhome/froogle.csv | mail -s "Forthill Froogle Exported" tech@odst.co.uk < logs/forthill.log

echo 'http://www.odst.co.uk/feeds/forthill.zip?type=nextag' > logs/forthill.log
mongoexport -d odstech -c live_forthillhome --csv -f 'productid','productname','price','shipping','description','availability','category','condition','manufacturer','features','deeplink','image_thumbnail','image_large','nextag_deeplink' -o files/hostedfeeds/forthillhome/nextag.csv | mail -s "Forthill Nextag Exported" tech@odst.co.uk < logs/forthill.log

echo 'http://www.odst.co.uk/feeds/forthill.zip?type=dooyoo' > logs/forthill.log
mongoexport -d odstech -c live_forthillhome --csv -f 'productid','productname','price','shipping','description','availability','category','condition','manufacturer','features','deeplink','image_thumbnail','image_large','dooyoo_deeplink' -o files/hostedfeeds/forthillhome/dooyoo.csv | mail -s "Forthill Dooyoo Exported" tech@odst.co.uk < logs/forthill.log

echo 'http://www.odst.co.uk/feeds/forthill.zip?type=bing' > logs/forthill.log
mongoexport -d odstech -c live_forthillhome --csv -f 'productid','productname','price','shipping','description','availability','category','condition','manufacturer','features','deeplink','image_thumbnail','image_large' -o files/hostedfeeds/forthillhome/bing.csv | mail -s "Forthill Bing Exported" tech@odst.co.uk < logs/forthill.log


./zipFiles.sh

php scripts/xmlGenerator.php forthillhome froogle | mail -s "Forthill Google XML Exported" tech@odst.co.uk