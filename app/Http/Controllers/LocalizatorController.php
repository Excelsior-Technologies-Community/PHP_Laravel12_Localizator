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

        $totalLanguages = Language::count();
        $totalKeys = Translation::count();

        return view(
            'localizator.index',
            compact(
                'languages',
                'totalLanguages',
                'totalKeys'
            )
        );
    }

    public function storeLanguage(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:5',
            'name' => 'required|string|max:50',
        ]);

        $existing = Language::where(
            'code',
            $request->code
        )->first();

        if ($existing) {
            return redirect()
                ->back()
                ->with(
                    'success',
                    "Language already exists!"
                );
        }

        Language::create([
            'code' => $request->code,
            'name' => $request->name
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'Language added successfully!'
            );
    }

    public function translations($lang)
    {
        $language = Language::where(
            'code',
            $lang
        )->firstOrFail();

        $translations = Translation::all();

        $total = $translations->count();

        $completed = 0;

        foreach ($translations as $translation) {

            if (
                !empty($translation->value[$lang] ?? null)
            ) {
                $completed++;
            }
        }

        $progress = $total
            ? round(
                ($completed / $total) * 100
            )
            : 0;

        return view(
            'localizator.translations',
            compact(
                'language',
                'translations',
                'completed',
                'total',
                'progress'
            )
        );
    }

    public function saveTranslations(
        Request $request,
        $lang
    ) {

        foreach (
            $request->keys as $key => $value
        ) {

            $translation =
                Translation::firstOrCreate([
                    'key' => $key
                ]);

            $current =
                $translation->value ?? [];

            if (
                !empty($value)
            ) {
                $current[$lang] = $value;
            }

            $translation->value =
                $current;

            $translation->save();
        }

        return redirect()
            ->back()
            ->with(
                'success',
                'Translations saved!'
            );
    }

    public function export($lang)
    {
        $translations =
            Translation::all();

        $data = [];

        foreach (
            $translations as $translation
        ) {

            $data[$translation->key] =
                $translation->value[$lang]
                ?? '';
        }

        $langPath =
            resource_path('lang');

        if (
            !File::exists(
                $langPath
            )
        ) {

            File::makeDirectory(
                $langPath,
                0755,
                true
            );
        }

        File::put(
            $langPath . "/{$lang}.json",
            json_encode(
                $data,
                JSON_PRETTY_PRINT |
                    JSON_UNESCAPED_UNICODE
            )
        );

        return redirect()
            ->back()
            ->with(
                'success',
                'JSON exported successfully!'
            );
    }
}
