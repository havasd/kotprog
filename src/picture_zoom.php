<?php
	require_once('model/Picture.php');
	require_once('dal/DaoDB.php');
	session_start();

	$usr = isset($_SESSION['userObject']) ? $_SESSION['userObject'] : null;
	$controller = new DaoDB();
	$pic_id = isset($_POST['img_id']) ? $_POST['img_id'] : null;
	$picture = isset($pic_id) ? $controller->getPictureById($pic_id) : null;

	if (isset($_POST["rate"])) {
		rate();
		exit();
	}

	if (isset($_POST["new_comment"])){
		$user_id = $_SESSION['userObject']->getId();
		$comment = $_POST["new_comment"];
		$answer = $_POST["answer"] == "null" ? null : $_POST["answer"];

		if ($controller->commentPicture($pic_id, $user_id, $comment, $answer)){
			echo json_encode(array("result" => "true"));
		} else {
			echo json_encode(array("result" => "false"));
		}
		exit();
	}

	if (isset($_POST["delete_comment"])){
		$comment_id = $_POST["delete_comment"];

		if ($controller->deleteComment($comment_id)) {
			echo json_encode(array("res" => "true"));
		} else {
			echo json_encode(array("res" => "false"));
		}
		exit();
	}

	if (isset($_POST["edit_comment"])){
		$comment_id = $_POST["edit_comment"];
		$text = $_POST["edit_text"];

		$controller->updateComment($comment_id, $text);
		exit();	
	}

	function rate(){
		global $controller;
		$res = $controller->ratePicture($_POST["img_id"], $_POST["rate"]);
		echo json_encode(array("result" => $res, "rate" => $controller->getPictureById($_POST["img_id"])->getRating()));
	}

	$city_country;
	$place = $picture->getPlace();
	$place = explode("_",$place);
	if(sizeof($place)== 2){
		$full_place = $controller->getCityById($place[0])."  ".$place[1];
	} else {
		$full_place = $place[0];
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
				overflow-y: scroll;
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
		        Info
		    </div>
		    <div id="picture_info" class="panel-content">
		    	Helyszín:'.$full_place.'<br>
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
			echo '<a id="com_' . $value['ID'] . '" class="list comment-el" data-answer-id="' . $value['ID'] . '">
	                    <div class="list-content" style="overflow-wrap: break-word;">
	                        <span class="list-title">'. $value['NEV'] . (($usr->getId() == $value['FELH_ID']) ? 
	                        	'<i class="btn_comm_delete icon-cancel-2 place-right fg-lightBlue" style="padding-left:5px;font-size: 15px;" title="Hozzászólás törlése"></i>
	                        	<i class="btn_comm_edit icon-wrench place-right fg-lightBlue" style="font-size: 15px;" title="Hozzászólás szerkesztése"></i>' : '')  . '</span>
	                        <span class="list-subtitle">' . $value['IDOBELYEG'] . '</span>
	                        <span class="list-remark" style="overflow-wrap: break-word; word-wrap: break-word; overflow: initial; white-space: normal; text-overflow: initial;" contenteditable="false" >' . $value['MEGJEGYZES'] .'</span>
	                    </div>
	                </a>';
	        $answers = $controller->getAnswersForComment($value['ID']);
        	foreach ($answers as $ans) {
				echo '<a id="com_' . $ans['ID'] . '" class="list comment-el marked" data-answer-id="' . $value['ID'] . '">
                    <div class="list-content" style="overflow-wrap: break-word;">
                        <span class="list-title">'. $ans['NEV'] . (($usr->getId() == $ans['FELH_ID']) ? 
                        	'<i class="btn_comm_delete icon-cancel-2 place-right fg-lightBlue" style="padding-left:5px;font-size: 15px;" title="Hozzászólás törlése"></i>
                        	<i class="btn_comm_edit icon-wrench place-right fg-lightBlue" style="font-size: 15px;" title="Hozzászólás szerkesztése"></i>' : '')  . '</span>
                        <span class="list-subtitle">' . $ans['IDOBELYEG'] . '</span>
                        <span class="list-remark" style="overflow-wrap: break-word; word-wrap: break-word; overflow: initial; white-space: normal; text-overflow: initial;" contenteditable="false" >' . $ans['MEGJEGYZES'] .'</span>
                    </div>
                </a>';
            }
		}
               
		echo  '		</div>
	    	</div>
        </div>
            <div class="input-control text" data-role="input-control">
                <textarea id="new_comment" type="text" placeholder="Hozzászólás írásához kattints ide..." style="resize: none"></textarea>
                <button class="btn-clear" tabindex="-1"></button>
        	</div>
        	<p><button id="btn_comment_send" data-answer="null">Elküldés</button></p>
		</div>
	</div>';
    }
    else {
    	echo '<div id="comments_panel" class="panel">
			    <div id="comments_title" class="panel-header bg-lightBlue fg-white">
			        A hozzászólások megtekintéséhez jelentkezz be!
			    </div>
			    <div  id="comments" class="panel-content">
			    </div>
			    </div>';
    }
		
	
?>
