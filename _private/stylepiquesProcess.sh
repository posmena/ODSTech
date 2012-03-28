# import packages
cd /var/www/odst-live/_private

# export regions
#db.packageschecksum.distinct('region')

php scripts/scraper.php stylepiques 1 1

echo 'http://www.odst.co.uk/feeds/stylepiques.zip?type=webgains' > logs/stylepiques.log
mongoexport -d odstech -c live_stylepiques --csv -f 'id','title','link','price','category','image_link','image_link2','image_link3','sizes','brand','availability','delivery_time','delivery_cost','description' -o files/hostedfeeds/stylepiques/webgains.csv | mail -s "Stylepiques Webgains Exported" tech@odst.co.uk < logs/stylepiques.log

./zipFiles.sh