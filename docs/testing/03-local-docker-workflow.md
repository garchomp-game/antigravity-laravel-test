# 03 ローカル/Docker 実行フロー（再現性重視）

## 1. 原則
- ローカル開発環境とCIは「同じ実行コマンド」で動くようにする
- `make test` や `composer test` など **入口を1つ**にする

## 2. DB戦略（おすすめ）
### A) SQLite（高速・Unit/Feature向け）
- 利点: 速い、セットアップが軽い
- 欠点: PostgreSQL固有の挙動（jsonb, enum, lock）との差

### B) PostgreSQL（本番寄せ・整合性向け）
- 利点: 本番と同じ
- 欠点: 遅い（ただしDockerで十分実用）

**推奨**: Feature/IntegrationでPostgreSQL、UnitはSQLiteでも可。
ただし “マイグレーションとクエリ” が多いなら最初からPostgreSQL一本化でもOK。

## 3. Dockerでのテスト実行（例）
- appコンテナで実行すること（ホスト差異を消す）

例（composeサービス名が app の場合）:
- `docker compose run --rm app php -v`
- `docker compose run --rm app composer install`
- `docker compose run --rm app php artisan test`（PHPUnit）
- `docker compose run --rm app ./vendor/bin/pest`（Pest）

## 4. テスト用ENVの管理
- `.env.testing` を用意（Laravel標準）
- 重要: `APP_ENV=testing`, `APP_DEBUG=true`
- DB設定は testing 専用に（本番/開発DBを誤って触らない）

## 5. マイグレーション
- `RefreshDatabase` を基本にする
- 速度が厳しいなら `DatabaseTransactions` を検討（ただし外部プロセスやQueue絡みは注意）
