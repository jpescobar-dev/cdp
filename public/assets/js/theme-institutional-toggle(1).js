
(function () {
    var saved = localStorage.getItem('institutional-theme');
    if (saved === 'dark' || saved === 'light') {
        document.documentElement.setAttribute('data-theme', saved);
    }
})();

function toggleInstitutionalTheme() {
    var root = document.documentElement;
    var current = root.getAttribute('data-theme') || 'light';
    var next = current === 'dark' ? 'light' : 'dark';
    root.setAttribute('data-theme', next);
    localStorage.setItem('institutional-theme', next);
}
