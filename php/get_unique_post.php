<?php
	include('connect_db.php');

	$get_unique_post_query = $_POST['get_unique_post_query'];

//checking if the visitor is friend of the user or user himself
	if(isset($_COOKIE['signed_username']))
	{
		$signed_username = $_COOKIE['signed_username'];
	}
	else
	{
		$signed_username = null;
	}

	$user_post_access = 1;

//fetching user_info
	$fetch_user_info_query = "SELECT * FROM users_info WHERE username = '" . $signed_username . "'";
	$fetch_user_info_query_run = mysqli_query($connect_link, $fetch_user_info_query);

	$fetched_users = mysqli_num_rows($fetch_user_info_query_run);

	if($fetched_users > 0)
	{
		$fetch_user_info_assoc = mysqli_fetch_assoc($fetch_user_info_query_run);

		$user_name = $fetch_user_info_assoc['name'];
		$user_username = $fetch_user_info_assoc['username'];
		$user_dp = $fetch_user_info_assoc['dp'];
		
		$dp_location =  "img/". $user_username . "_photo/" . $user_dp;
	}
	
//getting the post of the user
	if($get_post_query_run = mysqli_query($connect_link, $get_unique_post_query))
	{		
		while($get_post_assoc = mysqli_fetch_assoc($get_post_query_run)) 
		{
			$get_post_id = $get_post_assoc['id'];
			$get_post_text = $get_post_assoc['text'];
			$get_post_photo = $get_post_assoc['photo'];
			$get_post_video = $get_post_assoc['video'];
			$get_post_location = $get_post_assoc['location'];
			$get_post_time = $get_post_assoc['time'];

			echo "<div class=\"post_div\">
					<div class=\"post_text_container\">
						<div class=\"post_user_dp_name_mob\">
							<img class=\"post_dp_icon\" src=\"$dp_location\" onerror=\"this.onerror=null;this.src='img/def_user_dp.jpg';\"/>&emsp;$user_name";
							if($get_post_location != "")
							{
								echo "<span> at <img class=\"post_location_icon\" src=\"img/location.png\" />$get_post_location</span>";
							}

			echo		"</div>

						<div class=\"post_content_container\">";

							if($get_post_photo !="")
							{
								echo "<center><img class=\"post_image_content\" src=\"img/" . $signed_username . "_photo/" . $get_post_photo ." \" onerror=\"this.onerror=null;this.src='img/photo_placeholder.png';\" /></center>";
							}

							if($get_post_video !="")
							{
								echo "<center><video class=\"post_video_content\" controls>
										  <source src=\"vid/".$signed_username, "_video/". $get_post_video."#t=1\" type=\"video/mp4\">
										  Your browser does not support the video tag.
									</video></center>";
							}

							if($get_post_text !="")
							{
								echo "<div class=\"post_text_content\">$get_post_text</div>";
							}
							

			echo "		</div>";

						if($user_post_access == 1)
						{
							echo "	<div class=\"post_like_comment_button\">";

							//getting like count of a particular post
								$like_post_id = $signed_username . "_post_" . $get_post_id;
								$like_count_query = "SELECT id FROM likes WHERE post_id = '$like_post_id'";
								$like_count_query_run = mysqli_query($connect_link, $like_count_query);
								$like_count = mysqli_num_rows($like_count_query_run);

							//to know if user have liked a particular post or not
								$like_post_id = $signed_username . "_post_" . $get_post_id;
								$user_liked_query = "SELECT id FROM likes WHERE user_name = '$signed_username' && post_id = '$like_post_id'";
								$user_liked_query_run = mysqli_query($connect_link, $user_liked_query);
								$user_liked = mysqli_num_rows($user_liked_query_run);

								if($user_liked >= 1)
								{
									echo "	<span class=\"post_like_text\">unlike</span>
											<img class=\"post_like_button\" src=\"img/like_png.png\"/>";
								}
								else
								{
									echo "	<span class=\"post_like_text\">like</span>
											<img class=\"post_like_button\" src=\"img/like1.png\"/>";
								}


							echo "		<span class=\"post_like_count\">$like_count</span>
										&emsp;

										<img class=\"post_comment_button\" src=\"img/comment.png\"/>
										&emsp;

										<img class=\"post_send_story_button\" src=\"img/send_story.png\"/>

										<img class=\"post_fav_button\" src=\"img/add_fav.png\"/>
										&emsp;
									</div>";
						}
						else
						{
							echo "	<style>
										.post_content_container
										{
											height: 310px;
										}

										.post_image_content
										{
											height: 310px;
										}

										.post_video_content
										{
											height: 310px;
											padding: 5px;
											overflow: hidden;
										}

										@media screen and (max-width: 800px)
										{
											.post_content_container
											{
												height: 180px;
												overflow-y: scroll;
											}

											.post_image_content
											{
												height: 180px;
											}
										}
									</style>";
						}
			
			echo	"</div>

					<div class=\"post_comment_container\">
						<span class=\"post_id\">
							$get_post_id
						</span>
						<div class=\"post_like_comment_container\"></div>
					</div>
				</div>
				<br>";

		}
	}
	else
	{
		echo 'Something went wrong while fetching posts.';
	}

?>

<script>

//on clicking on like button
	$('.post_like_button').click(function()
	{
		var this_post = $(this).parent().parent().parent();
		var this_post_id = $.trim(this_post.find('.post_id').text());
		var username = $.trim("<?php echo $signed_username; ?>");

		var post_like_text = this_post.find('.post_like_text').text();
		//alert(post_like_text);

		if(post_like_text == "like")
		{
			$.post('php/add_like.php', {this_post_id: this_post_id, username: username}, function(data)
			{
				this_post.find('.post_like_count').text(data);
				this_post.find('.post_like_text').text("unlike");
				this_post.find('.post_like_button').attr("src", "img/like_png.png");
			});
		}
		else
		{
			$.post('php/remove_like.php', {this_post_id: this_post_id, username: username}, function(data)
			{			
				this_post.find('.post_like_count').text(data);
				this_post.find('.post_like_text').text("like");
				this_post.find('.post_like_button').attr("src", "img/like1.png");
			});
		}
	});

//on clicking on like count text of the post
	$('.post_like_count').click(function()
	{
		var this_post = $(this).parent().parent().parent();

		var username = $.trim("<?php echo $signed_username; ?>");
		var this_post_id = $.trim(this_post.find('.post_id').text());
		
		$.post('php/get_post_like.php', {this_post_id: this_post_id, username: username}, function(e)
		{
			this_post.find('.post_like_comment_container').html(e);
		});	
		
		this_post.find('.post_comment_container').fadeIn(500);
	});

//on clicking on comment icon of the post
	$('.post_comment_button').click(function()
	{
		var this_post = $(this).parent().parent().parent();

		var username = $.trim("<?php echo $signed_username; ?>");
		var this_post_id = $.trim(this_post.find('.post_id').text());
		
		$.post('php/get_post_comment.php', {this_post_id: this_post_id, username: username}, function(e)
		{
			this_post.find('.post_like_comment_container').html(e);
		});	
		
		this_post.find('.post_comment_container').fadeIn(500);
	});

</script>
