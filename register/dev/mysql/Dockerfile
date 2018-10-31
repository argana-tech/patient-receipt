FROM mysql:5.6

RUN { \
    echo '[client]'; \
    echo 'default-character-set=utf8'; \
} > /etc/mysql/conf.d/charset.cnf

COPY create_test_database.sql /docker-entrypoint-initdb.d/
