# Docházka – Laravel aplikace

Evidence pracovní doby s přihlašováním a rolemi (administrátor / zaměstnanec), přestávkami,
žádostmi o dovolenou a exportem do XLSX / PDF.

Tento balík obsahuje **aplikační kód** (migrace, modely, controllery, routy, blade šablony),
ne kompletní instalaci Laravelu — framework a jeho závislosti se stáhnou přes Composer.
Instalace vyžaduje internetové připojení, PHP 8.2+, Composer a databázi (MySQL/MariaDB/PostgreSQL/SQLite).

Kompletní instalační manuál pro projekt Docházka
Tento návod integruje všechny kroky potřebné k úspěšnému zprovoznění projektu "Docházka" v Laravelu 11 včetně řešení specifických chyb a úpravy kódu.
Instalace od nuly
Vytvořte čistý Laravel projekt:
Bash
composer create-project laravel/laravel dochazka
cd dochazka
Zkopírujte do něj obsah balíčku (přepište composer.json, routes/web.php a .env.example, doplňte app/, database/, resources/views/):
Bash
cp -r /cesta/k/tomuto/balicku/app ./
cp -r /cesta/k/tomuto/balicku/database/migrations ./database/
cp -r /cesta/k/tomuto/balicku/database/seeders ./database/
cp -r /cesta/k/tomuto/balicku/resources/views ./resources/
cp /cesta/k/tomuto/balicku/routes/web.php ./routes/web.php
Nainstalujte doplňkový balíček pro export do Excelu (s parametrem -W pro vyřešení případných konfliktů závislostí):
Bash
composer require maatwebsite/excel -W
Zaregistrujte middleware admin — otevřete bootstrap/app.php a do sekce ->withMiddleware(function (Middleware $middleware) { ... }) přidejte:
PHP
$middleware->alias([
    'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
]);
Připravte adresářovou strukturu a SQLite databázi:
Bash
mkdir -p bootstrap/cache storage
chmod -R 775 bootstrap/cache storage
touch database/database.sqlite
Vytvořte v adresáři app/Http/Controllers/ chybějící soubor Controller.php se základní třídou:
PHP
<?php

namespace App\Http\Controllers;

abstract class Controller
{
    //
}
Nastavte soubor .env (vytvořte ho ručně nebo zkopírujte ze vzoru) a zadejte SQLite připojení a databázové sessions:
Fragment kódu
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
SESSION_DRIVER=database
Vytvořte chybějící migraci pro tabulku sessions:
Bash
php artisan session:table
Upravte soubor app/Http/Controllers/DashboardController.php v metodě index, aby se předešlo chybě s metodou load() na smíšené kolekci:
PHP
$user = $request->user();

$query = $user->isAdmin()
    ? User::where('role', 'employee')->orderBy('name')
    : User::where('id', $user->id);

$employees = $query->with(['attendances' => function ($q) {
    $q->whereDate('clock_in', today())->with('breakPeriods');
}])->get();
Vymažte konfigurační cache, vygenerujte klíč a spusťte migrace se seedem:
Bash
php artisan config:clear
php artisan key:generate
php artisan migrate:fresh --seed
Spusťte lokální vývojový server:
Bash
php artisan serve
Otevřete http://localhost:8000. Přihlašovací údaje z ukázkových dat (seeder):
Administrátor: e-mail admin@example.com, heslo heslo1234
Zaměstnanci: Jana Nováková, Petr Svoboda — přihlašují se jen výběrem jména (bez hesla, model "sdíleného terminálu").


## Struktura

- `app/Models` – `User` (role admin/employee), `Attendance`, `BreakPeriod`, `VacationRequest`
- `app/Http/Controllers` – logika docházky, zaměstnanců, záznamů/exportů, dovolené, nastavení
- `app/Http/Middleware/EnsureUserIsAdmin.php` – ochrana administrátorských routes
- `database/migrations` – schéma databáze
- `database/seeders/DatabaseSeeder.php` – ukázková data
- `resources/views` – Blade šablony (sdílený layout + jednotlivé stránky)
- `app/Exports/AttendanceExport.php` – export docházky do XLSX (maatwebsite/excel)

## Co lze podle konkrétní zakázky dál rozšířit

- PIN/heslo i pro zaměstnance, případně 2FA pro administrátora
- Skutečné generování PDF na serveru (balíček `barryvdh/laravel-dompdf`) místo
  tiskového dialogu prohlížeče
- Notifikace e-mailem při schválení/zamítnutí dovolené (Laravel Notifications)
- Docházkové terminály / RFID karty / mobilní aplikace místo ručního klikání
- Napojení na mzdový systém (export ve formátu konkrétní mzdové účetní)
- Vícejazyčnost, více poboček/středisek, směnové plány
