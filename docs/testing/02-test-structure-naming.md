# 02 ディレクトリ構成・命名・規約

## 1. 推奨ツール
- テストランナー: **Pest**（読みやすく、AIが増やしやすい）
- ただし既存がPHPUnitなら無理に変えない（混在は避ける）

## 2. ディレクトリ構成（例）
- tests/
  - Unit/
    - Domain/
    - Services/
  - Feature/
    - Auth/
    - Tenancy/
    - Tickets/
    - Admin/
    - Audit/
  - Livewire/
    - Tickets/
    - Dashboard/
  - Support/
    - Helpers.php（必要なら）

## 3. 命名規約（原則）
- テストファイル名は「対象 + 期待結果」が分かること
- Pestなら describe/it を活用して文章化

例:
- `tests/Feature/Tenancy/TenantIsolationTest.php`
- `tests/Feature/Tickets/TicketStatusTransitionTest.php`
- `tests/Livewire/Tickets/TicketShowTest.php`

## 4. テストデータ構築の規約
- 原則: Factoryで作る（SeederはE2E/ローカル用）
- テナント境界があるため、必ず `tenant_id` を明示するか、
  `TenantFactory::forTenant($tenant)` のようなヘルパを用意

## 5. 失敗時の情報量を増やす
- assert失敗時に「何が期待で何が実際か」出るようにする
- `->dump()` は多用しない（ログが汚れる）
