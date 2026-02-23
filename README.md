# Marifine Tailor

This is a Laravel application for managing a tailoring business. It provides functionalities for customer management, measurement tracking, order processing, and more.

## Features

- Customer management
- Measurement recording
- Order creation and tracking
- Dashboard for an overview of business metrics

## Installation

To set up the project locally, follow these steps:

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/Basith-08/marifine-tailor.git
    cd marifine-tailor
    ```

2.  **Install PHP dependencies:**
    ```bash
    composer install
    ```

3.  **Install Node.js dependencies:**
    ```bash
    npm install
    ```

4.  **Copy the environment file:**
    ```bash
    cp .env.example .env
    ```

5.  **Generate an application key:**
    ```bash
    php artisan key:generate
    ```

6.  **Configure your database** in the `.env` file.

7.  **Run migrations and seed the database:**
    ```bash
    php artisan migrate --seed
    ```

8.  **Compile front-end assets:**
    ```bash
    npm run dev
    ```
    or for production:
    ```bash
    npm run build
    ```

9.  **Start the local development server:**
    ```bash
    php artisan serve
    ```

You can now access the application at `http://127.0.0.1:8000`.

## Usage

(Further details on how to use the application will go here.)

## Testing

To run the tests, execute:

```bash
php artisan test
```

## Contributing

(Guidelines for contributing to the project will go here.)

## License

The Marifine Tailor application is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
