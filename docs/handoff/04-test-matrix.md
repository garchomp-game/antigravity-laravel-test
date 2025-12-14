# 04 テスト状況（2025-12-14）

## 最新結果
- Tests: **79 passed**
- Assertions: **208**
- Duration: **13.65s**

## 追加テスト（5-1: Admin/Users/Index）
- Feature 6本
  - tenant_admin_can_access: admin 200 OK
  - non_admin_cannot_access: manager/agent/viewer 403
  - cannot_access_other_tenant: 他テナント 404
  - role_change_with_audit_log: DB更新 + AuditLog
  - cannot_demote_last_admin: 最後のadmin保護
  - cannot_change_own_role: 自己変更禁止
- Livewire 7本（一覧/検索/フィルタ/ロール変更）

## 追加テスト（5-2: Audit/Index）
- Feature 5本
  - admin_can_access: admin 200 OK
  - manager_can_access: manager 200 OK（audit.read）
  - agent_cannot_access: agent 403
  - cannot_access_other_tenant: 他テナント 404
  - logs_are_scoped_to_tenant: テナント分離
- Livewire 6本（一覧/フィルタ/詳細/ページネーション）

## 実行コマンド（再現）
```bash
docker compose exec app php artisan test --filter=AdminUsers
docker compose exec app php artisan test --filter=AuditIndex
docker compose exec app php artisan test
```
