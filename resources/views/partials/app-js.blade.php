<script>
(function () {
    if (localStorage.getItem('theme') === 'light') {
        document.body.classList.add('theme-light');
        const btn = document.getElementById('theme-toggle');
        if (btn) btn.textContent = '☀️';
    }
})();
function toggleTheme() {
    const isLight = document.body.classList.toggle('theme-light');
    localStorage.setItem('theme', isLight ? 'light' : 'dark');
    document.getElementById('theme-toggle').textContent = isLight ? '☀️' : '🌙';
}

const translations = {
    en: {
        seats:      'Seats',
        overview:   'Overview',
        actions:    'Actions',
        sign_out:   'Sign out',
        dashboard:  'Dashboard',
        employees:  'Employees',
        settings:   'Settings',
        export_all: 'Export All',
        live_map:   'Live Map',
    },
    ka: {
        seats:      'ადგილები',
        overview:   'მიმოხილვა',
        actions:    'მოქმედებები',
        sign_out:   'გასვლა',
        dashboard:  'პანელი',
        employees:  'თანამშრომლები',
        settings:   'პარამეტრები',
        export_all: 'ექსპორტი',
        live_map:   'რუქა',
    },
};

function setLang(lang) {
    localStorage.setItem('dashboard_lang', lang);
    document.querySelectorAll('[data-i18n]').forEach(el => {
        if (translations[lang]?.[el.dataset.i18n] !== undefined)
            el.textContent = translations[lang][el.dataset.i18n];
    });
    document.querySelectorAll('.lang-btn').forEach(b =>
        b.classList.toggle('active', b.textContent.toLowerCase() === lang)
    );
}
</script>

@yield('scripts')

<script>
(function () {
    const saved = localStorage.getItem('dashboard_lang') || 'en';
    if (saved !== 'en') setLang(saved);
    else document.querySelector('.lang-btn')?.classList.add('active');
})();
</script>
