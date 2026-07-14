<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Translation History - {{ $language->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bg: linear-gradient(135deg, #020617, #1e293b);
            --card: rgba(255,255,255,.07);
            --text: white;
            --input-bg: #111827;
            --btn: #7c3aed;
            --border: rgba(124,58,237,0.3);
            --item-bg: #111827;
            --val-bg: #1f2937;
        }
        [data-theme="light"] {
            --bg: linear-gradient(135deg, #f8fafc, #e2e8f0);
            --card: rgba(255,255,255,.9);
            --text: #1e293b;
            --input-bg: #f1f5f9;
            --btn: #7c3aed;
            --border: rgba(124,58,237,0.4);
            --item-bg: #f1f5f9;
            --val-bg: #ffffff;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: var(--bg);
            padding: 40px;
            color: var(--text);
            min-height: 100vh;
            transition: background 0.3s, color 0.3s;
        }

        .container { max-width: 1000px; margin: auto; }

        .theme-toggle {
            position: fixed; top: 20px; right: 20px;
            background: var(--card); border: 2px solid var(--btn);
            color: var(--text); padding: 10px 15px;
            border-radius: 50px; cursor: pointer; font-size: 20px;
            backdrop-filter: blur(10px); z-index: 1000;
        }

        .card {
            background: var(--card);
            backdrop-filter: blur(18px);
            padding: 30px;
            border-radius: 20px;
            border: 1px solid var(--border);
        }

        .card h1 { font-size: 1.6rem; margin-bottom: 5px; }
        .card .subtitle { color: #9ca3af; margin-bottom: 25px; font-size: 0.9rem; }

        .alert.success {
            background: #16a34a;
            padding: 14px; border-radius: 10px;
            margin-bottom: 20px; color: white;
        }

        .history-item {
            background: var(--item-bg);
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 14px;
            border-left: 4px solid #7c3aed;
            transition: transform 0.2s;
        }
        .history-item:hover { transform: translateX(4px); }

        .history-item .item-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .history-item h3 {
            color: #a78bfa;
            font-size: 1rem;
        }

        .history-item .meta {
            color: #9ca3af;
            font-size: 0.8rem;
            text-align: right;
        }

        .values {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 12px;
        }

        .value-box {
            background: var(--val-bg);
            padding: 12px;
            border-radius: 10px;
        }

        .value-box.old { border-left: 3px solid #ef4444; }
        .value-box.new { border-left: 3px solid #22c55e; }

        .value-box label {
            display: block;
            font-size: 0.75rem;
            font-weight: bold;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .value-box.old label { color: #ef4444; }
        .value-box.new label { color: #22c55e; }

        .value-box p {
            font-size: 0.95rem;
            word-break: break-word;
        }

        .rollback-form { margin-top: 12px; }

        .rollback-btn {
            background: #f59e0b;
            color: white;
            padding: 8px 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: background 0.2s;
        }
        .rollback-btn:hover { background: #d97706; }

        .empty-state {
            text-align: center;
            padding: 50px;
            color: #9ca3af;
        }
        .empty-state .icon { font-size: 3rem; margin-bottom: 15px; }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 24px;
            background: var(--input-bg);
            color: var(--text);
            text-decoration: none;
            border-radius: 10px;
            transition: background 0.2s;
        }
        .back-link:hover { background: var(--btn); color: white; }

        .pagination-wrap { margin-top: 20px; }
    </style>
</head>
<body>

<button class="theme-toggle" id="themeToggle">🌙</button>

<div class="container">
    <div class="card">
        <h1>
            @if($language->flag) {{ $language->flag }} @endif
            Translation History — {{ $language->name }}
        </h1>
        <p class="subtitle">All changes with rollback support</p>

        @if(session('success'))
            <div class="alert success">✅ {{ session('success') }}</div>
        @endif

        @forelse($histories as $history)
            <div class="history-item">
                <div class="item-header">
                    <h3>🔑 {{ $history->translation->key }}</h3>
                    <div class="meta">
                        🕐 {{ $history->created_at->format('M d, Y H:i') }}<br>
                        @if($history->user)
                            👤 {{ $history->user->name }}
                        @else
                            👤 Unknown
                        @endif
                    </div>
                </div>

                <div class="values">
                    <div class="value-box old">
                        <label>⬅ Old Value</label>
                        <p>{{ $history->old_value[$language->code] ?? '(empty)' }}</p>
                    </div>
                    <div class="value-box new">
                        <label>➡ New Value</label>
                        <p>{{ $history->new_value[$language->code] ?? '(empty)' }}</p>
                    </div>
                </div>

                <form class="rollback-form" method="POST" action="/rollback/{{ $language->code }}/{{ $history->id }}">
                    @csrf
                    <button type="submit" class="rollback-btn">↩ Rollback to This Version</button>
                </form>
            </div>
        @empty
            <div class="empty-state">
                <div class="icon">📭</div>
                <p>No history found for {{ $language->name }}</p>
            </div>
        @endforelse

        <div class="pagination-wrap">{{ $histories->links() }}</div>

        <a href="/translations/{{ $language->code }}" class="back-link">← Back to Translations</a>
    </div>
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
