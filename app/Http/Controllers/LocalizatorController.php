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