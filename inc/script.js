const form = document.querySelector('.search');
const chips = document.querySelector('.chips');
const results = document.getElementById('results');

// Ask index.php for a page in the background, then swap in the new parts
async function load(url) {
    const res = await fetch(url);
    const html = new DOMParser().parseFromString(await res.text(), 'text/html');

    results.innerHTML = html.getElementById('results').innerHTML;
    chips.innerHTML = html.querySelector('.chips').innerHTML;

    form.elements.category.value = url.searchParams.get('category') || '';
    form.elements.intl.value = url.searchParams.get('intl') || '';
}

// Chip click: keep the typed keyword, change the category

chips.addEventListener('click', function (e) {
  const btn = e.target.closest('button.chip');
  if (!btn) return;
  const url = new URL(location.pathname, location.origin);
  url.searchParams.set('q', form.elements.q.value);
  if (btn.dataset.category) url.searchParams.set('category', btn.dataset.category);
  if (btn.dataset.intl) url.searchParams.set('intl', '1');
  load(url);
});

// Search button: send the form values without reloading
form.addEventListener('submit', function (e) {
    e.preventDefault();
    const url = new URL(location.pathname, location.origin);
    url.search = new URLSearchParams(new FormData(form)).toString();
    load(url);
});

// text in searchbox refreshed and searched within timer
let timer;
form.elements.q.addEventListener('input', function () {
  clearTimeout(timer);
  timer = setTimeout(function () { form.requestSubmit(); }, 300);
});

const postDialog = document.getElementById('postDialog');

document.getElementById('openPost').addEventListener('click', function () { postDialog.showModal(); });
document.getElementById('cancelPost').addEventListener('click', function () { postDialog.close(); });

// If PHP found errors, reopen the dialog so the user sees them
if (postDialog.dataset.open) postDialog.showModal();