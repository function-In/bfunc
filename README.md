# bfunc

bfunc（全称：Bold Future Uncapped，寓意“无畏未来，不设上限”）是一个用于快速搭建 Web 站点的 PHP 框架项目。项目提供 PHP 后端基础结构，并包含可选的 React + Vite 前端开发环境。你可以根据需要使用传统 PHP 模板开发页面，也可以使用 React 开发后台或网站前端。

## 开发方案

项目的目标是支持以下几种 Web 站点和后台的开发、部署方式。它们是可选择的应用架构方向，并不要求在同一个项目中同时使用。当前仓库提供 PHP 与 React/Vite 的基础环境，各方案所需的业务路由、页面、API、静态资源部署和权限等功能，需要根据实际应用逐步实现。

如果你以服务端页面为主，选方案 1；如果后台需要 SPA 且与 PHP 同域部署，选方案 2；如果前端要独立部署到 CDN，选方案 3。

### 1. 传统 PHP 路由页面

使用 PHP 完成页面请求的处理和渲染，适合以服务端页面为主的站点：

```text
浏览器请求 -> PHP 路由 -> 控制器 -> 视图 -> PHP 模板 -> 返回页面
```

页面由 PHP 服务端生成和返回。开发这类页面时，可以使用下文的 BrowserSync 命令代理本地 PHP 站点，实现模板或相关文件变更后的浏览器刷新。

### 2. 本地部署的静态 SPA 页面

将 React 等前端构建为静态资源，部署在本地 PHP 站点中。后台页面由一个 PHP 入口路由承接，再由前端 SPA 负责页面切换和渲染：

```text
浏览器请求 -> 管理后台唯一 PHP 路由 -> SPA 页面
```

这种方式适合由 PHP 站点统一提供后台静态页面、并由 PHP 后端提供数据接口的场景。前端资源可通过 Vite 构建；具体的 PHP 路由回退、构建产物路径和 API 对接需要由应用配置。

### 3. 云端/CDN 部署的 SPA 页面

将后台前端构建产物部署到云端或 CDN，通过单独的后台网址或子域名访问；页面再通过 API 与 PHP 服务器交互：

```text
浏览器 -> 云端/CDN 上的后台 SPA -> API -> PHP 服务器
```

这种方式将前端页面和 PHP API 服务分开部署。实际使用时还需要配置 API 地址、跨域策略、身份验证及前端资源部署路径；本项目没有预设这些生产环境配置。

## 开发环境

请先安装以下工具：

- PHP >= 8.5
- Composer
- Node.js 和 npm（Composer 的 BrowserSync 命令通过 `npx` 启动）

在项目根目录安装 PHP 和 npm 依赖：

```bash
composer install
npm install
```

## PHP 页面开发

Composer 提供四条开发脚本，可根据本地环境选择使用：

| 命令 | 用途 |
| --- | --- |
| `composer run serve` | 使用 PHP 内置开发服务器，在 `0.0.0.0:8080` 提供项目页面 |
| `composer run browser` | 启动 BrowserSync，代理到 `localhost:8080` 并监视文件变化 |
| `composer run dev` | 同时启动 `serve` 和 `browser`，用于没有现成 PHP Web 服务器的情况 |
| `composer run bfunc` | 启动 BrowserSync，代理到固定目标 `bfunc.test`（命令名沿用项目名，与代理目标同名但含义不同） |

### 已有本地 Web 服务器

`bfunc.test` 是项目默认配置中固定的代理目标，供作者的本地开发环境使用。使用 `composer run bfunc` 时，不要为了适配自己的站点去修改 `composer.json` 中的默认目标。若你有自己的 Apache、Nginx 或其他本地 PHP Web 开发环境，可以在该环境中为本项目新增一个名为 `bfunc.test` 的本地站点（包括相应的虚拟主机和本地域名解析），并确认直接访问该站点可以正常显示项目页面，然后运行：

```bash
composer run bfunc
```

该命令会通过 BrowserSync 代理到 `bfunc.test`，并监视 `public/`、`bfunc/` 和 `src/` 目录中的文件。BrowserSync 默认会打开浏览器；开发时请使用它提供的 `http://localhost:3000` 地址，以获得文件变更后的自动刷新。

没有本地 Web 服务器时不要用这条命令，请改用 `composer run dev`

### 没有本地 Web 服务器

如果尚未搭建自己的本地 PHP Web 服务器，可以先后在两个终端分别运行：

```bash
composer run serve
```

```bash
composer run browser
```

`serve` 会启动 PHP 内置开发服务器，`browser` 会通过 BrowserSync 代理到它。也可以一步启动两项服务：

```bash
composer run dev
```

BrowserSync 默认会打开浏览器；如果没有自动打开，请访问 `http://localhost:3000`。此地址由 BrowserSync 提供，并代理到 `serve` 启动的 `localhost:8080`。上述方式同样会监视 `public/`、`bfunc/`、`src/` 目录中的文件变化并刷新页面。没有自己的 Web 开发服务器时，使用这种方式即可，无需改动 `bfunc.test` 这一默认代理目标。

## React / Vite 开发

项目包含 React + Vite 环境，可用于开发后台管理系统或网站前端。在项目根目录执行：

```bash
# 启动 Vite 开发服务器
npm run dev

# 构建生产环境静态资源
npm run build

# 本地预览生产构建结果
npm run preview

# 运行 ESLint
npm run lint
```

Vite 会在终端输出开发服务器地址。`npm run build` 生成的文件位于 `dist/`。如需将构建产物部署到 PHP 站点或 CDN，请按相应部署环境配置静态资源路径和 API 地址；当前项目未预设具体的前后端集成或部署流程。构建产物如何部署到 PHP 站点或 CDN，参见上文“开发方案”中的方案 2 和方案 3。

`composer run dev` 和 `npm run dev` 用途不同：前者启动 PHP 内置服务器并通过 BrowserSync 代理，后者启动 React/Vite 开发服务器。需要同时开发 PHP 页面和 React 前端时，可以在不同终端分别运行。
