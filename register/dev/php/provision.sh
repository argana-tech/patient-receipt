#!/bin/sh

cd /vagrant/dev/php


for cname in `docker ps --filter="name=tuhprr-php" --format "{{.Names}}" -q -a`
do
    if [ "$cname" = tuhprr-php ]
    then
        docker stop $cname
        docker rm $cname
    fi
done

docker build -t tuhprr/php .

docker run \
       -d \
       --restart=always \
       -v /etc/localtime:/etc/localtime:ro \
       --name tuhprr-php \
       --hostname tuhprr-php \
       -p 80:80 \
       -v /vagrant:/vagrant \
       --link tuhprr-mysql:tuhprr-mysql \
       -e DESKTOP_NOTIFIER_SERVER_URL=http://192.168.88.1:12345 \
       tuhprr/php

docker cp \
       /vagrant/dev/php/desktop-notifier-client \
       tuhprr-php:/usr/bin/notify-send

docker exec tuhprr-php /vagrant/dev/php/init-env.sh
