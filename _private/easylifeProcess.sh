# import packages
cd /var/www/odst-live/_private

# export regions
#db.packageschecksum.distinct('region')

php scripts/processFeed.php easylife

php scripts/scraper.php easylife

echo 'http://www.odst.co.uk/feeds/easylife.zip?type=afuture' > logs/easylife.log
mongoexport -d odstech -c ot_easylife --csv -f 'productid','productname','deeplink','brand','price','image_link','image_link2','category','availability','manufacturer','description' -o files/hostedfeeds/easylife/afuture.csv | mail -s "Easylife Afuture Exported" tech@odst.co.uk < logs/easylife.log

echo 'http://www.odst.co.uk/feeds/easylife.zip?type=custom' > logs/easylife.log
mongoexport -d odstech -c ot_easylife --csv -f 'id','title','deeplink','brand','price','image_link','image_link2','image_link3','product_type','availability','description','condition','gtin' -o files/hostedfeeds/easylife/custom.csv | mail -s "Easylife Custom Exported" tech@odst.co.uk < logs/easylife.log

./zipFiles.sh