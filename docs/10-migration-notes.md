# 10 Nx（Angular+Nest）サンプルからの概念マッピング（任意）

今回のNxサンプルに出ていた概念を、Laravel版でどう置き換えるかのメモ。

- Prisma schema -> Laravel Migration + Eloquent Model
- Nest Modules -> Laravelの機能単位（Service/Policy/Livewireコンポーネント）
- DTO -> FormRequest / Livewireのvalidated data / Data classes（必要なら）
- Guards -> Middleware + Policies
- Interceptors -> Middleware / Exception Handler / Logging context
- Queue -> Laravel Queue + Redis
- Docker compose（db） -> 同様（postgres/redis）

目的は「同じ業務要件を、より安定なモノリスで実装する」。
