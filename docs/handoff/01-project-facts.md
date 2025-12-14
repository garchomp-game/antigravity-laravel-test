# 01 プロジェクト事実（不変情報の要約）

## スタック
- Backend: Laravel（Breeze）
- UI: Blade + Livewire（data-testid運用）
- DB: PostgreSQL
- Cache/Queue: Redis + Laravel Queue
- Browser Test: Laravel Dusk（Selenium Chrome）
- RBAC: Spatie Laravel Permission

## Dockerサービス
- web: Nginx (8080)
- app: PHP-FPM + Laravel
- db: PostgreSQL
- redis: Redis
- queue: worker
- scheduler: scheduler
- selenium: Dusk用

## ドメイン主要モデル
- Tenant / User / Department / Team / UserTeam
- Ticket / TicketEvent
- AuditLog

## マルチテナント
- URL prefix: `/t/{tenant}/...`
- Global Scope により tenant_id を自動スコープ
- TenantContext（所属チェック）を使う箇所がある（Admin, Audit など）

## アクセス制御（概要）
- Admin Users: `can:admin.users.manage`
- Audit: `can:audit.read`（admin + manager想定、agentは不可）
- テナント越境は 404 で隠す方針
- 権限不足は 403 方針（ただし既存挙動に合わせる）
