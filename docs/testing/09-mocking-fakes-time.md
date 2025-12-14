# 09 Fake/Mock/Time（外部依存を制御して安定化）

## 1. 何をFakeにするか
- Notification / Mail / Queue / Events / HTTP Client / Time（Carbon）

## 2. 典型パターン
- 通知: `Notification::fake()` → `assertSentTo()`
- メール: `Mail::fake()` → `assertQueued()`
- キュー: `Queue::fake()` → `assertPushed()`
- 時刻: `Carbon::setTestNow()`

## 3. 失敗ジョブ（重要）
- failed_jobs テーブルの監視をテストで1本入れると運用が楽
