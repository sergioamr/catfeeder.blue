find /home/smartinez/catfeeder/resources/cache/. -mtime +1 -type f -name '*.jpg' -delete  
find /home/smartinez/catfeeder/gallery-images/. -mtime +31 -type f -name '*.jpg' -print -delete
find /home/smartinez/catfeeder/diffs/. -mtime +3 -type f -print -delete
#find /home/smartinez/catfeeder/gallery-images/. -mtime +120 -type f -name '*.txt' -print -delete

