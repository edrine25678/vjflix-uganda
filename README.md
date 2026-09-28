<a href="https://github.com/josuapsianturi/velflix"> <h1 align="center">Velflix</h1></a>
<p align="center"><a href="https://github.com/josuapsianturi/velflix/blob/main/LICENSE"><img src="https://poser.pugx.org/cpriego/valet-linux/license.svg" alt="License"></a>
</p>

## About

Velflix is a Laravel [Netflix](https://netflix.com) clone project using TALL stack ([Tailwindcss](https://tailwindcss.com/), [Alpinejs](https://github.com/alpinejs/alpine/), [Laravel](https://laravel.com/), [Livewire](https://laravel-livewire.com/) ).

> **Note**
> Work in Progress

## Table of Contents

* [Screenshots](#screenshots)
* [Requirements](#requirements)
* [Installation](#installation)
* [Testing](#testing)
* [Contributing](#contributing)
* [License](#license)

<a name="screenshots"></a>
## Screenshots

![home page](https://raw.githubusercontent.com/josuapsianturi/velflix/main/public/img/home.png)

see full page [here](https://raw.githubusercontent.com/josuapsianturi/velflix/main/public/img/home-full-page.png)

![movies header](https://raw.githubusercontent.com/josuapsianturi/velflix/main/public/img/movies-header.png)

![movies](https://raw.githubusercontent.com/josuapsianturi/velflix/main/public/img/movies.png)

see full page [here](https://raw.githubusercontent.com/josuapsianturi/velflix/main/public/img/movies-full-page.png)

![Detail movies](https://raw.githubusercontent.com/josuapsianturi/velflix/main/public/img/details-movie.png)

<a name="features"></a>
## Features

### Content & Streaming
- Movie and series catalog with Luganda translations
- Video streaming with progress tracking
- Episode management for TV series
- Genre-based browsing
- Search functionality

### User Experience
- Personalized AI recommendations
- Watchlist management
- Reviews and ratings system
- Watch history tracking
- User profiles

### Monetization
- Subscription plans (Free, Basic, Premium, Annual)
- MTN Mobile Money integration
- Airtel Money integration
- Payment status tracking
- Subscription management

### Analytics
- Platform statistics dashboard
- User analytics
- Content performance metrics
- Revenue tracking
- Daily active users monitoring

### Admin Features
- Content management (movies, series, episodes)
- VJ profile management
- Genre management
- Subscription plan management
- Payment tracking
- Analytics dashboard

<a name="requirements"></a>
## Requirements

Package | Version
--- | ---
[Node](https://nodejs.org/en/) | V14.19.1+
[Npm](https://nodejs.org/en/)  | V6.14.16+ 
[Composer](https://getcomposer.org/)  | V2.2.6+
[Php](https://www.php.net/)  | V8.0.17+
[Mysql](https://www.mysql.com/)  |V 8.0.27+

<a name="installation"></a>
## Installation

> **Warning**
> Make sure to follow the requirements first.

Here is how you can run the project locally:
1. Clone this repo
    ```sh
    git clone https://github.com/josuapsianturi/velflix.git
    ```

1. Go into the project root directory
    ```sh
    cd velflix
    ```

1. Copy .env.example file to .env file
    ```sh
    Tp .env.example .env:
   - 
    ``- 
- assword
    - Roles (super_admin, content_manager, vj_manager, subscriber, user)
    - Lnguage (Englih, Luganda, Sahili)
    - Genres (Actin, Comedy, Drama, Horor, Romance, Thriller)
    - VJs (Ugandan narrators)
    - Sample movies an series
    - Subscription plans (Free, Basic, Premium, Annual)
1. Create account and get an API key themoviedb [ here](https://www.themoviedb.org/settings/api). Make sure to copy `API Read Access Token (v4 auth)`.

1. Go to `.env` file 
    - set database credentials (`DB_DATABASE=velflix`, `DB_USERNAME=root`, `DB_PASSWORD=`)
    - paste `TMDB_TOKEN=(your API key)` 
    > Make sure to follow your database username and password

1. Install PHP dependencies 
    ```sh
    composer install
    ```

1. Generate key 
    ```sh
    php artisan key:generate
    ```

1. install front-end dependencies
    ```sh
    npm install && npm run build
    ```

1. Run migration
    ```
    php artisan migrate
    ```
    
1. Run seeder
    ```
    php artisan db:seed
    ```
    this command will create 2 users (admin and normal user):
     > email: admin@gmail.com , password: password

     > email: user@gmail.com , password: password 

1. Run server 
    > for valet users visit `velflix.test` in your favorite browser
   
    ```sh
    php artisan serve
    ```  

1. Visit `localhost:8000` in your favorite browser.     

    > Make sure to follow your Laravel local Development Environment.

1. Newsletter feature configuration (optional)
 - Go to [mailchimp](https://mailchimp.com)
 - Register your account, get API key and paste it into `.env` file. If you need help, you can follow these steps:
    - Click Sign Up Free
    - Enter your data, check your email and verify
    - select Free, Next
    - Do you have a list of contacts? (NO)
    - Do you sell products or services online? (Neither, Products)
    - continue
 - Go to Profile > Extras > API keys
 - Create a key and copy API key
 - open the velflix project, go to `.env` file and paste it into `MAILCHIMP_KEY=paste API key here`
 - Go to configuration"></a>
## Configuration

### Payment Gateway Configuration

For production payment integration, configure the following in `.env`:

```env
# MTN Mobile Money
MTN_MOMO_API_KEY=your_api_key
MTN_MOMO_API_SECRET=your_api_secret
MTN_MOMO_ENVIRONMENT=production
MTN_MOMO_CALLBACK_URL=https://yourdomain.com/payments/callback/mtn

# Airtel Money
AIRTEL_MONEY_CLIENT_ID=your_client_id
AIRTEL_MONEY_CLIENT_SECRET=your_client_secret
AIRTEL_MONEY_ENVIRONMENT=production
AIRTEL_MONEY_CALLBACK_URL=https://yourdomain.com/payments/callback/airtel
```

> **Note**: Payment services are currently in sandbox mode. Configure with real credentials for production.

<a name="environment-variables"></a>
## Environment Variables

Key environment variables to configure:

```env
APP_NAME=VJFlix
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=pgsql
DB_HOST=your_db_host
DB_PORT=5432
DB_DATABASE=velflix
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password

# TMDB API
TMDB_TOKEN=your_tmdb_api_token

# Mailchimp (optional)
MAILCHIMP_KEY=your_mailchimp_key
MAILCHIMP_LIST_SUBSCRIBERS=your_list_id

# Google Socialite (optional)
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT=https://yourdomain.com/login/google/callback

# Payment Gateways
MTN_MOMO_API_KEY=
MTN_MOMO_API_SECRET=
MTN_MOMO_ENVIRONMENT=sandbox
MTN_MOMO_CALLBACK_URL=

AIRTEL_MONEY_CLIENT_ID=
AIRTEL_MONEY_CLIENT_SECRET=
AIRTEL_MONEY_ENVIRONMENT=sandbox
AIRTEL_MONEY_CALLBACK_URL=
```

<a name="deployment"></a>
## Deployment

### Production Checklist

1. **Environment Setup**
   - Set `APP_ENV=production` and `APP_DEBUG=false`
   - Configure production database credentials
   - Set strong `APP_KEY` (run `php artisan key:generate`)
   - Configure proper `APP_URL`

2. **Dependencies**
   - Run `composer install --optimize-autoloader --no-dev`
   - Run `npm install && npm run build`
   - Clear and cache configurations: `php artisan config:cache`
   - Cache routes: `php artisan route:cache`
   - Cache views: `php artisan view:cache`

3. **Database**
   - Run migrations: `php artisan migrate --force`
   - Run seeders: `php artisan db:seed --force`
   - Ensure database indexes are created

4. **Storage**
   - Set storage link: `php artisan storage:link`
   - Configure proper file permissions for `storage` directory
   - Ensure uploads directory is writable

5. **Queue Workers** (if using queues)
   - Configure queue driver in `.env`
   - Start queue worker: `php artisan queue:work --daemon`

6. **SSL/HTTPS**
   - Enable SSL certificate
   - Force HTTPS in production
   - Configure trusted proxies if behind load balancer

7. **Monitoring**
   - Set up error logging
   - Configure payment logging channel
   - Monitor disk space for uploads

<a name="web.php and paste this code at the bottom or you can follow the documentation [here](https://mailchimp.com/developer/marketing/api/lists/get-lists-info/)
 ```php
    Route::get('ping', function() {
    $mailchimp = new MailchimpMarketing\ApiClient();
    $mailchimp->setConfig([
        'apiKey' => config('services.mailchimp.key'),
        'server' => 'us5',
    ]);

    $response = $mailchimp->lists->getAllLists();
    ddd($response);
    });
 ```

 > make sure you fill in the `server` correctly, check the link at the top of your admin Mailchimp, for me its `https://us5.admin.mailchimp.com/account/api/` so i give the value of server is `us5`. if you get us6, change the server value to be `us6`.

- visit `localhost:8000/ping` or `velflix.test/ping` and copy value of id in the ` "lists" > 0 > "id"`
- open project, in .env file paste the id into `MAILCHIMP_LIST_SUBSCRIBERS=paste id here` and we ready to go
- visit `localhost:8000` or `velflix.test` test email for subscribing , and refresh your admin mailchimp it should be Your audience has increased 1 contact. 

14. Setup Laravel Socialite login with Google account (optional)
 - Go to the [Google Developers Console](https://console.cloud.google.com/apis) get "GOOGLE_CLIENT_ID" and "GOOGLE_CLIENT_SECRET". paste it into `.env` file.
 if you need help, you can follow these steps:
 - Click Credentials menu, click "select a project" at the navbar > ALL > No organization > new project.
 - project name 'velflix', location should be no organization > Create.
 - Go to OAuth consent screen menu > Select External and Create
 - App Information > app name 'velflix' choose user support email, fill email in developer contact information, save and continue
 - Go to Credentials menu > click `+Create Credentials` at the top > select "OAuth Client ID" > select Application type "Web Application" > Name 'velflix'
 - At the Authorized redirect URIs > +ADD URI > paste this into it `http://127.0.0.1:8000/login/google/callback` > Create.

 > NOTE: you can change the port to be `8080` or others, but make sure when you run `php artisan serve`, your project run in the same port.

 -  Copy `Your Client ID` and `Your Client Secret` 
 - Open velflix project, go to `.env` file and paste it in `GOOGLE_CLIENT_ID=paste_here` and `GOOGLE_CLIENT_SECRET=paste_here` and we ready to go
    ```sh
    php artisan serve
    ```
 - let's test, visit the project in your browser > Login > Login Google > choose account > and if success, it should be redirect to the movies page. 
 
 > Let me know if you get in trouble.

<a name="testing"></a>
## Testing

### <a href="https://pestphp.com/">Pest</a>
1. To run PHP testing for Laravel
    > **Warning**
    > Every time you run testing, you should run `php artisan db:seed` first

```sh
vendor/bin/pest
```

### <a href="https://www.cypress.io/">Cypress</a>


2. To run E2E testing
```sh
npx cypress run
```

### <a href="https://laravel.com/docs/9.x/pint">Laravel Pint</a>

3. To run coding style checks
```sh
vendor/bin/pint
```
### <a href="https://psalm.dev/">Laravel Psalm</a>

4. To run static analysis with Psalm
```sh
vendor/bin/psalm
```
### <a href="https://github.com/nunomaduro/larastan">Larastan </a>

5. To run static analysis with PHPStan
```sh
vendor/bin/phpstan analyse
```

<a name="contributing"></a>
## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

<a name="license"></a>
## License
Velflix is an open-sourced software licensed under [the MIT license](https://github.com/josuapsianturi/velflix/blob/main/LICENSE)
