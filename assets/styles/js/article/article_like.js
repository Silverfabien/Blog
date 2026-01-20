document.addEventListener("click", async (e) => {
    const btn = e.target.closest('[data-like-button]');

    if (!btn) return;

    const articleId = btn.dataset.articleId;
    const res = await fetch(`/article/${articleId}/like`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

    if (!res.ok) return;

    const data = await res.json();

    btn.querySelector('[data-like-count]').textContent = data.count;

    const icon = btn.querySelector('i');

    icon.classList.toggle('fas', data.liked);
    icon.classList.toggle('far', !data.liked);
    icon.classList.toggle('text-error', data.liked);
})
