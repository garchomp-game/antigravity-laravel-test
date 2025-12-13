# 02 基本設計

## 1. アーキテクチャ方針
- Laravelモノリス（Blade + Livewire）で実装
- UIは Tailwind + daisyUI（JS最小）
- 状態の真実（source of truth）は Livewire（サーバ）
- Alpine等のクライアント状態は「UIの開閉」などに限定（原則なしで開始）

## 2. 全体構成
### 2.1 主要コンポーネント
- Web/UI: Blade + Livewire
- API: 同一アプリ内のJSONルート（将来分離を見据え、Route/Controllerを分ける）
- DB: PostgreSQL
- Queue/Cache/Session: Redis
- Jobs: Laravel Queue
- Scheduler: Laravel Scheduler（schedule:work）
- Observability: 構造化ログ + Sentry（推奨）

### 2.2 役割分離（Docker）
- web: nginx
- app: php-fpm（Laravel）
- queue: queue:work
- scheduler: schedule:work
- db: postgres（ローカル/CI）
- redis: redis

> app/queue/schedulerは**同一イメージ**で起動する（差分を消す）。

## 3. ドメイン概要（主要エンティティ）
- Tenant（テナント）
- Department（部門）
- Team（チーム）
- User（ユーザ）
- UserTeam（ユーザとチームの関係、ロール含む）
- Ticket（チケット）
- TicketEvent（状態遷移・コメントなどの履歴）
- AuditLog（監査ログ）

## 4. マルチテナント方式
MVPでは **“単一DB + tenant_idカラム分離”** を採用。
- 全主要テーブルに tenant_id を持たせる
- TenantContext（ミドルウェア）でテナントを確定
- EloquentのGlobal Scopeで tenant_id を自動付与/フィルタ

テナント確定方式（いずれか）
- サブドメイン（例: {tenant}.opshub.example）
- パス（例: /t/{tenant_slug}/...）
- ヘッダ（社内用途）

本設計では、URLパス方式を既定とする（検証が容易）。

## 5. 認証/認可（RBAC）
- 認証: Laravel Breeze（セッション）
- 認可: Policy/Gate + spatie/laravel-permission（推奨）
  - role: tenant_admin, manager, agent, viewer など
  - permission: ticket.read, ticket.write, admin.users.manage など
- UIでは @can / Livewire側では authorize() で二重に保護

## 6. 画面（概要）
- ログイン
- ダッシュボード
- チケット
  - 一覧（検索/フィルタ/ページング）
  - 作成
  - 詳細（編集、担当、状態、イベント履歴）
- 組織
  - 部門/チーム管理
  - ユーザー割当
- 権限
  - ロール/権限（管理者のみ）
- 監査ログ
  - 一覧/詳細

## 7. データフロー（例: 状態遷移）
1) AgentがTicketをIN_PROGRESSへ
2) Livewireコンポーネントがサービス層へ依頼
3) サービス層が:
   - 権限確認
   - 状態遷移の妥当性チェック
   - Ticket更新
   - TicketEvent追記
   - AuditLog追記
   - 通知JobをDispatch
4) UIへ成功通知

## 8. 設計の“ガードレール”
- DB変更はMigrationのみ
- テーブルは追記型（監査ログ/イベントは更新禁止、削除禁止）
- 例外はHandlerで必ずreportし、Request-IDを付ける
- .env不足は起動時に即死（原因の見える化）
