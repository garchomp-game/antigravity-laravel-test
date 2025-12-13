# OpsHub (Laravel + Livewire + Blade + Tailwind + daisyUI) – 設計ドキュメント（ZIP同梱）

- 生成日: 2025-12-13
- バージョン: 0.1.0-docs
- 想定構成: Laravel（モノリス） + Livewire + Blade + Tailwind + daisyUI + PostgreSQL + Redis + Docker（本番寄せ）
- 目的: **AI（例: Antigravity）に実装を任せても迷いにくい**、業務アプリ用の要件/設計パッケージ

## 収録物
- `01-requirements.md` 要件定義（MVP/拡張、非機能、スコープ）
- `02-basic-design.md` 基本設計（アーキ、モジュール、画面、認証認可、データ管理）
- `03-detailed-design.md` 詳細設計（境界、Livewire設計、DB、API、ジョブ、監査ログ、通知）
- `04-data-model.md` ER/テーブル定義（DDL例、索引、制約）
- `05-ui-design.md` 画面設計（daisyUI部品割当、画面遷移、コンポーネント）
- `06-api-spec.md` API/ルーティング仕様（Web/API共存、命名規約）
- `07-docker-design.md` Docker設計（compose、役割分離、本番/CIの寄せ方）
- `08-logging-observability.md` ログ/監視（Request-ID、構造化ログ、Sentry、キュー/スケジューラ）
- `09-testing-strategy.md` テスト戦略（Pest/PhpUnit、E2E、CI）
- `10-migration-notes.md` 既存Nxサンプルからの概念マッピング（任意）

## 前提（プロジェクトの“あるある”要件）
- マルチテナント（Tenant）
- 部門（Department）/チーム（Team）/ユーザー
- チケット（Incident/Request/Change/Task） + 状態遷移
- 監査ログ（誰がいつ何をしたか）
- ジョブ（通知/集計/エクスポート）
- RBAC（ロール/権限）

> この設計は「大規模すぎないが、業務要件が濃い」アプリのテンプレとして作ってあります。
