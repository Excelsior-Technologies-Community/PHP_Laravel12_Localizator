<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Laravel Localizator</title>

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            font-family:'Segoe UI';
            background:linear-gradient(
            135deg,
            #0f172a,
            #1e293b
            );

            min-height:100vh;
            color:white;
            padding:40px;
        }

        .container{
            max-width:1100px;
            margin:auto;
        }

        .stats{
            display:grid;
            grid-template-columns:repeat(2,1fr);
            gap:20px;
            margin-bottom:30px;
        }

        .card{
            background:rgba(
            255,255,255,.07);

            backdrop-filter:blur(18px);

            padding:25px;

            border-radius:20px;
        }

        .stat h2{
            font-size:35px;
        }

        .success{
            background:#16a34a;
            padding:15px;
            border-radius:10px;
            margin-bottom:20px;
        }

        input{
            width:100%;
            padding:15px;
            margin-bottom:15px;
            border:none;
            border-radius:10px;
            background:#111827;
            color:white;
        }

        button{
            width:100%;
            padding:15px;
            border:none;
            border-radius:10px;
            cursor:pointer;
            background:#7c3aed;
            color:white;
        }

        button:hover{
            opacity:.9;
        }

        .language{
            margin-top:15px;
            padding:18px;
            border-radius:12px;
            background:#111827;
            transition:.3s;
        }

        .language:hover{
            transform:translateY(-3px);
        }

        a{
            text-decoration:none;
            color:white;
        }

    </style>

</head>

<body>

<div class="container">

    <div class="stats">

        <div class="card stat">
            <h2>{{$totalLanguages}}</h2>
            <p>Total Languages</p>
        </div>

        <div class="card stat">
            <h2>{{$totalKeys}}</h2>
            <p>Translation Keys</p>
        </div>

    </div>

    <div class="card">

        <h1 style="margin-bottom:20px;">
            Language Manager
        </h1>

        @if(session('success'))
            <div class="success">
                {{session('success')}}
            </div>
        @endif

        <form method="POST" action="/language">

            @csrf

            <input
                type="text"
                name="code"
                placeholder="Language code (en)"
                required
            >

            <input
                type="text"
                name="name"
                placeholder="Language name"
                required
            >

            <button>
                Add Language
            </button>

        </form>

        <h2 style="margin-top:30px;">
            Languages
        </h2>

        @foreach($languages as $lang)

        <div class="language">

            <a href="/translations/{{$lang->code}}">

                {{$lang->code}}
                —
                {{$lang->name}}

            </a>

        </div>

        @endforeach

    </div>

</div>

</body>
</html>