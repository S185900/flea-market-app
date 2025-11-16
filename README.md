# coachtechフリマ

## 環境構築
**Dockerビルド**
1. GitHubからプロジェクトをクローン
    `git@github.com:S185900/flea-market-app.git`
2. プロジェクトディレクトリに移動
    `cd flea-market-app`
3. (初回のみ)MySQL用の空ディレクトリを作成
    `mkdir -p docker/mysql/data`
4. DockerDesktopアプリを立ち上げる
5. Dockerコンテナをビルド＆起動
    `docker-compose up -d --build`

> *MacのM1・M2チップのPCの場合、`no matching manifest for linux/arm64/v8 in the manifest list entries`のメッセージが表示されビルドができないことがあります。
エラーが発生する場合は、docker-compose.ymlファイルの「mysql」内に「platform」の項目を追加で記載してください*
``` bash
mysql:
    platform: linux/x86_64(この文追加)
    image: mysql:8.0.26
    environment:
```

**Laravel環境構築**
1. `docker-compose exec php bash`
2. `composer install`
3. 「.env.example」ファイルを 「.env」ファイルに命名を変更。または、新しく.envファイルを作成
4. .envに以下の環境変数を追加
``` text
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```
5. アプリケーションキーの作成
``` bash
php artisan key:generate
```

6. マイグレーションの実行
``` bash
php artisan migrate
```

7. シーディングの実行
``` bash


php artisan migrate　マイグレーションする
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=BrandSeeder
php artisan db:seed --class=CustomProductSeeder


```
8. シンボリックリンク作成
``` bash
php artisan storage:link
```

## 使用技術(実行環境)
- PHP 8.1.33
- Laravel 8.83.8
- MySQL　8.0.26

## ER図
![ER図](./er.drawio.png)


## URL
- 開発環境：http://localhost/
- phpMyAdmin:：http://localhost:8080/

