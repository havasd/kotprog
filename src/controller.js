$(document).ready(function(){
	
});
var countrylist;
var citylist;
var deleteMode = false;
var deleteNot = null;
var editMode = false;
var editNot = null;

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

function initThumbs(){
    $(".picture").each(function(){
            var pic = $(this).children().children();
            var pic_id = $(this).attr("id").substr(4);
            $.post( "dal/DaoDB.php", 'getThumb='+pic_id)
             .done(function(data) {
                 pic.attr("src", data);
                 pic.show();
            });
    });
}
//HOME
$(document).on("click", "#home_btn", function(){
	$.post( "home.php", 'header=1')
     .done(function(data) {
        $("#content-header").html(data);
        $(document).on("click","#categories > button, #order_by > button", function(){
            //$("#categories").init();
            var cat_id = $("#categories .active").attr("id").substr(4);
            var order_id = $("#order_by .active").attr("id");
            var order;
            if (order_id == "order_time_desc") order = "FELTOLTES_IDEJE DESC";
            if (order_id == "order_time_asc") order = "FELTOLTES_IDEJE";
            if (order_id == "order_rating_desc") order = "RATE DESC";
            if (order_id == "order_rating_asc") order = "RATE";
            $.post( "home.php", 'header=0&category='+cat_id+'&orderby='+order)
             .done(function(data) {
                $("#content").html(data);
                initThumbs();
            });
        });
    });
     
    $.post( "home.php", 'header=0&category=all&orderby=FELTOLTES_IDEJE DESC')
     .done(function(data) {
        $("#content").html(data);
        initThumbs();
    });
});

/*
--------------------------------------------------------------
--------------------- Image zoom begin -----------------------
--------------------------------------------------------------
*/

