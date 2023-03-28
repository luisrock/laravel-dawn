## Laravel Dawn

Starter pack with Laravel 10, Authentication by Laravel Breeze, Roles and Permissions by Spatie laravel-permission and basic admin UI (Tailwind CSS) for managing users, roles and permissions.

## Instructions

1. clone https://github.com/luisrock/laravel-dawn.git
2. mv laravel-dawn [newname]
3. valet secure [newname] 
(if not using valet, skip it)
4. cd [newname]
5. cp .env.example .env
6. nano .env (add db credentials)
7. composer install
8. npm install
9. php artisan migrate
10. php artisan key:generate
11. php artisan db:seed --class=RolesAndPermissionsSeeder
12. npm run dev

## Roles and Permissions

Initial roles and permissions: 
 - superadmin (all)
 - admin (manage_all, manage_users)
 - registered
 - subscriber
 - premium

 ## Superadmin credentials

- username: admin
- email: admin@admin.com
- password: abc12345

Enjoy!

## Security Vulnerabilities

If you discover a security vulnerability, please send an e-mail to Luis Rock via [trator70@gmail.com](mailto:trator70@gmail.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
