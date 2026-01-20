function loadAvatars(root = document) {
    root.querySelectorAll('.avatar[data-user-id]').forEach(avatar => {
        if (avatar.querySelector('img')) return;

        const userId = avatar.dataset.userId;
        const size = avatar.dataset.avatarSize ?? 'large';

        const apiUrl = import.meta.env.VITE_URL;
        const avatarUrl = `${apiUrl}/user/${userId}/avatar`;

        fetch(avatarUrl)
            .then(response => {
                if (response.status === 204) return null;
                if (!response.ok) throw new Error();
                return response.blob();
            })
            .then(blob => {
                if (!blob) return;

                const img = document.createElement('img');
                img.src = URL.createObjectURL(blob);
                img.alt = 'Avatar utilisateur';

                img.className =
                    size === 'small'
                        ? 'rounded-2xl w-12 h-12 object-cover'
                        : 'rounded-2xl w-24 h-24 object-cover';

                avatar.innerHTML = '';
                avatar.classList.remove('placeholder');
                avatar.appendChild(img);
            })
            .catch(() => {});
    });
}

document.addEventListener('turbo:load', () => {
    loadAvatars();
});

document.addEventListener('turbo:load', () => {
    const container = document.getElementById('comments-list');
    if (!container) return;

    const observer = new MutationObserver(mutations => {
        for (const mutation of mutations) {
            mutation.addedNodes.forEach(node => {
                if (node.nodeType !== 1) return;

                // Nouveau commentaire OU frame injectée
                if (
                    node.matches('.avatar[data-user-id]') ||
                    node.querySelector?.('.avatar[data-user-id]')
                ) {
                    loadAvatars(node);
                }
            });
        }
    });

    observer.observe(container, {
        childList: true,
        subtree: true
    });
});
