# import packages
cd /var/www/odst-live/_private

# export regions
#db.packageschecksum.distinct('region')

php scripts/benchuk.php uk

echo 'http://www.odst.co.uk/feeds/benchuk.zip?type=all' > logs/benchuk.log
mongoexport -d odstech -c dump_bench --csv -f 'id','title','link','price','color','sizes','description','condition','shipping_cost_uk','brand','image_link','additional_image_link','category' -o files/hostedfeeds/benchuk/all.csv

sed 's/\\"/""/g' files/hostedfeeds/benchuk/all.csv > files/hostedfeeds/benchuk/all2.csv
mv files/hostedfeeds/benchuk/all2.csv files/hostedfeeds/benchuk/all.csv

echo 'http://www.odst.co.uk/feeds/benchuk.zip?type=froogle' > logs/jtspatype.log
mongoexport -d odstech -c dump_google_bench --csv -f 'id','item_group_id','title','link','price','sale_price','color','size','gender','description','condition','shipping_cost_uk','brand','image_link','additional_image_link','category' -o files/hostedfeeds/benchuk/froogle.csv

sed 's/\\"/""/g' files/hostedfeeds/benchuk/froogle.csv > files/hostedfeeds/benchuk/froogle2.csv
mv files/hostedfeeds/benchuk/froogle2.csv files/hostedfeeds/benchuk/froogle.csv

./zipFiles.sh

php scripts/xmlGenerator.php benchuk froogle



php scripts/benchuk.php de

echo 'http://www.odst.co.uk/feeds/benchde.zip?type=all' > logs/benchde.log
mongoexport -d odstech -c dump_bench --csv -f 'id','title','link','price','color','sizes','description','condition','brand','image_link','additional_image_link','category' -o files/hostedfeeds/benchde/all.csv

sed 's/\\"/""/g' files/hostedfeeds/benchde/all.csv > files/hostedfeeds/benchde/all2.csv
mv files/hostedfeeds/benchde/all2.csv files/hostedfeeds/benchde/all.csv

php scripts/addbom.php files/hostedfeeds/benchde/all.csv

echo 'http://www.odst.co.de/feeds/benchde.zip?type=froogle' > logs/jtspatype.log
mongoexport -d odstech -c dump_google_bench --csv -f 'id','item_group_id','title','link','price','sale_price','shipping','color','size','gender','description','condition','brand','image_link','additional_image_link','category' -o files/hostedfeeds/benchde/froogle.csv

sed 's/\\"/""/g' files/hostedfeeds/benchde/froogle.csv > files/hostedfeeds/benchde/froogle2.csv
mv files/hostedfeeds/benchde/froogle2.csv files/hostedfeeds/benchde/froogle.csv

php scripts/addbom.php files/hostedfeeds/benchde/froogle.csv

./zipFiles.sh

php scripts/xmlGenerator.php benchde froogle