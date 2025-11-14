# Copilot / AI agent instructions for the `maturita` project

These notes are intended to help an AI assistant be productive quickly in this repository.
They describe the high-level architecture, common patterns, important files, and gotchas discovered by reading the code.

1) Big-picture
- This is a small PHP MVC-style CMS with two public entrypoints:
  - `index.php` (root) routes requests to `/admin` or to `web/index.php` for the public site.
  - `admin/index.php` is the admin-router and contains the route map (path -> [Controller::class, method]).

2) Autoloading and namespaces
- A simple PSR-4-like autoloader is in `config/autoloader.php`. Classes under namespace `App\` map to files under `app/`.
  - Example: `App\Controllers\Admin\HomeAdminController` -> `app/Controllers/Admin/HomeAdminController.php`.

3) Routing and controllers
- Routes are defined in `admin/index.php` as an associative array where keys are path patterns (plain or regex) and values are [ControllerClass, method].
  - Regex style examples: `articles/edit/(\d+)` maps to `ArticleAdminController::edit` and the captured group is passed as the first arg.
  - Controllers are instantiated with a PDO DB connection: `new $controllerClass($db)`.

4) Database and models
- DB connection lives in `config/db.php` and returns a PDO instance. Models accept the PDO in their constructor (e.g., `new User($db)`).
  - Use prepared statements everywhere (models use PDO prepare/execute).
  - Several helpers and models assume the DB schema exists (see `config/*.sql` files under `config/` and `web/`).

5) Views and render pattern
- Controllers set simple variables (e.g., `$disableNavbar`, `$css`, `$adminTitle`, `$view`) and then include `app/Views/Admin/layout/base.php`.
  - Views are plain PHP templates (no templating engine). Use the same include pattern when adding pages.

6) Security & middleware
- Auth is session-based: `AuthMiddleware::check($db)` is called at admin entrypoint. Many controllers call `session_start()` if needed.
- CSRF protection is implemented using `App\\Helpers\\CSRFHelper` — controller actions validate tokens with `CSRFHelper::checkPostToken()`.

7) Common patterns & pitfalls
- Relative includes: many files use relative paths (e.g., `include '../app/Views/Admin/layout/base.php'`). Maintain the same relative layout when moving files.
- Routing uses string patterns trimmed from `$_SERVER['REQUEST_URI']` — be careful with leading/trailing slashes and query strings.
- Passwords use `password_hash`/`password_verify` (see `app/Models/User.php` and `LoginController.php`).

8) Developer workflows (how to run locally)
- There are no automated build scripts. Quick local run using PHP built-in server (from project root):

  php -S localhost:8000

  Then open http://localhost:8000/ (or /admin for admin UI).

- Note: the project expects to run with the project root as document root because `index.php` dispatches to `web` or `admin`.

9) Sensitive data
- `config/db.php` currently contains real DB credentials. Avoid printing or committing secrets. When creating tests or samples, mock PDO or use environment-based configuration.

10) Files to inspect when asked about specific features
- Routing and admin logic: `admin/index.php`
- Public site: `web/index.php` and files under `web/`
- Autoloading: `config/autoloader.php`
- DB config: `config/db.php` and SQL under `config/*.sql` and `web/*.sql`
- Controllers: `app/Controllers/` (Admin and Web subfolders)
- Models: `app/Models/` (data access and common DB queries)

11) Examples to reference when making changes
- Add a new admin route: update `$routes` in `admin/index.php` with a path and controller class; implement controller under `app/Controllers/Admin/` and view under `app/Views/Admin/` following existing patterns.
- Use `$db = (new Database())->connect();` to get the PDO instance in entry scripts.

12) What NOT to change lightly
- The autoloader and file structure (namespace -> app/ folder mapping). Keep `App\` namespace mapping intact.
- The routing entrypoint `admin/index.php` contains important session and middleware checks. Preserve the order of middleware and the `AuthMiddleware::check($db)` call.

If anything in these notes is unclear or you'd like additional examples (e.g., how to add a REST endpoint, how to run a lightweight unit test, or which SQL to run to reproduce data), tell me what to expand and I will update this file.
