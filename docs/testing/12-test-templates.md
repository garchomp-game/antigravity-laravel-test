# 12 テストテンプレ集（AIが増やす用）

## Feature: テナント隔離
- Given: TenantAのユーザー、TenantBのチケット
- When: TenantAのユーザーがTenantBのチケットにアクセス
- Then: 404（または403）

## Feature: RBAC
- Given: Viewer
- When: 状態変更
- Then: 403/404、DBは変わらない

## Unit: 状態遷移
- datasetsで遷移可否を列挙し can() を検証

## Livewire: 状態遷移
- call → errorsなし → DB更新

## E2E: 最小フロー
- login → create → list reflect → 권限NG確認
