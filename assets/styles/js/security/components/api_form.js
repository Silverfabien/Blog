export function handleForm(formId, apiUrl, fields, extras = {}) {
    $(formId).on('submit', function(e){
        e.preventDefault();

        const data = {};
        fields.forEach(field => {
            // On cherche l'élément dont l'ID finit par le nom du champ
            // Exemple : #reset_password_password_first
            const $el = $(formId).find(`[id$='_${field}']`);

            if ($el.length > 0) {
                // On garde une clé propre pour l'API (ex: 'password' au lieu de 'password_first')
                const cleanKey = field.split('_')[0];
                data[cleanKey] = $el.val();
            } else {
                console.warn(`Champ non trouvé avec l'ID finissant par : _${field}`);
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
                    $(`${formId} ~ #result`).text('Opération réussie.');
                }
            },
            error: function (t) {
                $(`${formId} ~ #result`).text('Une erreur est survenue.');
            }
        });
    });
}
