$(document).ready(function(){
    function handleForm(formId, apiUrl, fields, extras = {}) {
        $(formId).on('submit', function(e){
            e.preventDefault();

            const data = {};
            fields.forEach(field => {
                data[field] = $(`${formId} [name$='[${field}]']`).val();
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

    const baseUrl = window.location.origin;
    const resetPath = '/reset-forgot-password';
    const resetUrl = baseUrl + resetPath;
    const apiUrl = 'https://127.0.0.1:8000/api'

    function getResetTokenFormUrl() {
        const match = window.location.pathname.match(/\/reset-forgot-password\/([^/?#]+)/);
        return match ? match[1] : null;
    }

    const resetToken = getResetTokenFormUrl();

    handleForm('#login-form', apiUrl+'/login_check', ['email', 'password']);
    handleForm('#register-form', apiUrl+'/register', ['username', 'email', 'password']);
    handleForm('#forgot-password-form', apiUrl+'/forgot-password', ['email'], { url: resetUrl });
    handleForm('#reset-forgot-password-form', apiUrl+`/reset-forgot-password/${resetToken}`, ['password']);
})
