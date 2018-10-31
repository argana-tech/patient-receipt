初回設置
```
cd /var/www/patient-recept/tuh_patient_recept
php composer.phar install --prefer-dist
```

以下のファイルを環境に合わせて修正
```
public/.htaccess
fuel/app/config/my.php (fuel/app/config/my.php.sample をコピーして使用します。)
```

マイグレーション実行
```
cd /var/www/patient-recept/tuh_patient_recept
FUEL_ENV=production php oil r migrate
```

通知タスク
```sh
# 共有ディレクトリチェック、通知
FUEL_ENV=production php oil r notify

# 再通知
FUEL_ENV=production php oil r notify:resend_notify
```

初回起動
```
FUEL_ENV=production /usr/bin/php /var/www/patient-recept/tuh_patient_recept/oil r exec_notify
```
※ cron設定でmonitorがスタートしている場合には、不要です。

cron設定
```
*/1 * * * * FUEL_ENV=production /usr/bin/php /var/www/patient-recept/tuh_patient_recept/oil r notify:resend_notify
*/1 * * * * FUEL_ENV=production /usr/bin/php /var/www/patient-recept/tuh_patient_recept/oil r monitor
```

コンフィグファイルやコード 変更した場合は Taskのdaemeonを再起動します （cronに登録があれば kill のみ）
```
$ ps aux | grep php
$ kill xxxxxx # 該当のTaskを止めます
```

患者受付のタスクを登録
```
*/1 * * * * FUEL_ENV=production /usr/bin/php /var/www/patient-recept/tuh_patient_recept/oil r exec_linked
```
