# 04 CI / 品質ゲート（AIが迷わない“合否判定”）

## 1. 目的
- AIが「何を直せば良いか」を即断できるログを残す
- “たまたま通る” を防ぎ、再現性を上げる

## 2. 推奨ジョブ順
1) 依存導入（composer install）
2) 静的解析（任意）
   - Laravel Pint（整形）
   - PHPStan/Psalm（型）
3) マイグレーション（必須）
4) テスト（Unit/Feature）
5) E2E（任意、nightlyでもOK）

## 3. 失敗時に残す情報
- php -v / composer -V
- `php artisan --version`
- migration一覧（`php artisan migrate:status`）

## 4. “品質ゲート” の例
- Lint: Pintが通る（もしくは差分なし）
- Tests: `php artisan test` が通る
- Coverage（任意）: コア層だけ 70% 以上
- Security（任意）: Composer audit

## 5. AI向け運用ルール
- CIが落ちたら「最初の失敗」だけ直す（連鎖修正で迷走しない）
- 直したら必ず “同じコマンド” をローカル（Docker）で再実行
