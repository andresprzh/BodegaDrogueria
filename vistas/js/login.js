$(document).ready(function () {

    $('#login').submit(function (e) {
        e.preventDefault();
        const username = $('#usuario').val();
        const passowrd = $('#password').val();

        console.log(passowrd);
        // return 0;
        $.ajax({
            type: 'POST',
            url: 'api/usuarios/login',
            dataType: 'JSON',
            data: { 'username': username, 'password': passowrd },
            success: function (res) {
                console.log(res);
                if (res) {
                    window.location = 'inicio';
                } else {
                    console.log("es falso");
                    $('#error').removeClass('hide');
                }

            }
        });
    });
});