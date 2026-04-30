# LISTA — Band Genealogy Directory

## Session Log
| Date | Agent | Description |
|------|-------|-------------|
| 2026-04-30 01:45 | CLI | Grid/layout optimization + home JSON-LD fix. All routes 200. |
| 2026-04-30 13:45 | CLI | Installed Laravel Boost v2.4.6. MCP server OK (9 tools). |
| 2026-04-30 14:45 | CLI | P0 concluído: Labels, Tags, SEO titles, Contador membros, Export CSV, Filtros combinados + DB indexes. Meta tags escaping fix. |
| 2026-04-30 15:30 | CLI | P1 concluído: Soft deletes, /genres, Albums + discografia, Dashboard chart. |
| 2026-04-30 16:00 | CLI | P2 concluído: Audit log, User roles, API REST, Timeline, Clusters, Tooltip, Fullscreen. All routes 200. |
| 2026-04-30 16:30 | CLI | P3 concluído: Comentários, Sugestões, Moderation, Blog, i18n, lazy loading, vis.js async. All routes 200. |
| 2026-04-30 17:15 | CLI | Auto-WebP, Hero SVG animado, Dark mode suave, Genealogy refactor (clusters/hierarchical/focus), Like/favorite, Fotos nas listagens, Stats home, Lightbox, Breadcrumb component. All routes 200. |

## Stack
- **Laravel 13** + PHP 8.4
- **MySQL** (MariaDB)
- **Filament v5** (admin)
- **Tailwind v4** + **Flux UI** (frontend)
- **vis-network** (genealogy graph)
- **Alpine.js** (interactivity)
- **Livewire v4** (search)
- **Docker** (Sail, dev local)

## Dev Environment
```bash
sail up -d
sail artisan make:migration
sail composer require PACKAGE
sail npm install && sail npm run build
```

### Cache bug (__PHP_Incomplete_Class)
```bash
sail bash -c 'rm -rf storage/framework/cache'
sail artisan cache:clear
```

## Admin
`http://localhost/admin` — `admin@lista.site` / `1234`

4 resources: Bands, Artists, Memberships (pivot), Relationships.
Import CSV via BandImporter / ArtistImporter.
Relation Managers inline na edit page.

## Routes
```
GET  /                    → Home
GET  /bands               → Lista + filtros
GET  /bands/{slug}        → Detail + graph
GET  /artists             → Lista
GET  /artists/{slug}      → Detail + timeline
GET  /genealogy           → Grafo completo
GET  /api/bands/{slug}/graph   → JSON
GET  /api/search?q=       → JSON busca
GET  /sitemap.xml         → Sitemap
```

## Design Tokens
- `brand-*`: Emerald — bands, CTAs
- `accent-*`: Purple — artists
- `warm-*`: Amber — relationships
- `surface-*`: Warm gray — bg, borders, text
- Dark mode via Flux + localStorage

## Cronograma de Melhorias

### P0 — Próximos

### P1 — Curto prazo
- [ ] 

### P2 — Médio prazo
- [ ] 

### P3 — Longo prazo
- [ ] 

### Prod
- [ ] Cache: reativar Cache::remember (file store) — ativar só em producao
- [ ] config:cache + route:cache + view:cache

