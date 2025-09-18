# Cutlery

**Set the table first.**  
A minimalist, light-weight PHP framework.

Designed to be as simple as possible, while still providing the essentials for building web applications.

---

## Quick start

Create a new project:

```bash
composer create-project knorkfork/cutlery-app myapp
cd myapp
php -S 127.0.0.1:8080 -t public
```

## Framework development

```bash
docker-compose up -d --build

docker/composer install
```

Run tests:

```bash
docker/phpunit
```

-------------

## dev only

to-do:
- add tests for commands
- add migratecommand to db package
- load the rest from https://github.com/knork-fork/zet-gtfs-backend - done?
- publish this repo to Packagist as knorkfork/cutlery-app
- add packages/ to .gitattributes and publish packages to packagist, remove from root/composer.json autoload

monorepo layout:

```
cutlery/                      # <- this repo (published to Packagist as knorkfork/cutlery-app)
├─ composer.json              # type: project (the *skeleton* package)
├─ public/
│  └─ index.php               # tiny entrypoint calling Cutlery\Kernel
├─ src/                       # App\* starter code (optional)
├─ config/
│  └─ routes.yaml             # commented template (created by installer if missing)
├─ bin/
│  ├─ installer               # post-create script (idempotent)
│  └─ init-docker             # copies stubs/docker/* to root if missing
├─ packages/
│  ├─ cutlery-framework/
│  │  └─ src/…                # Cutlery\* (env, kernel, router glue, etc.)
│  └─ cutlery-db-postgres/
│     └─ src/…                # Cutlery\Postgres\*
├─ stubs/
│  ├─ config/
│  │  └─ database.yaml
│  └─ docker/
│     ├─ Dockerfile
│     ├─ docker-compose.yml
│     └─ .dockerignore
├─ tests/
│  └─ …                       # your test suite (autoload-dev)
├─ .gitattributes             # controls what’s included in dist (see below)
├─ .gitignore
└─ LICENSE / README.md
```

what the user gets after composer create-project:

```
myapp/
├─ composer.json              # copied from your root (same package)
├─ vendor/                    # installed deps
├─ public/
│  └─ index.php               # from your repo
├─ src/                       # from your repo
├─ config/
│  └─ routes.yaml             # exists (or created by installer)
├─ bin/
│  ├─ installer               # from your repo
│  └─ init-docker             # from your repo
├─ var/                       # created by installer (writable)
└─ .env                       # created by installer from .env.example (if you ship one)
```