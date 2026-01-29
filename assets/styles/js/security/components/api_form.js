import { saveFlashForNextPage } from "./../../components/apiFlash.js";

export function handleForm(formId, apiUrl, fields, extras = {}) {
    $(formId).on('submit', function(e){
        e.preventDefault();

        const data = {};
        fields.forEach(field => {
            const $el = $(formId).find(`[id$='_${field}']`);

            // Pour RepeatedPassword
            if ($el.length > 0) {
                if (field.includes('_')) {
                    const parts = field.split('_');
                    const mainKey = parts[0];
                    const subKey = parts[1];

                    if (!data[mainKey]) data[mainKey] = {};
                    data[mainKey][subKey] = $el.val();
                } else {
                    data[field] = $el.val();
                }
            }
        });

        Object.assign(data, extras);

        $.ajax({
            url: apiUrl,
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            data: JSON.stringify(data),
            xhrFields: { withCredentials: true },
            success: function (response) {
                import('../../components/apiFlash.js').then(({ handleApiFlash, addFlash }) => {
                    if (!handleApiFlash(response)) {
                        addFlash('success', 'Opération réussi')
                    }
                })

                const fileInput = document.querySelector(
                    `${formId} input[type="file"]`
                );

                if (fileInput && fileInput.files.length > 0) {
                    const formData = new FormData();
                    formData.append('picture', fileInput.files[0]);

                    fetch(
                        import.meta.env.VITE_API_URL + '/user/avatar',
                        {
                            method: 'POST',
                            body: formData,
                            credentials: "include"
                        }
                    ).finally(() => {
                        if (formId === '#user-edit') {
                            Turbo.visit(window.location.pathname, {
                                frame: 'account-profil',
                            });
                        }
                    });
                }

                if (apiUrl.endsWith('/login_check')) {
                    saveFlashForNextPage(response.type ?? 'success', response.message ?? 'Connexion réussie.');
                    window.location.href = '/';
                } else {
                    $(`${formId} ~ #result`).removeClass('text-danger').addClass('text-success').text('Opération réussie.');
                }

                setTimeout(() => {
                    $('#username').text(response.username);
                    $('#email').text(response.email);
                    Turbo.visit(window.location.pathname, {
                        frame: 'account-profil'
                    });
                }, 1000)
            },
            error: function (xhr) {
                const response = xhr.responseJSON;

                import('../../components/apiFlash.js').then(({ handleApiFlash, addFlash }) => {
                    if (!handleApiFlash(response)) {
                        addFlash('error', 'Une erreur est survenue.');
                    }
                })

                if (response && response.errors) {
                    $(`${formId} ~ #result`).removeClass('text-success').addClass('text-danger').text(response.errors[0]);
                } else {
                    $(`${formId} ~ #result`).removeClass('text-success').addClass('text-danger').text('Une erreur est survenue.');
                }
            }
        });
    });
}
