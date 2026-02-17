import { saveFlashForNextPage } from "./../../components/apiFlash.js";

export function handleForm(formId, apiUrl, fields, extras = {}, options = {}) {
    $(formId).on('submit', function(e){
        e.preventDefault();

        const data = {};
        fields.forEach(field => {
            const baseSelector = `[id$='_${field}']`;

            const $checkbox = $(formId).find(`${baseSelector}[type="checkbox"]`);
            const $el = $checkbox.length
                ? $checkbox
                : $(formId).find(`${baseSelector}:not([type="hidden"])`).first();

            if ($el.length === 0) {
                return;
            }

            const value = $el.is(':checkbox') ? $el.prop('checked') : $el.val();

            if (field.includes('_')) {
                const [mainKey, subKey] = field.split('_');

                if (!data[mainKey]) data[mainKey] = {};
                data[mainKey][subKey] = value;
            } else {
                data[field] = value;
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

                if (options.logoutOnSuccess === true) {
                    const apiBaseUrl = import.meta.env.VITE_API_URL;

                    $.ajax({
                        url: apiBaseUrl + '/logout',
                        method: 'POST',
                        xhrFields: { withCredentials: true },
                        complete: function () {
                            $.ajax({
                                url: '/logout-session',
                                method: 'POST',
                                complete: function () {
                                    window.location.href = '/';
                                }
                            });
                        }
                    });

                    return;
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
