$(document).ready(function () {

    $('#login').submit(function (e) {
        e.preventDefault();
        const username = $('#usuario').val();
        const passowrd = $('#password').val();

        
        // return 0;
        $.ajax({
            type: 'POST',
            url: 'api/usuarios/login',
            dataType: 'JSON',
            data: { 'username': username, 'password': passowrd },
            success: function (res) {
                
                if (res) {
                    window.location = 'inicio';
                } else {
                    
                    $('#error').removeClass('hide');
                }

            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert('Error conexion ' + XMLHttpRequest.readyState);
            }
        });
    });
});