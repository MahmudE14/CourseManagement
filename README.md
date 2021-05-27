## Course Management System

### Installation
You can upload the added database `course_management.sql`

##### Or,
you can follow the **installation guideline** below:
- Create a `.env` file if one is not already there
- Copy `.env.example` contents in `.env`
- Change the following accordingly:
    - Set `APP_URL` to your hosting domain
    - Set `DB_DATABASE` to your hosting database name
    - Set `DB_USERNAME` to your `username` associated with the database
    - Set `DB_PASSWORD` to your `password` associated with the database
- Run this commnd in your terminal `php artisan migrate`
- Then run the following in the terminal `php artisan db:seed`

#### Admin credentials
- Login link: `{YOUR_DOMAIN}/login`
- E-Mail Address: `admin@mail.com`
- Password: `12345678`
