<?php
	require_once('model/Picture.php');
	require_once('dal/DaoDB.php');
	session_start();
	if(isset($_SESSION['userObject'])){
		$usr = $_SESSION['userObject'];
	}
	
	$controller = new DaoDB();
	$pic_id = $_POST['img_id'];
	$picture = $controller->getPictureById($pic_id);

	if (isset($_POST["rate"])) {
		rate();
		exit();
	}

	if (isset($_POST["new_comment"])){
		global $controller;
		global $pic_id;
		$user_id = $_SESSION['userObject']->getId();
		$comment = $_POST["new_comment"];

		if ($controller->commentPicture($pic_id, $user_id, $comment)){
			echo json_encode(array("result" => "true"));
		} else {
			echo json_encode(array("result" => "false"));
		}
		exit();
	}

	function rate(){
		global $controller;
		$res = $controller->ratePicture($_POST["img_id"], $_POST["rate"]);
		echo json_encode(array("result" => $res, "rate" => $controller->getPictureById($_POST["img_id"])->getRating()));
	}

	echo '<style>

			#picture{
				display: inline-block;
				float: left;

				height: 540px;
				width: 700px;
				display:-moz-box;
						-moz-box-pack:center;
						-moz-box-align:center;

				display:-webkit-box;
						-webkit-box-pack:center;
						-webkit-box-align:center;

				display:box; 
						box-pack:center;
						box-align:center;
			}
			#picture img{
				display: block;
				vertical-align: middle;

			  	margin-left: auto;
			  	margin-right: auto;
				height: auto;
				width: auto;
				max-width: 700px;
				max-height: 540px;
			}

			#comment_list {
				height: 210px;
				overflow: scroll;
			}

			#info .panel-header{
				line-height: 60%;
			}
			#new_comment {
				width: 300px;
				height: 40px;
			}

			</style>

			';

	echo 	'<div id="previous_picture" 
			   	style="	display: inline-block;
						float: left;
						width: 25px;
						height: 25px;
						padding-top: 260px;">
		  		<a style="color: black;" href="#"><i class="icon-arrow-left-3 on-left"></i></a>
		  	</div>
		  	<div id="picture">';
	echo '	<img src="data:image/jpeg;base64,'.$picture->getPictureBinary().'">';
	echo 	'</div>
		 	<div id="next_picture" 
		 		style="	display: inline-block;
						float: left;
						width: 25px;
						height: 25px;
						padding-top: 260px;
						padding-left: 10px;">
	  			<a style="color: black;" href="#"><i class="icon-arrow-right-3 on-left"></i></a>
	  	  	</div>';
	echo '<div id="info"
				style=" display: inline-block;
						float: right;
						width: 300px;">
		<div id="picture_data" class="panel">
		    <div id="picture_title" class="panel-header bg-lightBlue fg-white">
		        Helyszín:'.$picture->getPlace().'
		    </div>
		    <div id="picture_info" class="panel-content">
		        Készítette: '.$picture->getOwner().'<br>
		        Feltöltés ideje: '.$picture->getUploadTime().'<br>
		        Leírás: '.$picture->getDescription().'<br>
		    </div>
		</div>
		<div class="panel">
			<div class="panel-header bg-lightBlue fg-white">
		        Értékelés
		    </div>
		    <div class="panel-content">
		    	<div id="pic_rating" class="rating">
				</div>';
	if (isset($usr)){
		echo '	<script>
                    $(function(){
                        $("#pic_rating").rating({
                            static: false,
                            score: ' . $picture->getRating() . ',
                            stars: 5,
                            showHint: false,
                            showScore: false,
                            click: function(value, rating){

                                jQuery.ajax({
									type: "POST",
									dataType: "json",
									url: "picture_zoom.php",
									data: "rate=" + value + "&img_id=" + ' . $picture->getId() . ',
									success: function(data){
										if (data.result == true){
											rating.rate(data.rate);

										}
									},
									error: function(jqXHR, exception) {
										alert(\'Uncaught Error.\n\' + jqXHR.responseText);
							        }
								});
								return false;
                    		}
						});
					});
                </script>';
    } else {
    	echo '	<script>
                    $(function(){
                        $("#pic_rating").rating({
                            static: true,
                            score: ' . $picture->getRating() . ',
                            stars: 5,
                            showHint: false,
                            showScore: false,
						});
					});
                </script>';
    }
    echo '</div>
		</div>';
    if (isset($usr)){
    	echo '	
		<div id="comments_panel" class="panel">
		    <div id="comments_title" class="panel-header bg-lightBlue fg-white">
		        Hozzászólások
		    </div>
		    <div  id="comments" class="panel-content" >
			    <div id="comment_list" class="listview-outlook" data-role="listview">';
		//hozzászólások betöltése
	$comments = $controller->getComments($pic_id);
	foreach ($comments as $value) {
		echo '      <a class="list marked" href="#">
	                    <div class="list-content">
	                        <span class="list-title">'.$value['FELHASZNALONEV'].'</span>
	                        <span class="list-remark">'.$value['MEGJEGYZES'].'</span>
	                    </div>
	                </a>';
	}
               
	echo  '		</div>
	    	</div>
        </div>
            <div class="input-control text" data-role="input-control">
                <textarea id="new_comment" type="text" placeholder="Hozzászólás írásához kattints ide..." style="resize: none"></textarea>
                <button class="btn-clear" tabindex="-1"></button>
        	</div>
        	<p><button id="btn_comment_send">Elküldés</button></p>
		</div>
	</div>';
    }
    else {
    	echo '<div id="comments_panel" class="panel">
			    <div id="comments_title" class="panel-header bg-lightBlue fg-white">
			        A hozzászólások megtekintéséhez jelenetkezz be!
			    </div>
			    <div  id="comments" class="panel-content">
			    </div>
			    </div>';
    }
		
	
?>
