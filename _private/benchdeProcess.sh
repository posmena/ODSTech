# import packages
cd /var/www/odst-live/_private

# export regions
#db.packageschecksum.distinct('region')

php scripts/benchuk.php de

echo 'http://www.odst.co.uk/feeds/benchde.zip?type=all' > logs/benchde.log
mongoexport -d odstech -c dump_bench --csv -f 'id','title','link','price','color','sizes','description','condition','brand','image_link','additional_image_link','category' -o files/hostedfeeds/benchde/all.csv

sed 's/\\"/""/g' files/hostedfeeds/benchde/all.csv > files/hostedfeeds/benchde/all2.csv
mv files/hostedfeeds/benchde/all2.csv files/hostedfeeds/benchde/all.csv

php scripts/addbom.php files/hostedfeeds/benchde/all.csv

echo 'http://www.odst.co.de/feeds/benchde.zip?type=froogle' > logs/jtspatype.log
mongoexport -d odstech -c dump_google_bench --csv -f 'id','item_group_id','title','link','price','sale_price','shipping','color','size','gender','description','mpn','availability','google_product_category','condition','brand','image_link','additional_image_link','category' -o files/hostedfeeds/benchde/froogle.csv

sed 's/\\"/""/g' files/hostedfeeds/benchde/froogle.csv > files/hostedfeeds/benchde/froogle2.csv
mv files/hostedfeeds/benchde/froogle2.csv files/hostedfeeds/benchde/froogle.csv

php scripts/addbom.php files/hostedfeeds/benchde/froogle.csv

./zipFiles.sh

php scripts/xmlGenerator.php benchde froogle