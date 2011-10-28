for i in $(ls -a files/hostedfeeds/easyjet | grep '.csv' | grep -v '.zip'); do
   zip -j "files/compressedfeeds/easyjet/$i.zip" "files/hostedfeeds/easyjet/$i"
   rm "files/hostedfeeds/easyjet/$i"
done

for i in $(ls -a files/hostedfeeds/jtspas | grep '.csv' | grep -v '.zip'); do
   zip -j "files/compressedfeeds/jtspas/$i.zip" "files/hostedfeeds/jtspas/$i"
   rm "files/hostedfeeds/jtspas/$i"
done

for i in $(ls -a files/hostedfeeds/forthillhome | grep '.csv' | grep -v '.zip'); do
   zip -j "files/compressedfeeds/forthillhome/$i.zip" "files/hostedfeeds/forthillhome/$i"
   rm "files/hostedfeeds/forthillhome/$i"
done

for i in $(ls -a files/hostedfeeds/chesca | grep '.csv' | grep -v '.zip'); do
   zip -j "files/compressedfeeds/chesca/$i.zip" "files/hostedfeeds/chesca/$i"
   rm "files/hostedfeeds/chesca/$i"
done


for i in $(ls -a files/hostedfeeds/damsel | grep '.csv' | grep -v '.zip'); do
   zip -j "files/compressedfeeds/damsel/$i.zip" "files/hostedfeeds/damsel/$i"
   rm "files/hostedfeeds/damsel/$i"
done