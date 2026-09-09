# Panduan Instalasi (Setup & Installation)

## Prasyarat
*   PHP >= 8.2
*   Composer >= 2.0
*   MySQL Server

## Langkah Instalasi
1.  **Klon Repositori:**
    ```bash
    git clone [URL_REPOSITORI] learsigns-core
    cd learsigns-core
    ```
2.  **Instalasi Dependensi:**
    ```bash
    composer install
    ```
3.  **Konfigurasi Environment:**
    Salin file `.env.example` menjadi `.env` lalu perbarui bagian ini:
    ```env
    DB_CONNECTION=mysql
    DB_DATABASE=viba_db
    DB_USERNAME=root
    DB_PASSWORD=

    GOOGLE_CLIENT_ID=kredensial_google_anda
    GOOGLE_CLIENT_SECRET=rahasia_google_anda
    ```
4.  **Inisialisasi Sistem:**
    ```bash
    php artisan key:generate
    php artisan migrate --seed
    php artisan serve
    ```