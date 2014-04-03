//bejelelentkezés form betöltése
$(document).ready(function(){
	$("#content-header").html("Legfrissebb képek");
	$("#content").load("home.php");
	$(".button-set").buttongroup();
});

$(document).on("click", "#login", function(){
	$("#content-header").html("Bejelelentkezés");
	$("#content").load("login.html");
});

//bejelentkezés
$(document).on("click", "#submit_login", function(){
	username=$("#username").val();
	password=$("#password").val();
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
		}
	});
	//very important magic, így nem töltődik újra az oldal
	//de mégse kell
	return false;

});

//kijelentkezés
$(document).on("click", "#logout", function(){
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
$(document).on("click", "#register", function(){
	$("#content-header").html("Regisztráció");
	$("#content").load("register.html");

});

//regisztráció
$(document).on("click", "#submit_reg", function(){
		var form = $("#registration").serialize();
		$.post( "register.php", form)
		 .done(function(data) {
				console.log(data);
		});
		return false;
});


//main
$(document).on("click", "#home", function(){
	$("#content-header").html("Legfrissebb képek");
	$("#content").load("home.php");
});

$(document).on('click', ".tile", function(){
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
