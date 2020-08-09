# Laravel Vue (SPA) Admin Panel

## Features

- Uses [architectui-vue-theme-free](https://github.com/DashboardPack/architectui-vue-theme-free)
- Datatable with server side pagination, searching, sorting and filtering
- OTP verification at login
- OTP types
    - Pin
    - Mail
    - SMS
    - [Google2fa](https://github.com/antonioribeiro/google2fa-laravel)
- Client (Browser) and IP tracking capability
- Allow a fixed number of clients to access app
- Allow a single IP address to access app

## Installation

- git clone `https://github.com/rahamatj/laravel-vue-admin.git`
- `cd laravel-vue-admin`

### Server setup

- `cd server && composer install`
- `cp .env.example .env`
- Create database and update `server/.env`
- `cd server && php artisan migrate --seed`
- Set server URL in `server/.env` (If not set properly APIs won't work in production)

```
APP_URL=http://localhost/laravel-vue-admin/server/public
```

- (Optional) Set SMS API in `server/.env`
    - e.g: `SMS_API=https://sms-api.com?to={to}&message={message}`
    - SMS API must have `{to}` and `{message}` in the string
- Run queue `cd server && php artisan queue:work` or set `QUEUE_CONNECTION=sync` if you don't want queue

### Client setup

- `cd client && npm install`
- Set your API URL in `client/utils/app.js`

```
apiUrl: process.env.NODE_ENV === 'production'
            ? window.apiUrl
            : 'http://localhost/laravel-vue-admin/server/public',
```

- `cd client && npm run serve`

### Credentials

- Default login email: `admin@email.com`
- Default login password: `12345678`