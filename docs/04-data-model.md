# 04 データモデル

## 1. ER概要
- Tenant 1 - N Department
- Department 1 - N Team
- Team N - N User（中間: user_teams）
- Team 1 - N Ticket（任意: team_idを持つ）
- Ticket 1 - N TicketEvent
- Tenant 1 - N AuditLog

## 2. テーブル定義（例）
### tenants
- id uuid PK
- name text
- slug text unique
- created_at, updated_at

### departments
- id uuid PK
- tenant_id uuid FK -> tenants
- name text
- created_at, updated_at
- index: (tenant_id, name)

### teams
- id uuid PK
- tenant_id uuid FK
- department_id uuid FK
- name text
- created_at, updated_at
- index: (tenant_id, department_id)

### users
- id uuid PK
- tenant_id uuid FK（テナント内ユーザにする場合）
- email text unique (tenant_id, email) を推奨
- name text
- password text
- created_at, updated_at

### user_teams
- tenant_id uuid
- user_id uuid
- team_id uuid
- role text（MVP: team内の役割）
- PK: (user_id, team_id)
- index: (tenant_id, team_id)

### tickets
- id uuid PK
- tenant_id uuid
- type enum
- status enum
- title text
- description text nullable
- priority int nullable（拡張）
- created_by uuid FK users
- assigned_to uuid nullable FK users
- department_id uuid nullable FK
- team_id uuid nullable FK
- created_at, updated_at
- index: (tenant_id, status, created_at)
- index: (tenant_id, assigned_to, status)

### ticket_events
- id uuid PK
- tenant_id uuid
- ticket_id uuid FK
- kind text（comment/status_change/system）
- body text nullable
- from_status enum nullable
- to_status enum nullable
- actor_id uuid nullable
- created_at
- index: (tenant_id, ticket_id, created_at)

### audit_logs
- id uuid PK
- tenant_id uuid
- actor_id uuid nullable
- action text（ticket.create 等）
- entity_type text（Ticket 等）
- entity_id uuid
- request_id text
- ip inet nullable
- user_agent text nullable
- meta jsonb
- created_at
- index: (tenant_id, created_at)
- index: (tenant_id, action, created_at)

## 3. DDL断片（例）
> 実際はLaravel Migrationで表現する。ここではイメージのみ。

```sql
create table audit_logs (
  id uuid primary key,
  tenant_id uuid not null,
  actor_id uuid null,
  action text not null,
  entity_type text not null,
  entity_id uuid not null,
  request_id text not null,
  meta jsonb not null default '{}'::jsonb,
  created_at timestamptz not null default now()
);
create index audit_logs_tenant_created_at on audit_logs(tenant_id, created_at desc);
```
