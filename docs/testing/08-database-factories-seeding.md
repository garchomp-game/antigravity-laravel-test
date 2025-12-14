# 08 Factories / Seeding / テストデータ

## 1. 方針
- テストは Factory を中心に組む
- Seederはローカルのデモ/手動検証/E2E用に限定

## 2. 必須Factory（OpsHub想定）
- TenantFactory
- UserFactory（role付与のヘルパ付き）
- DepartmentFactory / TeamFactory
- TicketFactory（status/type/assignedなどstateが切れる）
- TicketEventFactory
