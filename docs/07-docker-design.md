# 07 Docker設計（本番寄せ）

## 1. 目的
- ローカル/CI/本番の差分を最小化し、再現性を高める
- app/queue/scheduler を同一イメージで動かし、環境差を排除

## 2. サービス設計
- web: nginx（静的配信 + php-fpmへproxy）
- app: php-fpm（Laravel）
- queue: `php artisan queue:work`
- scheduler: `php artisan schedule:work`
- db: postgres（ローカル/CI）
- redis: redis

## 3. 環境変数
- ローカル: `.env`
- 本番: ECS Task Definition / SSM / Secrets Manager 等へ移行可能に
- 必須ENVは起動時に検証し、不足時は即終了（無限ループ回避）

## 4. ログ
- stdout/stderr に統一
- 可能ならJSONログ（Monolog formatter）
- Request-ID を常時付与

## 5. CI
- DB/Redisをcomposeで起動し、`migrate` を必ず通す
- lockfile（composer.lock / package-lock）により依存固定

## 6. 本番移行方針
- db/redis はマネージドに差し替え
- composeで `db` を無効化し、外部接続へ
- アプリは同一イメージをweb/app/queue/schedulerとして起動
