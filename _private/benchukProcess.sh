# import packages
cd /var/www/odst-live/_private

# export regions
#db.packageschecksum.distinct('region')

php scripts/benchuk.php 

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