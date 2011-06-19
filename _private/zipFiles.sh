#for i in files/hostedfeeds/easyjet/*; do 
#  zip -r "compressed/$i.zip" "$i"; 
#done

for i in $(ls -a files/hostedfeeds/easyjet | grep '.csv' | grep -v '.zip'); do
   zip -r "files/compressedfeeds/easyjet/$i.zip" "files/hostedfeeds/easyjet/$i"
   rm "files/hostedfeeds/easyjet/$i"
done
