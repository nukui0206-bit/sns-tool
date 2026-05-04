# SNS Tool — Xserver デプロイ手順

本番環境：**Xserver Business**（共有ホスティング）  
公開URL：**https://sns.laiweb-dash.com**  
GitHub：https://github.com/nukui0206-bit/sns-tool

## 1. 本番サーバー要件

| 項目 | 必要バージョン | 備考 |
|---|---|---|
| PHP | **8.2 系**（8.2.x） | `composer.json` で `config.platform.php = 8.2.0` 固定。Xserver Business のサーバーパネルで「PHP 8.2」を選択 |
| MySQL | 5.7 / 8.0 系 | Xserver の MySQL を使用 |
| Composer | 2.x | Xserver は標準で利用可能（`/usr/bin/composer`） |
| Node.js | 18 以上推奨 | **本番ではビルド不要**（`public/build/` をコミット運用 or ローカルでビルドして push する手順を採用） |
| Git | 2.x | `git pull` で更新 |
| 拡張 | mbstring, openssl, pdo_mysql, tokenizer, xml, ctype, bcmath, json, fileinfo | Laravel 11 標準。Xserver Business なら基本入っている |

**Cron：** Xserver サーバーパネルの「cron 設定」から1分毎に1行登録（後述）。

**SSL：** Xserver 無料独自SSL（Let's Encrypt）を `sns.laiweb-dash.com` で発行済み前提。

## 2. ディレクトリ構成

```
/home/laide/
├── sns.laiweb-dash.com/         ← サブドメイン公開フォルダ（Xserver で自動作成）
│   └── public_html/             ← ここに sns-tool/public の中身をシンボリックリンク or 直接配置
└── sns-tool/                    ← Laravel アプリケーション本体（GitHub からクローン）
    ├── app/
    ├── public/                  ← public_html からシンボリックリンクで参照
    ├── ...
```

**推奨：シンボリックリンク方式**
```bash
# サブドメインのドキュメントルートを sns-tool/public に向ける
ln -s /home/laide/sns-tool/public /home/laide/sns.laiweb-dash.com/public_html
```

または Xserver サーバーパネルの **「ドメイン設定 → 公開ディレクトリ」** で `sns-tool/public` を直接指定。

## 3. 初回デプロイ手順（クローン〜起動まで）

### 3.1 SSH 接続

```bash
ssh laide@sv????.xserver.jp
```

### 3.2 リポジトリクローン

```bash
cd ~
git clone https://github.com/nukui0206-bit/sns-tool.git
cd sns-tool
```

### 3.3 .env ファイル作成

```bash
cp .env.example .env
```

下記設定で書き換え（後述「.env テンプレート」参照）。

### 3.4 composer install（本番モード）

```bash
composer install --no-dev --optimize-autoloader --no-interaction
```

### 3.5 APP_KEY 生成

```bash
php artisan key:generate
```

