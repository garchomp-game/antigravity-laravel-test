# OpsHub 引き継ぎパック（ChatGPT / Antigravity 用）
生成日: 2025-12-14

> **注意**: このフォルダは **一時的な引き継ぎ専用**です。  
> プロジェクト固定の仕様・設計は `docs/product/` や `docs/ops/` に記載してください。

このフォルダは、**新セッションへ切り替えるための「最小で強い」引き継ぎ資料**です。  
目的は、ChatGPT/Antigravity が *過去ログを読まずに* すぐ開発を再開できること。

## 使い方（おすすめ）
1. この `docs/handoff/` をZIPのまま新セッションへアップロード
2. 併せて以下コマンドの出力（コピペ）を貼る
   - `git log -1 --oneline`
   - `git status -sb`
   - `docker compose ps`
   - `docker compose exec app php -v`
   - `docker compose exec app php artisan --version`
3. 新セッションでは `06-session-prompts.md` の「貼り付け用プロンプト」を最初に投げる

## このパックに含まれるもの
- 00-executive-summary.md: 現在地（進捗・成果）
- 01-project-facts.md: 仕様と技術の「不変部分」
- 02-runbook.md: 起動/テスト/デバッグの手順
- 03-decision-log.md: 重要な設計判断（迷いどころを潰す）
- 04-test-matrix.md: テスト状況と追加テストの要点
- 05-open-tasks.md: 次にやるべきこと（Phase 5-3）
- 06-session-prompts.md: 新セッション用（ChatGPT / Antigravity）プロンプト
- 07-handoff-checklist.md: 引き継ぎ前のチェックリスト（秘密情報対策含む）
