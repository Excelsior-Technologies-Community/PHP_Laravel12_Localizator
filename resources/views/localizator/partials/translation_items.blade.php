@foreach($translations as $translation)
<div class="translation-item {{ !empty($translation->value[$lang] ?? '') ? 'has-value' : 'no-value' }}">
    <label title="{{ $translation->key }}">{{ $translation->key }}</label>
    <input
        type="text"
        name="keys[{{ $translation->key }}]"
        value="{{ $translation->value[$lang] ?? '' }}"
        data-key="{{ $translation->key }}"
        placeholder="Enter translation..."
        class="trans-input"
    >
</div>
@endforeach
