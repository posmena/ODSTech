# import packages
cd /var/www/odst-live/_private

# export regions
#db.packageschecksum.distinct('region')

php scripts/processFeed.php 1 | mail -s 'Packages Updated' tech@odst.co.uk

# export regions
#db.packageschecksum.distinct('region')


for region in 'Algarve' 'Amsterdam' 'Barcelona' 'Corfu' 'Crete' 'Cyprus' 'Egypt' 'Fuerteventura' 'Halkidiki' 'Ibiza' 'Kos' 'Lanzarote' 'Lombardy' 'Madeira' 'Madrid' 'Majorca' 'Malta' 'Menorca' 'Morocco' 'Mykonos' 'Paris' 'Prague' 'Rhodes' 'Rome' 'Santorini' 'Tenerife' 'Turkey' 'Venice' 'Zante'; do
        mongodump --db odstech --collection packageschecksum --query '{"region":"'$region'"}' > logs/region.log
        mongorestore -d odstech -c tempfeed --drop dump/odstech/packageschecksum.bson
        somevar="files/hostedfeeds/easyjet/region"$region".csv"
        echo 'http://www.odst.co.uk/feeds/easyjet.zip?region='$region >> logs/region.log
		mongoexport -d odstech -c tempfeed --csv -f package_id,name,country,region,search_deeplink,resort,duration,board,cost,currency,departure_date,rating,room_type,out_departure_airport_code,out_departure_airport_name,out_flight_departure_date,out_destination_airport_name,out_destination_airport_code,ret_departure_airport_code,ret_departure_airport_name,ret_flight_departure_date,ret_destination_airport_name,ret_destination_airport_code,image1url,image2url,image3url,description -o $somevar | mail -s "Region $region Exported" tech@odst.co.uk < region.log

./zipFiles.sh
done

mongodump --db odstech --collection packageschecksum --query '{"region":"Costa Blanca"}' > logs/region.log
region='Costa_Blanca'
somevar="files/hostedfeeds/easyjet/region"$region".csv"
mongorestore -d odstech -c tempfeed --drop dump/odstech/packageschecksum.bson
echo 'http://www.odst.co.uk/feeds/easyjet.zip?region='$region >> logs/region.log
mongoexport -d odstech -c tempfeed --csv -f package_id,name,country,region,search_deeplink,resort,duration,board,cost,currency,departure_date,rating,room_type,out_departure_airport_code,out_departure_airport_name,out_flight_departure_date,out_destination_airport_name,out_destination_airport_code,ret_departure_airport_code,ret_departure_airport_name,ret_flight_departure_date,ret_destination_airport_name,ret_destination_airport_code,image1url,image2url,image3url,description -o $somevar | mail -s "Region $region Exported" tech@odst.co.uk < logs/region.log

mongodump --db odstech --collection packageschecksum --query '{"region":"Costa Brava"}' > logs/region.log
region='Costa_Brava'
somevar="files/hostedfeeds/easyjet/region"$region".csv"
mongorestore -d odstech -c tempfeed --drop dump/odstech/packageschecksum.bson
echo 'http://www.odst.co.uk/feeds/easyjet.zip?region='$region >> logs/region.log
mongoexport -d odstech -c tempfeed --csv -f package_id,name,country,region,search_deeplink,resort,duration,board,cost,currency,departure_date,rating,room_type,out_departure_airport_code,out_departure_airport_name,out_flight_departure_date,out_destination_airport_name,out_destination_airport_code,ret_departure_airport_code,ret_departure_airport_name,ret_flight_departure_date,ret_destination_airport_name,ret_destination_airport_code,image1url,image2url,image3url,description -o $somevar | mail -s "Region $region Exported" tech@odst.co.uk < logs/region.log