//image zoom
$(document).on("click", ".picture", function(){
    var image_id = $(this).attr('id').substr(4);    
    
    if (editMode){
        createPictureDialog(image_id);
        return false;
    }

    if (deleteMode) {
        if ($(this).hasClass("selected")) {
            $(this).removeClass("selected");
        } else {
            $(this).addClass("selected");
        }
        return false;
    }

    var next_image_id = $(this).next().attr('id');
    var prev_image_id = $(this).prev().attr('id');
    if (prev_image_id == undefined){
        prev_image_id = $(".picture").last().attr('id');   
    }
    if (next_image_id == undefined){
        next_image_id = $(".picture").first().attr('id');
    }
    //alert(prev_image_id+"    "+next_image_id);
    $.Dialog({
        height: 600,
        width: 1100,
        //modal: true,
        overlay: true,
        shadow: true,
        flat: true,
        icon: '',
        title: '',
        content: '',
        onShow: function(_dialog){
            //var content = _dialog.children('.content');
            //content.html(data);
            
        }
    });
    
    $.post( "picture_zoom.php", 'img_id='+image_id)
     .done(function(data) {
        if ($.Dialog.opened){
            $.Dialog.content(data);
            ////////////////////////////////
            // Dialogon belüli eseménykezelők
            ////////////////////////////////
            $("#previous_picture").on('click', function() 
            {
                //alert("prev")
                $("#"+prev_image_id).click();
            });
            $("#next_picture").on('click', function() 
            {
                //alert("next");
                $("#"+next_image_id).click();
            });
            $("#btn_comment_send").on('click', function(){
                var comment = $("#new_comment").val();
                jQuery.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "picture_zoom.php",
                    data: "img_id=" + image_id + "&new_comment=" + comment,
                    success: function(data){
                        if (data.result == 'true'){
                            $("#pic_"+image_id).click();
                        } else {
                            alert("Hiba történt a hozzászólás feltöltése során");
                            $("#pic_"+image_id).click();
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
            // delete selected comment
            $(".btn_comm_delete").on("click", function(){
                var comm = $(this).parent().parent().parent(); // span div a
                //alert("click delete comment " + $(comm).attr('id').substr(4));
                $.post("picture_zoom.php", 'delete_comment=' + $(comm).attr('id').substr(4), function(data){
                    if (data.res == "true"){
                        $(comm).remove();
                    } else {
                        alert("delete comm fail");
                    }
                }, "json");
            });
            $(".btn_comm_edit").on("click", function(){
                var text = $(this).parent().next().next();
                alert("edit");
                $(text).attr('contenteditable', "true");
                $(text).focus();
            });
            $(".list-remark").on("focusout", function(){
                $(this).attr('contenteditable', "false");
            });
        } 
    });
});


/*
--------------------------------------------------------------
--------------------- Image zoom end  -------------------------
--------------------------------------------------------------
*/

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
$(document).on("submit","#f_avatar",function(){
	event.stopPropagation(); // Stop stuff happening
    event.preventDefault(); // Totally stop stuff happening
    var data = new FormData();
    $.each(files, function(key,value){
				data.append(key, value);
    });
    data.append('mode', 'avatar');

	$.ajax({
        url: 'userdata.php',
        type: 'POST',
        data: data,
        cache: false,
        dataType: 'json',
        processData: false, // Don't process the files
        contentType: false, // Set content type to false as jQuery will tell the server its a query string request
        success: function(data, textStatus, jqXHR)
        {
            if (data.result == "true") {
                $.Notify.show("Avatar feltöltése sikeres.");
                $("#d_avatar").html(data.avatar);
            } else {
                $.Notify.show("Avatar feltöltése sikertelen.");
            }
        },
		error: function(jqXHR, exception) {
            alert('Uncaught Error.\n' + jqXHR.responseText);
        }
    });
});

// password change submit
$(document).on("submit", "#f_password_change", function(){
    event.stopPropagation(); // Stop stuff happening
    event.preventDefault(); // Totally stop stuff happening
    var form = $("#f_password_change").serialize();
    form += '&mode=pwd';

    $.post("userdata.php", form, function(data) {
            if (data.result == "true") {
                $.Notify.show("Jelszó módosítása sikeres.");
                $("#password_old").attr('data-state', "");
                $("#password_new").attr('data-state', "");
                $("#password_new2").attr('data-state', "");
            } else {
                var errormsg = "Jelszó megváltoztatása sikertelen!";
                $("#password_old").attr('data-state', "");
                $("#password_new").attr('data-state', "");
                $("#password_new2").attr('data-state', "");
                switch (data.password) {
                    case 1:
                        $("#password_new").attr('data-state', "error");
                        $("#password_new2").attr('data-state', "error");
                        errormsg = "Két jelszó nem egyezik meg.";
                        break;
                    case 4:
                        $("#password_old").attr('data-state', "error");
                        errormsg = "Jelenlegi jelszó nem megfelelő.";
                        break;
                    case 2:
                        $("#password_new").attr('data-state', "error");
                        errormsg = "A jelszó összetétele nem megfelelő.";
                        break;
                    case 3:
                        errormsg = "Belső hiba.";
                        break;
                }
                $.Notify({  
                    caption: "Jelszó módosítása sikertelen!",
                    content: errormsg,
                    timeout: 3000,
                    style: {background: 'red', color: 'white'}
                });
            }
        }, "json");
});

// personal data change submit
$(document).on("submit", "#f_personaldata_change", function(){
    event.stopPropagation(); // Stop stuff happening
    event.preventDefault(); // Totally stop stuff happening
    var form = "mode=chg";
    var changed = false;
    var id = [ "name_new", "email_new", "country_new", "city_new" ];
    for (var i = 0; i < id.length; ++i) {
        var name = $("#" + id[i]);
        if (name.val() != name.attr('data-orig')) {
            form += "&" + id[i] + "=" + name.val();
            name.attr('data-orig', name.val());
            changed = true;
        }
    }

    if (!changed) {
        $.Notify.show("Nem történt adat módosítás.");
        return false;
    }
    //alert(form);


    $.post("userdata.php", form, function(data) {
            if (data.result == "true") {
                $.Notify.show("Adatok módosítása sikeres.");
            }
        }, "json");
});
/*
--------------------------------------------------------------
--------------------- Saját képek begin ----------------------
--------------------------------------------------------------
*/
// clicks

// reset delete mode on page switch
$(document).on("click", ".element", function(){
    deleteMode = false;
    editMode = false;

    if (deleteNot != null) {
        deleteNot.close();
    }
   if (editNot != null) {
        editNot.close();
    }
});

// edit mode
$(document).on("click", "#b_edit", function(){
    if (editMode) {
        if (editNot != null)
            editNot.close();
    } else {
        editNot = $.Notify({
            caption: "Szerkesztés",
            content: "Szerkesztéshez válassz ki egy képet vagy albumot.",
            timeout: 10000
        });
        deleteMode = false;
        if (deleteNot != null)
            deleteNot.close();
        $(".selected").removeClass("selected");
    }

    editMode = !editMode;
});

// delete mode
$(document).on("click", "#b_delete", function(){
    if (deleteMode) {
        $(".selected").each(function(){
            var id = $(this).attr("id").substr(4);
            if ($(this).hasClass("picture")) {
                $.post( "dal/DaoDB.php", 'deletePicture=' + id);
            } else {
                $.post( "dal/DaoDB.php", 'deleteAlbum=' + id);
            }
        });
        $(".selected").remove();
        if (deleteNot != null)
            deleteNot.close();
    } else if (!deleteMode) {
        deleteNot = $.Notify({
            caption: "Törlés",
            content: "Törléshez válassz ki elemeket majd kattints újra a törlés gombra.",
            timeout: 10000
        });
        editMode = false;
        if (editNot != null)
            editNot.close();
    }

    deleteMode = !deleteMode;
});


$(document).on("click", "#mypictures_btn", function(){
    $.post("mypictures.php", "header=1").done(function(data){
        $("#content-header").html(data);
    });
    $.post("mypictures.php", "header=0").done(function(data){
        $("#content").html(data);
        initThumbs();
    });
});

function createAlbumDialog(id){
    if (id === undefined) 
        id = 0;

    //alert(id);
    $.Dialog({
        height: 250,
        width: 300,
        overlay: true,
        shadow: true,
        flat: true,
        icon: '',
        title: id ? 'Album módosítása' : 'Új album létrehozása',
        content: '',
        onShow: function(_dialog){
            var content = _dialog.children('.content');
            $.post("albumdialog.php", 'id=' + id).done(function(data){
                content.html(data);
            });
        }
    });
}

function createPictureDialog(id){
    if (id == undefined)
        id = 0;

    $.ajax({
        url: 'dal/DaoDB.php',
        type: 'POST',
        data: 'getCountries=1',
        dataType: 'json',
        success : function(data){
            countrylist= data;
        }
    });

    $.ajax({
        url: 'dal/DaoDB.php',
        type: 'POST',
        data: 'getCities=1',
        dataType: 'json',
        success : function(data){
            citylist= data;
        }
    });

    var content;
    $.Dialog({
        height: 600,
        width: 300,
        overlay: true,
        shadow: true,
        flat: true,
        icon: '',
        title: id ? 'Kép adatainak módosítása' : 'Új képek feltöltése',
        content: '',
        onShow: function(_dialog){
            content = _dialog.children('.content');                
            var curr_album = "0";
            if ($("#btn_album_back").attr("data-id"))
                curr_album = $("#btn_album_back").attr("data-id");
            
            $.post("pictureupload.php", 'id=' + id + '&curr_album=' + curr_album).done(function(data){
                content.html(data)
                $("#in_file_country").autocomplete({
                    source: countrylist,
                    appendTo: content
                });
                $("#in_file_city").autocomplete({
                    minlenght: 3,
                    source: citylist,
                    appendTo: content
                });
            });
        }
    });
}
// picture upload click

$(document).on("click", "#btn_new_picture", function(){
var countrylist;
var citylist;
createPictureDialog(0);
});

// new album click
$(document).on("click", "#btn_new_album", function(){
    createAlbumDialog();
});

// picture upload click
$(document).on("click", "#btn_new_picture", function(){
    createPictureDialog();
});

// form album create
$(document).on("submit", "#f_new_album", function(){
    event.stopPropagation(); // Stop stuff happening
    event.preventDefault(); // Totally stop stuff happening
    var form = $("#f_new_album").serialize();
    form += '&id=' + $(this).attr('data-id');
    jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: "albumdialog.php",
        data:  form,
        success: function(result){
            if (result.create == "true"){
                    $.Dialog.close();
                    if ($(this).attr('data-id') == "0")
                        $.Notify.show("Album sikeresen létrehozva.");
                    else
                        $.Notify.show("Album sikeresen módosítva.");

                    $.post("mypictures.php", "header=0").done(function(data){
                        $("#content").html(data);
                        initThumbs();
                    });
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

//picture upload prepare
$(document).on("change", "#in_file_picture", function(){
    files = event.target.files;
});



// picture upload
$(document).on("submit", "#f_new_pictures", function(){
    event.stopPropagation(); // Stop stuff happening
    event.preventDefault(); // Totally stop stuff happening
    var data = new FormData();
    var picid = $(this).attr('data-id');
    if (picid == "0") {
        $.each(files, function(key, value){
            data.append(key, value);
        });
    }

    data.append('file_desc', $("#in_file_desc").val());
    data.append('file_album', $("#in_file_album").val());
    data.append('file_category', $("#in_file_category").val());
    data.append('id', picid);

    var country = $("#in_file_country").val();
    var city = $("#in_file_city").val();
    var place = $("#in_file_place").val();
    var city_id;

     $.post("dal/DaoDB.php", 'getCityId='+city+'&country='+country).done(function(result){
            alert(result);
            if (result == ""){
                $.post("dal/DaoDB.php", 'addCity='+city+'&country='+country).done(function(result){
                    data.append('file_place', result+'_'+place);
                });
            } else {
                data.append('file_place', result+'_'+place);
            }
            
            uploadpicture(data);
    });
    
});


function uploadpicture(data){

    alert("id: " + picid + " desc: " + $("#in_file_desc").val() +  " albumid:" +  $("#in_file_album").val() + " catid:" + $("#in_file_category").val());

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
                    if (picid == "0")
                        $.Notify.show("Fényképek feltöltése sikeres.");
                    $.post("mypictures.php", "header=1").done(function(data){
                        $("#content-header").html(data);
                    });
                    $.post("mypictures.php", "header=0").done(function(data){
                        $("#content").html(data);
                        initThumbs();
                    });
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
}
// back button when usr in an album
$(document).on("click", "#btn_album_back", function(){
    $.post("mypictures.php", "header=1").done(function(data){
        $("#content-header").html(data);
    });
    $.post("mypictures.php", "header=0").done(function(data){
        $("#content").html(data);
        initThumbs();
    });

    deleteMode = false;
    editMode = false;
    if (deleteNot != null) {
        deleteNot.close();
    }
    if (editNot != null) {
        editNot.close();
    }
});

// album navigation
$(document).on("click", ".album", function(){
    $(this).trigger('mouseleave');

    if (editMode) {
        var id = $(this).attr("id").substr(4);
        createAlbumDialog(id);
        return false;
    }
    if (deleteMode) {
        if ($(this).hasClass("selected")) {
            $(this).removeClass("selected");
        } else {
            $(this).addClass("selected");
        }
        return false;
    }

    $.post("mypictures.php", "header=1&alb=" + $(this).attr('id')).done(function(data){
        console.log(data);
        $("#content-header").html(data);
    });
    $.post("mypictures.php", "header=0&alb=" + $(this).attr('id')).done(function(data){
        console.log(data);
        $("#content").html(data);
        initThumbs();
    });
    
});

/*
--------------------------------------------------------------
--------------------- Saját képek end ------------------------
--------------------------------------------------------------
*/
