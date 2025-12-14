# 05 Unit（ドメインルール）設計

## 1. Unitで囲うべき領域
- TicketStatusの状態遷移ルール（許可遷移/禁止遷移）
- 権限判定ロジック（Policy/Gateの“ルール”部分）
- テナント境界ルール（tenant_id付与漏れ防止用のヘルパ/スコープ）

## 2. 推奨パターン
- 状態遷移: `TicketStatusTransition::can($from, $to, $role)` のような純粋関数に寄せる
- 例外: ドメイン例外（InvalidTransitionException）を投げ、FeatureでHTTP変換を検証

## 3. テスト粒度
- “意味のある代表ケース” + “境界ケース” に絞る
- データ駆動テスト（Pest datasets）推奨
