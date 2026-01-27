import $ from 'jquery';
import { handleForm } from './components/api_form.js';

$(document).ready(function(){
    const baseUrl = window.location.origin;
    const resetPath = '/reset_forgot_password';
    const resetUrl = baseUrl + resetPath;
    const apiUrl = import.meta.env.VITE_API_URL;

    function getResetTokenFromQuery() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('resetToken');
    }

    const resetToken = getResetTokenFromQuery();

    if (resetToken) {
        const modal = document.getElementById('reset_password_modal');
        if (modal) {
            modal.showModal();
            const cleanUrl = window.location.pathname;
            window.history.replaceState({}, document.title, cleanUrl);
        }
    }

    handleForm('#login-form', apiUrl+'/login_check', ['email', 'password'], {
        url: baseUrl
    });
    handleForm('#register-form', apiUrl+'/register', ['username', 'email', 'password_first', 'password_second'], {
        url: baseUrl
    });
    handleForm('#forgot-password-form', apiUrl+'/forgot_password', ['email'], {
        url: resetUrl
    });
    if (resetToken) {
        handleForm('#reset-forgot-password-form', apiUrl+`/reset_forgot_password/${resetToken}`, ['password_first', 'password_second']);
    }

});
