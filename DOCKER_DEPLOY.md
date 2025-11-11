# Docker 部署指南

本文档介绍如何使用 Docker 和 Docker Compose 快速部署 PHP-Code-Sec 靶场环境。

## 环境要求

- Docker 20.10+
- Docker Compose 2.0+
- 至少 2GB 可用内存
- 至少 5GB 可用磁盘空间

## 快速启动

### 1. 启动所有服务

在项目根目录执行：

```bash
docker-compose up -d
```

这将启动以下服务：
- **MySQL 8.0**：数据库服务（端口 3306）
- **PHP-FPM 8.1**：PHP 运行环境
- **Nginx**：Web 服务器（端口 8080）

### 2. 查看服务状态

```bash
docker-compose ps
```

### 3. 访问应用

打开浏览器访问：

```
http://localhost:8080
```

### 4. 初始化配置

首次访问需要完成安装向导：

1. 访问 `http://localhost:8080/setup/install.php`
2. 填写数据库连接信息：
   - **主机**：`mysql`
   - **端口**：`3306`
   - **用户名**：`phpsec_user`
   - **密码**：`phpsec_pass`
   - **数据库名**：`phpsec_lab`
3. 点击"开始安装"
4. 安装成功后返回首页

**注意**：MySQL 容器已自动创建数据库和初始数据，安装向导会生成应用配置文件。

## 默认账户信息

### 应用账户
- **管理员**：`admin` / `admin123`
- **普通用户**：`guest` / `guest`

### MySQL 数据库
- **Root 密码**：`root123456`
- **应用用户**：`phpsec_user`
- **应用密码**：`phpsec_pass`
- **数据库名**：`phpsec_lab`

## 常用命令

### 启动服务
```bash
docker-compose up -d
```

### 停止服务
```bash
docker-compose down
```

### 重启服务
```bash
docker-compose restart
```

### 查看日志
```bash
# 查看所有服务日志
docker-compose logs -f

# 查看特定服务日志
docker-compose logs -f nginx
docker-compose logs -f php-fpm
docker-compose logs -f mysql
```

### 进入容器
```bash
# 进入 PHP 容器
docker-compose exec php-fpm bash

# 进入 MySQL 容器
docker-compose exec mysql bash

# 进入 Nginx 容器
docker-compose exec nginx sh
```

### 重新构建镜像
```bash
docker-compose build --no-cache
docker-compose up -d
```

### 清理数据（完全重置）
```bash
# 停止并删除容器、网络、卷
docker-compose down -v

# 重新启动
docker-compose up -d
```

## 目录结构

```
PHP-Code-Sec/
├── docker/
│   ├── nginx/
│   │   └── default.conf      # Nginx 配置文件
│   ├── php/
│   │   └── php.ini            # PHP 配置文件
│   └── mysql/
│       └── init.sql           # MySQL 初始化脚本
├── Dockerfile                 # PHP 应用镜像定义
├── docker-compose.yml         # Docker Compose 配置
└── .dockerignore              # Docker 构建忽略文件
```

## 数据持久化

MySQL 数据通过 Docker Volume 持久化存储，即使删除容器数据也不会丢失。

如需完全清理数据，执行：
```bash
docker-compose down -v
```

## 端口配置

默认端口映射：
- **Web 服务**：`8080:80`（宿主机:容器）
- **MySQL**：`3306:3306`（宿主机:容器）

如需修改端口，编辑 `docker-compose.yml` 文件中的 `ports` 配置。

## 故障排查

### 1. 端口被占用

错误信息：`Bind for 0.0.0.0:8080 failed: port is already allocated`

解决方法：
- 修改 `docker-compose.yml` 中的端口映射
- 或停止占用端口的其他服务

### 2. 数据库连接失败

解决方法：
- 确保 MySQL 容器已完全启动（可能需要等待 10-20 秒）
- 检查数据库连接信息是否正确
- 查看 MySQL 容器日志：`docker-compose logs mysql`

### 3. 文件权限问题

错误信息：无法写入 `config/` 或 `uploads/` 目录

解决方法：
```bash
# 在宿主机上执行
chmod -R 777 config uploads data
```

### 4. 重装应用

如需重新安装，访问：
```
http://localhost:8080/setup/install.php?force=1
```

## 安全警告

**重要提示**：
- 本项目是安全漏洞演示靶场，包含大量安全漏洞
- 仅用于学习和测试目的
- 请勿在公网环境部署
- 建议在隔离的网络环境中运行
- 使用完毕后及时删除容器和数据

## 性能优化

### 开发环境
默认配置已针对开发环境优化，支持代码热重载。

### 生产环境模拟
如需模拟生产环境（不推荐，仅用于测试）：

1. 修改 `docker/php/php.ini`：
   ```ini
   display_errors = Off
   display_startup_errors = Off
   ```

2. 重启服务：
   ```bash
   docker-compose restart php-fpm
   ```

## 备份与恢复

### 备份数据库
```bash
docker-compose exec mysql mysqldump -u phpsec_user -pphpsec_pass phpsec_lab > backup.sql
```

### 恢复数据库
```bash
docker-compose exec -T mysql mysql -u phpsec_user -pphpsec_pass phpsec_lab < backup.sql
```

## 技术支持

如遇到问题，请检查：
1. Docker 和 Docker Compose 版本是否满足要求
2. 系统资源是否充足
3. 防火墙是否阻止了端口访问
4. 查看容器日志获取详细错误信息

## 许可证

本项目仅用于安全学习与审计实操示范。未经授权，禁止用于非法用途。
