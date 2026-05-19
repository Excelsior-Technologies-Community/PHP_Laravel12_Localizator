<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <title>
        {{$language->name}}
        Translations
    </title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {

            font-family: 'Segoe UI';

            background:
                linear-gradient(135deg,
                    #020617,
                    #1e293b);

            padding: 40px;

            color: white;

            min-height: 100vh;

        }

        .container {

            max-width: 1000px;
            margin: auto;

        }

        .card {

            background:
                rgba(255, 255, 255, .07);

            backdrop-filter:
                blur(18px);

            padding: 30px;

            border-radius: 20px;

        }

        .search {

            margin: 20px 0;

        }

        input {

            width: 100%;

            padding: 14px;

            background: #111827;

            border: none;

            border-radius: 10px;

            color: white;

        }

        .progress-box {

            height: 35px;

            background: #111827;

            border-radius: 30px;

            overflow: hidden;

            margin: 15px 0 25px;

        }

        .progress {

            height: 100%;

            display: flex;

            justify-content: center;

            align-items: center;

            background: #7c3aed;

            font-weight: bold;

        }

        .translation-item {

            display: flex;

            justify-content: space-between;

            align-items: center;

            padding: 15px;

            margin-top: 15px;

            background: #111827;

            border-radius: 12px;

        }

        .translation-item input {

            width: 320px;

        }

        button {

            margin-top: 25px;

            width: 100%;

            padding: 15px;

            border: none;

            background: #7c3aed;

            color: white;

            border-radius: 10px;

            cursor: pointer;

        }

        .links {

            display: flex;

            gap: 15px;

            margin-top: 20px;

        }

        .links a {

            flex: 1;

            padding: 15px;

            background: #111827;

            text-align: center;

            border-radius: 10px;

            text-decoration: none;

            color: white;

        }

        .success {

            background: #16a34a;

            padding: 15px;

            border-radius: 10px;

            margin-top: 20px;

        }
    </style>

</head>

<body>

    <div class="container">

        <div class="card">

            <h1>

                {{$language->name}}
                Translations

            </h1>

            <p style="margin-top:10px">

                Completed:
                {{$completed}}
                /
                {{$total}}

            </p>

            <div class="progress-box">

                <div
                    class="progress"
                    style="width:{{$progress}}%">

                    {{$progress}}%

                </div>

            </div>

            <input
                id="search"
                class="search"
                placeholder="Search translation key...">

            @if(session('success'))

            <div class="success">

                {{session('success')}}

            </div>

            @endif

            <form
                method="POST"
                action="/translations/save/{{$language->code}}">

                @csrf

                @foreach($translations as $translation)

                <div class="translation-item">

                    <label>
                        {{$translation->key}}
                    </label>

                    <input
                        type="text"
                        name="keys[{{$translation->key}}]"
                        value="{{$translation->value[$language->code] ?? ''}}">

                </div>

                @endforeach

                <button>

                    Save Translations

                </button>

            </form>

            <div class="links">

                <a href="/export/{{$language->code}}">
                    Export JSON
                </a>

                <a href="/">
                    Back
                </a>

            </div>

        </div>

    </div>


    <script>
        document
            .getElementById(
                'search'
            )
            .addEventListener(
                'keyup',

                function() {

                    let value =
                        this.value
                        .toLowerCase();

                    document
                        .querySelectorAll(
                            '.translation-item'
                        )

                        .forEach(
                            item => {

                                item.style.display =

                                    item.innerText
                                    .toLowerCase()
                                    .includes(value)

                                    ?

                                    'flex'

                                    :

                                    'none';

                            });

                });
    </script>

</body>

</html>