mongodump --db odstech --collection packageschecksum --query '{"region":"Costa Del Sol"}' > logs/region.log
region='Costa_Del_Sol'
somevar="files/hostedfeeds/easyjet/region"$region".csv"
mongorestore -d odstech -c tempfeed --drop dump/odstech/packageschecksum.bson
echo 'http://www.odst.co.uk/feeds/easyjet.zip?region='$region >> logs/region.log
mongoexport -d odstech -c tempfeed --csv -f package_id,name,country,region,search_deeplink,resort,duration,board,cost,currency,departure_date,rating,room_type,out_departure_airport_code,out_departure_airport_name,out_flight_departure_date,out_destination_airport_name,out_destination_airport_code,ret_departure_airport_code,ret_departure_airport_name,ret_flight_departure_date,ret_destination_airport_name,ret_destination_airport_code,image1url,image2url,image3url,description -o $somevar | mail -s "Region $region Exported" tech@odst.co.uk < logs/region.log

mongodump --db odstech --collection easyjet_scrape > logs/region.log
region='Costa_Dorada'
somevar="files/hostedfeeds/easyjet/region"$region".csv"
mongorestore -d odstech -c properties_scrape --drop dump/odstech/properties_scrape.bson
echo 'http://www.odst.co.uk/feeds/easyjet.zip?region='$region >> logs/region.log
mongoexport -d odstech -c tempfeed --csv -f package_id,name,country,region,search_deeplink,resort,duration,board,cost,currency,departure_date,rating,room_type,out_departure_airport_code,out_departure_airport_name,out_flight_departure_date,out_destination_airport_name,out_destination_airport_code,ret_departure_airport_code,ret_departure_airport_name,ret_flight_departure_date,ret_destination_airport_name,ret_destination_airport_code,image1url,image2url,image3url,description -o $somevar | mail -s "Region $region Exported" tech@odst.co.uk < logs/region.log

mongodump --db odstech --collection packageschecksum --query '{"region":"Gran Canaria"}' > logs/region.log
region='Gran_Canaria'
somevar="files/hostedfeeds/easyjet/region"$region".csv"
mongorestore -d odstech -c tempfeed --drop dump/odstech/packageschecksum.bson
echo 'http://www.odst.co.uk/feeds/easyjet.zip?region='$region >> logs/region.log
mongoexport -d odstech -c tempfeed --csv -f package_id,name,country,region,search_deeplink,resort,duration,board,cost,currency,departure_date,rating,room_type,out_departure_airport_code,out_departure_airport_name,out_flight_departure_date,out_destination_airport_name,out_destination_airport_code,ret_departure_airport_code,ret_departure_airport_name,ret_flight_departure_date,ret_destination_airport_name,ret_destination_airport_code,image1url,image2url,image3url,description -o $somevar | mail -s "Region $region Exported" tech@odst.co.uk < logs/region.log

mongodump --db odstech --collection packageschecksum --query '{"region":"Tel Aviv"}' > logs/region.log
region='Tel_Aviv'
somevar="files/hostedfeeds/easyjet/region"$region".csv"
mongorestore -d odstech -c tempfeed --drop dump/odstech/packageschecksum.bson
echo 'http://www.odst.co.uk/feeds/easyjet.zip?region='$region >> logs/region.log
mongoexport -d odstech -c tempfeed --csv -f package_id,name,country,region,search_deeplink,resort,duration,board,cost,currency,departure_date,rating,room_type,out_departure_airport_code,out_departure_airport_name,out_flight_departure_date,out_destination_airport_name,out_destination_airport_code,ret_departure_airport_code,ret_departure_airport_name,ret_flight_departure_date,ret_destination_airport_name,ret_destination_airport_code,image1url,image2url,image3url,description -o $somevar | mail -s "Region $region Exported" tech@odst.co.uk < logs/region.log



#php scripts/scraper.php easyjet 1  1
#php scripts/processProperties.php easyjet
echo 'http://www.odst.co.uk/feeds/easyjet.zip?propertylist=true' > logs/region.log
mongoexport -d odstech -c easyjet_properties --csv -f propertyid,name,resort,resortid,region,country,rating,hoteltype,airportcode,image1url,image2url,image3url,description,address,price,url -o files/hostedfeeds/easyjet/propertiesfull.csv | mail -s 'Easyjet Properties Exported' tech@odst.co.uk < logs/region.log
./zipFiles.sh

