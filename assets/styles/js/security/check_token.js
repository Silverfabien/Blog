import $ from 'jquery';

const apiUrl = import.meta.env.VITE_API_URL;

setInterval(function(){
   $.ajax({
       url: apiUrl+'/check_token',
       method: 'GET',
       xhrFields: { withCredentials: true },
       success: function () {
           location.reload();
       },
       error: function (xhr) {
           if (xhr.status === 401) {
               $.ajax({
                   url: apiUrl+'/logout',
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
