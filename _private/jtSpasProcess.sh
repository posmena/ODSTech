# import packages
cd /var/www/odst-live/_private

# export regions
#db.packageschecksum.distinct('region')

php scripts/processFeed.php 3 | mail -s 'JtSpas' tech@odst.co.uk

echo 'http://www.odst.co.uk/feeds/jtspas.zip?type=all' > jtspatype.log
/usr/local/mongodb/bin/mongoexport -d odstech -c jtspas --csv -f 'id','title','deeplink','price','description','condition','shipping','shipping_weight','gtin','brand','mpn','image_link','category','quantity','availability','expiration_date','webgains_category','shipping_uk','shipping_cost_uk' -o files/hostedfeeds/jtspas/jtspasall.csv | mail -s "All JtSpas Exported" tech@odst.co.uk < jtspatype.log

echo 'http://www.odst.co.uk/feeds/jtspas.zip?type=webgains' > jtspatype.log
/usr/local/mongodb/bin/mongoexport -d odstech -c jtspas --csv -f 'id','title','deeplink','price','description','condition','gtin','brand','mpn','image_link','quantity','availability','expiration_date','webgains_category','shipping','shipping_weight','shipping_uk','shipping_cost_uk' -o files/hostedfeeds/jtspas/webgains.csv | mail -s "Webgains JtSpas Exported" tech@odst.co.uk < jtspatype.log

echo 'http://www.odst.co.uk/feeds/jtspas.zip?type=froogle' > jtspatype.log
/usr/local/mongodb/bin/mongoexport -d odstech -c jtspas --csv -f 'id','title','link','price','description','condition','shipping','shipping_weight','gtin','brand','mpn','image_link','category','quantity','availability','expiration_date' -o files/hostedfeeds/jtspas/froogle.csv | mail -s "Froogle JtSpas Exported" tech@odst.co.uk < jtspatype.log


./zipFiles.sh

