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