# HTX Zero Fee Simulator

## Setup

composer install
npm install
cp .env.example .env
php artisan key:generate

php artisan serve
npm run dev

## Features

- Live HTX BTC pricing
- Free vs Pro fee simulation
- Rebate model
- Extended calculation mode
- Responsive UI

## Business Logic

HTX charges 0.1%
Broker rebate = 60%

Effective platform cost:
0.04%