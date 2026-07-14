<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laravel Localizator</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bg: linear-gradient(135deg, #0f172a, #1e293b);
            --card: rgba(255,255,255,.07);
            --text: white;
            --input-bg: #111827;
            --btn: #7c3aed;
            --lang-bg: #111827;
            --success-bg: #16a34a;
            --border: rgba(124,58,237,0.3);
        }
        [data-theme="light"] {
            --bg: linear-gradient(135deg, #f8fafc, #e2e8f0);
            --card: rgba(255,255,255,.9);
            --text: #1e293b;
            --input-bg: #f1f5f9;
            --btn: #7c3aed;
            --lang-bg: #e2e8f0;
            --success-bg: #22c55e;
            --border: rgba(124,58,237,0.4);
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            color: var(--text);
            padding: 40px;
            transition: background 0.3s, color 0.3s;
        }

        .container { max-width: 1100px; margin: auto; }

        .theme-toggle {
            position: fixed; top: 20px; right: 20px;
            background: var(--card); border: 2px solid var(--btn);
            color: var(--text); padding: 10px 15px;
            border-radius: 50px; cursor: pointer; font-size: 20px;
            backdrop-filter: blur(10px); z-index: 1000;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .top-bar h1 { font-size: 1.8rem; }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .user-badge.admin { background: #7c3aed; color: white; }
        .user-badge.translator { background: #0891b2; color: white; }

        .logout-link {
            padding: 8px 16px;
            background: var(--input-bg);
            color: var(--text);
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.85rem;
            transition: background 0.2s;
        }
        .logout-link:hover { background: #ef4444; color: white; }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--card);
            backdrop-filter: blur(18px);
            padding: 25px;
            border-radius: 20px;
            border: 1px solid var(--border);
            text-align: center;
        }

        .stat-card h2 { font-size: 2.5rem; color: #a78bfa; }
        .stat-card p { color: #9ca3af; margin-top: 5px; }

        .card {
            background: var(--card);
            backdrop-filter: blur(18px);
            padding: 30px;
            border-radius: 20px;
            border: 1px solid var(--border);
            margin-bottom: 25px;
        }

        .card h2 { margin-bottom: 20px; font-size: 1.3rem; }

        .alert.success {
            background: var(--success-bg);
            padding: 14px; border-radius: 10px;
            margin-bottom: 20px; color: white;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 80px;
            gap: 12px;
            align-items: end;
        }

        input[type="text"] {
            width: 100%;
            padding: 13px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: var(--input-bg);
            color: var(--text);
            font-size: 0.95rem;
        }
        input[type="text"]:focus { outline: none; border-color: #7c3aed; }

        .flag-input { font-size: 1.3rem; text-align: center; }

        button[type="submit"] {
            padding: 13px 20px;
            border: none;
            border-radius: 10px;
            background: var(--btn);
            color: white;
            cursor: pointer;
            font-size: 0.95rem;
            transition: opacity 0.2s;
            white-space: nowrap;
        }
        button[type="submit"]:hover { opacity: 0.85; }

        .languages-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .lang-card {
            padding: 18px;
            background: var(--lang-bg);
            border-radius: 14px;
            border: 1px solid var(--border);
            transition: transform 0.2s, box-shadow 0.2s;
            text-decoration: none;
            color: var(--text);
            display: block;
        }
        .lang-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(124,58,237,0.3);
        }

        .lang-card .flag { font-size: 2rem; margin-bottom: 8px; }
        .lang-card .code { font-weight: bold; font-size: 1.1rem; }
        .lang-card .name { color: #9ca3af; font-size: 0.85rem; margin-top: 3px; }

        /* Admin Access Panel */
        .access-form {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 12px;
            align-items: end;
        }

        select {
            width: 100%;
            padding: 13px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: var(--input-bg);
            color: var(--text);
            font-size: 0.95rem;
        }
        select:focus { outline: none; border-color: #7c3aed; }

        .access-list { margin-top: 20px; }

        .access-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 15px;
            background: var(--input-bg);
            border-radius: 10px;
            margin-bottom: 8px;
        }

        .revoke-btn {
            padding: 6px 14px;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.8rem;
        }
        .revoke-btn:hover { background: #dc2626; }

        .section-label {
            font-size: 0.8rem;
            color: #9ca3af;
            margin-bottom: 6px;
        }
    </style>
</head>
<body>

<button class="theme-toggle" id="themeToggle">🌙</button>

<div class="container">

    <div class="top-bar">
        <h1>🌐 Localizator</h1>
        @auth
        <div class="user-info">
            <span>👤 {{ Auth::user()->name }}</span>
            <span class="user-badge {{ Auth::user()->role }}">{{ strtoupper(Auth::user()->role) }}</span>
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" style="padding:8px 16px; background:var(--input-bg); color:var(--text); border:none; border-radius:8px; cursor:pointer; font-size:0.85rem;">
                    Logout
                </button>
            </form>
        </div>
        @endauth
    </div>

    <!-- Stats -->
    <div class="stats">
        <div class="stat-card">
            <h2>{{ $totalLanguages }}</h2>
            <p>Languages</p>
        </div>
        <div class="stat-card">
            <h2>{{ $totalKeys }}</h2>
            <p>Translation Keys</p>
        </div>
        <div class="stat-card">
            <h2>{{ $totalLanguages * $totalKeys }}</h2>
            <p>Total Translations</p>
        </div>
    </div>

    <!-- Add Language (Admin only) -->
    @if(Auth::user()->role === 'admin')
    <div class="card">
        <h2>➕ Add Language</h2>

        @if(session('success'))
            <div class="alert success">✅ {{ session('success') }}</div>
        @endif

        <form method="POST" action="/language">
            @csrf
            <div class="form-grid">
                <div>
                    <div class="section-label">Language Code</div>
                    <input type="text" name="code" placeholder="en, fr, gu..." required>
                </div>
                <div>
                    <div class="section-label">Language Name</div>
                    <input type="text" name="name" placeholder="English, French..." required>
                </div>
                <div>
                    <div class="section-label">Flag</div>
                    <input type="text" name="flag" placeholder="🇺🇸" class="flag-input">
                </div>
            </div>
            <button type="submit" style="margin-top:15px; width:100%; padding:13px;">Add Language</button>
        </form>
    </div>
    @endif

    <!-- Languages List -->
    <div class="card">
        <h2>🌍 Languages</h2>
        <div class="languages-grid">
            @forelse($languages as $lang)
                @if(Auth::user()->role === 'admin' || Auth::user()->hasLanguageAccess($lang->id))
                <a href="/translations/{{ $lang->code }}" class="lang-card">
                    <div class="flag">{{ $lang->flag ?: '🌐' }}</div>
                    <div class="code">{{ strtoupper($lang->code) }}</div>
                    <div class="name">{{ $lang->name }}</div>
                </a>
                @endif
            @empty
                <p style="color:#9ca3af;">No languages added yet.</p>
            @endforelse
        </div>
    </div>

    <!-- Admin: User Access Management -->
    @if(Auth::user()->role === 'admin')
    <div class="card">
        <h2>🔐 User Language Access</h2>

        @if(session('access_success'))
            <div class="alert success">✅ {{ session('access_success') }}</div>
        @endif

        <form method="POST" action="/admin/access/grant">
            @csrf
            <div class="access-form">
                <div>
                    <div class="section-label">Select User</div>
                    <select name="user_id" required>
                        <option value="">Choose user...</option>
                        @foreach($users as $user)
                            @if($user->role !== 'admin')
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div>
                    <div class="section-label">Select Language</div>
                    <select name="language_id" required>
                        <option value="">Choose language...</option>
                        @foreach($languages as $lang)
                            <option value="{{ $lang->id }}">{{ $lang->flag }} {{ $lang->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <div class="section-label">&nbsp;</div>
                    <button type="submit">Grant Access</button>
                </div>
            </div>
        </form>

        <div class="access-list">
            @foreach($accessList as $access)
            <div class="access-item">
                <span>👤 {{ $access->user->name }} → {{ $access->language->flag }} {{ $access->language->name }}</span>
                <form method="POST" action="/admin/access/revoke/{{ $access->id }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="revoke-btn">Revoke</button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

<script>
    const themeToggle = document.getElementById('themeToggle');
    const html = document.documentElement;
    const savedTheme = localStorage.getItem('theme') || 'dark';
    html.setAttribute('data-theme', savedTheme);
    themeToggle.textContent = savedTheme === 'dark' ? '🌙' : '☀️';

    themeToggle.addEventListener('click', () => {
        const newTheme = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        themeToggle.textContent = newTheme === 'dark' ? '🌙' : '☀️';
    });
</script>

</body>
</html>
