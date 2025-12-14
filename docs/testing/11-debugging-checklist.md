# 11 失敗時チェックリスト（AI向け）

## まず確認
- 最初に落ちているテストはどれか
- `.env.testing` が読み込まれているか
- DBが testing 用か（誤接続していないか）
- migrationが最新か（migrate:status）
- cacheが悪さしてないか（config/route/clear）

## Livewireで落ちる
- mount引数、認可、assertSeeの過剰固定を疑う

## 権限/テナントで落ちる
- role/permission付与、tenant_id一致、404/403方針のズレ

## 分からない時
- `php artisan test --filter=... -v`
- `storage/logs` を確認
- 例外スタックの“最初の原因”だけを見る
