# 09 テスト戦略

## 1. 方針
- MVP段階でも「壊れやすいところ」からテスト
  - 状態遷移（TicketStatus）
  - RBAC（権限）
  - テナント境界（tenant_id漏れ防止）
- CIで `migrate + test` を必須

## 2. 種類
- Unit: 状態遷移の妥当性、サービス層
- Feature: HTTP/Livewireの挙動
- Integration: DB/Redis含む
- E2E（任意）: Playwright等

## 3. 推奨ツール
- Pest or PHPUnit（チーム好みで）
- Laravelのテストヘルパ
- DBはRefreshDatabase（テストDB）

## 4. 重要テストケース例
- テナントAのユーザがテナントBのチケットを参照できない
- Agentは ticket.transition できるが viewer はできない
- 状態遷移が不正な場合は例外 + 監査ログは残さない
