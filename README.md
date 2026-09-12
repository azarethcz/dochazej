# Docházkový systém Docházej

Docházkový systém vytvořený v **Laravelu**. Obsahuje správu zaměstnanců, evidenci docházky, přestávek, dovolených a export docházky do XLSX.

---

## 🚀 Instalace od nuly

### 1. Vytvoření čistého Laravel projektu

Vytvořte nový Laravel projekt:

```bash
composer create-project laravel/laravel dochazka
cd dochazka
```

### 2. Zkopírování obsahu balíčku

Do nově vytvořeného projektu zkopírujte obsah tohoto balíčku.

> **Poznámka:** Přepište zejména `composer.json`, `routes/web.php` a `.env.example`.

```bash
cp -r /cesta/k/tomuto/balicku/app ./
cp -r /cesta/k/tomuto/balicku/database/migrations ./database/
cp -r /cesta/k/tomuto/balicku/database/seeders ./database/
cp -r /cesta/k/tomuto/balicku/resources/views ./resources/
cp /cesta/k/tomuto/balicku/routes/web.php ./routes/web.php
```

---

## 📦 Instalace závislostí

Nainstalujte doplňkový balíček pro export docházky do Excelu:

```bash
composer require maatwebsite/excel -W
```

Parametr `-W` (`--with-all-dependencies`) umožní Composeru aktualizovat potřebné závislosti a pomáhá řešit případné konflikty verzí balíčků.

---

## 🔐 Registrace Admin Middleware

Otevřete soubor:

```text
bootstrap/app.php
```

Do části:

```php
->withMiddleware(function (Middleware $middleware) {
    // ...
})
```

přidejte alias pro administrátorské middleware:

```php
$middleware->alias([
    'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
]);
```

Middleware následně umožňuje chránit administrátorské routy pomocí:

```php
->middleware('admin')
```

---

## 🗄️ Příprava adresářů a SQLite databáze

Vytvořte potřebné adresáře:

```bash
mkdir -p bootstrap/cache storage
```

Nastavte oprávnění:

```bash
chmod -R 775 bootstrap/cache storage
```

Vytvořte SQLite databázi:

```bash
touch database/database.sqlite
```

Pokud používáte SQLite, zkontrolujte v `.env`, že je databáze nastavena například takto:

```env
DB_CONNECTION=sqlite
```

---

## 🧩 Controller.php

Pokud v projektu chybí základní controller, vytvořte soubor:

```text
app/Http/Controllers/Controller.php
```

se základní třídou:

```php
<?php

namespace App\Http\Controllers;

abstract class Controller
{
    //
}
```

---

## 👤 Načtení zaměstnanců a dnešní docházky

Příklad logiky pro načtení zaměstnanců a jejich dnešní docházky:

```php
$user = auth()->user();

$query = $user->isAdmin()
    ? User::where('role', 'employee')->orderBy('name')
    : User::where('id', $user->id);

$employees = $query
    ->with([
        'attendances' => function ($q) {
            $q->whereDate('clock_in', today())
              ->with('breakPeriods');
        }
    ])
    ->get();
```

> Ujistěte se, že jsou v příslušném controlleru importovány potřebné modely, například `User`.

---

## ⚙️ Inicializace aplikace

Před prvním spuštěním vymažte konfigurační cache, vygenerujte aplikační klíč a vytvořte databázové tabulky včetně ukázkových dat:

```bash
php artisan config:clear
php artisan key:generate
php artisan migrate:fresh --seed
```

> ⚠️ `migrate:fresh` smaže všechny existující tabulky v databázi a vytvoří je znovu. Nepoužívejte jej na produkční databázi, pokud nechcete přijít o její data.

---

## ▶️ Spuštění aplikace

Spusťte lokální vývojový server:

```bash
php artisan serve
```

Aplikace bude dostupná na:

```text
http://localhost:8000
```

---

# 🔑 Přihlašovací údaje

Ukázková data jsou vytvořena pomocí `DatabaseSeeder`.

### Administrátor

```text
E-mail: admin@example.com
Heslo: heslo1234
```

### Zaměstnanci

Ukázkoví zaměstnanci:

* Jana Nováková
* Petr Svoboda

Zaměstnanci se přihlašují pouze **výběrem svého jména bez hesla**.

Tento způsob přihlášení je určen pro použití ve scénáři **sdíleného docházkového terminálu**.

> ⚠️ Výchozí heslo administrátora je určeno pouze pro ukázková data. V produkčním prostředí jej změňte.

---

# 📁 Struktura projektu

