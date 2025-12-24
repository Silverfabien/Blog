import $ from 'jquery';
import { handleForm } from './components/api_form.js';

$(document).ready(function(){
    const baseUrl = window.location.origin;
    const resetPath = '/reset_forgot_password';
    const resetUrl = baseUrl + resetPath;
    const apiUrl = import.meta.env.VITE_API_URL;

    function getResetTokenFormUrl() {
        const match = window.location.pathname.match(/\/reset_forgot_password\/([^/?#]+)/);
        return match ? match[1] : null;
    }

    const resetToken = getResetTokenFormUrl();

    handleForm('#login-form', apiUrl+'/login_check', ['email', 'password']);
    handleForm('#register-form', apiUrl+'/register', ['username', 'email', 'password_first'], {
        url: baseUrl
    });
    handleForm('#forgot-password-form', apiUrl+'/forgot_password', ['email'], { url: resetUrl });
    handleForm('#reset-forgot-password-form', apiUrl+`/reset_forgot_password/${resetToken}`, ['password_first']);
});
