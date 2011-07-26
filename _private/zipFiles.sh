for i in $(ls -a files/hostedfeeds/easyjet | grep '.csv' | grep -v '.zip'); do
   zip -j "files/compressedfeeds/easyjet/$i.zip" "files/hostedfeeds/easyjet/$i"
   rm "files/hostedfeeds/easyjet/$i"
done

for i in $(ls -a files/hostedfeeds/jtspas | grep '.csv' | grep -v '.zip'); do
   zip -j "files/compressedfeeds/jtspas/$i.zip" "files/hostedfeeds/jtspas/$i"
   rm "files/hostedfeeds/jtspas/$i"
done