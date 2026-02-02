# Laravel Docker Setup

## Getting Started

After cloning the repository, run the following commands:

### 1. Copy environment file

```bash
cp .env.example .env
```

### 2. Start Docker containers

```bash
docker-compose up -d
```

### 3. Install dependencies

```bash
docker-compose exec app composer install
```

### 4. Generate application key

```bash
docker-compose exec app php artisan key:generate
```

### 5. Run migrations

```bash
docker-compose exec app php artisan migrate
```

### 6. Access the application

Open your browser and go to:

```
http://localhost:8000
```

## Useful Commands

### Stop containers

```bash
docker-compose down
```

### View logs

```bash
docker-compose logs -f
```

### Access the app container

```bash
docker-compose exec app bash
```

### Run artisan commands

```bash
docker-compose exec app php artisan [command]
```

### Access PostgreSQL

```bash
docker-compose exec db psql -U laravel -d laravel
```

## Changing Database Credentials

If you want to use different database credentials:

1. Update the environment variables in `docker-compose.yml` under the `db` service
2. Update the corresponding values in your `.env` file
3. Restart the containers with `docker-compose down` and `docker-compose up -d`

## Troubleshooting

### Permission Issues

If you encounter permission issues:

```bash
docker-compose exec app chown -R www-data:www-data /var/www
docker-compose exec app chmod -R 755 /var/www/storage
```

### Clear Cache

```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan view:clear
```

### Rebuild Containers

If you make changes to the Dockerfile:

```bash
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```