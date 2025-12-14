# 02 Runbook（起動・テスト・デバッグ）

## 起動
```bash
docker compose up -d
docker compose ps
```

## マイグレーション（ローカル検証）
```bash
docker compose exec app php artisan migrate:fresh --seed
```

## テスト
### 全テスト
```bash
docker compose exec app php artisan test
```

### Dusk
```bash
docker compose exec app php artisan dusk
```

### 目的別（例）
```bash
docker compose exec app php artisan test --filter=AdminUsers
docker compose exec app php artisan test --filter=AuditIndex
```

## 失敗時の最短ルート（重要）
1) **最初に落ちたテスト1件だけ**を見る（連鎖修正しない）
2) `.env` / `.env.testing` / DB接続 を疑う
3) `php artisan config:clear && php artisan route:clear` を必要に応じて
4) `storage/logs/laravel.log` を確認