### ✅ Concluído
- [x] Vite minify (já ativo)
- [x] system-ui font (sem Google Fonts extra)
- [x] Laravel Boost v2.4.6 instalado + MCP 9 tools
- [x] Labels/gravadoras: migration + admin + frontend + filtro
- [x] Tags comunitárias: polymorphic (Band + Artist) + moderation (is_approved)
- [x] SEO titles automáticos: SeoData value object em todas as views
- [x] Contador de membros na listagem (withCount + badge)
- [x] Export CSV admin (Filament BandExporter + ArtistExporter)
- [x] Filtros combinados: genre + year + origin + DB indexes
- [x] Soft deletes (migration + Filament trash + restore)
- [x] Páginas /genres/{slug}
- [x] Albums: migration + model + admin + discografia frontend
- [x] Dashboard: gráfico bands por genre (ChartWidget)
- [x] Audit log (migration + Auditable trait + admin viewer)
- [x] User roles (admin, editor, viewer) + middleware
- [x] API pública REST (6 endpoints: bands, artists, genres, labels)
- [x] Timeline interativa no artist show
- [x] Clusters no genealogy graph (colorir por gênero)
- [x] Tooltip rico (anos, gênero, papel)
- [x] Fullscreen no genealogy
- [x] Comentários (comunidade) — polymorphic + admin moderation + frontend form
- [x] Sugestões de edição por usuários — migration + model + admin CRUD + frontend form
- [x] Moderation queue — Filament page (pending comments, tags, suggestions)
- [x] Blog/notícias (SEO bait) — migration + admin CRUD + frontend
- [x] i18n — lang files published
- [x] lazy loading imagens — já implementado
- [x] vis.js CDN async (só genealogy) — `defer` adicionado
- [x] Imagens otimizadas (auto-WebP) — ImageOptimizer service + command `images:optimize`
- [x] Genealogy refactor — endpoint único, clusters, hierarchical, hover labels, focus
- [x] Like/favorite system — migration + toggle + frontend ♡/♥ + page /favorites
- [x] Fotos nas listagens — thumbnails 48×48 nos cards de bands/artists/home
- [x] Stats bar na home — 4 contadores (bands, artists, memberships, relationships)
- [x] Hero visual + SVG animado na home — banner aleatório + linhas genealógicas animadas
- [x] Lightbox Alpine.js — galeria clicável com overlay fullscreen
- [x] Breadcrumb component — `<x-breadcrumb>` reutilizável
- [x] Dark mode suave — transição CSS no toggle
- [x] Hero gradient por gênero — gradiente temático quando não há hero_image
- [x] Security: Purifier (XSS) — `stevebauman/purify` em todos outputs Markdown
- [x] Security Headers — CSP, HSTS, X-Frame-Options, X-Content-Type-Options
- [x] Session encrypt — `SESSION_ENCRYPT=true`
- [x] Upload limits — `maxSize(5120)` + `mimeTypes` em todos FileUpload
- [x] User registration — `/register` com validação + nav link
- [x] Favorites page — `/favorites` listando bands/artists favoritados
- [x] Edit suggestion frontend — formulário na detail page da banda
- [x] `.env.production.example` — template com `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`
- [x] Rate limit admin — `throttle:10,1` no Filament panel
- [x] GitHub — repo `thiagogaspar/lista` com 3 commits pusheados

### Deploy Hostinger
1. git push ou FTP p/ public_html
2. composer install --no-dev --optimize-autoloader
3. cp .env.example .env + configurar DB
4. php artisan key:generate
5. npm run build
6. php artisan migrate --force
7. php artisan config:cache && route:cache && view:cache
8. php artisan storage:link
9. Cron: php /path/to/artisan sitemap:generate daily

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.5
- filament/filament (FILAMENT) - v5
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
- livewire/flux (FLUXUI_FREE) - v2
- livewire/livewire (LIVEWIRE) - v4
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- phpunit/phpunit (PHPUNIT) - v12
- tailwindcss (TAILWINDCSS) - v4

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `vendor/bin/sail npm run build`, `vendor/bin/sail npm run dev`, or `vendor/bin/sail composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `vendor/bin/sail artisan route:list`). Use `vendor/bin/sail artisan list` to discover available commands and `vendor/bin/sail artisan [command] --help` to check parameters.
- Inspect routes with `vendor/bin/sail artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `vendor/bin/sail artisan config:show app.name`, `vendor/bin/sail artisan config:show database.default`. Or read config files directly from the `config/` directory.
- To check environment variables, read the `.env` file directly.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `vendor/bin/sail artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `vendor/bin/sail artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== sail rules ===

# Laravel Sail

- This project runs inside Laravel Sail's Docker containers. You MUST execute all commands through Sail.
- Start services using `vendor/bin/sail up -d` and stop them with `vendor/bin/sail stop`.
- Open the application in the browser by running `vendor/bin/sail open`.
- Always prefix PHP, Artisan, Composer, and Node commands with `vendor/bin/sail`. Examples:
    - Run Artisan Commands: `vendor/bin/sail artisan migrate`
    - Install Composer packages: `vendor/bin/sail composer install`
    - Execute Node commands: `vendor/bin/sail npm run dev`
    - Execute PHP scripts: `vendor/bin/sail php [script]`
- View all available Sail commands by running `vendor/bin/sail` without arguments.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `vendor/bin/sail artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `vendor/bin/sail artisan list` and check their parameters with `vendor/bin/sail artisan [command] --help`.
- If you're creating a generic PHP class, use `vendor/bin/sail artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `vendor/bin/sail artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `vendor/bin/sail artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `vendor/bin/sail npm run build` or ask the user to run `vendor/bin/sail npm run dev` or `vendor/bin/sail composer run dev`.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/sail bin pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/sail bin pint --test --format agent`, simply run `vendor/bin/sail bin pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `vendor/bin/sail artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should cover all happy paths, failure paths, and edge cases.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

## Running Tests

- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `vendor/bin/sail artisan test --compact`.
- To run all tests in a file: `vendor/bin/sail artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `vendor/bin/sail artisan test --compact --filter=testName` (recommended after making a change to a related file).

</laravel-boost-guidelines>