```text
app/
├── Exports/
│   └── AttendanceExport.php
│
├── Http/
│   ├── Controllers/
│   │   ├── ...
│   │   └── Controller.php
│   │
│   └── Middleware/
│       └── EnsureUserIsAdmin.php
│
└── Models/
    ├── User.php
    ├── Attendance.php
    ├── BreakPeriod.php
    └── VacationRequest.php

database/
├── migrations/
│   └── ...
│
└── seeders/
    └── DatabaseSeeder.php

resources/
└── views/
    ├── layouts/
    └── ...

routes/
└── web.php

bootstrap/
└── app.php
```

### Hlavní části projektu

| Cesta                                       | Popis                                 |
| ------------------------------------------- | ------------------------------------- |
| `app/Models/`                               | Datové modely aplikace                |
| `app/Models/User.php`                       | Uživatelé a role `admin` / `employee` |
| `app/Models/Attendance.php`                 | Záznamy docházky                      |
| `app/Models/BreakPeriod.php`                | Evidence přestávek                    |
| `app/Models/VacationRequest.php`            | Žádosti o dovolenou                   |
| `app/Http/Controllers/`                     | Logika aplikace                       |
| `app/Http/Middleware/EnsureUserIsAdmin.php` | Ochrana administrátorských rout       |
| `database/migrations/`                      | Databázové schéma                     |
| `database/seeders/DatabaseSeeder.php`       | Ukázková data                         |
| `resources/views/`                          | Blade šablony                         |
| `app/Exports/AttendanceExport.php`          | Export docházky do XLSX               |
| `routes/web.php`                            | Definice webových rout                |

---

# 📊 Export docházky

Export docházky do formátu **XLSX** je řešen pomocí balíčku:

```text
maatwebsite/excel
```

Hlavní exportní třída:

```text
app/Exports/AttendanceExport.php
```

Instalace:

```bash
composer require maatwebsite/excel -W
```

---

# 🔮 Možná další rozšíření

Aplikaci lze podle konkrétního zadání dále rozšířit.

### 🔐 Bezpečnost a přihlášení

* PIN nebo heslo také pro zaměstnance
* 2FA (dvoufaktorové ověření) pro administrátora
* omezení přístupu podle IP adresy
* auditní log administrátorských akcí

### 📄 PDF dokumenty

Skutečné generování PDF přímo na serveru pomocí balíčku:

```bash
composer require barryvdh/laravel-dompdf
```

Místo současného využití tiskového dialogu prohlížeče by tak bylo možné generovat PDF dokumenty přímo v aplikaci.

### 📧 E-mailové notifikace

Pomocí **Laravel Notifications** lze přidat například:

* oznámení o schválení dovolené
* oznámení o zamítnutí dovolené
* upozornění administrátora na nové žádosti
* automatické e-mailové reporty docházky

### 🪪 Docházkové terminály

Možné je doplnit podporu pro:

* RFID karty
* NFC
* docházkové terminály
* QR kódy
* mobilní aplikaci
* automatickou identifikaci zaměstnance

### 💰 Napojení na mzdový systém

Export docházky lze přizpůsobit konkrétnímu mzdovému systému nebo účetnímu programu.

Například:

```text
CSV
XLSX
XML
API
```

### 🌍 Více poboček a středisek

Možné rozšíření o:

* více poboček
* střediska
* oddělení
* manažery jednotlivých poboček
* oprávnění podle poboček

### 🕐 Směnové plány

Další možností je implementace:

* směnových kalendářů
* ranních / odpoledních / nočních směn
* plánování směn
* automatického vyhodnocení přesčasů
* evidence práce o víkendech a svátcích

---

# 🛠️ Technologie

Projekt je postaven na:

* **PHP**
* **Laravel**
* **Blade**
* **SQLite**
* **Composer**
* **Maatwebsite Laravel Excel**

---

## 📌 Rychlý start

Pro rychlé spuštění projektu lze postupovat následovně:

```bash
composer create-project laravel/laravel dochazka
cd dochazka

# Zkopírování souborů projektu
cp -r /cesta/k/balicku/app ./
cp -r /cesta/k/balicku/database/migrations ./database/
cp -r /cesta/k/balicku/database/seeders ./database/
cp -r /cesta/k/balicku/resources/views ./resources/
cp /cesta/k/balicku/routes/web.php ./routes/web.php

# Instalace Excel exportu
composer require maatwebsite/excel -W

# SQLite
touch database/database.sqlite

# Laravel
php artisan config:clear
php artisan key:generate
php artisan migrate:fresh --seed

# Spuštění
php artisan serve
```

Poté otevřete:

```text
http://localhost:8000
```

**Administrátor:** `admin@example.com` / `heslo1234`

**Zaměstnanci:** přihlášení výběrem jména.
