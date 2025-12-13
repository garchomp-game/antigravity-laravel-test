# 03 詳細設計

## 1. コード構成（推奨）
Laravel標準を崩しすぎず、業務ロジックは Service/Action に寄せる。

- app/
  - Actions/
  - Services/
  - Policies/
  - Http/
    - Middleware/TenantContext.php
  - Livewire/
    - Dashboard/
    - Tickets/
    - Admin/

**MVPでは「Services + Policies + Livewire」で十分**。

## 2. テナント境界
### 2.1 TenantContext ミドルウェア
- URL: /t/{tenant}/... から tenant を解決
- request()->attributes に tenant を保持
- グローバルスコープが参照できる形で Tenant::current() を提供

### 2.2 Eloquent Global Scope
- 作成時: tenant_idを自動付与
- 取得時: tenant_idで自動フィルタ
- 管理者用にスコープ解除（例: withoutTenantScope）を用意

## 3. RBAC
- ルート単位: middleware('can:ticket.read')
- Livewire: mount()/actionメソッド先頭で $this->authorize(...)
- 重要操作はAuditLogへ

## 4. Livewireコンポーネント設計
### 4.1 命名例
- Tickets/Index（一覧: filter + paging）
- Tickets/Create（作成フォーム）
- Tickets/Show（詳細: 変更・アサイン・状態遷移・イベント）
- Dashboard/Overview（集計表示）
- Admin/Users/Index（ユーザ一覧）
- Admin/Roles/Edit（ロール編集）
- Audit/Index（監査ログ）

### 4.2 状態とイベント
- コンポーネントのpublicプロパティ = UI状態
- DBの真実 = サービス層で更新
- 成功/失敗は browserEvent or session flash で通知
- 画面内モーダルはLivewireで表示制御（JS無しでOK）

## 5. サービス層（例）
- TicketService
  - createTicket(dto)
  - assignTicket(ticketId, userId)
  - transitionStatus(ticketId, newStatus, comment?)
  - addComment(ticketId, body)
- AdminService
  - assignRole(userId, role)
- AuditService
  - record(action, entity, entityId, meta)

## 6. ジョブ/通知
### 6.1 Job
- NotifyTicketCreated
- NotifyTicketAssigned
- NotifyTicketStatusChanged

### 6.2 通知チャネル
- MVP: DB通知（notificationsテーブル） + メール（任意）
- 将来: Slack/Teams

## 7. 監査ログ設計
- AuditLogは追記型（Update/Delete禁止）
- payload(meta)はJSONで、リクエストID・入力要約・差分などを格納
- 出力の最重要: who/when/what/where(request-id)

## 8. 例外設計
- Handlerで全例外をreport
- Request-IDをLog::withContextで常時付与
- ENV不足・接続不可は明確なRuntimeExceptionで即死

## 9. パフォーマンス
- Ticket一覧: index(tenant_id, status, assigned_to, created_at)など
- 監査ログ: index(tenant_id, created_at) + action
- N+1回避: eager load徹底
- 集計: キャッシュ or nightly job（拡張）

## 10. 設定（推奨）
- config/opshub.php: 業務ドメインの定数（状態/タイプ等）
- config/logging.php: json formatter
