<?php
	require_once('model/Picture.php');
	require_once('dal/DaoDB.php');
	session_start();
	$usr = $_SESSION['userObject'];
	$controller = new DaoDB();
	$pic_id = $_POST['img_id'];
	$picture = $controller->getPictureById($pic_id);

	if (isset($_POST["rate"])) {
		rate();
		exit();
	}

	function rate(){
		global $controller;
		$res = $controller->ratePicture($_POST["img_id"], $_POST["rate"]);
		echo json_encode(array("result" => $res, "rate" => $controller->getPictureById($_POST["img_id"])->getRating()));
	}

	echo '<style>
			#picture_zoom {
			}
			#picture{
				position: relative;
				top: -40px;
				left: 0px;
				height: 600px;
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
				max-height: 600px;
			}
			#info{
				position: relative;
				top:-600px;
				left: 710px;
			}

			</style>';
	echo '<div id="picture_zoom">
		  <div id="picture">';
	echo '<img src="data:image/jpeg;base64,'.$picture->getPictureBinary().'">';
	echo '</div>';
	echo '<div id="info">
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
		    	<div id="pic_rating" class="rating"
				</div>';
	if (isset($usr))
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
    else
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

		echo '	</div>
		</div>
		<div id="comments_panel" class="panel">
		    <div id="comments_title" class="panel-header bg-lightBlue fg-white">
		        Hozzászólások
		    </div>
		    <div  id="comments" class="panel-content">
			    <div class="listview-outlook" data-role="listview">
	                <a class="list marked" href="#">
	                    <div class="list-content">
	                        <span class="list-title">Felhasználó</span>
	                        <span class="list-remark">Komment</span>
	                    </div>
	                </a>
	                <a class="list" href="#">
	                    <div class="list-content">
	                        <span class="list-title">Felhasználó</span>
	                        <span class="list-remark">Komment</span>
	                    </div>
	                </a>
	                <a class="list" href="#">
	                    <div class="list-content">
	                        <span class="list-title">Felhasználó</span>
	                        <span class="list-remark">Komment</span>
	                    </div>
	                </a>
	                <a class="list" href="#">
	                    <div class="list-content">
	                        <span class="list-title">Felhasználó</span>
	                        <span class="list-remark">Komment</span>
	                    </div>
	                </a>
	            </div>
        	</div>
            <div class="input-control text" data-role="input-control">
                <textarea rows="3" cols="35" id="new_comment" type="text" placeholder="Hozzászólás írásához kattints ide..."></textarea>
                <button class="btn-clear" tabindex="-1"></button>
        	</div></br>
        	<button>Elküldés</button>
		</div>
	</div>';
?>
