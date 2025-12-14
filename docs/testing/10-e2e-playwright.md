# 10 E2E（Playwright）最小構成で最大効果

## 1. 本数（推奨）
- 3本で十分
  1) ログイン → ダッシュボード表示
  2) チケット作成 → 一覧に反映
  3) Viewerで編集操作ができない

## 2. 安定化Tips
- セレクタは `data-testid` を使う（クラス/文言依存を避ける）
- 画面遷移後はURL/可視性で待つ

## 3. Dockerで回す
- web/app/db/redis を compose で起動
- playwrightコンテナ or nodeサービスで実行
