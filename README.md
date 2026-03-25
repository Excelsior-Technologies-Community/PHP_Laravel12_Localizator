# PHP_Laravel12_Localizator



## Project Description:

PHP_Laravel12_Localizator is a web-based Laravel 12 application designed to manage multiple languages and translations in a centralized way. This project allows developers or content managers to:

- Add new languages dynamically.
- Create and manage translation keys.
- Input translations for each language.
- Export translations as JSON files, ready for use in Laravel localization or other applications.

It’s ideal for multilingual Laravel applications where maintaining translation files manually is cumbersome. This tool automates the process and provides a clean UI for management.



## Key Features:

- Language Management: Add, view, and manage multiple languages in your application.
- Translation Keys Management: Predefine keys like welcome, login, dashboard etc., and manage them in one       place.
- Dynamic Translation Editing: Enter translations for each language using a friendly UI.
- JSON Export: Automatically generate JSON files in resources/lang/ for Laravel localization.
- Dark-Themed Interface: Minimal and modern UI for comfortable usage.
- Database-Driven: All languages and translations stored in MySQL for easy persistence.
- Validation & Feedback: Alerts users for existing languages and success messages after actions.


## How to Add Translations

1. Go to Languages → Add 'en' for English.
2. Click on 'en' → Add translations for 'welcome', 'login', etc.
3. Click 'Export JSON' → JSON saved in resources/lang/en.json



## Technologies Used:

- Backend: PHP 8.x, Laravel 12
- Frontend: Blade Templates, HTML, CSS (Dark-themed UI)
- Database: MySQL (or MariaDB)
- Packages: None required (uses default Laravel packages)
- Features: JSON export for translations, database-driven language management


---



## Installation Steps


---


## STEP 1: Create Laravel 12 Project

### Open terminal / CMD and run:

```
composer create-project laravel/laravel PHP_Laravel12_Localizator "12.*"

```

### Go inside project:

```
cd PHP_Laravel12_Localizator

```

#### Explanation:

Installs a fresh Laravel 12 project and moves into the project folder.




## STEP 2: Database Setup 

### Update database details:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel12_Localizator
DB_USERNAME=root
DB_PASSWORD=

```

### Create database in MySQL / phpMyAdmin:

```
Database name: laravel12_Localizator

```

### Then Run:

```
php artisan migrate

```


#### Explanation:

Connects Laravel to MySQL and creates default tables for authentication and other system features.



## STEP 3: Create Models + Migrations 

### Run:

```
php artisan make:model Language -m
php artisan make:model Translation -m

```

#### Explanation:

Creates Language and Translation models with migration files for DB structure.




## STEP 4: Define Migrations & Model

### database/migrations/xxxx_create_languages_table.php

```
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};

```

#### Explanation:

Stores language code & name.



### database/migrations/xxxx_create_translations_table.php

```
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->json('value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};

```

#### Explanation:

Stores translation keys and values as JSON.



### Then Run:

```
php artisan migrate

```

#### Explanation:

Executes migration files to create the necessary database tables.



### app/Models/Language.php

```
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name'];
}

```


### app/Models/Translation.php

```
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];
    protected $casts = ['value' => 'array'];
}

```

#### Explanation:

Defines which fields can be mass-assigned and sets JSON casting for translations.




## STEP 5: Create Controller

### Run:

```
php artisan make:controller LocalizatorController

