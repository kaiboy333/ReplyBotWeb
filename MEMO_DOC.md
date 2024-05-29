# 躓いたところ
## checkconfigではない
最近のLinuxではcheckconfigではなく、systemctlをしようするらしい。

```
# 再起動時にApache自動起動
sudo systemctl enable httpd.service

# 起動
sudo systemctl start httpd

# 停止
sudo systemctl stop httpd

# 再起動
sudo systemctl restart httpd

# 起動状態をみる
systemctl list-units -t service

# 自動起動が設定されているか見る
systemctl list-unit-files -t service

# 特定のサービスが自動起動が設定されているか見る
systemctl is-enabled <サービス名>

# サービスの依存関係を表示
systemctl list-dependencies <サービス名>
```

## 500 response
これはサーバーエラーである。Apacheの設定を間違えたのかもしれない。

エラーメッセージないのかとおもったが、/etc/httpd/conf/httpd.confでAPP_DEBUG=falseにしていたからだった。trueにしたらエラーの詳細が表示された。

## SQLSTATE[HY000]: General error: 8 attempt to write a readonly database
データベースファイルが読み取り専用になっているらしい。
グループに書き込み権限を付与してあげたらできた。
```
sudo chmod g+rw /var/www/<プロジェクト名>/database/database.sqlite
```

## Let's Encryptoがうまくいかない
```
$ sudo certbot certonly --standalone
Saving debug log to /var/log/letsencrypt/letsencrypt.log
Plugins selected: Authenticator standalone, Installer None
Please enter in your domain name(s) (comma and/or space separated)  (Enter 'c'
to cancel): skyboy333.freedynamicdns.net
Requesting a certificate for skyboy333.freedynamicdns.net
Performing the following challenges:
http-01 challenge for skyboy333.freedynamicdns.net
Cleaning up challenges
Problem binding to port 80: Could not bind to IPv4 or IPv6.
```
うーん、うまくいかない。
他に80番ポートをしようしているかららしい。

そういえば80はApacheを使用していた。
`--standalone`はcertbotが独自のスタンドアロンHTTPサーバーを起動し、そのサーバーを使用してACME認証を実行するモードを指定するオプションとのこと。
Apache使用しているので何かおかしい。

以前、cerbot-autoではドキュメントルートを指定していたのでそれを入れてみた。standaloneはいらないので外す。
```
sudo certbot certonly --webroot -w /var/www/ReplyBotWeb/public
```

```
IMPORTANT NOTES:
 - Congratulations! Your certificate and chain have been saved at:
   /etc/letsencrypt/live/skyboy333.freedynamicdns.net/fullchain.pem
   Your key file has been saved at:
   /etc/letsencrypt/live/skyboy333.freedynamicdns.net/privkey.pem
   Your certificate will expire on 2024-08-01. To obtain a new or
   tweaked version of this certificate in the future, simply run
   certbot again. To non-interactively renew *all* of your
   certificates, run "certbot renew"
 - If you like Certbot, please consider supporting our work by:

   Donating to ISRG / Let's Encrypt:   https://letsencrypt.org/donate
   Donating to EFF:                    https://eff.org/donate-le
```
うまくいったみたい。鍵は、

- /etc/letsencrypt/live/skyboy333.freedynamicdns.net/fullchain.pem
- /etc/letsencrypt/live/skyboy333.freedynamicdns.net/privkey.pem

に生成されているみたい。
メールもきて認証する必要があったので認証した。

Apache < 2.4.8だったので、fullchainは使用しない。

cert.pemとchain.pem、privkey.pemを使用するらしい。

参考: https://eff-certbot.readthedocs.io/en/latest/using.html#where-are-my-certificates

## 接続ができない
SSHやHTTP、HTTPS接続をするときにtimeoutしてしまう。
その時は大体ポート開放していないせい。
今回はEC2を使用しているのでセキュリティグループのインバウンドルールで設定する。

# 便利だと思ったこと
## Laravelでログ出力
`/var/www/ReplyBotWeb/storage/logs/laravel.log`でログを確認できる。

ログは、
```
Log::info($request);
```
のように書く。

## phpの簡単テスト
軽くphpをしようしたいだけなら、
```
php artisan tinker
```
を使用するとphp構文が打てて、変数を入れると自動で中身が確認できる。

`<モデル名>>::all();`
とか使って

## db関連コマンド
### migareファイルからテーブルを作成する
```
php artisan migrate
```
### migrateをリセット(テーブルも消える)
```
php artisan migrate:reset
```
### リセットしてテーブル作成
```
php artisan migrate:refresh
```

## config関連コマンド
### キャッシュファイルクリア
```
php artisan config:clear
```

## ルーティング確認
どのルーティングがなんのメソッドを呼ぶのかが確認できる。
表示されなかったらうまくいっていない。
```
php artisan route:list
```

## 作成コマンド
### コントローラー
```
php artisan make:controller <コントローラー名>
```
### モデル
```
php artisan make:model <モデル名>
```