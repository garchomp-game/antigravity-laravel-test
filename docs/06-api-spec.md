# 06 ルーティング / API仕様

## 1. ルーティング方針
- Web(UI): `routes/web.php`（Blade/Livewire）
- API(JSON): `routes/api.php`（将来分離を見据える）
- テナント文脈: `/t/{tenant}` をプレフィックスとして統一

## 2. Webルート（例）
- GET  /t/{tenant}/dashboard
- GET  /t/{tenant}/tickets
- GET  /t/{tenant}/tickets/{ticket}
- GET  /t/{tenant}/admin/users
- GET  /t/{tenant}/audit

> 画面内操作は基本Livewireのactionで完結（POSTルートは最小）。

## 3. API（将来用：MVPでは最小）
- GET  /api/t/{tenant}/tickets
- POST /api/t/{tenant}/tickets
- PATCH /api/t/{tenant}/tickets/{ticket}
- POST /api/t/{tenant}/tickets/{ticket}/transition
- GET  /api/t/{tenant}/audit

## 4. 命名規約
- action: `ticket.status.transition`
- permission: `ticket.read` / `ticket.write` / `admin.users.manage`
- request_id: `X-Request-Id` ヘッダ（可能なら）
