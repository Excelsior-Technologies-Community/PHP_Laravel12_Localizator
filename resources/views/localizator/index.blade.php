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