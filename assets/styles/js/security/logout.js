import $ from 'jquery';

$.ajax({
    url: 'https://127.0.0.1:8000/api/logout',
    method: 'POST',
    xhrFields: { withCredentials: true },
    success: function () {
        $.ajax({
            url: '/logout-session',
            method: 'POST',
            success: function () {
                window.location.href = '/login';
            }
        });
    }
});
