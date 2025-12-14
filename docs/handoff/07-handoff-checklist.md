# 07 引き継ぎチェックリスト（新セッションへ移る前に）

## A. Gitのチェックポイント（必須）
- `git status -sb` がクリーンか確認
- 変更があるならコミット（例: `chore: checkpoint after phase 5-2`）
- `git log -1 --oneline` を控える（新セッションへ貼る）

## B. 環境情報（貼ると強い）
- `docker compose ps`
- `docker compose exec app php -v`
- `docker compose exec app php artisan --version`
- `docker compose exec app composer --version`
- `docker compose exec app php artisan test` の結果

## C. 秘密情報（重要）
- **.env は貼らない/アップロードしない（原則）**
- `.env.example` や「必要キー一覧」だけを共有
- DBパスワード等が含まれる場合は必ずマスク

## D. 新セッションに渡すファイル（おすすめ）
- `docs/handoff/*`（このパック）
- `docs/testing/*`（既にある場合はそのまま）
- `docker-compose.yml`
- `composer.json` / `composer.lock`
- `package.json` / `package-lock.json`（Duskやフロント関連がある場合）
- `routes/web.php`
- Admin/Audit/Tenant関連の Livewire / Blade

## E. “重い” を防ぐコツ
- 1セッション1タスク（Phase 5-3だけ、など）
- 変更量が大きい時は「サブタスク3つ」に切って投げる
- CI/テスト結果を毎回貼る（AIが迷走しにくい）