```

### app/Http/Controllers/LocalizatorController.php

```
<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LocalizatorController extends Controller
{
    public function index()
    {
        $languages = Language::all();
        return view('localizator.index', compact('languages'));
    }

    public function storeLanguage(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:5',
            'name' => 'required|string|max:50',
        ]);

        // Check if language code already exists
        $existing = Language::where('code', $request->code)->first();
        if ($existing) {
            return redirect()->back()->with('success', "Language '{$request->code}' already exists!");
        }

        Language::create([
            'code' => $request->code,
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', "Language '{$request->name}' added successfully!");
    }

    public function translations($lang)
    {
        $language = Language::where('code', $lang)->firstOrFail();
        $translations = Translation::all();
        return view('localizator.translations', compact('language', 'translations'));
    }

    public function saveTranslations(Request $request, $lang)
    {
        foreach ($request->keys as $key => $value) {
            // Create or get existing
            $tr = Translation::firstOrCreate(['key' => $key]);

            // Get existing array or empty
            $current = $tr->value ?? [];

            // Update language value only if not empty
            if (!is_null($value) && $value !== '') {
                $current[$lang] = $value;
            }

            $tr->value = $current;
            $tr->save();
        }

        return redirect()->back()->with('success', 'Translations saved!');
    }

    public function export($lang)
    {
        $translations = Translation::all();

        $data = [];
        foreach ($translations as $translation) {
            $value = $translation->value[$lang] ?? '';
            $data[$translation->key] = $value;
        }

        // Ensure the resources/lang folder exists
        $langPath = resource_path('lang');
        if (!File::exists($langPath)) {
            File::makeDirectory($langPath, 0755, true);
        }

        // Save the JSON file
        $file = $langPath . "/{$lang}.json";
        File::put($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return redirect()->back()->with('success', "Language '{$lang}' exported successfully!");
    }
}

```

#### Explanation:

Controller centralizes logic for adding languages, updating translations, and exporting JSON files.




## STEP 6: Add Routes

### routes/web.php

```
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocalizatorController;

Route::get('/', [LocalizatorController::class, 'index']);
Route::post('/language', [LocalizatorController::class, 'storeLanguage']);
Route::get('/translations/{lang}', [LocalizatorController::class, 'translations']);
Route::post('/translations/save/{lang}', [LocalizatorController::class, 'saveTranslations']);
Route::get('/export/{lang}', [LocalizatorController::class, 'export']);

```

#### Explanation:

Defines URLs for listing languages, managing translations, and exporting JSON files.




## STEP 7: Create Views File

### Create folder:

```
resources/views/localizator

```

### resources/views/localizator/index.blade.php

```
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Languages</title>
    <style>
        body {
            background-color: #121212;
            color: #e0e0e0;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {
            background-color: #1e1e1e;
            padding: 30px 40px;
            border-radius: 12px;
            min-width: 300px;
            text-align: center;
            box-shadow: 0 0 15px rgba(98, 0, 234, 0.5);
        }

        h1 {
            margin-bottom: 20px;
            color: #fff;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 20px;
        }

        input[type="text"] {
            padding: 10px;
            border: none;
            border-radius: 6px;
            width: 100%;
            background-color: #121212;
            color: #fff;
        }

        button {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            background-color: #6200ea;
            color: #fff;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background-color: #3700b3;
        }

        .languages {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .language-item {
            padding: 10px;
            background-color: #121212;
            border-radius: 6px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .language-item:hover {
            transform: scale(1.05);
            box-shadow: 0 0 8px #6200ea;
        }

        .language-item a {
            text-decoration: none;
            color: #fff;
            font-weight: bold;
            display: block;
        }

        .success {
            padding: 10px;
            margin-bottom: 15px;
            background-color: #388e3c;
            color: #fff;
            border-radius: 6px;
        }
    </style>
</head>

<body>

    <div class="card">
        <h1>Languages</h1>

        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="/language">
            @csrf
            <input type="text" name="code" placeholder="Language Code (e.g., en)" required>
            <input type="text" name="name" placeholder="Language Name (e.g., English)" required>
            <button>Add</button>
        </form>

        <div class="languages">
            @foreach($languages as $lang)
                <div class="language-item">
                    <a href="/translations/{{ $lang->code }}">
                        {{ $lang->code }} — {{ $lang->name }}
                    </a>
                </div>
            @endforeach
        </div>
    </div>

</body>

</html>

```


### resources/views/localizator/translations.blade.php

```
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Translations - {{ $language->name }}</title>
    <style>
        body {
            background-color: #121212;
            color: #e0e0e0;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {
            background-color: #1e1e1e;
            padding: 40px 50px;
            border-radius: 16px;
            min-width: 500px;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            text-align: center;
            box-shadow: 0 0 30px rgba(98, 0, 234, 0.5);
        }

        h1 {
            margin-bottom: 25px;
            color: #fff;
            font-size: 1.8rem;
        }

        .success {
            padding: 12px;
            margin-bottom: 20px;
            background-color: #388e3c;
            color: #fff;
            border-radius: 6px;
            font-weight: bold;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .translation-item {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            background-color: #121212;
            padding: 12px 15px;
            border-radius: 8px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .translation-item:hover {
            transform: scale(1.02);
            box-shadow: 0 0 10px #6200ea;
        }

        label {
            font-weight: bold;
            margin-right: 15px;
            min-width: 100px;
            text-align: left;
            font-size: 1rem;
        }

        input[type="text"] {
            width: 250px;
            padding: 8px 12px;
            border: 1px solid #333;
            border-radius: 6px;
            background-color: #1e1e1e;
            color: #fff;
            font-size: 1rem;
            box-sizing: border-box;
        }

        button {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            background-color: #6200ea;
            color: #fff;
            cursor: pointer;
            transition: background 0.3s;
            align-self: center;
            margin-top: 10px;
            font-size: 1rem;
        }

        button:hover {
            background-color: #3700b3;
        }

        .card-buttons {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 20px;
        }

        .card-buttons a {
            display: block;
            text-align: center;
            text-decoration: none;
            color: #fff;
            font-weight: bold;
            padding: 10px;
            border-radius: 6px;
            background-color: #333;
            transition: background 0.3s;
        }

        .card-buttons a:hover {
            background-color: #6200ea;
        }
    </style>
</head>

<body>

    <div class="card">
        <h1>{{ $language->name }} Translations</h1>

        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="/translations/save/{{ $language->code }}">
            @csrf
            @foreach($translations as $translation)
                <div class="translation-item">
                    <label>{{ $translation->key }}</label>
                    <input type="text" name="keys[{{ $translation->key }}]"
                        value="{{ $translation->value[$language->code] ?? '' }}">
                </div>
            @endforeach

            <button>Save Translations</button>
        </form>

        <div class="card-buttons">
            <a href="/export/{{ $language->code }}">Export JSON</a>
            <a href="/">← Back to Languages</a>
        </div>
    </div>

</body>

</html>

```

#### Explanation:

Provides a dark-themed UI to view and manage languages and translations.




## STEP 9: Seed Default Keys

### Run:

```
php artisan make:seeder TranslationSeeder

```

### database/seeders/TranslationSeeder.php

```
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Translation;  // <-- add this line

class TranslationSeeder extends Seeder
{
    public function run()
    {
        $keys = [
            'welcome',
            'login',
            'logout',
            'dashboard',
        ];

        foreach ($keys as $key) {
            Translation::create(['key' => $key, 'value' => []]);
        }
    }
}

```


### Run seeder:

```
php artisan db:seed --class=TranslationSeeder

```

#### Explanation:

Seeds initial translation keys so you can start adding translations immediately.





## STEP 10: Config Locales

### In config/app.php set:

```
'locale' => 'en',

```

#### Explanation:

Sets the default language for your Laravel app.




## STEP 11: Run the App  

### Start dev server:

```
php artisan serve

```

### Open in browser:

```
http://127.0.0.1:8000

```

#### Explanation:

Starts the Laravel development server so you can view and test your Localizator application.


## Expected Output:


### Manage Languages:


<img src="screenshots/Screenshot 2026-03-25 164856.png" width="900">


### Add New Language:


<img src="screenshots/Screenshot 2026-03-25 164456.png" width="900">


### Success Message:


<img src="screenshots/Screenshot 2026-03-25 164509.png" width="900">


### Manage(Save) Translations:


<img src="screenshots/Screenshot 2026-03-25 165214.png" width="900">


### Export Translations:


<img src="screenshots/Screenshot 2026-03-25 165225.png" width="900">


### All Translations Example:

#### English Translations:


<img src="screenshots/Screenshot 2026-03-25 165728.png" width="900">


#### French Translations:


<img src="screenshots/Screenshot 2026-03-25 165739.png" width="900">


#### Spanish Translations:


<img src="screenshots/Screenshot 2026-03-25 165750.png" width="900">



### All .JSON Example:

#### English JSON(en.json):


<img src="screenshots/Screenshot 2026-03-25 174348.png" width="900">


#### French JSON(fr.json):


<img src="screenshots/Screenshot 2026-03-25 174401.png" width="900">


#### Spanish JSON(es.json):


<img src="screenshots/Screenshot 2026-03-25 174411.png" width="900">




---

## Project Folder Structure:

```
PHP_Laravel12_Localizator/
│
├── app/
│   ├── Console/
│   ├── Exceptions/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── LocalizatorController.php
│   │   ├── Middleware/
│   │   └── Kernel.php
│   ├── Models/
│   │   ├── Language.php
│   │   └── Translation.php
│   └── Providers/
│
├── bootstrap/
│   └── app.php
│
├── config/
│   └── app.php
│
├── database/
│   ├── factories/
│   ├── migrations/
│   │   ├── xxxx_create_languages_table.php
│   │   └── xxxx_create_translations_table.php
│   └── seeders/
│       └── TranslationSeeder.php
│
├── public/
│   ├── index.php
│   └── css/ (optional)
│
├── resources/
│   ├── views/
│   │   └── localizator/
│   │       ├── index.blade.php
│   │       └── translations.blade.php
│   └── lang/   (JSON files will be exported here)
│
├── routes/
│   └── web.php
│
├── storage/
│   ├── app/
│   ├── framework/
│   └── logs/
│
├── tests/
│   └── Feature/
│
├── vendor/
│
├── artisan
├── composer.json
├── composer.lock
├── package.json
├── phpunit.xml
└── README.md

```
