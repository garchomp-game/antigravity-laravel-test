# Testing Guide for OpsHub (Laravel + Livewire) – ベストプラクティス資料
生成日: 2025-12-14

この資料は「**実際のテストコード実装・修正はAIに任せる**」前提で、
AIが迷走しないように **方針・粒度・命名・環境・テンプレ** を固めるためのドキュメント群です。

## 目標
- 仕様のブレを減らし、AIが「何をもって完成か」を判断できるようにする
- 失敗時に原因が分かるテスト（ログ/ダンプ/診断手順）を用意する
- Docker/CIで再現性の高い実行ルートを確立する

## 収録
- 01-testing-principles.md … 全体方針（テストピラミッド、TDD運用）
- 02-test-structure-naming.md … ディレクトリ/命名/規約
- 03-local-docker-workflow.md … Dockerでの実行/DB戦略
- 04-ci-quality-gates.md … CI設計、品質ゲート、失敗時の情報量
- 05-unit-domain-rules.md … ドメイン/状態遷移などUnitの設計
- 06-feature-http-routes.md … Feature（HTTP/認証/権限/テナント境界）
- 07-livewire-component-tests.md … Livewire v3のテスト作法
- 08-database-factories-seeding.md … Factories/Seed/テストデータ構築
- 09-mocking-fakes-time.md … Fake/Mock/Time/Queue/Notificationの扱い
- 10-e2e-playwright.md … E2E（最小本数で最大効果）
- 11-debugging-checklist.md … 失敗時のチェックリスト（AI向け）
- 12-test-templates.md … AIがコピペで増やせるテンプレ集
