# pull latest style-guide from github
cd /var/www/freecomett-reg/css
git pull
if [ ! -d ../public/css ];then
	mkdir ../public/css
fi

# generate CSS from SASS
sass reg/main.scss > /tmp/reg.scss
if [ $? -eq 0 ];then
	mv /tmp/reg.scss ../public/css/fc-reg.css
fi

# link images to sub-dir of web root
cd /var/www/freecomett-reg/public
if [ ! -L images ];then
	ln -s ../css/images .
fi
