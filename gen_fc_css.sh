cd /var/www/freecomett-reg/css
git pull
if [ ! -d ../public/css ];then
	mkdir ../public/css
fi
sass core.scss > ../public/css/fc-reg.css
