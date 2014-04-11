//bejelelentkezés form betöltése
$(document).ready(function(){
	
});

$(document).on("click", "#login_btn", function(){
	$("#content-header").html("Bejelelentkezés");
	$("#content").load("login.html");
});

//bejelentkezés
$(document).on("click", "#submit_login", function(){
	var username = $("#login2_username").val();
	var password = $("#login2_pwd").val();
	jQuery.ajax({
		type: "POST",
		dataType: "json",
		url: "login.php",
		data: "username="+username+"&password="+password,
		success: function(data){
			if (data.login == 'true'){
				window.location.reload();
			} else {
				$("#content-header").html("Sikertelen bejelentkezés!");
			}
		},
		error: function(jqXHR, exception) {
            if (jqXHR.status === 0) {
                alert('Not connect.\n Verify Network.');
            } else if (jqXHR.status == 404) {
                alert('Requested page not found. [404]');
            } else if (jqXHR.status == 500) {
                alert('Internal Server Error [500].');
            } else if (exception === 'parsererror') {
                alert(jqXHR.responseText);
            } else if (exception === 'timeout') {
                alert('Time out error.');
            } else if (exception === 'abort') {
                alert('Ajax request aborted.');
            } else {
                alert('Uncaught Error.\n' + jqXHR.responseText);
            }
        }
	});
	//very important magic, így nem töltődik újra az oldal
	//de mégse kell
	return false;

});

//kijelentkezés
$(document).on("click", "#logout_btn", function(){
	jQuery.ajax({
		dataType: "json",
		url: "logout.php",
		success: function(data){
			if (data.logout == 'true'){
				window.location.reload();
			} else {
				$("#content-header").html("Sikertelen kijelentkezés !");
			}
		}
	});
	return false;
});

//regisztráció form betöltése
$(document).on("click", "#register_btn", function(){
	$("#content-header").html("Regisztráció");
	$("#content").load("register.html");
});

//regisztráció
$(document).on("click", "#submit_reg", function(){
	$("#reg_error").empty();
	var form = $("#registration").serialize();
	jQuery.ajax({
		type: "POST",
		dataType: "json",
		url: "register.php",
		data: form,
		success: function(result){
			console.log(result);
			if (result.register == "true"){
				$("#content").html("Sikeres regisztráció !");
			}
			else {
				if (result.username == "regex mismatch"){
					$("#reg_error").append("Nem megfelelő felhasználónév!<br>");
				}
				if (result.username == "taken username"){
					$("#reg_error").append("Foglalt felhasználónév! Válassz másikat !<br>");
				}
				if (result.password == "different passwords"){
					$("#reg_error").append("Nem egyezik meg a két jelszó !<br>");
				}
				if (result.password == "regex mismatch"){
					$("#reg_error").append("Nem megengedett karakterek használata a jelszóban !<br>");
				}
				if (result.name == "regex mismatch"){
					$("#reg_error").append("Nem megengedett karakterek használata a névben !<br>");
				}  
				if (result.email == "regex mismatch"){
					$("#reg_error").append("Nem megfelelő az email cím !<br>");
				}
				if (result.country == "regex mismatch"){
					$("#reg_error").append("Nem megengedett karakterek használata az országnévben !<br>");
				}  
				if (result.city == "regex mismatch"){
					$("#reg_error").append("Nem megengedett karakterek használata a városnévben !<br>");
				}  
			}	
		},
		error: function(jqXHR, exception) {
            if (jqXHR.status === 0) {
                alert('Not connect.\n Verify Network.');
            } else if (jqXHR.status == 404) {
                alert('Requested page not found. [404]');
            } else if (jqXHR.status == 500) {
                alert('Internal Server Error [500].');
            } else if (exception === 'parsererror') {
                alert(jqXHR.responseText);
            } else if (exception === 'timeout') {
                alert('Time out error.');
            } else if (exception === 'abort') {
                alert('Ajax request aborted.');
            } else {
                alert('Uncaught Error.\n' + jqXHR.responseText);
            }
        }
	});
	return false;
});


//main
$(document).on("click", "#home_btn", function(){
	$("#content-header").html("Legfrissebb képek");
	$("#content").load("home.php");
});

//image zoom
$(document).on("click", ".tile", function(){
	var image = $(this).find("img").attr('src');
	$.Dialog({
		height: 600,
		width: 1000,
        overlay: true,
        shadow: true,
        flat: true,
        icon: '',
        title: '',
        content: '',
        onShow: function(_dialog){
            var content = _dialog.children('.content');
            $.post( "picture_zoom.php", { img: image})
			 .done(function(data) {
				content.html(data);
			});
        }
    });
});

//személyes adatok
$(document).on("click", "#userdata_btn",function(){
		$("#content-header").html("Személyes adatok");
		$("#content").load("userdata.php");
});

//avatar
$(document).on("change","#avatar_file",function(){
	files = event.target.files;
});

// Add events
$(document).on("submit","#avatar",function(){
	event.stopPropagation(); // Stop stuff happening
    event.preventDefault(); // Totally stop stuff happening
    var data = new FormData();
    $.each(files, function(key,value)
        	{
				data.append(key, value);
			});
	$.ajax({
        url: 'avatar_upload.php',
        type: 'POST',
        data: data,
        cache: false,
        dataType: 'json',
        processData: false, // Don't process the files
        contentType: false, // Set content type to false as jQuery will tell the server its a query string request
        success: function(data, textStatus, jqXHR)
        {
        	//console.log(data);
        	$("#content").empty().load("userdata.php");
        },
		error: function(jqXHR, exception) {
            if (jqXHR.status === 0) {
                alert('Not connect.\n Verify Network.');
            } else if (jqXHR.status == 404) {
                alert('Requested page not found. [404]');
            } else if (jqXHR.status == 500) {
                alert('Internal Server Error [500].');
            } else if (exception === 'parsererror') {
                alert(jqXHR.responseText);
            } else if (exception === 'timeout') {
                alert('Time out error.');
            } else if (exception === 'abort') {
                alert('Ajax request aborted.');
            } else {
                alert('Uncaught Error.\n' + jqXHR.responseText);
            }
        }
    });
});

// sajat kepek
$(document).on("click", "#mypictures_btn", function(){
    $("#main_p").load("mypictures.php");
});