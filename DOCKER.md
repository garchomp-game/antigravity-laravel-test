# OpsHub - 開発環境セットアップ

## Docker環境での実行（推奨）

### 1. 初回セットアップ

```bash
# .env.docker を .env としてコピー
cp .env.docker .env

# アプリケーションキーを生成
php artisan key:generate

# コンテナをビルド・起動
docker compose up -d --build

# マイグレーション実行
docker compose exec app php artisan migrate
```

### 2. アクセス

- **Web**: http://localhost:8080
- **PostgreSQL**: localhost:5432
- **Redis**: localhost:6379

### 3. よく使うコマンド

```bash
# コンテナ起動
docker compose up -d

# コンテナ停止
docker compose down

# ログ確認
docker compose logs -f app

# artisanコマンド実行
docker compose exec app php artisan [command]

# tinkerを起動
docker compose exec app php artisan tinker

# キューワーカーのログ確認
docker compose logs -f queue

# コンテナに入る
docker compose exec app sh
```

### 4. データベース操作

```bash
# マイグレーション
docker compose exec app php artisan migrate

# シーダー実行
docker compose exec app php artisan db:seed

# リセット（全削除＋マイグレーション）
docker compose exec app php artisan migrate:fresh --seed
```

---

## ローカル開発（Docker なし）

SQLite を使った簡易開発環境。

```bash
# .env.example を .env としてコピー
cp .env.example .env

# アプリケーションキーを生成
php artisan key:generate

# SQLiteファイル作成
touch database/database.sqlite

# マイグレーション
php artisan migrate

# 開発サーバー起動
php artisan serve
```

アクセス: http://localhost:8000

---

## サービス構成（Docker）

| サービス  | 役割                    | ポート |
|-----------|-------------------------|--------|
| web       | Nginx（リバースプロキシ）| 8080   |
| app       | PHP-FPM（Laravel）      | 9000   |
| queue     | キューワーカー          | -      |
| scheduler | スケジューラー          | -      |
| db        | PostgreSQL              | 5432   |
| redis     | Redis                   | 6379   |

---

## 注意事項

- Docker環境では `APP_URL=http://localhost:8080` を使用
- ローカル開発では `APP_URL=http://localhost:8000` を使用
- 本番環境では Redis / PostgreSQL を外部マネージドサービスに接続
