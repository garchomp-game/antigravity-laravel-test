# OpsHub Documentation

**Laravel + Livewire マルチテナント チケット管理システム**

このドキュメントは、OpsHub プロジェクトの仕様、設計、運用、テスト、引き継ぎに関する情報をまとめたものです。

---

## 📚 ドキュメント構成

### 🎯 [product/](./product/) — 仕様・設計
プロダクトの要件定義、設計ドキュメント

- [01-requirements.md](./product/01-requirements.md) — 要件定義
- [02-basic-design.md](./product/02-basic-design.md) — 基本設計
- [03-detailed-design.md](./product/03-detailed-design.md) — 詳細設計
- [04-data-model.md](./product/04-data-model.md) — データモデル
- [05-ui-design.md](./product/05-ui-design.md) — UI設計
- [06-api-spec.md](./product/06-api-spec.md) — API仕様

### ⚙️ [ops/](./ops/) — 運用・インフラ
Docker、ログ、マイグレーション等の運用関連

- [07-docker-design.md](./ops/07-docker-design.md) — Docker構成
- [08-logging-observability.md](./ops/08-logging-observability.md) — ログ・観測性
- [10-migration-notes.md](./ops/10-migration-notes.md) — マイグレーション注意事項

### 🧪 [testing/](./testing/) — テスト戦略・実装
TDD、テストピラミッド、各種テスト手法

- [README.md](./testing/README.md) — **テストガイドの入口（必読）**
- [09-testing-strategy.md](./testing/09-testing-strategy.md) — テスト戦略
- 01-13: テスト原則、構造、Docker実行、CI、各種テスト手法、AIプロンプト

### 🤝 [handoff/](./handoff/) — 引き継ぎ（一時的）
新セッション開始時の引き継ぎパック

- [README.md](./handoff/README.md) — 引き継ぎパックの使い方
- 00-07: サマリ、技術スタック、Runbook、設計判断、テスト状況、次タスク

> **注**: `handoff/` は **一時的な引き継ぎ用**です。定常的な仕様・設計は `product/` や `ops/` に記載してください。

### 📄 [html/](./html/) — 生成HTML
テストレポートやドキュメント生成物

- [index.html](./html/index.html)
- [phpunit.html](./html/phpunit.html)
- [audit.html](./html/audit.html)
- [dusk.html](./html/dusk.html)

---

## 🚀 クイックスタート

1. **環境構築**: `DOCKER.md` または [ops/07-docker-design.md](./ops/07-docker-design.md)
2. **テスト実行**: [testing/README.md](./testing/README.md) または [testing/03-local-docker-workflow.md](./testing/03-local-docker-workflow.md)
3. **現在の進捗**: [handoff/00-executive-summary.md](./handoff/00-executive-summary.md)

---

## 📝 ドキュメント追加・更新ルール

### 配置ルール
- **仕様・設計**: `product/` に追加
- **運用・インフラ**: `ops/` に追加
- **テスト関連**: `testing/` に追加
- **引き継ぎ（一時）**: `handoff/` に追加（プロジェクト固定情報は product/ や ops/ へ）
- **HTML生成物**: `html/` に配置

### 命名規則
- 番号プレフィクス（`01-`, `02-`）は読む順を示す場合に使用
- Markdownファイルは小文字＋ハイフン（例: `data-model.md`）

### 更新手順
1. 新ドキュメント作成・既存ドキュメント更新
2. **必ず** `docs/README.md` のTOCも更新
3. 関連リンク（他ドキュメントからの参照）を確認

---

**最終更新**: 2025-12-14
