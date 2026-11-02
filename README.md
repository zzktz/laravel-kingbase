# Laravel Kingbase

为 Laravel 8 及以上版本提供 KingbaseES 数据库连接驱动。驱动通过 Laravel 服务容器注册 `kingbase` 连接，不修改 `vendor/laravel/framework` 文件。

## 要求

- PHP 7.3 或更高版本
- Laravel 8 或更高版本
- Kingbase 官方 PHP `pdo_kdb` 扩展

当前版本支持 Laravel 的 `illuminate/database` 8.30、9、10、11、12 和 13 系列。Laravel 版本和 PHP 版本仍需遵循对应 Laravel 版本的官方要求。

## 安装

```bash
composer require zzktz/laravel-kingbase:^1.0
```

Laravel 会通过 Composer package discovery 自动加载 `KingbaseServiceProvider`。如果项目关闭了 package discovery，手动在 `config/app.php` 添加：

```php
Zzktz\LaravelKingbase\KingbaseServiceProvider::class,
```

## 配置

在 `config/database.php` 中添加连接：

```php
'kingbase' => [
    'driver' => 'kingbase',
    'host' => env('KB_DB_HOST', '127.0.0.1'),
    'port' => env('KB_DB_PORT', '54321'),
    'database' => env('KB_DB_DATABASE', 'doc'),
    'username' => env('KB_DB_USERNAME', 'forge'),
    'password' => env('KB_DB_PASSWORD', ''),
    'charset' => 'utf8',
    'prefix' => '',
    'prefix_indexes' => true,
    'search_path' => 'public',
    'sslmode' => 'prefer',
],
```

需要使用 Kingbase 作为默认连接时，在 `.env` 中设置：

```env
DB_CONNECTION=kingbase
```

修改后清理配置缓存：

```bash
php artisan config:clear
php artisan config:cache
```

## 连接能力

连接器使用 Kingbase PDO 驱动的 `kdb:` DSN，并保留当前项目已经验证过的连接初始化能力：

- 字符集 `charset`
- Schema/search path
- 时区 `timezone`
- 事务隔离级别 `isolation_level`
- 应用名 `application_name`
- 同步提交 `synchronous_commit`
- SSL 参数 `sslmode`、`sslcert`、`sslkey`、`sslrootcert`

查询和 Schema 语法基于 Laravel PostgreSQL 实现。Kingbase 业务 SQL 仍应在目标 Kingbase 版本上验证，尤其是手写 DDL、迁移和数据库特有函数。

## 从当前定制支持迁移

1. 安装并启用 PHP 8.2 CLI 和 PHP-FPM 使用的 `pdo_kdb` 扩展。
2. 安装本包并确认 `KingbaseServiceProvider` 已加载。
3. 将连接配置的 `driver` 保持为 `kingbase`。
4. 删除或还原 Laravel `vendor` 中的 Kingbase 定制文件。
5. 清理并重建 Composer、Laravel 自动加载和配置缓存。
6. 在测试库验证连接、CRUD、事务、分页、迁移、Schema、备份和队列。

本包不依赖 `lmo/laravel-kdb`，避免 `kdb` 驱动名与本项目原有 `kingbase` 配置冲突。迁移期间不要同时让两个包注册同一连接驱动。

## 开发测试

```bash
composer install
composer test
```

测试连接需要真实的 Kingbase 服务和 `pdo_kdb` 扩展。未安装扩展时，可以运行静态检查和不连接数据库的单元测试，但不能证明数据库连接可用。
