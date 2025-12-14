# 06 新セッションで貼るプロンプト集

## 6.1 ChatGPT 新セッション用（最初の1投）
下をそのまま貼って、さらに `docs/handoff/` をアップロードする。

```text
あなたは「Laravel + Livewire プロジェクト OpsHub」の開発支援AIです。
添付の docs/handoff を読み、現状（進捗・設計判断・テスト方針・残タスク）を理解してください。

最優先タスクは Phase 5-3（テナント切り替えUI）です。
作業はTDDで進め、UIテストは data-testid を優先し、HTML/CSSの細部に依存しないでください。
テナント越境は404、権限不足は403の方針を維持します。

まず、Phase 5-3 を「最小スコープ」で実装するために、
(1) Given/When/Then の受け入れ条件トップ10
(2) 追加するテスト一覧（Unit/Feature/Livewire/Dusk）
(3) 実装計画（ファイル単位）
を提示してください。
```

## 6.2 Antigravity へ投げる「再開指示」
```text
We are working on OpsHub (Laravel + Livewire, Docker, PostgreSQL, Redis, Spatie Permission).
Phase 5-1 (Admin/Users/Index) and Phase 5-2 (Audit/Index) are complete, all tests pass (79 tests, 208 assertions).

Please read docs/handoff/* to understand the current state and decisions:
- tenant boundary => 404 (hide existence)
- permission/role insufficient => 403
- UI tests should use data-testid
- proceed strictly with TDD (Red->Green->Refactor), fix only the first failing test at a time

Next task: Phase 5-3 Tenant switching UI (MVP scope):
- Show current tenant in navbar
- Provide tenant switch dropdown (only tenants the user belongs to)
- Redirect to /t/{tenant}/dashboard (or equivalent)
- Accessing a tenant not in user's memberships must return 404
Provide tests first (Feature + Livewire; Dusk optional 1 test).
At the end, output changed tests, changed files, and docker commands to verify.
```

## 6.3 Phase 5-3 用タスク投入テンプレ（日本語）
```text
【タスク名】
Phase 5-3 テナント切り替えUI（最小スコープ）

【受け入れ条件（Given/When/Then）】
Given:
- ユーザーUがTenant AとTenant Bに所属している
- ユーザーVはTenant Aにのみ所属している

When:
- Uがログインしている
Then:
- ナビに現在のテナント名が表示される

When:
- Uがテナント切り替えドロップダウンでTenant Bを選ぶ
Then:
- /t/{tenantB}/dashboard（または既存のトップ）へ遷移し、Tenant Bのデータスコープになる

When:
- VがTenant BへURLを直接入力してアクセスする
Then:
- 404（存在隠し）

【追加テスト（目安）】
- Feature: 2〜3本（所属テナント一覧、切替、越境404）
- Livewire: 2〜3本（表示、切替イベント）
- Dusk: 1本（任意：ドロップダウン操作）

【制約】
- data-testid を使用
- UIの細部固定を避ける
- 既存79テストを壊さない
- docker内で再現できるコマンドを提示する
```
