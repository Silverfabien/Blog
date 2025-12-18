import $ from 'jquery';

setInterval(function(){
   $.ajax({
       url: 'https://127.0.0.1:8000/api/check_token',
       method: 'GET',
       xhrFields: { withCredentials: true },
       success: function () {
           location.reload();
       },
       error: function (xhr) {
           if (xhr.status === 401) {
               $.ajax({
                   url: 'https://127.0.0.1:8000/api/logout',
                   method: 'POST',
                   xhrFields: { withCredentials: true },
                   complete: function () {
                       $.ajax({
                           url: '/logout-session',
                           method: 'POST',
                           success: function () {
                               location.reload();
                           }
                       });
                   }
               });
           }
       }
   });
}, 60000);
