<?php require_once __DIR__ . '/core/init.php'; ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>PHP-Code-Sec 靶场</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav">
    <div class="brand">PHP<span class="accent">-Code</span>-Sec</div>
    <div class="links">
      <a href="/">首页</a>
      <a href="/profile.php">个人中心</a>
      <a href="/writeup.md" target="_blank">通关教程</a>
      <?php if (current_user()): ?>
        <span class="status">已登录：<?php echo h(current_user()['username']); ?></span>
        <a class="btn" href="/logout.php">退出</a>
      <?php else: ?>
        <a class="btn" href="/login.php">登录</a>
        <a class="btn" href="/register.php">注册</a>
      <?php endif; ?>
    </div>
  </header>

  <section class="hero container">
    <h1>PHP 代码审计靶场</h1>
    <p>覆盖常见审计知识点，离线部署。建议在隔离环境中操作。</p>
    <div class="panel">
      <div class="field">
        <label>筛选模块</label>
        <input id="module-filter" placeholder="输入关键字，如: SQL, XSS, SSRF..." />
      </div>
      <div class="tags">
        <span class="tag" data-tag="sql">SQL注入</span>
        <span class="tag" data-tag="xss">XSS</span>
        <span class="tag" data-tag="csrf">CSRF</span>
        <span class="tag" data-tag="upload">文件上传</span>
        <span class="tag" data-tag="fi">文件包含</span>
        <span class="tag" data-tag="path">路径遍历</span>
        <span class="tag" data-tag="cmd">命令注入</span>
        <span class="tag" data-tag="ssrf">SSRF</span>
        <span class="tag" data-tag="unserialize">反序列化</span>
        <span class="tag" data-tag="phar">PHAR</span>
        <span class="tag" data-tag="redirect">开放重定向</span>
        <span class="tag" data-tag="mail">邮件头注入</span>
        <span class="tag" data-tag="crypto">弱加密</span>
        <span class="tag" data-tag="auth">权限/会话</span>
        <span class="tag" data-tag="host">Host Header</span>
        <span class="tag" data-tag="logic">逻辑漏洞</span>
        <span class="tag" data-tag="xxe">XXE</span>
        <span class="tag" data-tag="rce">RCE</span>
        <span class="tag" data-tag="jwt">JWT</span>
        <span class="tag" data-tag="idor">IDOR</span>
        <span class="tag" data-tag="cors">CORS</span>
        <span class="tag" data-tag="hpp">HPP</span>
        <span class="tag" data-tag="write">任意写入</span>
        <span class="tag" data-tag="phpinfo">phpinfo</span>
        <span class="tag" data-tag="clickjack">点击劫持</span>
        <span class="tag" data-tag="typing">弱类型比较</span>
        <span class="tag" data-tag="override">变量覆盖</span>
        <span class="tag" data-tag="xpath">XPath</span>
        <span class="tag" data-tag="redos">ReDoS</span>
        <span class="tag" data-tag="wrapper">流包装器</span>
        <span class="tag" data-tag="ldap">LDAP</span>
        <span class="tag" data-tag="cache">缓存投毒</span>
        <span class="tag" data-tag="doubleparse">二次解析</span>
      </div>
    </div>
  </section>

  <main class="container">
    <div class="grid">
      <div class="card">
        <h3><span class="icon">🧪</span> SQL 注入</h3>
        <p>数字型 / 字符型 / 登录绕过 / 搜索注入</p>
        <p class="principle">原理：将用户输入拼接到 SQL 语句，改变语义执行。</p>
        <div class="actions"><a class="btn btn-primary" href="/modules/sqli/index.php">进入</a></div>
        <div class="meta" data-tags="sql,injection" data-group="输入注入"></div>
      </div>
      <div class="card">
        <h3><span class="icon">⚠️</span> XSS</h3>
        <p>反射型 / 存储型 / DOM 型</p>
        <p class="principle">原理：不可信输入进入 HTML/DOM，引入脚本在浏览器执行。</p>
        <div class="actions"><a class="btn btn-primary" href="/modules/xss/reflected.php">反射</a> <a class="btn" href="/modules/xss/stored.php">存储</a> <a class="btn" href="/modules/xss/dom.php">DOM</a></div>
        <div class="meta" data-tags="xss,web" data-group="Web交互"></div>
      </div>
      <div class="card">
        <h3><span class="icon">🎯</span> CSRF</h3>
        <p>邮箱修改演示，无令牌校验</p>
        <p class="principle">原理：跨站伪造请求利用登录态，缺少随机令牌校验。</p>
        <div class="actions"><a class="btn btn-primary" href="/modules/csrf/change_email.php">进入</a></div>
        <div class="meta" data-tags="csrf,web" data-group="Web交互"></div>
      </div>
      <div class="card">
        <h3><span class="icon">📤</span> 文件上传</h3>
        <p>扩展名校验不严，双扩展绕过</p>
        <p class="principle">原理：仅扩展名/后缀校验可绕过，上传可执行或恶意文件。</p>
        <div class="actions"><a class="btn btn-primary" href="/modules/upload/index.php">进入</a></div>
        <div class="meta" data-tags="upload,file" data-group="文件与路径"></div>
      </div>
      <div class="card">
        <h3><span class="icon">📑</span> 文件包含</h3>
        <p>LFI / RFI 示例</p>
        <p class="principle">原理：通过可控路径 include/require，包含本地或远程文件。</p>
        <div class="actions"><a class="btn btn-primary" href="/modules/fi/index.php">进入</a></div>
        <div class="meta" data-tags="fi,file" data-group="文件与路径"></div>
      </div>
      <div class="card">
        <h3><span class="icon">🧭</span> 路径遍历</h3>
        <p>任意文件读取演示</p>
        <p class="principle">原理：未规范化路径，允许 `../` 目录穿越读取任意文件。</p>
        <div class="actions"><a class="btn btn-primary" href="/modules/path/index.php">进入</a></div>
        <div class="meta" data-tags="path,file" data-group="文件与路径"></div>
      </div>
      <div class="card">
        <h3><span class="icon">⚙️</span> 命令注入</h3>
        <p>拼接系统命令（Windows ping）</p>
        <p class="principle">原理：将输入拼接到系统命令或 shell，触发命令执行。</p>
        <div class="actions"><a class="btn btn-primary" href="/modules/command/index.php">进入</a></div>
        <div class="meta" data-tags="cmd,injection" data-group="输入注入"></div>
      </div>
      <div class="card">
        <h3><span class="icon">🌐</span> SSRF</h3>
        <p>后端发起请求，内网探测</p>
        <p class="principle">原理：服务端抓取用户指定 URL，可访问内网/本机资源。</p>
        <div class="actions"><a class="btn btn-primary" href="/modules/ssrf/index.php">进入</a></div>
        <div class="meta" data-tags="ssrf,network" data-group="网络与服务"></div>
      </div>
      <div class="card">
        <h3><span class="icon">🧩</span> 反序列化</h3>
        <p>unserialize 对象注入 / __destruct</p>
        <p class="principle">原理：反序列化不可信数据触发魔术方法，执行攻击链。</p>
        <div class="actions"><a class="btn btn-primary" href="/modules/unserialize/index.php">进入</a></div>
        <div class="meta" data-tags="unserialize,injection" data-group="输入注入"></div>
      </div>
      <div class="card">
        <h3><span class="icon">📦</span> PHAR 注入</h3>
        <p>利用 getimagesize 等触发反序列化</p>
        <p class="principle">原理：读取 phar 路径时解析元数据，触发反序列化。</p>
        <div class="actions"><a class="btn btn-primary" href="/modules/phar/index.php">进入</a></div>
        <div class="meta" data-tags="phar,injection" data-group="输入注入"></div>
      </div>
      <div class="card">
        <h3><span class="icon">↪️</span> 开放重定向</h3>
        <p>未校验跳转目标</p>
        <p class="principle">原理：无白名单校验的跳转参数，可导向恶意站点。</p>
        <div class="actions"><a class="btn btn-primary" href="/modules/redirect/index.php">进入</a></div>
        <div class="meta" data-tags="redirect,web" data-group="Web交互"></div>
      </div>
      <div class="card">
        <h3><span class="icon">✉️</span> 邮件头注入</h3>
        <p>mail From 头拼接注入</p>
        <p class="principle">原理：将输入拼接到邮件头部，追加 BCC/篡改收件人。</p>
        <div class="actions"><a class="btn btn-primary" href="/modules/mail/index.php">进入</a></div>
        <div class="meta" data-tags="mail,web" data-group="网络与服务"></div>
      </div>
      <div class="card">
        <h3><span class="icon">🔑</span> 弱加密</h3>
        <p>MD5 / rand 令牌示例</p>
        <p class="principle">原理：弱散列或随机性不足，令牌可预测或撞库。</p>
        <div class="actions"><a class="btn btn-primary" href="/modules/crypto/index.php">进入</a></div>
        <div class="meta" data-tags="crypto,auth" data-group="认证与权限"></div>
      </div>
      <div class="card">
        <h3><span class="icon">🔐</span> 权限逻辑/会话</h3>
        <p>逻辑绕过 / 会话固定示例</p>
        <p class="principle">原理：缺少权限/所有权校验或不更新 Session ID 导致越权。</p>
        <div class="actions"><a class="btn btn-primary" href="/modules/auth/index.php">权限绕过</a> <a class="btn" href="/modules/session/demo_login.php">会话示例</a></div>
        <div class="meta" data-tags="auth,session" data-group="认证与权限"></div>
      </div>
      <div class="card">
        <h3><span class="icon">🪪</span> Host Header 注入</h3>
        <p>依赖不可信 Host 构造重置链接</p>
        <p class="principle">原理：信任 HTTP Host 生成链接，域名可被伪造用于钓鱼。</p>
        <div class="actions"><a class="btn btn-primary" href="/modules/host/index.php">进入</a></div>
        <div class="meta" data-tags="host,network" data-group="网络与服务"></div>
      </div>
      <div class="card">
        <h3><span class="icon">🧾</span> XXE</h3>
        <p>外部实体解析读取文件/内网</p>
        <p class="principle">原理：启用外部实体解析，读取本地文件或发起内网请求。</p>
        <div class="actions"><a class="btn btn-primary" href="/modules/xxe/index.php">进入</a></div>
        <div class="meta" data-tags="xxe,injection" data-group="输入注入"></div>
      </div>
      <div class="card">
        <h3><span class="icon">🧨</span> RCE</h3>
        <p>eval 执行用户输入的代码</p>
        <p class="principle">原理：对用户输入进行 eval/exec/include，导致远程代码执行。</p>
        <div class="actions"><a class="btn btn-primary" href="/modules/rce/eval.php">进入</a></div>
        <div class="meta" data-tags="rce,injection" data-group="输入注入"></div>
      </div>
      <div class="card">
        <h3><span class="icon">🎫</span> JWT 弱校验</h3>
        <p>接受伪造签名或 alg=none</p>
        <p class="principle">原理：签名校验弱或算法混淆，伪造令牌可被接受。</p>
        <div class="actions"><a class="btn btn-primary" href="/modules/jwt/index.php">进入</a></div>
        <div class="meta" data-tags="jwt,auth" data-group="认证与权限"></div>
      </div>
      <div class="card">
        <h3><span class="icon">🔓</span> IDOR 越权</h3>
        <p>通过 ID 读取他人敏感信息</p>
        <p class="principle">原理：仅凭资源 ID 访问，无所有权/权限校验导致越权。</p>
        <div class="actions"><a class="btn btn-primary" href="/modules/idor/user.php">进入</a></div>
        <div class="meta" data-tags="idor,auth" data-group="认证与权限"></div>
      </div>
      <div class="card">
        <h3><span class="icon">🔗</span> CORS 误配置</h3>
        <p>反射 Origin 并允许凭据</p>
        <p class="principle">原理：跨域信任过宽与允许凭据，敏感数据可被跨域读取。</p>
        <div class="actions"><a class="btn btn-primary" href="/modules/cors/index.php">进入</a></div>
        <div class="meta" data-tags="cors,web" data-group="Web交互"></div>
      </div>
      <div class="card">
        <h3><span class="icon">🔀</span> 参数污染 HPP</h3>
        <p>多值参数导致逻辑偏差</p>
        <p class="principle">原理：同名参数多值覆盖或取值差异导致逻辑偏差。</p>
        <div class="actions"><a class="btn btn-primary" href="/modules/hpp/index.php">进入</a></div>
        <div class="meta" data-tags="hpp,web" data-group="Web交互"></div>
      </div>
      <div class="card">
        <h3><span class="icon">✍️</span> 任意文件写入</h3>
        <p>路径遍历覆盖并落地 WebShell</p>
        <p class="principle">原理：路径可控并允许写入，任意位置生成文件甚至 WebShell。</p>
        <div class="actions"><a class="btn btn-primary" href="/modules/write/index.php">进入</a></div>
        <div class="meta" data-tags="write,file" data-group="文件与路径"></div>
      </div>
      <div class="card">
        <h3><span class="icon">📄</span> phpinfo 泄露</h3>
        <p>暴露环境、扩展与配置详情</p>
        <p class="principle">原理：调试信息页暴露系统版本、扩展、路径与环境变量。</p>
        <div class="actions"><a class="btn btn-primary" href="/modules/phpinfo/index.php">进入</a></div>
        <div class="meta" data-tags="phpinfo,config" data-group="网络与服务"></div>
      </div>
      <div class="card">
        <h3><span class="icon">🧠</span> 订单逻辑漏洞</h3>
        <p>前端价格可篡改，未后端校验</p>
        <p class="principle">原理：后端信任前端传入价格/参数，缺少服务端验证。</p>
        <div class="actions"><a class="btn btn-primary" href="/modules/logic/order.php">进入</a></div>
        <div class="meta" data-tags="logic,auth" data-group="认证与权限"></div>
      </div>
      <div class="card">
        <h3><span class="icon">🪟</span> 点击劫持</h3>
        <p>无 X-Frame-Options，易被覆盖点击</p>
        <p class="principle">原理：页面可被第三方 iframe 嵌入，覆盖敏感按钮诱导点击。</p>
        <div class="actions"><a class="btn btn-primary" href="/modules/clickjack/target.php">目标页</a> <a class="btn" href="/modules/clickjack/evil.html" target="_blank">恶意页</a></div>
        <div class="meta" data-tags="clickjack,web" data-group="Web交互"></div>
      </div>
      <div class="card">
        <h3><span class="icon">🧮</span> 弱类型比较</h3>
        <p>0e 魔法哈希与 == 比较绕过</p>
        <p class="principle">原理：`==` 自动类型转换导致 `0e...` 魔法哈希被误判相等。</p>
        <div class="actions"><a class="btn btn-primary" href="/modules/typing/index.php">进入</a></div>
        <div class="meta" data-tags="typing,auth" data-group="认证与权限"></div>
      </div>
      <div class="group-title">扩展模块</div>
      <div class="card">
        <h3><span class="icon">🧰</span> 变量覆盖（extract）</h3>
        <p>extract($_GET) 导致权限逻辑被覆盖</p>
        <p class="principle">原理：EXTR_OVERWRITE 覆盖同名关键变量，造成越权。</p>
        <div class="actions"><a class="btn btn-primary" href="/modules/override/index.php">进入</a></div>
        <div class="meta" data-tags="override,logic" data-group="逻辑漏洞"></div>
      </div>
      <div class="card">
        <h3><span class="icon">🔎</span> XPath 注入</h3>
        <p>拼接表达式绕过条件读取敏感节点</p>
        <p class="principle">原理：未参数化的 XPath 拼接导致条件绕过。</p>
        <div class="actions"><a class="btn btn-primary" href="/modules/xpath/index.php">进入</a></div>
        <div class="meta" data-tags="xpath,injection" data-group="输入注入"></div>
      </div>
      <div class="card">
        <h3><span class="icon">⏱️</span> 正则 ReDoS</h3>
        <p>灾难性回溯导致 CPU 飙高</p>
        <p class="principle">原理：嵌套量词触发指数级回溯，服务阻塞。</p>
        <div class="actions"><a class="btn btn-primary" href="/modules/redos/index.php">进入</a></div>
        <div class="meta" data-tags="redos,perf" data-group="性能与稳定性"></div>
      </div>
      <div class="card">
        <h3><span class="icon">📂</span> 流包装器泄露</h3>
        <p>php://filter Base64 读取源码</p>
        <p class="principle">原理：允许包装器前缀读取源码泄露敏感信息。</p>
        <div class="actions"><a class="btn btn-primary" href="/modules/wrapper/index.php">进入</a></div>
        <div class="meta" data-tags="wrapper,file" data-group="文件与路径"></div>
      </div>
      <div class="card">
        <h3><span class="icon">📚</span> LDAP 注入</h3>
        <p>过滤器拼接绕过检索约束</p>
        <p class="principle">原理：将输入拼接到 LDAP 过滤器导致结果被扩展。</p>
        <div class="actions"><a class="btn btn-primary" href="/modules/ldap/index.php">进入</a></div>
        <div class="meta" data-tags="ldap,network" data-group="网络与服务"></div>
      </div>
      <div class="card">
        <h3><span class="icon">🧪</span> Web 缓存投毒/欺骗</h3>
        <p>不一致的缓存键导致投毒</p>
        <p class="principle">原理：缓存键未包含变动参数/头，静态化动态响应。</p>
        <div class="actions"><a class="btn btn-primary" href="/modules/cache/index.php">进入</a></div>
        <div class="meta" data-tags="cache,web" data-group="Web交互"></div>
      </div>
      <div class="card">
        <h3><span class="icon">🧩</span> 二次解析</h3>
        <p>双重解码触发路径穿越/绕过</p>
        <p class="principle">原理：服务器与应用分别解码，导致安全校验失效。</p>
        <div class="actions"><a class="btn btn-primary" href="/modules/doubleparse/index.php">进入</a></div>
        <div class="meta" data-tags="doubleparse,encoding" data-group="解析与编码"></div>
      </div>
    </div>

    <div class="footer">
      仅供安全学习使用，请勿用于生产环境或非法用途。
    </div>
  </main>
  <script src="/assets/js/app.js"></script>
</body>
</html>