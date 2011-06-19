for i in $(ls -a files/hostedfeeds/easyjet | grep '.csv' | grep -v '.zip'); do
   zip -j "files/compressedfeeds/easyjet/$i.zip" "files/hostedfeeds/easyjet/$i"
   rm "files/hostedfeeds/easyjet/$i"
done