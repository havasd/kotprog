$(document).ready(function(){
	$("#logout").hide();
	$("#userdata").hide();
	$("#pics").hide();
	$("#albums").hide();
});

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
				$("#content").empty();
				$("#login").hide();
				$("#logout").show();
				$("#userdata").show();
				$("#pics").show();
				$("#albums").show();
				$("#content-header").html("Sikeres bejelentkezés");
				$("#content").html("te kis buzi");
			} else {
				$("#content-header").html("Valamit elbasztál! Let's try again!");
			}
		}
	});
	//very important magic, így nem töltődik újra az oldal
	return false;
});



