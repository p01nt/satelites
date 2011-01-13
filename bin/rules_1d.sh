home='/server/www/satelites'
php $home/bin/updateWhois.php > /dev/null
php $home/bin/clearCache.php
php $home/public/index.php > /dev/null

