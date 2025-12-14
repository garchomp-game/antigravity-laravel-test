# 05 UI設計（Blade + Livewire + daisyUI）

## 1. デザイン方針
- daisyUIの部品を標準化し、独自CSSを最小化
- “一覧 + 詳細 + モーダル” の定番パターンを多用
- 画面単位でLivewireコンポーネントを割当

## 2. 画面一覧と部品割当
### ダッシュボード
- cards / stats / table

### チケット一覧
- input（検索）
- select（状態/タイプ）
- table（一覧）
- pagination
- badge（ステータス）
- modal（作成/簡易編集）

### チケット詳細
- tabs（概要/履歴/監査）
- textarea（コメント）
- timeline風（events）※daisyUI + custom minimal
- dropdown（状態変更）

### ユーザー/権限
- table（ユーザ一覧）
- modal（ロール付与）
- alert/toast

### 監査ログ
- table（操作履歴）
- drawer or modal（詳細meta JSON）

## 3. Livewireコンポーネント（推奨）
- tickets.index
- tickets.create-modal（子コンポーネント）
- tickets.show
- admin.users.index
- admin.roles.edit
- audit.index

## 4. UXガイド
- すべての破壊的操作は confirm modal
- 例外時は toast + request-id を表示（問い合わせ用）
