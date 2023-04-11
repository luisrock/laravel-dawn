## Laravel Dawn

Starter pack with Laravel 10, Authentication by Laravel Breeze, Roles and Permissions by Spatie laravel-permission and basic admin UI (Tailwind CSS) for managing users, roles and permissions.

## Instructions

1. clone https://github.com/luisrock/laravel-dawn.git
2. mv laravel-dawn [newname]
3. valet secure [newname]
   (if not using valet, skip it)
4. cd [newname]
5. cp .env.example .env
6. nano .env (add db credentials and all necessary values)
7. composer install
8. npm install
9. php artisan migrate
10. php artisan key:generate
11. php artisan db:seed --class=RolesAndPermissionsSeeder
12. npm run dev

## Roles and Permissions

Initial roles and permissions:

-   superadmin (all)
-   admin (manage_all, manage_users)
-   registered
-   subscriber
-   premium

## Superadmin credentials

-   username: admin
-   email: admin@admin.com
-   password: abc12345

## What to do initially

-   If using Stripe, create one or more products.
-   Each product must have just one price. If set more than one, Laravel-Dawn will ignore the non-default ones.
-   To offer a different price for the same product, you can create a coupon on the Stripe dashboard
-   When creating a product on Stripe, you need to fill the metadata with
    role: {roleName}
    period_frequency: {year/month/day}
    period_number: {number of period_frequency}
-   Don't forget to create that role on the site admin area
-   Product visibility can be set on the site admin area
-   config/app: define timezone; locale etc.
-   Google credentials on .env (if wanna use Google Auth)
-   On th server, add the following cron:
    `* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1`
-   Translations by https://github.com/barryvdh/laravel-translation-manager
-   Run `php artisan translations:import` and go to `/translations`

## Artisan Custom

-   `php artisan user:change-role 3 registered` (param1: userId, param2: roleName)

Enjoy!

## Security Vulnerabilities

If you discover a security vulnerability, please send an e-mail to Luis Rock via [trator70@gmail.com](mailto:trator70@gmail.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
