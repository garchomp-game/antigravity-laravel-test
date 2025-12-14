# 08 ログ/監視（Observability）

## 1. 目標
- 原因追跡可能なログ（request-id、actor、tenant、entity）
- 例外の重複排除とアラート（Sentry推奨）
- キュー/スケジューラの失敗も見える化

## 2. Request-ID
- Middlewareで生成/引継ぎ
- `Log::withContext(['request_id' => ...])` を常時付与
- 画面トースト等に request-id を表示（問い合わせ用）

## 3. 構造化ログ
- Monolog JSON formatter
- fields: timestamp, level, message, request_id, tenant_id, user_id, route, meta

## 4. 例外ハンドリング
- Handlerで report しつつ、PIIはマスク
- DB接続/ENV不足は明確なRuntimeExceptionに

## 5. Sentry（推奨）
- release/commit紐付け
- queue/scheduler の例外も送信
- alert: error rate / new issue

## 6. 運用チェック
- queue dead letter（failed_jobs）監視
- schedule実行のheartbeat（拡張）
