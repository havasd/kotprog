//bejelelentkezés form betöltése
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

