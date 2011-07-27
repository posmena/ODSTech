# import packages
cd /var/www/odst-live/_private

# export regions
#db.packageschecksum.distinct('region')

php scripts/processFeed.php 2 | mail -s 'Forthill Processed' tech@odst.co.uk


echo 'http://www.odst.co.uk/feeds/forthill.zip?type=all' > logs/forthill.log
mongoexport -d odstech -c forthillhome --csv -f 'productcode','productid','productname','warehousecustom','productdescription','availability','freeshippingitem','google_product_type','productcondition','productmanufacturer','productfeatures','deeplink','imagethumbnail','imagelarge' -o files/hostedfeeds/forthill/all.csv | mail -s "All Forthill Exported" tech@odst.co.uk < logs/forthill.log

echo 'http://www.odst.co.uk/feeds/forthill.zip?type=webgains' > logs/forthill.log
mongoexport -d odstech -c forthillhome --csv -f 'productcode','productid','productname','warehousecustom','productdescription','availability','freeshippingitem','google_product_type','productcondition','productmanufacturer','productfeatures','deeplink','imagethumbnail','imagelarge' -o files/hostedfeeds/forthill/webgains.csv | mail -s "Webgains Forthill Exported" tech@odst.co.uk < logs/forthill.log

echo 'http://www.odst.co.uk/feeds/forthill.zip?type=froogle' > logs/forthill.log
mongoexport -d odstech -c forthillhome --csv -f 'productcode','productid','productname','warehousecustom','productdescription','availability','freeshippingitem','google_product_type','productcondition','productmanufacturer','productfeatures','deeplink','imagethumbnail','imagelarge' -o files/hostedfeeds/forthill/froogle.csv | mail -s "Froogle Forthill Exported" tech@odst.co.uk < logs/forthill.log


echo 'http://www.odst.co.uk/feeds/forthill.zip?type=nextag' > logs/forthill.log
mongoexport -d odstech -c forthillhome --csv -f 'productcode','productid','productname','warehousecustom','productdescription','availability','freeshippingitem','google_product_type','productcondition','productmanufacturer','productfeatures','deeplink','imagethumbnail','imagelarge' -o files/hostedfeeds/forthill/nextag.csv | mail -s "Nextag Forthill Exported" tech@odst.co.uk < logs/forthill.log

./zipFiles.sh