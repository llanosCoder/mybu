// This is called with the results from from FB.getLoginStatus().
function statusChangeCallback(response) {
    //console.log('statusChangeCallback');
    //console.log(response);
    // The response object is returned with a status field that lets the
    // app know the current login status of the person.
    // Full docs on the response object can be found in the documentation
    // for FB.getLoginStatus().
    if (response.status === 'connected') {
        // Logged into your app and Facebook.
        testAPI();
    } else if (response.status === 'not_authorized') {
        FB.login(function (response) {
            if (response.authResponse) {
                console.log('Welcome!  Fetching your information.... ');
                FB.api('/me', function (response) {
                    console.log('Good to see you, ' + response.name + '.');
                });
                checkLoginState();
            } else {
                $('.nfn-overlay').hide();
                mostrar_notificacion('Información','Debes autorizar la aplicacion desde Facebook','warning');
                console.log('User cancelled login or did not fully authorize.');
            }
        }, {scope: 'public_profile,email'});
    } else {
        // The person is not logged into Facebook, so we're not sure if
        // they are logged into this app or not.
        //document.getElementById('status').innerHTML = 'Please log ' +
        //    'into Facebook.';
        FB.login(function (response) {
            if (response.authResponse) {
                console.log('Welcome!  Fetching your information.... ');
                FB.api('/me', function (response) {
                    console.log('Good to see you, ' + response.name + '.');
                });
                checkLoginState();
            } else {
                $('.nfn-overlay').hide();
                mostrar_notificacion('Información','Debes autorizar la aplicacion desde Facebook','warning');
                console.log('User cancelled login or did not fully authorize.');
            }
        }, {scope: 'public_profile,email'});
    }
}

// This function is called when someone finishes with the Login
// Button.  See the onlogin handler attached to it in the sample
// code below.
function checkLoginState() {
    $('.nfn-overlay').show();
    FB.getLoginStatus(function (response) {
        statusChangeCallback(response);
    });
}

window.fbAsyncInit = function () {
    FB.init({
        appId: '1641950849383890',
        cookie: true, // enable cookies to allow the server to access 
        // the session
        xfbml: true, // parse social plugins on this page
        version: 'v2.2' // use version 2.2
    });

    // Now that we've initialized the JavaScript SDK, we call 
    // FB.getLoginStatus().  This function gets the state of the
    // person visiting this page and can return one of three states to
    // the callback you provide.  They can be:
    //
    // 1. Logged into your app ('connected')
    // 2. Logged into Facebook, but not your app ('not_authorized')
    // 3. Not logged into Facebook and can't tell if they are logged into
    //    your app or not.
    //
    // These three cases are handled in the callback function.

    /*FB.getLoginStatus(function(response) {
      statusChangeCallback(response);
    });*/

};

// Load the SDK asynchronously
(function (d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s);
    js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

// Here we run a very simple test of the Graph API after login is
// successful.  See statusChangeCallback() for when this call is made.
function testAPI() {

    //console.log('Welcome!  Fetching your information.... ');
    FB.api('/me', function (response) {
        //console.log('Successful login for: ' + response.name);
        /*document.getElementById('status').innerHTML =
          'Thanks for logging in, ' + response.name + '!';*/
        var url = "classes/login-callback.php";
        $.post(url, {
                access: FB.getAuthResponse()['accessToken']
            },
            function (data) {
                data = $.parseJSON(data);
                if (data.resultado === 1) {
                    window.location.replace('punto_venta.html');
                } else if (data.resultado === 2) {
                    $('.nfn-overlay').hide();
                    $('#signin-wrapper').hide('fast');
                    $('#register-wrapper').show('fast');
                    $('#newUser').val(data.email);
                    $('#newUser').prop('disabled',true);
                    
                }else if (data.resultado === 0) {
                    $('.nfn-overlay').hide();
                    mostrar_notificacion('Información','Su cuenta de Facebook no permite Iniciar Sesión, intente con otra','warning');
                }
            }).done(
            function () {}
        );
    });


}