<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Live Preview - {{ $language->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bg: linear-gradient(135deg, #020617, #1e293b);
            --card: rgba(255,255,255,.07);
            --text: white;
            --input-bg: #111827;
            --btn: #7c3aed;
            --preview-bg: #1f2937;
            --border: rgba(124,58,237,0.3);
        }
        [data-theme="light"] {
            --bg: linear-gradient(135deg, #f8fafc, #e2e8f0);
            --card: rgba(255,255,255,.9);
            --text: #1e293b;
            --input-bg: #f1f5f9;
            --btn: #7c3aed;
            --preview-bg: #ffffff;
            --border: rgba(124,58,237,0.4);
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: var(--bg);
            padding: 40px;
            color: var(--text);
            min-height: 100vh;
            transition: background 0.3s, color 0.3s;
        }

        .container { max-width: 1300px; margin: auto; }

        .theme-toggle {
            position: fixed; top: 20px; right: 20px;
            background: var(--card); border: 2px solid var(--btn);
            color: var(--text); padding: 10px 15px;
            border-radius: 50px; cursor: pointer; font-size: 20px;
            backdrop-filter: blur(10px); z-index: 1000;
        }

        .header { margin-bottom: 30px; }
        .header h1 { font-size: 1.8rem; }
        .header p { color: #9ca3af; margin-top: 8px; }

        .layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .panel {
            background: var(--card);
            backdrop-filter: blur(18px);
            padding: 25px;
            border-radius: 20px;
            border: 1px solid var(--border);
        }

        .panel h3 {
            color: #a78bfa;
            margin-bottom: 20px;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .edit-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            margin-bottom: 10px;
            background: var(--input-bg);
            border-radius: 10px;
        }

        .edit-row label {
            min-width: 120px;
            font-weight: 600;
            font-size: 0.85rem;
            color: #9ca3af;
        }

        .edit-row input {
            flex: 1;
            padding: 8px 12px;
            background: var(--preview-bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-size: 0.95rem;
            transition: border-color 0.2s;
        }

        .edit-row input:focus {
            outline: none;
            border-color: #7c3aed;
        }

        .preview-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            margin-bottom: 10px;
            background: var(--input-bg);
            border-radius: 10px;
            transition: background 0.2s;
        }

        .preview-row.updated {
            background: rgba(124, 58, 237, 0.15);
            border-left: 3px solid #7c3aed;
        }

        .preview-row .key-label {
            min-width: 120px;
            font-size: 0.8rem;
            color: #6b7280;
        }

        .preview-row .val {
            flex: 1;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s;
        }

        .preview-row .val.empty {
            color: #ef4444;
            font-style: italic;
            font-size: 0.85rem;
        }

        .badge {
            font-size: 0.7rem;
            padding: 2px 8px;
            border-radius: 20px;
            background: #7c3aed;
            color: white;
        }

        .save-btn {
            width: 100%;
            padding: 14px;
            background: var(--btn);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1rem;
            margin-top: 15px;
            transition: opacity 0.2s;
        }
        .save-btn:hover { opacity: 0.85; }

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

        .stats-bar {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-chip {
            padding: 8px 16px;
            background: var(--input-bg);
            border-radius: 20px;
            font-size: 0.85rem;
        }

        .stat-chip span { color: #a78bfa; font-weight: bold; }

        @media (max-width: 768px) {
            .layout { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<button class="theme-toggle" id="themeToggle">🌙</button>

<div class="container">
    <div class="header">
        <h1>
            @if($language->flag) {{ $language->flag }} @endif
            Live Preview — {{ $language->name }}
        </h1>
        <p>Type in the left panel to see real-time preview on the right</p>
    </div>

    <div class="stats-bar">
        <div class="stat-chip">Total: <span id="totalCount">{{ count($translationsData) }}</span></div>
        <div class="stat-chip">Translated: <span id="translatedCount">{{ collect($translationsData)->filter()->count() }}</span></div>
        <div class="stat-chip">Missing: <span id="missingCount">{{ collect($translationsData)->filter(fn($v) => empty($v))->count() }}</span></div>
    </div>

    <div class="layout">
        <!-- Edit Panel -->
        <div class="panel">
            <h3>✏️ Edit Translations</h3>
            <form method="POST" action="/translations/save/{{ $language->code }}" id="previewForm">
                @csrf
                @foreach($translationsData as $key => $value)
                <div class="edit-row">
                    <label title="{{ $key }}">{{ Str::limit($key, 15) }}</label>
                    <input
                        type="text"
                        name="keys[{{ $key }}]"
                        value="{{ $value }}"
                        data-key="{{ $key }}"
                        placeholder="Enter translation..."
                        class="preview-input"
                    >
                </div>
                @endforeach
                <button type="submit" class="save-btn">💾 Save All Translations</button>
            </form>
        </div>

        <!-- Live Preview Panel -->
        <div class="panel">
            <h3>👁️ Live Preview <span class="badge" id="liveIndicator">LIVE</span></h3>
            @foreach($translationsData as $key => $value)
            <div class="preview-row" id="preview-row-{{ Str::slug($key, '_') }}">
                <span class="key-label">{{ Str::limit($key, 15) }}</span>
                <span class="val {{ empty($value) ? 'empty' : '' }}" id="preview-{{ Str::slug($key, '_') }}">
                    {{ $value ?: '— not translated —' }}
                </span>
            </div>
            @endforeach
        </div>
    </div>

    <a href="/translations/{{ $language->code }}" class="back-link">← Back to Translations</a>
</div>

<script>
    // Real-time preview update
    document.querySelectorAll('.preview-input').forEach(input => {
        input.addEventListener('input', function() {
            const key = this.dataset.key;
            const slug = key.replace(/[^a-zA-Z0-9]/g, '_').replace(/_+/g, '_').replace(/^_|_$/g, '');
            const previewEl = document.getElementById('preview-' + slug);
            const rowEl = document.getElementById('preview-row-' + slug);

            if (previewEl) {
                const val = this.value.trim();
                if (val) {
                    previewEl.textContent = val;
                    previewEl.classList.remove('empty');
                    rowEl.classList.add('updated');
                } else {
                    previewEl.textContent = '— not translated —';
                    previewEl.classList.add('empty');
                    rowEl.classList.remove('updated');
                }
                updateStats();
            }
        });
    });

    function updateStats() {
        const inputs = document.querySelectorAll('.preview-input');
        let translated = 0;
        inputs.forEach(i => { if (i.value.trim()) translated++; });
        document.getElementById('translatedCount').textContent = translated;
        document.getElementById('missingCount').textContent = inputs.length - translated;
    }

    // Theme toggle
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

    // Blink live indicator
    setInterval(() => {
        const el = document.getElementById('liveIndicator');
        el.style.opacity = el.style.opacity === '0' ? '1' : '0';
    }, 800);
</script>

</body>
</html>
