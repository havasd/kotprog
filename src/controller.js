$(document).ready(function(){
	
});

//bejelelentkezés form betöltése
$(document).on("click", "#login_btn", function(){
	$("#content-header").html("Bejelelentkezés");
	$("#content").load("login.php");
});

//bejelentkezés
$(document).on("click", "#submit_login", function(){
	var username = $("#login2_username").val();
	var password = $("#login2_pwd").val();
	jQuery.ajax({
		type: "POST",
		dataType: "json",
		url: "login.php",
		data: "username=" + username + "&password=" + password,
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
	$("#content").load("register.php");
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
			} else {
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
$(document).on("click", ".picture", function(){
    var image_id = $(this).attr('id').substr(4);
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
            $.post( "picture_zoom.php", 'img_id='+image_id)
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

//avatar prepare
$(document).on("change","#avatar_file",function(){
	files = event.target.files;
});

// avatar submit
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

/*
--------------------------------------------------------------
--------------------- Saját képek begin ----------------------
--------------------------------------------------------------
*/
// clicks
$(document).on("click", "#mypictures_btn", function(){
    $("#content-header").load("mypictures.php", "header=1");
    $("#content").load("mypictures.php", "header=0");
});

// new album click
$(document).on("click", "#btn_new_album", function(){
    $.Dialog({
        height: 250,
        width: 300,
        overlay: true,
        shadow: true,
        flat: true,
        icon: '',
        title: 'Új album létrehozása',
        content: '',
        onShow: function(_dialog){
            var content = _dialog.children('.content');
            $(content).load("NewAlbumPage.php");
        }
    });
});

// picture upload click
$(document).on("click", "#btn_new_picture", function(){
    $.Dialog({
        height: 450,
        width: 300,
        overlay: true,
        shadow: true,
        flat: true,
        icon: '',
        title: 'Új képek feltöltése',
        content: '',
        onShow: function(_dialog){
            var content = _dialog.children('.content');
            $(content).load("pictureupload.php");
        }
    });
});

//picture upload prepare
$(document).on("change", "#in_file_picture", function(){
    files = event.target.files;
});

// picture upload
$(document).on("submit", "#f_new_pictures", function(){
    event.stopPropagation(); // Stop stuff happening
    event.preventDefault(); // Totally stop stuff happening
    var data = new FormData();
    $.each(files, function(key, value){
        data.append(key, value);
    });
    data.append('file_desc', $("#in_file_desc").val());
    data.append('file_place', $("#in_file_place").val());
    data.append('file_album', $("#in_file_album").val());
    $.ajax({
        url: 'pictureupload.php',
        type: 'POST',
        data: data,
        cache: false,
        dataType: 'json',
        processData: false, // Don't process the files
        contentType: false, // Set content type to false as jQuery will tell the server its a query string request
        success: function(data, textStatus, jqXHR)
        {
            if (data.create == "true"){
                    $.Dialog.close();
                    $.Notify.show("Fényképek feltöltése sikeres.");
                    $("#content-header").load("mypictures.php", "header=1");
                    $("#content").load("mypictures.php", "header=0");
            } else {
                $.Notify.show("Fénykép feltöltése sikertelen.");
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
});

// back button when usr in an album
$(document).on("click", "#btn_album_back", function(){
    $("#content-header").load("mypictures.php", "header=1");
    $("#content").load("mypictures.php", "header=0");
});

// album navigation
$(document).on("click", ".album", function(){
    $(this).trigger('mouseleave');
    $("#content-header").load("mypictures.php", "header=1&alb=" + ($(this).attr('id')));
    $("#content").load("mypictures.php", "header=0&alb=" + ($(this).attr('id')));
});

// form album create
$(document).on("submit", "#f_new_album", function(){
    event.stopPropagation(); // Stop stuff happening
    event.preventDefault(); // Totally stop stuff happening
    var form = $("#f_new_album").serialize();
    jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: "NewAlbumPage.php",
        data:  form,
        success: function(result){
            if (result.create == "true"){
                    $.Dialog.close();
                    $.Notify.show("Album sikeresen létrehozva.");
                    $("#content-header").load("mypictures.php", "header=1");
                    $("#content").load("mypictures.php", "header=0");
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
/*
--------------------------------------------------------------
--------------------- Saját képek end ------------------------
--------------------------------------------------------------
*/
