import $ from 'jquery';
import { handleForm } from '../../security/components/api_form.js';

$(document).ready(function(){
    const $form = $('#user-edit');
    const baseUrl = window.location.origin;
    if ($form.length === 0) return;

    const apiUrl = import.meta.env.VITE_API_URL;
    const userApiId = $form.data('user-api-id');

    handleForm('#user-edit', apiUrl+'/admin/user_edit', ['username', 'email', 'role', 'picture'], {
        id: userApiId,
        url: baseUrl
    });
});
