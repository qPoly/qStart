# qStart
qPoly's starter kit with a dashboard placeholder and user management including roles and permissions

## Installation
### Clone repo
```
git clone https://github.com/qPoly/qStart.git
cd qStart
```
### Install dependencies
```
composer install
npm ci
```
### Configure environment
```
copy .env.example .env
php artisan key:generate
```
### Run migrations, seed the database and start dev server
```
php artisan migrate --seed
composer run dev
```
### Multi-tenancy
This project supports multi-tenancy, allowing you to use it for multiple organizations. Each organization has isolated data, and only an administrator can manage organizations. See branch `multi-tenancy`.