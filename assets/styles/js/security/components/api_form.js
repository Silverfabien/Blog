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
            success: function () {
                if (apiUrl.endsWith('/login_check')) {
                    window.location.href = '/';
                } else {
                    $(`${formId} ~ #result`).removeClass('text-danger').addClass('text-success').text('Opération réussie.');
                }
            },
            error: function (xhr) {
                const response = xhr.responseJSON;
                if (response && response.errors) {
                    $(`${formId} ~ #result`).removeClass('text-success').addClass('text-danger').text(response.errors[0]);
                } else {
                    $(`${formId} ~ #result`).removeClass('text-success').addClass('text-danger').text('Une erreur est survenue.');
                }
            }
        });
    });
}
