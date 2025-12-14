# 03 Decision Log（設計判断のログ）

## テナント境界のHTTPステータス
- 他テナントへのアクセスは **404（存在隠し）**
- 権限不足（ロール不足）は **403**
- 例外があれば「既存挙動と合わせる」を優先

## RBAC
- Spatie Laravel Permission を採用
- ルートで `can:` ミドルウェアを使用（例: `can:admin.users.manage`, `can:audit.read`）

## 安全策
- 最後のadminを降格させない
- 自分自身のロール変更は禁止

## 監査ログ
- 重要操作は AuditService 経由で AuditLog に記録
- Admin/Users のロール変更、Audit閲覧なども対象に（必要に応じて拡張）

## テスト方針
- UIの断定は避ける（HTML/CSSの細部に依存しない）
- UI操作は `data-testid` に寄せる
- 変更はTDD（Red→Green→Refactor）を厳守
