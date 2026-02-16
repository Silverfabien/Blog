import $ from 'jquery';
import { handleForm } from './components/api_form.js';

$(document).ready(function(){
    const apiUrl = import.meta.env.VITE_API_URL;

    handleForm('#user-edit', apiUrl+'/user_edit', ['username', 'email']);
    handleForm('#reset-password', apiUrl+'/reset_password', ['password_first', 'password_second']);
    handleForm('#soft-delete', apiUrl+'/soft_delete', ['reason'], {}, { logoutOnSuccess: true });
    handleForm('#hard-delete', apiUrl+'/hard_delete', [], {}, { logoutOnSuccess: true });
});