**⚠️ 重要：APP_KEY は一度生成したら絶対に変えない**。理由は「[デプロイ時の注意点](#9-デプロイ時の注意点)」参照。

### 3.6 storage / bootstrap/cache の権限

```bash
chmod -R 775 storage bootstrap/cache
```

### 3.7 MySQL データベース作成

Xserver サーバーパネル → **MySQL設定** で以下を作成：
- DB名：`laide_snsdash`
- ユーザー：`laide_snsdash`
- パスワード：強固なものを生成 → `.env` の `DB_PASSWORD` に設定

文字コード：utf8mb4。

### 3.8 マイグレーション

```bash
php artisan migrate --force
```

`--force` は本番環境で必須（プロンプト無視）。

### 3.9 シーダー（初期管理者ユーザー）

```bash
php artisan db:seed --force
```

`AdminUserSeeder` で `admin@laiweb-dash.com / password` が投入される。  
**⚠️ ログイン直後にプロフィール画面でパスワードを変更すること**。

### 3.10 storage シンボリックリンク

```bash
php artisan storage:link
```

`public/storage → storage/app/public` のリンクを作成。Phase 2+ でメディアアップロードを使う場合に必要（現状 SNS Tool では不要だが将来用）。

### 3.11 ビルド済みアセットの配置

ローカルで：
```bash
npm run build
git add public/build && git commit -m "build: production assets" && git push
```

サーバーで：
```bash
git pull origin main
```

または、本番でビルドする場合：
```bash
# サーバーで（Node.js が利用可能な場合）
npm ci
npm run build
```

**推奨：ローカルでビルド + コミット**（本番に Node 環境を求めない）。

### 3.12 cron 設定

Xserver サーバーパネル → **cron設定** で以下1行を追加：

```
* * * * * cd /home/laide/sns-tool && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

これで `routes/console.php` 内のスケジュール（`posts:publish-due` + `queue:work --stop-when-empty`）が毎分実行される。

### 3.13 動作確認

ブラウザで `https://sns.laiweb-dash.com/login`：
- ログイン画面表示
- `admin@laiweb-dash.com / password` でログイン
- ダッシュボード表示
- **必ずパスワードを変更**（プロフィール画面）

## 4. 通常デプロイ手順（更新時）

GitHub に push されたコミットを本番に反映：

```bash
ssh laide@sv????.xserver.jp
cd ~/sns-tool

git pull origin main
composer install --no-dev --optimize-autoloader --no-interaction
php artisan migrate --force
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

**注意：** `config:cache` / `route:cache` は本番で有効化を検討（速度向上）。ただしクロージャベースのルートがあるとキャッシュ不可になるので、`routes/web.php` と `routes/console.php` がコントローラ呼び出しのみであることを確認してから。

JS / CSS を変更した場合は `public/build/` も pull で反映される（ローカルで `npm run build` してコミット → push する運用）。

## 5. .env テンプレート（本番）

```env
APP_NAME="SNS Tool"
APP_ENV=production
APP_KEY=                              # php artisan key:generate で生成
APP_DEBUG=false
APP_TIMEZONE=Asia/Tokyo
APP_URL=https://sns.laiweb-dash.com
APP_LOCALE=ja
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=ja_JP

LOG_CHANNEL=stack
LOG_STACK=daily
LOG_LEVEL=warning

DB_CONNECTION=mysql
DB_HOST=mysql????.xserver.jp           # Xserver で発行されるホスト名
DB_PORT=3306
DB_DATABASE=laide_snsdash
DB_USERNAME=laide_snsdash
DB_PASSWORD=                           # 強固なパスワード

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

CACHE_STORE=database

QUEUE_CONNECTION=database              # Phase 4 で必須

# メール（MEO 同様）
MAIL_MAILER=smtp
MAIL_SCHEME=smtp                       # MEO 経験：これが無いと Xserver で送れない
MAIL_HOST=sv????.xserver.jp
MAIL_PORT=587
MAIL_USERNAME=info@laiweb-dash.com
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="info@laiweb-dash.com"
MAIL_FROM_NAME="${APP_NAME}"

# Phase 4: 投稿者ドライバ
SOCIAL_POSTER_DRIVER=stub              # Phase 8/9 で 'instagram' / 'tiktok' に切替
```

## 6. cron 設定（再掲）

Xserver サーバーパネル → **cron設定**：

```
* * * * * cd /home/laide/sns-tool && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

**`/usr/bin/php` は Xserver の標準パス**。サーバーパネルで PHP バージョンを 8.2 に設定していれば、これが 8.2 を指す。

複数の PHP バージョンが共存している場合は、明示的に：
```
* * * * * cd /home/laide/sns-tool && /usr/bin/php8.2 artisan schedule:run >> /dev/null 2>&1
```

ログを残したい場合：
```
* * * * * cd /home/laide/sns-tool && /usr/bin/php artisan schedule:run >> /home/laide/sns-tool/storage/logs/cron.log 2>&1
```

## 7. queue / scheduler 運用

### 仕組み

`routes/console.php` で2つのスケジュールが毎分実行される：

1. `posts:publish-due`  
   `status=scheduled AND scheduled_at <= now()` の posts を抽出して `PublishPostJob` を database queue に積む。

2. `queue:work --stop-when-empty --queue=default --max-time=50 --tries=3`  
   積まれたジョブを drain して終了。`--max-time=50` で1分内に必ず終わる。`--stop-when-empty` で空になったら即終了。**常駐 worker 不要**。

### 監視

- **ジョブ失敗の確認**：管理画面の `/failed_jobs`、もしくは `storage/logs/laravel-{date}.log`
- **手動再実行**：`/failed_jobs/{id}` の「再実行する」ボタン、または CLI `php artisan queue:retry {id}`
- **ジョブ全削除**（緊急時）：`/failed_jobs` の「すべて削除」ボタン、または CLI `php artisan queue:flush`

### 動作確認

サーバー上で手動実行：
```bash
cd ~/sns-tool
php artisan posts:publish-due
php artisan queue:work --stop-when-empty
```

## 8. GitHub からの pull 運用

### ブランチ戦略

- `main`：本番反映ブランチ（保護ブランチ推奨）
- 開発は feature ブランチ → PR → main へマージ → 本番 pull

### デプロイフロー

1. ローカルで開発・コミット
2. `npm run build`（フロントエンド変更時）
3. `git push origin main`
4. SSH で本番に入って `git pull origin main`
5. 必要に応じて `composer install --no-dev --optimize-autoloader` / `php artisan migrate --force`
6. キャッシュクリア（`config:clear` `view:clear` `route:clear`）

### 自動化（任意）

Xserver は GitHub Webhook を直接受けられないため、簡易シェルスクリプト `deploy.sh` を作成：

```bash
#!/bin/bash
set -e
cd /home/laide/sns-tool
git pull origin main
composer install --no-dev --optimize-autoloader --no-interaction
php artisan migrate --force
php artisan config:clear
php artisan view:clear
php artisan route:clear
echo "deploy completed: $(date)"
```

SSH で `./deploy.sh` 1コマンドで全部走る。

## 9. デプロイ時の注意点

### 🔴 重大な禁忌：APP_KEY ローテーション

**APP_KEY を変更すると `social_accounts.access_token` / `refresh_token` が復号不能になる**（Eloquent `'encrypted'` cast は APP_KEY で AES-256 暗号化）。

- 一度本番に投入した APP_KEY は絶対に変えない
- バックアップ：本番 `.env` の `APP_KEY` 行を別管理（パスワードマネージャ等）にコピーしておく
- 万一変えてしまった場合：全ての SocialAccount のトークンを再連携（Phase 8/9 で OAuth フロー実装済みなら再連携できる）

### 🟡 文字コード

- DB の charset は **utf8mb4 / utf8mb4_unicode_ci** で作成すること（絵文字対応）
- `.env` の `APP_LOCALE=ja` `APP_TIMEZONE=Asia/Tokyo` を必ず設定

### 🟡 cron の PHP パス

PHP バージョンが意図しないバージョン（7.x など）になっていると、`composer install` も migrate も動かない。Xserver の **サーバーパネル → PHP Ver.切替** で 8.2 に統一。

cron 内では `/usr/bin/php` が現在のサーバーパネル設定を反映するか不安な場合は `/usr/bin/php8.2` で明示。

### 🟡 storage 権限

`storage/` `bootstrap/cache/` を 775 にしていないとログ書き込みやセッション保存で 500 エラー。  
初回デプロイの 3.6 で実行 → ログを見て書き込み権限エラーが出ていないか確認。

### 🟡 .env を git に上げない

`.gitignore` で除外済み。**本番 .env はサーバー上で直接編集**、変更履歴はパスワードマネージャ等で別管理。

### 🟡 キャッシュ問題

Laravel の config / view / route キャッシュが残っていると、コード変更が反映されないことがある。デプロイ後は必ず：
```bash
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

`config:cache` / `route:cache` で速度向上できるが、その場合 .env を変更したら必ず再キャッシュが必要。**初期は無効でいい**。

### 🟡 npm run build を本番で走らせるか

- **推奨：ローカルでビルド → `public/build/` をコミット → push → 本番で git pull**
- 本番に Node.js を入れたくない / 高速化したいなら有効
- デメリット：開発者全員がビルド済みアセットをコミットする運用に

代替：GitHub Actions で push 時に build → PR で artifacts コミット、という自動化（後日検討）。

### 🟡 SOCIAL_POSTER_DRIVER の切替

- 初期は `stub`（StubPoster で常に成功扱い）
- Phase 8（Instagram）完了後 → `instagram` に切替（Meta App 承認・トークン取得が前提）
- Phase 9（TikTok）完了後 → クライアントごとに driver を切り替える設計を Phase 8/9 で実装

## 10. ロールバック / 障害対応

### コードのロールバック

```bash
cd ~/sns-tool
git log --oneline -10                  # 直近10コミット確認
git reset --hard <安定版のコミットSHA>
composer install --no-dev --optimize-autoloader --no-interaction
php artisan config:clear && php artisan view:clear && php artisan route:clear
```

### マイグレーションのロールバック

```bash
php artisan migrate:rollback --force --step=1
```

ただし、データ破壊系のマイグレーションは慎重に。本番 DB のバックアップ後に実行。

### 障害時の確認順

1. `storage/logs/laravel-{date}.log` のエラー
2. `/failed_jobs` の例外
3. cron が動いているか：`tail -f /home/laide/sns-tool/storage/logs/cron.log`（cron 出力先を指定している場合）
4. PHP バージョン：`php -v` で 8.2 系か
5. `.env` の DB / MAIL 設定

### バックアップ

- DB：Xserver サーバーパネル → MySQL設定 → エクスポート（手動 or 定期）
- コード：GitHub にあるので不要
- `.env`：パスワードマネージャに別管理
- `storage/app/public/`：将来メディア利用時にバックアップ対象に追加

---

最終更新：2026-05-04（Phase 0〜5+10 完了時点）  
作成者：y-nuk
