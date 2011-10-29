# import packages
cd /var/www/odst-live/_private

# export regions
#db.packageschecksum.distinct('region')

php scripts/processFeed.php easylife | mail -s 'EasyLife Processed' tech@odst.co.uk

echo 'http://www.odst.co.uk/feeds/easylife.zip?type=afuture' > logs/jtspatype.log
mongoexport -d odstech -c live_easylife --csv -f 'productid','productname','deeplink','brand','price','image_link','category','availability','manufacturer','description' -o files/hostedfeeds/easylife/afuture.csv | mail -s "Easylife Afuture Exported" tech@odst.co.uk < logs/easylife.log


./zipFiles.sh