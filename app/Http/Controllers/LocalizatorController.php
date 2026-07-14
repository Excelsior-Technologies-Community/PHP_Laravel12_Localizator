<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\LanguageAccess;
use App\Models\Translation;
use App\Models\TranslationHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class LocalizatorController extends Controller
{
    public function index()
    {
        $languages = Language::all();
        $totalLanguages = Language::count();
        $totalKeys = Translation::count();
        $users = User::all();
        $accessList = LanguageAccess::with(['user', 'language'])->get();

        return view('localizator.index', compact(
            'languages', 'totalLanguages', 'totalKeys', 'users', 'accessList'
        ));
    }

    public function grantAccess(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'language_id' => 'required|exists:languages,id',
        ]);

        LanguageAccess::firstOrCreate([
            'user_id' => $request->user_id,
            'language_id' => $request->language_id,
        ]);

        return redirect()->back()->with('access_success', 'Access granted successfully!');
    }

    public function revokeAccess($id)
    {
        LanguageAccess::findOrFail($id)->delete();
        return redirect()->back()->with('access_success', 'Access revoked.');
    }

    public function storeLanguage(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:5',
            'name' => 'required|string|max:50',
            'flag' => 'nullable|string|max:10',
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
            'name' => $request->name,
            'flag' => $request->flag
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

        if (Auth::check() && !Auth::user()->hasLanguageAccess($language->id)) {
            abort(403, 'You do not have access to this language.');
        }

        $translations = Translation::paginate(20);

        $total = Translation::count();

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
        $language = Language::where('code', $lang)->firstOrFail();

        if (Auth::check() && !Auth::user()->hasLanguageAccess($language->id)) {
            abort(403, 'You do not have access to this language.');
        }

        $errors = [];

        foreach (
            $request->keys as $key => $value
        ) {

            $translation =
                Translation::firstOrCreate([
                    'key' => $key
                ]);

            $current =
                $translation->value ?? [];
            $oldValue = $current[$lang] ?? null;

            if (
                !empty($value)
            ) {
                $current[$lang] = $value;
            }

            $newValue = $current[$lang] ?? null;

            if ($oldValue !== $newValue && !empty($newValue)) {
                $placeholders = [];
                preg_match_all('/:([a-zA-Z_]+)/', $key, $matches);
                if (!empty($matches[1])) {
                    foreach ($matches[1] as $placeholder) {
                        if (strpos($newValue, ':' . $placeholder) === false) {
                            $errors[$key] = "Missing placeholder :{$placeholder} in translation";
                        }
                    }
                }

                TranslationHistory::create([
                    'translation_id' => $translation->id,
                    'language_code' => $lang,
                    'old_value' => [$lang => $oldValue],
                    'new_value' => [$lang => $newValue],
                    'user_id' => Auth::id(),
                ]);
            }

            $translation->value =
                $current;

            $translation->save();
        }

        if (!empty($errors)) {
            return redirect()
                ->back()
                ->withErrors($errors)
                ->with('warning', 'Translations saved but some placeholders are missing!');
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

    public function history($lang)
    {
        $language = Language::where('code', $lang)->firstOrFail();
        $histories = TranslationHistory::with(['translation', 'user'])
            ->where('language_code', $lang)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('localizator.history', compact('language', 'histories'));
    }

    public function rollback($lang, $historyId)
    {
        $history = TranslationHistory::findOrFail($historyId);
        $translation = $history->translation;

        $current = $translation->value ?? [];
        $current[$lang] = $history->old_value[$lang] ?? '';

        TranslationHistory::create([
            'translation_id' => $translation->id,
            'language_code' => $lang,
            'old_value' => [$lang => $current[$lang]],
            'new_value' => [$lang => $history->old_value[$lang] ?? ''],
            'user_id' => Auth::id(),
        ]);

        $translation->value = $current;
        $translation->save();

        return redirect()
            ->back()
            ->with('success', 'Translation rolled back successfully!');
    }

    public function search(Request $request, $lang)
    {
        $query = $request->get('q');

        $translations = Translation::where('key', 'like', "%{$query}%")
            ->paginate(20);

        $total = Translation::count();

        $completed = 0;

        foreach ($translations as $translation) {
            if (!empty($translation->value[$lang] ?? null)) {
                $completed++;
            }
        }

        $progress = $total ? round(($completed / $total) * 100) : 0;

        return response()->json([
            'html' => view('localizator.partials.translation_items', compact('translations', 'lang'))->render(),
            'pagination' => $translations->links()->toHtml(),
            'completed' => $completed,
            'total' => $total,
            'progress' => $progress
        ]);
    }

    public function preview($lang)
    {
        $language = Language::where('code', $lang)->firstOrFail();
        $translations = Translation::all()->pluck('value', 'key');
        
        $translationsData = [];
        foreach ($translations as $key => $value) {
            $translationsData[$key] = $value[$lang] ?? '';
        }

        return view('localizator.preview', compact('language', 'translationsData'));
    }
}
