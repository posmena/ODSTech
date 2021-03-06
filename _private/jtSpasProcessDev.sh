# import packages
cd /Users/bobbeh/Sites/techodst-dev/_private

# export regions
#db.packageschecksum.distinct('region')

php scripts/processFeed.php jtspas | mail -s 'JtSpas Processed' tech@odst.co.uk
php scripts/temporaryData.php jtspas | mail -s 'JtSpas Price Update Complete' tech@odst.co.uk

echo 'http://www.odst.co.uk/feeds/jtspas.zip?type=all' > logs/jtspatype.log
/usr/local/mongodb/bin/mongoexport -d odstech -c live_jtspas --csv -f 'id','title','deeplink','price','description','condition','shipping','shipping_weight','gtin','brand','mpn','image_link','category','quantity','availability','expiration_date','webgains_category','shipping_uk','shipping_cost_uk' -o files/hostedfeeds/jtspas/all.csv | mail -s "All JtSpas Exported" tech@odst.co.uk < logs/jtspatype.log

echo 'http://www.odst.co.uk/feeds/jtspas.zip?type=webgains' > logs/jtspatype.log
/usr/local/mongodb/bin/mongoexport -d odstech -c live_jtspas --csv -f 'id','title','deeplink','price','description','condition','gtin','brand','mpn','image_link','quantity','availability','expiration_date','webgains_category','shipping','shipping_weight','shipping_uk','shipping_cost_uk','webgains_deeplink' -o files/hostedfeeds/jtspas/webgains.csv | mail -s "Webgains JtSpas Exported" tech@odst.co.uk < logs/jtspatype.log

echo 'http://www.odst.co.uk/feeds/jtspas.zip?type=froogle' > logs/jtspatype.log
/usr/local/mongodb/bin/mongoexport -d odstech -c live_jtspas --csv -f 'id','title','link','price','description','condition','shipping','shipping_weight','gtin','brand','mpn','image_link','category','quantity','availability','expiration_date','google_deeplink' -o files/hostedfeeds/jtspas/froogle.csv | mail -s "Froogle JtSpas Exported" tech@odst.co.uk < logs/jtspatype.log

echo 'http://www.odst.co.uk/feeds/jtspas.zip?type=nextag' > logs/jtspatype.log
/usr/local/mongodb/bin/mongoexport -d odstech -c live_jtspas --csv -f 'title','mpn','id','description','price','deeplink','image_link','nextag_category','category','availability','shipping','condition','shipping','shipping_weight','shipping_uk','shipping_cost_uk','gtin','brand','nextag_deeplink' -o files/hostedfeeds/jtspas/nextag.csv | mail -s "Nextag JtSpas Exported" tech@odst.co.uk < logs/jtspatype.log


#./zipFiles.sh

