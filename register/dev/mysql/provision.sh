#!/bin/sh

cd /vagrant/dev/mysql

data=false

for cname in `docker ps --filter="name=tuhprr-mysql" --format "{{.Names}}" -q -a`
do
    if [ "$cname" = tuhprr-mysql ]
    then
        docker stop $cname
        docker rm $cname
    fi

    if [ "$cname" = tuhprr-mysql-data ]
    then
        data=true
    fi
done

if [ "$data" = false ]
then
    docker run --name tuhprr-mysql-data -v /var/lib/mysql busybox
fi

docker build -t tuhprr/mysql .

docker run \
       -d \
       --restart=always \
       -v /etc/localtime:/etc/localtime:ro \
       --name tuhprr-mysql \
       --hostname tuhprr-mysql \
       -p 3306:3306 \
       --volumes-from tuhprr-mysql-data \
       -e MYSQL_DATABASE=tuhprr \
       -e MYSQL_USER=tuhprr \
       -e MYSQL_PASSWORD=tuhprr \
       -e MYSQL_ALLOW_EMPTY_PASSWORD=yes \
       tuhprr/mysql \
       --character-set-server=utf8 \
       --collation-server=utf8_unicode_ci
