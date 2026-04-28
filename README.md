# ALL-NOTE

ALL-NOTE 是一个面向个人或小团队使用的统一记录管理系统，用于集中管理密码、IT/资产、证件和事件处理记录。

系统默认要求用户登录后使用，不提供访客访问和管理员后台。每个用户维护自己的数据和标签。

## 功能概览

- 用户登录、注册、修改密码，用户名支持中文。
- 密码管理：记录账号、密码、网址、备注等信息，敏感内容加密存储。
- IT/资产管理：记录实体设备、云服务器、域名等资产信息和到期时间。
- 证件管理：记录证件信息、到期时间和相关备注。
- 事件管理：记录事件标题、状态、来源/对象、发生日期、过程、结果、标签、附件和可见性。
- 事件正文支持文字和图片混排，附件支持多个文件。
- 事件可设置为仅自己可见或公开；公开事件不展示标签。
- 首页展示统计、提醒和最近事件一览。
- 适配移动端列表显示，手机访问尽量只需要上下滑动。
- 支持 Apple 设备网站图标：`public/apple-touch-icon.png`。

## 技术栈

- PHP 8.3+
- Laravel 13
- MySQL / MariaDB / SQLite
- Vite 8
- Tailwind CSS 4
- Alpine.js
- PHPUnit

## 环境要求

本地开发或服务器部署前，请确认环境包含：

- PHP 8.3 或更高版本
- Composer
- Node.js 和 npm
- MySQL 8、MariaDB，或 SQLite
- 常用 PHP 扩展：`openssl`、`pdo_mysql`、`mbstring`、`fileinfo`、`xml`、`ctype`、`json`、`curl`、`tokenizer`

## 本地开发

克隆项目后进入项目目录：

```bash
git clone <your-repository-url> ALL-NOTE
cd ALL-NOTE
```

安装后端依赖：

```bash
composer install
```

安装前端依赖：

```bash
npm install
```

创建环境配置：

```bash
cp .env.example .env
php artisan key:generate
```

根据实际数据库修改 `.env`。例如使用 MySQL：

```env
APP_NAME=ALL-NOTE
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
APP_LOCALE=zh_CN
APP_FALLBACK_LOCALE=zh_CN

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=all_note
DB_USERNAME=root
DB_PASSWORD=
```

执行数据库迁移：

```bash
php artisan migrate
```

如需创建演示数据：

```bash
php artisan db:seed
```

演示账号：

```text
用户名：demo
密码：Demo@123
```

启动开发服务：

```bash
php artisan serve
npm run dev
```

访问：

```text
http://127.0.0.1:8000
```

## 生产部署：宝塔面板

以下以宝塔 Linux 面板为例，假设项目目录为：

```text
/www/wwwroot/ALL-NOTE
```

### 1. 准备站点

在宝塔中新建站点，域名填写你的实际域名。

站点运行目录必须设置为：

```text
/www/wwwroot/ALL-NOTE/public
```

不要把网站根目录指向项目根目录，否则 `.env`、源码和缓存文件可能被外部访问。

### 2. 拉取代码

进入站点目录：

```bash
cd /www/wwwroot
git clone <your-repository-url> ALL-NOTE
cd ALL-NOTE
```

如果代码已经存在，后续更新使用：

```bash
git pull
```

### 3. 配置 `.env`

复制环境文件：

```bash
cp .env.example .env
```

生产环境建议配置：

```env
APP_NAME=ALL-NOTE
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
APP_LOCALE=zh_CN
APP_FALLBACK_LOCALE=zh_CN

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=all_note
DB_USERNAME=all_note
DB_PASSWORD=your-database-password

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=database
```

首次部署新系统时生成应用密钥：

```bash
php artisan key:generate
```

如果是迁移已有数据，尤其是已有密码数据，必须沿用旧项目的 `APP_KEY`。密码字段依赖 `APP_KEY` 加密，换掉后旧数据将无法正常解密。

### 4. 安装依赖

安装 PHP 依赖：

```bash
composer install --no-dev --optimize-autoloader
```

安装并构建前端资源：

```bash
npm install
npm run build
```

如果服务器 Node.js 环境不方便构建，可以在本地执行 `npm run build` 后，将生成的 `public/build` 上传到服务器对应目录。

### 5. 数据库迁移

确认数据库已在宝塔中创建，并且 `.env` 中的数据库账号密码正确，然后执行：

```bash
php artisan migrate --force
```

生产环境不建议执行 `php artisan db:seed`，除非你明确需要演示账号和演示数据。

### 6. 设置权限

Laravel 需要写入 `storage` 和 `bootstrap/cache`：

```bash
chown -R www:www storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

如果宝塔中的运行用户不是 `www`，请按实际 PHP-FPM 运行用户调整。

### 7. 配置伪静态

Nginx 站点伪静态配置：

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

Apache 通常可使用 Laravel 自带的 `public/.htaccess`。

### 8. 缓存优化

部署完成后执行：

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

如果修改了 `.env`、路由或视图，重新执行对应缓存命令。

## 更新项目

每次从 Git 仓库更新服务器代码时，建议流程如下：

```bash
cd /www/wwwroot/ALL-NOTE
git pull
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

如果只是改了 PHP 或 Blade 文件，也可以按实际情况省略 `npm install` 和 `npm run build`。

## 测试

运行自动化测试：

```bash
php artisan test
```

检查 Blade 视图是否可以正常缓存：

```bash
php artisan view:cache
php artisan view:clear
```

## 数据和备份

部署、更新或迁移前建议备份：

- 数据库
- `.env`
- `storage` 目录
- `public/apple-touch-icon.png`
- 如有本地上传构建产物，备份 `public/build`

事件附件、正文图片等上传文件由应用存储并通过系统路由访问。迁移服务器时不要只迁移数据库，也要同步上传文件目录。

## 安全建议

- 不要提交 `.env` 到 Git 仓库。
- 生产环境必须设置 `APP_DEBUG=false`。
- 生产环境建议启用 HTTPS。
- 迁移已有数据时必须保留原 `APP_KEY`。
- 生产环境不要保留不需要的演示账号。
- 定期备份数据库和上传文件。

## 常见问题

### 首页可以打开，其他页面 404

检查网站运行目录是否为 `public`，并确认 Nginx 伪静态包含：

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 页面 500 或空白

检查：

- `.env` 是否存在
- `APP_KEY` 是否已设置
- 数据库连接是否正确
- `storage` 和 `bootstrap/cache` 是否可写
- `storage/logs/laravel.log` 中的错误信息

### 前端样式或脚本没有生效

执行：

```bash
npm install
npm run build
```

并确认服务器存在：

```text
public/build
```

### 旧密码数据无法解密

通常是服务器 `.env` 中的 `APP_KEY` 与旧环境不一致。恢复旧环境的 `APP_KEY` 后再访问。

### 文件或图片上传失败

检查：

- PHP 上传大小限制
- Nginx 或 Apache 上传大小限制
- `storage` 目录权限
- 磁盘空间

## 目录说明

```text
app/                应用核心代码
database/           数据库迁移、工厂和种子
public/             Web 入口和公开资源
resources/          Blade 视图、CSS、JS
routes/             路由定义
storage/            日志、缓存和上传文件
tests/              自动化测试
```
