# 登録用端末

## Development

```
$ git clone

$ cd register/dev
$ vagrant up
```

When the virtual machine is up successfully, access to http://localhost:8080.

## SSHing to web server.

```
local$ cd dev
local$ vagrant ssh
tuhprr$ docker exec -it tuhprr-php /bin/bash
```


### 初回設置
```
cd /var/www/patient-recept-register
php composer.phar install --prefer-dist
```

以下のファイルを環境に合わせて修正
```
public/.htaccess
fuel/app/config/my.php
```

### mount初回起動
```
```
※ cron設定ですでにmonitorがスタートしている場合には、不要です。

cron設定
```
*/1 * * * * FUEL_ENV=production /usr/bin/php /var/www/patient-recept-register/oil r monitor
```
