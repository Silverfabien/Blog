import $ from 'jquery';

const apiUrl = import.meta.env.VITE_API_URL;

$.ajax({
    url: apiUrl+'/logout',
    method: 'POST',
    xhrFields: { withCredentials: true },
    success: function () {
        $.ajax({
            url: '/logout-session',
            method: 'POST',
            success: function () {
                window.location.href = '/';
            }
        });
    }
});
