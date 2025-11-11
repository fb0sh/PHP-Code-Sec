# 模块源代码索引（Summary）

> 便于新手快速定位各漏洞模块的源代码位置进行分析与学习。

## 安装与核心
- `setup/install.php` — 安装向导（生成配置与初始化数据库）
- `core/init.php` — 初始化、会话、通用函数（如 `h()`、`current_user()`）
- `core/db.php` — 数据库封装与连接
- `config/config.inc.php` — 安装生成的数据库配置

## 认证与用户
- `login.php` — 演示弱口令、SQL 注入与会话固定（不 regenerate）
- `register.php` — 演示弱加密 MD5 与注入风险
- `logout.php` / `profile.php` — 退出与个人中心

## SQL 注入
- `modules/sqli/index.php` — 数字型/字符型/搜索注入示例
- `login.php` / `register.php` — 登录与注册语句拼接（易注入）

## XSS
- `modules/xss/reflected.php` — 反射型 XSS
- `modules/xss/stored.php` — 存储型 XSS（留言）
- `modules/xss/dom.php` — DOM 型 XSS（将 `location.hash` 注入 `innerHTML`）

## CSRF
- `modules/csrf/change_email.php` — 通过 GET 直接改邮箱，无 CSRF Token

## 文件上传
- `modules/upload/index.php` — 仅扩展名校验（双扩展绕过），无 MIME/内容校验

## 文件包含 LFI/RFI
- `modules/fi/index.php` — 未白名单 `include`，可 LFI/RFI（配置允许时）
- `pages/home.php` / `pages/about.php` — 演示包含用页面

## 路径遍历
- `modules/path/index.php` — 通过 `file` 参数读取任意文件
- `data/notes.txt` — 示范数据文件

## 命令注入
- `modules/command/index.php` — Windows `ping` 拼接命令

## SSRF
- `modules/ssrf/index.php` — 后端使用 cURL 请求任意 URL 并回显

## 反序列化 / PHAR
- `modules/unserialize/index.php` — 不安全反序列化与 `__destruct` 执行
- `modules/phar/index.php` — `getimagesize` 触发 PHAR 元数据反序列化

## 开放重定向
- `modules/redirect/index.php` — 直接跳转到用户提供的 URL

## 邮件头注入
- `modules/mail/index.php` — 将 `from` 直接拼接到邮件头，允许 CRLF 注入

## 弱加密与弱随机
- `modules/crypto/index.php` — `md5 + rand` 生成可预测令牌

## 权限逻辑漏洞
- `modules/auth/index.php` — 通过 `?admin=1` 绕过并访问敏感信息

## 会话固定
- `modules/session/demo_login.php` — 登录后不重新生成会话 ID
- 相关：`login.php` — 登录逻辑中无 `session_regenerate_id(true)`

## Host Header 注入
- `modules/host/index.php` — 利用 `HTTP_HOST` 构造密码重置链接
- `reset.php` — 重置链接示范页（提示修复方案）

## 订单逻辑漏洞
- `modules/logic/order.php` — 前端可篡改价格，后端未校验

## XXE 注入
- `modules/xxe/index.php` — `DOMDocument` 开启实体解析与 DTD 加载，读取文件/内网

## RCE（代码执行）
- `modules/rce/eval.php` — 直接 `eval` 执行用户输入的 PHP 代码

## JWT 弱校验
- `modules/jwt/index.php` — 伪造签名或 `alg=none` 仍被接受

## IDOR 越权访问
- `modules/idor/user.php` — 通过 `id` 读取他人信息，无所有权校验

## CORS 误配置
- `modules/cors/index.php` — 反射任意 `Origin` 并允许凭据，返回敏感 JSON

## 参数污染 HPP
- `modules/hpp/index.php` — 多值参数导致逻辑偏差（价格/角色）

## 任意文件写入
- `modules/write/index.php` — 允许路径遍历与覆盖，写入任意文件（甚至 WebShell）

## phpinfo 敏感信息泄露
- `modules/phpinfo/index.php` — 暴露环境与配置详情

## 点击劫持
- `modules/clickjack/target.php` — 未设置防护头的目标页
- `modules/clickjack/evil.html` — 恶意覆盖点击的演示页（透明 iframe）

## 弱类型比较（魔法哈希）
- `modules/typing/index.php` — 使用 `==` 比较触发 `0e...` 绕过与魔法哈希示例

## 变量覆盖（extract）
- `modules/override/index.php` — `extract($_GET)` 导致覆盖关键变量并权限绕过

## XPath 注入
- `modules/xpath/index.php` — 直接拼接用户输入到 XPath 表达式，条件绕过与读取敏感节点

## 正则 ReDoS
- `modules/redos/index.php` — 含嵌套量词的正则在特定输入上触发灾难性回溯

## PHP 流包装器信息泄露
- `modules/wrapper/index.php` — 使用 `php://filter` Base64 读取源码导致信息泄露

## LDAP 注入
- `modules/ldap/index.php` — 将输入拼接到 LDAP 过滤器，扩展检索结果（如管理员）

## Web 缓存投毒与欺骗
- `modules/cache/index.php` — 缓存键未包含影响响应的参数/头部，导致被投毒

## 二次解析（双重解码）
- `modules/doubleparse/index.php` — 双重解码绕过校验，拼接路径导致穿越读取