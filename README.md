# EzRewards

## Installation

1. Configure environment variables

```bash
cp .env.example .env
```

2. Generate an application key

```bash
php artisan key:generate
```

3. Generate JWT secret key

```bash
php artisan jwt:secret
```

4. Run migrations

```bash
php artisan migrate
```