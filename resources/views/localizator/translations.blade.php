<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $language->name }} Translations</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bg: linear-gradient(135deg, #020617, #1e293b);
            --card: rgba(255,255,255,.07);
            --text: white;
            --input-bg: #111827;
            --btn: #7c3aed;
            --progress-bg: #111827;
            --link-bg: #111827;
            --success-bg: #16a34a;
            --border: rgba(124,58,237,0.3);
            --preview-bg: #1f2937;
        }
        [data-theme="light"] {
            --bg: linear-gradient(135deg, #f8fafc, #e2e8f0);
            --card: rgba(255,255,255,.9);
            --text: #1e293b;
            --input-bg: #f1f5f9;
            --btn: #7c3aed;
            --progress-bg: #e2e8f0;
            --link-bg: #e2e8f0;
            --success-bg: #22c55e;
            --border: rgba(124,58,237,0.4);
            --preview-bg: #ffffff;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: var(--bg);
            padding: 30px;
            color: var(--text);
            min-height: 100vh;
            transition: background 0.3s, color 0.3s;
        }

        .container { max-width: 1400px; margin: auto; }

        .theme-toggle {
            position: fixed; top: 20px; right: 20px;
            background: var(--card); border: 2px solid var(--btn);
            color: var(--text); padding: 10px 15px;
            border-radius: 50px; cursor: pointer; font-size: 20px;
            backdrop-filter: blur(10px); z-index: 1000;
        }

        .main-layout {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 25px;
            align-items: start;
        }

        .card {
            background: var(--card);
            backdrop-filter: blur(18px);
            padding: 30px;
            border-radius: 20px;
            border: 1px solid var(--border);
        }

        .card h1 { font-size: 1.6rem; margin-bottom: 5px; }

        .progress-box {
            height: 32px;
            background: var(--progress-bg);
            border-radius: 30px;
            overflow: hidden;
            margin: 15px 0 20px;
        }

        .progress {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(90deg, #7c3aed, #a855f7);
            font-weight: bold;
            font-size: 0.85rem;
            transition: width 0.5s ease;
        }

        .search-box {
            position: relative;
            margin-bottom: 20px;
        }

        .search-box input {
            width: 100%;
            padding: 12px 12px 12px 40px;
            background: var(--input-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text);
            font-size: 0.95rem;
        }

        .search-box input:focus { outline: none; border-color: #7c3aed; }

        .search-box::before {
            content: '🔍';
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 0.9rem;
        }

        .alert {
            padding: 14px;
            border-radius: 10px;
            margin-bottom: 15px;
            font-weight: 500;
        }
        .alert.success { background: var(--success-bg); color: white; }
        .alert.warning { background: #f59e0b; color: white; }
        .alert.error { background: #ef4444; color: white; font-size: 0.9rem; }

        .translation-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 15px;
            margin-bottom: 10px;
            background: var(--input-bg);
            border-radius: 10px;
            border-left: 3px solid transparent;
            transition: border-color 0.2s, transform 0.2s;
        }

        .translation-item:hover { transform: translateX(3px); border-left-color: #7c3aed; }
        .translation-item.has-value { border-left-color: #22c55e; }
        .translation-item.no-value { border-left-color: #ef4444; }

        .translation-item label {
            font-weight: 600;
            font-size: 0.9rem;
            color: #a78bfa;
            min-width: 130px;
        }

        .translation-item input {
            flex: 1;
            padding: 8px 12px;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-size: 0.95rem;
            margin-left: 15px;
        }

        .translation-item input:focus { outline: none; border-color: #7c3aed; }

        .save-btn {
            margin-top: 20px;
            width: 100%;
            padding: 14px;
            border: none;
            background: var(--btn);
            color: white;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1rem;
            transition: opacity 0.2s;
        }
        .save-btn:hover { opacity: 0.85; }

        .links {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 15px;
        }

        .links a {
            padding: 12px;
            background: var(--link-bg);
            text-align: center;
            border-radius: 10px;
            text-decoration: none;
            color: var(--text);
            font-size: 0.9rem;
            transition: background 0.2s;
        }
        .links a:hover { background: var(--btn); color: white; }

        /* Live Preview Panel */
        .preview-panel {
            position: sticky;
            top: 20px;
        }

        .preview-panel h3 {
            color: #a78bfa;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .live-badge {
            font-size: 0.65rem;
            padding: 2px 8px;
            border-radius: 20px;
            background: #22c55e;
            color: white;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        .preview-item {
            padding: 10px 12px;
            margin-bottom: 8px;
            background: var(--preview-bg);
            border-radius: 8px;
            border-left: 3px solid transparent;
        }

        .preview-item.filled { border-left-color: #22c55e; }
        .preview-item.empty { border-left-color: #ef4444; }

        .preview-item .pk {
            font-size: 0.75rem;
            color: #6b7280;
            margin-bottom: 3px;
        }

        .preview-item .pv {
            font-size: 0.95rem;
            font-weight: 500;
        }

        .preview-item .pv.empty-val {
            color: #ef4444;
            font-style: italic;
            font-size: 0.8rem;
        }

        .pagination-wrap { margin-top: 20px; }

        @media (max-width: 900px) {
            .main-layout { grid-template-columns: 1fr; }
            .preview-panel { position: static; }
        }
    </style>
</head>
<body>

<button class="theme-toggle" id="themeToggle">🌙</button>

<div class="container">
    <div class="main-layout">

        <!-- Main Translation Card -->
        <div class="card">
            <h1>
                @if($language->flag) {{ $language->flag }} @endif
                {{ $language->name }} Translations
            </h1>
            <p style="color:#9ca3af; margin-top:5px;">
                Completed: <strong id="completedCount">{{ $completed }}</strong> / <strong>{{ $total }}</strong>
            </p>

            <div class="progress-box">
                <div class="progress" id="progressBar" style="width:{{ $progress }}%">
                    <span id="progressText">{{ $progress }}%</span>
                </div>
            </div>

            <div class="search-box">
                <input id="searchInput" placeholder="Search translation key...">
            </div>

            @if(session('success'))
                <div class="alert success">✅ {{ session('success') }}</div>
            @endif
            @if(session('warning'))
                <div class="alert warning">⚠️ {{ session('warning') }}</div>
            @endif
            @if($errors->any())
                @foreach($errors->all() as $error)
                    <div class="alert error">❌ {{ $error }}</div>
                @endforeach
            @endif

            <form method="POST" action="/translations/save/{{ $language->code }}" id="translationForm">
                @csrf
                <div id="translationItems">
                    @foreach($translations as $translation)
                    <div class="translation-item {{ !empty($translation->value[$language->code] ?? '') ? 'has-value' : 'no-value' }}">
                        <label title="{{ $translation->key }}">{{ $translation->key }}</label>
                        <input
                            type="text"
                            name="keys[{{ $translation->key }}]"
                            value="{{ $translation->value[$language->code] ?? '' }}"
                            data-key="{{ $translation->key }}"
                            placeholder="Enter translation..."
                            class="trans-input"
                        >
                    </div>
                    @endforeach
                </div>

                <button type="submit" class="save-btn">💾 Save Translations</button>
            </form>

            <div class="pagination-wrap" id="paginationLinks">
                {{ $translations->links() }}
            </div>

            <div class="links">
                <a href="/export/{{ $language->code }}">📤 Export JSON</a>
                <a href="/preview/{{ $language->code }}">👁️ Full Preview</a>
                <a href="/history/{{ $language->code }}">📜 History</a>
                <a href="/">← Back</a>
            </div>
        </div>

        <!-- Live Preview Side Panel -->
        <div class="preview-panel">
            <div class="card">
                <h3>👁️ Live Preview <span class="live-badge">LIVE</span></h3>
                <div id="livePreviewList">
                    @foreach($translations as $translation)
                    @php $val = $translation->value[$language->code] ?? ''; @endphp
                    <div class="preview-item {{ $val ? 'filled' : 'empty' }}" id="lp-{{ Str::slug($translation->key, '_') }}">
                        <div class="pk">{{ $translation->key }}</div>
                        <div class="pv {{ $val ? '' : 'empty-val' }}" id="lpv-{{ Str::slug($translation->key, '_') }}">
                            {{ $val ?: '— not translated —' }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    // Live preview update on input
    document.querySelectorAll('.trans-input').forEach(input => {
        input.addEventListener('input', function() {
            const key = this.dataset.key;
            const slug = slugify(key);
            const previewVal = document.getElementById('lpv-' + slug);
            const previewRow = document.getElementById('lp-' + slug);
            const itemRow = this.closest('.translation-item');

            if (previewVal && previewRow) {
                const val = this.value.trim();
                if (val) {
                    previewVal.textContent = val;
                    previewVal.classList.remove('empty-val');
                    previewRow.classList.replace('empty', 'filled');
                    itemRow.classList.replace('no-value', 'has-value');
                } else {
                    previewVal.textContent = '— not translated —';
                    previewVal.classList.add('empty-val');
                    previewRow.classList.replace('filled', 'empty');
                    itemRow.classList.replace('has-value', 'no-value');
                }
            }
        });
    });

    function slugify(str) {
        return str.replace(/[^a-zA-Z0-9]/g, '_').replace(/_+/g, '_').replace(/^_|_$/g, '');
    }

    // AJAX Search
    const searchInput = document.getElementById('searchInput');
    const translationItems = document.getElementById('translationItems');
    const paginationLinks = document.getElementById('paginationLinks');
    let timeout;

    searchInput.addEventListener('keyup', function() {
        clearTimeout(timeout);
        const query = this.value;
        timeout = setTimeout(() => {
            if (query.length > 0) {
                fetch(`/search/{{ $language->code }}?q=${encodeURIComponent(query)}`)
                    .then(r => r.json())
                    .then(data => {
                        translationItems.innerHTML = data.html;
                        paginationLinks.innerHTML = data.pagination;
                        document.getElementById('completedCount').textContent = data.completed;
                        document.getElementById('progressBar').style.width = data.progress + '%';
                        document.getElementById('progressText').textContent = data.progress + '%';
                        // Re-attach listeners
                        attachInputListeners();
                    });
            } else {
                location.reload();
            }
        }, 300);
    });

    function attachInputListeners() {
        document.querySelectorAll('.trans-input').forEach(input => {
            input.addEventListener('input', function() {
                const key = this.dataset.key;
                const slug = slugify(key);
                const previewVal = document.getElementById('lpv-' + slug);
                const previewRow = document.getElementById('lp-' + slug);
                const itemRow = this.closest('.translation-item');
                if (previewVal && previewRow) {
                    const val = this.value.trim();
                    if (val) {
                        previewVal.textContent = val;
                        previewVal.classList.remove('empty-val');
                        previewRow.classList.replace('empty', 'filled');
                        if (itemRow) itemRow.classList.replace('no-value', 'has-value');
                    } else {
                        previewVal.textContent = '— not translated —';
                        previewVal.classList.add('empty-val');
                        previewRow.classList.replace('filled', 'empty');
                        if (itemRow) itemRow.classList.replace('has-value', 'no-value');
                    }
                }
            });
        });
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
</script>

</body>
</html>
