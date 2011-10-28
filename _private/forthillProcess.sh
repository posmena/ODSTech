# import packages
cd /var/www/odst-live/_private

# export regions
#db.packageschecksum.distinct('region')

php scripts/processFeed.php forthillhome | mail -s 'Forthill Processed' tech@odst.co.uk


echo 'http://www.odst.co.uk/feeds/forthill.zip?type=all' > logs/forthill.log
mongoexport -d odstech -c live_forthillhome --csv -f 'productid','productname','price','description','availability','category','condition','manufacturer','features','deeplink','image_thumbnail','image_large','google_product_type','nextag_deeplink','dooyoo_deeplink','google_deeplink' -o files/hostedfeeds/forthillhome/all.csv | mail -s "All Forthill Exported" tech@odst.co.uk < logs/forthill.log

echo 'http://www.odst.co.uk/feeds/forthill.zip?type=webgains' > logs/forthill.log
mongoexport -d odstech -c live_forthillhome --csv -f 'productid','productname','price','description','availability','category','condition','manufacturer','features','deeplink','image_thumbnail','image_large','webgains_deeplink' -o files/hostedfeeds/forthillhome/webgains.csv | mail -s "Webgains Forthill Exported" tech@odst.co.uk < logs/forthill.log

echo 'http://www.odst.co.uk/feeds/forthill.zip?type=froogle' > logs/forthill.log
mongoexport -d odstech -c live_forthillhome --csv -f 'id','title','link','price','description','condition','image_thumbnail','category','quantity','availability','google_deeplink','google_product_type' -o files/hostedfeeds/forthillhome/froogle.csv | mail -s "Froogle Forthill Exported" tech@odst.co.uk < logs/forthill.log

echo 'http://www.odst.co.uk/feeds/forthill.zip?type=nextag' > logs/forthill.log
mongoexport -d odstech -c live_forthillhome --csv -f 'productid','productname','price','description','availability','category','condition','manufacturer','features','deeplink','image_thumbnail','image_large','nextag_deeplink' -o files/hostedfeeds/forthillhome/nextag.csv | mail -s "Nextag Forthill Exported" tech@odst.co.uk < logs/forthill.log

echo 'http://www.odst.co.uk/feeds/forthill.zip?type=dooyoo' > logs/forthill.log
mongoexport -d odstech -c live_forthillhome --csv -f 'productid','productname','price','description','availability','category','condition','manufacturer','features','deeplink','image_thumbnail','image_large','dooyoo_deeplink' -o files/hostedfeeds/forthillhome/dooyoo.csv | mail -s "Dooyoo Forthill Exported" tech@odst.co.uk < logs/forthill.log

echo 'http://www.odst.co.uk/feeds/forthill.zip?type=bing' > logs/forthill.log
mongoexport -d odstech -c live_forthillhome --csv -f 'productid','productname','price','description','availability','category','condition','manufacturer','features','deeplink','image_thumbnail','image_large' -o files/hostedfeeds/forthillhome/bing.csv | mail -s "Bing Forthill Exported" tech@odst.co.uk < logs/forthill.log


./zipFiles.sh

php scripts/xmlGenerator.php forthillhome froogle | mail -s "Google XML Forthill Exported"