home='/server/www/satelites'
php $home/bin/updateWhois.php > /dev/null
rm $home/tmp/cache/*.index.tpl.php
php $home/public/index.php > /dev/null

