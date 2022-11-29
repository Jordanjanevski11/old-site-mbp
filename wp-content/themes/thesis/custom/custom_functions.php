<?php

remove_action('wp_head', 'wp_generator');

add_theme_support( 'post-thumbnails', array('page'));

remove_action('thesis_hook_footer', 'thesis_attribution');

remove_action('thesis_hook_before_header', 'thesis_nav_menu'); 
add_action('thesis_hook_after_header', 'thesis_nav_menu'); 

add_filter( 'thesis_next_post', create_function('$a', "return 'Nästa inlägg: ';") );
add_filter( 'thesis_previous_post', create_function('$a', "return 'Föregående inlägg: ';") );

//add_filter('thesis_next_post', create_function('next_post_text',"return 'Nästa inlägg:'");

//add_action('thesis_hook_before_header', 'thesis_hook_feature_box');

//add_action('wp_update_nav_menu', 'mbp_sitemap');

add_action('thesis_hook_before_content_box', 'mbp_puffar');

add_action('thesis_hook_before_content', 'select_output');

function select_output()
{
	if( is_front_page() )  
	{
		show_home_page();
	}
	elseif( is_page(11) && function_exists( 'show_campusmap' ) )
	{
		the_post();
		show_campusmap();
	}
	elseif( is_page(182) )
	{
		show_press_page();
	}
	elseif( is_home() )
	{
		query_posts("cat=-9");
		function my_comments_link()
		{
			?>
			<?php comments_popup_link('Bli först att kommentera', '1 kommentar', '% kommentarer', 'comments-link', ''); ?><br>
			Kategori: <?php the_category(', '); ?><br>
			<?php
		}
		remove_action('thesis_hook_after_post', 'thesis_comments_link');
		add_action('thesis_hook_after_post', 'my_comments_link');
	}
}

function mbp_puffar()
{
	// only show "puffs" on front page
	if( is_front_page() )
	{
		$wpquery = new WP_Query('p=5&post_type=page');
		$wpquery->the_post();
	
	?>
	<div id="puffar">
		<div id="left_puff">
			<h1 class="puff_header"><?php the_title(); ?></h1>
			<div class="puff_text"><a href="<?php the_permalink(); ?>"><p><?php echo mbp_the_content(350); ?></p></a></div>
			<a class="puff_link" href="<?php the_permalink(); ?>">Läs mer om A4 Campus</a>
		</div>
	<?php
		$wpquery->query('cat=4,-9');
		$wpquery->the_post();
	?>
		<div id="right_puff">
			<h1 class="puff_header">Just nu på Campus</h1>
			<div class="puff_text">
				<h4><?php the_title(); ?></h4>
				<a href="<?php the_permalink(); ?>"><p><?php echo mbp_the_content(250); ?></p></a>
			</div>
			<a class="puff_link" href="/nyheter">Fler händelser från A4 Campus</a>
		</div>
	</div>
	<?php
	}
}

function url_get_contents($Url)
{
	if (!function_exists('curl_init'))
	{ 
		die('CURL is not installed!');
	}
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $Url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}

function show_press_page()
{
	the_post();
	
	?>
	<h2 class="press_title"><?php the_title(); ?></h2>
	<br>
	<?php
	
	$filename = "http://www.newsmachine.com/external/a4campus_feed.xml";
	$xml = url_get_contents( $filename );

	$feed = produce_XML_object_tree($xml);
	//var_dump( $feed );
	
	$postsperpage = 10;
	$buttons = 10; // in addition to the current so even numbers
	$page = ($_GET['page']) ? intval($_GET['page']) : 0;
	$countposts = 0;
	
	foreach ($feed->agents->agent as $entry)
	{
		foreach($entry->resultsets->resultset as $item)
		{
			if( ($countposts >= ($page * $postsperpage)) && ($countposts <= (($page * $postsperpage) + $postsperpage - 1 )) )
			{
				?>
				<div class="press_post">
					<h2>
						<?php echo $item->documents->document->title; ?>
					</h2>
				<p class="byline_date"><abbr title="<?php echo date("Y-m-d", strtotime($item->documents->document->date) ); ?>">
				<?php echo date("Y-m-d", strtotime($item->documents->document->date) ); ?></abbr></p><br>
				<p><?php echo strip_tags($item->results->result); ?></p><br>
				<a target="_BLANK" class="puff_link" href="<?php echo $item->documents->document->url; ?>">Länk till artikeln</a></div>
				<?php
			} 
			$countposts++; 
		}
	}
	$countposts--;
	
	$maxpages = intval($countposts / $postsperpage);
	
	$startvalue = intval($page - ($buttons / 2)) > 0 ? intval($page - ($buttons / 2)) : 0;
	$endvalue = intval($page + ($buttons / 2)) < $maxpages ? intval($page + ($buttons / 2)) : $maxpages;
	
	if( ($countposts / $postsperpage) > $buttons)
	{
		$additionalbuttons = ($startvalue == 0) ? ($buttons - $endvalue) : 0;
		$lessbuttons = ($endvalue == $maxpages) ?  (($buttons / 2) - ($endvalue - $page)) : 0;
	}
	
	/*
	echo "<br>posts: ". $countposts;
	echo "<br>maxpages: ". $maxpages;
	echo "<br>start: ". $startvalue;
	echo "<br>end: ". $endvalue;
	echo "<br>current: ". $page;
	echo "<br>less: ". $lessbuttons;
	echo "<br>more: ". $additionalbuttons;
	*/
	
	// pagineringsknappar
	?>
	<div class="pagination">
		<?php
		for( $i = $startvalue - $lessbuttons; $i <= $endvalue + $additionalbuttons; $i++)
		{
			?>
			<a href="/nyheter/pressarkiv/?page=<?php echo $i; ?>" class="pagination_button<?php if($i == $page){ echo " cur"; } ?>"><?php echo $i + 1; ?></a>
			<?php
		}
		?>
	</div>
	<?php
	
}

function show_home_page()
{
	the_post();

	$wpquery = new WP_Query('p=9&post_type=page');
	$wpquery->the_post();
	?>
	<style>
	#content_box
	{
		padding-bottom:0px;
	}
	#footer_area
	{
		margin-top:-110px;
	}
	</style>
	<div id="page_image_links">
	<div class="startpage_imgpuff clickableDiv">
		<h3><?php the_title(); ?></h3>
		<img src="/wp-content/themes/thesis/lib/scripts/thumb.php?src=<?php echo mbp_getfirstimg(); ?>&h=150&w=240&zc=1&q=95">
		<div class="trans_hover">
			<div style="width:220px;height:110px;">
				<a href="<?php the_permalink(); ?>">
				<?php echo mbp_the_content(200); ?></a>
			</div>
			<a class="puff_link_white" href="<?php the_permalink(); ?>">Läs mer</a>
		</div>
	</div>
	<?php 

	$wpquery = new WP_Query('p=7&post_type=page');
	$wpquery->the_post();
	?>
	<div class="startpage_imgpuff clickableDiv">
		<h3><?php the_title(); ?></h3>
		<img src="/wp-content/themes/thesis/lib/scripts/thumb.php?src=<?php echo mbp_getfirstimg(); ?>&h=150&w=240&zc=1&q=95">
		<div class="trans_hover">
			<div style="width:220px;height:110px;">
				<a href="<?php the_permalink(); ?>">
				<?php echo mbp_the_content(200); ?></a>
			</div>
			<a class="puff_link_white" href="<?php the_permalink(); ?>">Läs mer</a>
		</div>
	</div>
	<?php 
	// hämta från startpuff kategorin
	$wpquery = new WP_Query('cat=9');
	
	// om det inte är några poster 
	if($wpquery->post_count < 1)
	{	
		//hämta från den vanliga sidan
		$wpquery = new WP_Query('p=534&post_type=page');
	}			
	

	$wpquery->the_post();
	?>
	<div class="startpage_imgpuff clickableDiv" style="width:380px;margin-right:0;">
		<h3><?php the_title(); ?></h3>
		<img src="/wp-content/themes/thesis/lib/scripts/thumb.php?src=<?php echo mbp_getfirstimg(); ?>&h=150&w=400&zc=1&q=95">
		<div class="trans_hover" style="width:380px;">
			<div style="width:380px;height:110px;">
				<a href="<?php the_permalink(); ?>">
				<?php echo mbp_the_content(300); ?></a>
			</div>
			<a class="puff_link_white" href="<?php the_permalink(); ?>">Läs mer</a>
		</div>
	</div>
	</div>
	<?php 

	$wpquery = new WP_Query('p=205&post_type=page');
	$wpquery->the_post();
	?>
	<div id="press_container">
		<div class="leftcontent">
			<h2><a href="/press">A4 Campus i press</a></h2>
			<div style="width:234px;height:93px;">
		<?php
			$xml = url_get_contents("http://www.newsmachine.com/external/a4campus_feed.xml");
			//$xmlTree = new SimpleXMLElement($xml);
			$feed = produce_XML_object_tree($xml);
			//var_dump($feed);
			
			//$feed->agents->agent[0] as $agent; $agent->resultsets->resultset[0] as $result; $result->results->result;
			
			foreach ($feed->agents->agent as $entry)
			{
				foreach($entry->resultsets->resultset as $item)
				{
					$maxchars = 100;
					$content = (strlen(strip_tags($item->results->result)) > $maxchars) ? substr(strip_tags($item->results->result), 0, $maxchars)."..." : strip_tags($item->results->result);
					
					echo "<a style='color:#130202;font-size:12px;' href='".$item->documents->document->url."'>".$item->documents->document->title."</a><br>";
					echo "<b>".$item->documents->document->source." ".date("Y-m-d", strtotime($item->documents->document->date) )."</b><br><br>";
					echo "<p>".$content. "</p>";
					break;
				}
				break;
			}
		?>
			</div>
			<a class="puff_link" href="/press">Mer från pressen</a>
		</div>
		<div class="middlecontent">
			<?php
			
			$wpquery = new WP_Query('p=11&post_type=page');
			$wpquery->the_post();
			?>
			<div class="clickableDiv" style="margin-right:30px;">
				<img src="/wp-content/themes/thesis/lib/scripts/thumb.php?src=<?php echo mbp_getfirstimg(); ?>&h=150&w=240&zc=1&q=95">
				<div class="white_hover">
					<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
				</div>
			</div>
		</div>
		<div class="rightcontent">
			<div style="float:left;">
				<h2>Kontakt</h2>
				Ann-Christin Björklund<br>
				063 - 12 70 10<br>
				<br>
				<h2>Kontakta din husvärd</h2>
				<table>
				<tr>
					<td>
					Robert Björklund&nbsp;&nbsp;&nbsp;&nbsp;
					</td>
					<td>
					070-325 66 23
					</td>
				</tr>
				<tr>
					<td>
					Mikael Nilsson  
					</td>
					<td>
					070-325 60 02
					</td>
				</tr>
				<tr>
					<td>
						Lars Hallqvist 
					</td>
					<td>
						070-325 66 36
					</td>
				</tr>
				</table>
			</div>
			<div style="width:130px;height:125px;float:right;">
				<h4>Besöksadress</h4>
				A4 Campus<br>
				Studentplan 2<br>
				831 40 Östersund
			</div>
			<a style="margin-right:1px;overflow:visible;clear:none;" class="puff_link" href="/kontakt">Kontaktsidan</a>
		</div>
	</div>
	
	<?php
}

function produce_XML_object_tree($raw_XML)
{
	libxml_use_internal_errors(true);
	try
	{
		$xmlTree = new SimpleXMLElement($raw_XML);
	} 
	catch (Exception $e)
	{
		// Something went wrong.
		$error_message = 'SimpleXMLElement threw an exception.';
		foreach(libxml_get_errors() as $error_line)
		{
			$error_message .= "\t" . $error_line->message;
		}
		trigger_error($error_message);
		return false;
	}
	return $xmlTree;
}

function mbp_the_content($max_chars = 120)
{ 
	global $post;
	if( !has_excerpt() || empty($post->post_excerpt) )
	{
		return ( strlen($post->post_content) > $max_chars ) ? substr(strip_tags($post->post_content), 0, $max_chars)."..." : $post->post_content;
	}
	else
	{
		return ( strlen($post->post_excerpt) > $max_chars ) ? substr(strip_tags($post->post_excerpt), 0, $max_chars)."..." : $post->post_excerpt;
	}
}

function mbp_getfirstimg()
{
	global $post;
	ob_start();
	ob_end_clean();
	$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
	$first_img = $matches[1][0];
	return $first_img;
}


function mbp_topbox()
{
		?>
		<div id="top_logo" class="clickableDiv">
			<a href="<?php bloginfo('url'); ?>">
				<img src="/wp-content/themes/thesis/custom/images/Campuslogo.png">
			</a>
		</div>
		<div id="searchbox">
			<form method="get" id="searchform" action="<?php bloginfo('home'); ?>/">
			<div>
				<input type="text" size="25" name="s" id="s" value="sök" onfocus="if(this.value==this.defaultValue)this.value='';" onblur="if(this.value=='')this.value=this.defaultValue;"/>
			</div>
			</form>
		</div>
		<?php
				
		global $post;
		
		if( has_post_thumbnail() ) // posten har en specifik header image satt
		{
			?>
			<div id="top_image">
				<div class="margin_image">
					<?php the_post_thumbnail(); ?>
				</div>
			</div>
			<?php
		}
		else // annars visa bildspel
		{
			?>
			<div id="top_image">
				<div class="coda-slider preload" id="coda-slider-5">
			<?php
			
			$cfArr = array();
			
			query_posts( array('showposts' => 100, 'post_type' => 'page') );

			while (have_posts())
			{ 
				the_post();
				/* ta bild från utvald bild */
				if ( has_post_thumbnail() )
				{
					$cfArr[] = get_the_post_thumbnail();
				}
			}
			
			// Randomize image order
			shuffle($cfArr);
			
			wp_reset_query();
			
			foreach($cfArr as $image)
			{
				?>
					<div class="panel">
						<div class="panel-wrapper">
						<?php echo $image; ?>
						</div>
					</div>
				<?php
			}
			?>
				</div>
			</div>
			<?php
		}
}
add_action('thesis_hook_header', 'mbp_topbox');

function mbp_footer()
{
	?>
	<div id="footer_content">
		<div class="left">
			<p>Håll kontakten med A4 Campus</p>
			<a href="http://feeds.feedburner.com/a4campus" target="_BLANK">
				<p style="margin-left:40px;">RSS</p>
				<img src="/wp-content/themes/thesis/custom/images/rssknappen.png">
			</a>
			<a href="http://feedburner.google.com/fb/a/mailverify?uri=a4campus&amp;loc=en_US" target="_BLANK">
				<p style="margin-left:40px;">E-post</p>
				<img src="/wp-content/themes/thesis/custom/images/epostknappen.png">
			</a>
		</div>
		<div class="right">
			<!-- AddThis Button BEGIN -->
			<a class="addthis_button" href="http://www.addthis.com/bookmark.php?v=250&amp;pubid=xa-4df0b9d550f8541b"><img src="/wp-content/themes/thesis/custom/images/delaknappen.png" alt="Dela A4 Campus" style="border:0"/><p>Dela denna sida</p></a>
			<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4df0b9d550f8541b"></script>
			<!-- AddThis Button END -->

		</div>
	</div>
	<?php
}
add_action('wp_footer','mbp_footer');


function add_to_head()
{
	?>
	<link rel="stylesheet" href="/wp-content/themes/thesis/custom/coda-slider-2.0.css" type="text/css" media="screen" />
	<!--[if IE]>
        <link rel="stylesheet" type="text/css" href="/wp-content/themes/thesis/custom/ie-style.css" />
	<![endif]-->	
	<script type="text/javascript" src="/wp-content/themes/thesis/custom/js/jquery.lavalamp.js"></script>
	<script type="text/javascript" src="/wp-content/themes/thesis/custom/js/jquery.easing.min.js"></script>
	<script type="text/javascript" src="/wp-content/themes/thesis/custom/js/jquery.easing.1.3.js"></script>
	<script type="text/javascript" src="/wp-content/themes/thesis/custom/js/jquery.coda-slider-2.0.js"></script>
	<script type="text/javascript">
		$(function() 
		{
			$("#menu-topmenu").lavaLamp({
			fx: "backout",
			speed: 600,
			click: function(event, menuItem) {
				return true;
			}
			});
		});
	</script>	    
	<?php
	
	
}
add_action('wp_head','add_to_head');

function mbp_add_scripts()
{
	?>
	<script type="text/javascript">
		window.onresize = function ()
		{
			moveShadows();
		}

		$(document).ready(function(){
			$('#top_image').show();
		});
		
		function moveShadows()
		{
			$("#shadowLeft").css('left', $("#content_box").offset().left-13 );
			$("#shadowLeft").css('top', $("#content_box").offset().top );
			$("#shadowLeft").css('height', $("#content_box").height() );
			
			$("#shadowRight").css('left', parseInt($("#content_box").offset().left)+962);
			$("#shadowRight").css('top', $("#content_box").offset().top );
			$("#shadowRight").css('height', $("#content_box").height() );
		}

		$('.clickableDiv').click(function(e) 
		{
			e.preventDefault();
			window.location = $(this).find('a:first').attr('href');
		});


		function toggleSitemap()
		{
			if($("#sitemap").is(":visible"))
			{
				$("#sitemap").slideUp(400);
				$("#menu-topmenu li a").show();
				$("li.back").css('background', 'url("/wp-content/themes/thesis/custom/images/menuback.png") no-repeat scroll center top transparent');
			}
			else
			{
				$("#sitemap").slideDown(400);
				$("#menu-topmenu li a").hide();
				$("li.back").css('background', 'none');
			}
		}
	
		function addShadowBorders()
		{
			$("#content_box").before('<div id="shadowLeft"></div>');
			$("#content_box").after('<div id="shadowRight"></div>');
			
			moveShadows();
			clearTimeout(x);
		}
		
		$(document).ready(function()
		{
			if( (navigator.userAgent.indexOf("Firefox") != -1) )
			{
				$('.coda-slider').css('margin-top', 0);
			}
			
			$('#coda-slider-5').codaSlider({
				autoSlide: true,
				autoHeight: false,
				dynamicArrows: false,
				dynamicTabs: false,
				dynamicTabsPosition: "bottom",
				dynamicTabsAlign: "right",
				autoSlideInterval: 4000,
				autoSlideStopWhenClicked: true
			});
			
			
			var data = '<?php
			// hämtar topmenyn
			$items = wp_get_nav_menu_items(3);
			foreach( $items as $item )
			{
				// visa inte "startsidan" i sitemapen
				if( $item->object_id != "91" )
				{
					$id = intval($item->object_id); 
					
					$args = array(
					'date_format'  => get_option('date_format'),
					'child_of'     => $id,
					'title_li'     => '<h4><a href="'.get_permalink( $id ).'">'.get_the_title($id).'</a></h4>', 
					'echo'         => 0,
					'depth'        => 1,
					'sort_column'  => 'menu_order, post_title');
					
					if( wp_list_pages($args) )
					{
						// skriv ut en lista med undersidor för varje sida i menyn
						echo '<div class="subpage_list">'.str_replace("\n", "", strip_tags(trim(wp_list_pages($args)), '<a><h4>')).'</div>';
						//echo '<div class="subpage_list">ensida</div>';
					}
					else
					{
						// om inga undersidor
						echo '<div class="subpage_list"><h4><a href="'.$item->url.'">'.$item->title.'</a></h4></div>';
					}
				}
			}
			?>';
			
			x = setTimeout('addShadowBorders()', 1500);
			//t = setTimeout(function(){ $('.coda-slider .panel').css('display', 'block'); }, 1000);
			//$('.coda-slider .panel-container .panel .panel-wrapper img').css('display', 'block'); 
			
			t = setTimeout(function()
			{
				$('.coda-slider .panel-container .panel .panel-wrapper img').css('display', 'block'); 
			}, 1000);
			
			//alert( "nu" );
			//$('.coda-slider .panel .panel-wrapper img').css('cssText', 'display: block !important');
		
			$("#menu-topmenu").append('<a onclick="javascript:toggleSitemap();" class="sitemap">Sitemap</a><div id="sitemap"></div>');
			$("#sitemap").append(data);
			
		});
	</script>
	<?php
}
add_action('wp_footer', 'mbp_add_scripts');

function init_scripts() 
{
	if( !is_admin() )
	{
		wp_deregister_script('jquery');
		wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"), false, '1.5.2', false);
		wp_enqueue_script('jquery');
	}
}
add_action('init', 'init_scripts');

function custom_byline() 
{
	if( !is_page() )
	{
		?><p class="byline_date"><abbr title="<?php echo get_the_time('j-n-Y H:i'); ?>"><?php echo get_the_time('j M Y'); ?></abbr></p><?php 
	}
}
add_action('thesis_hook_after_headline', 'custom_byline');

add_filter('upload_mimes', 'custom_upload_mimes');
function custom_upload_mimes ( $existing_mimes=array() )
{
	$existing_mimes['eps'] = 'application/postscript, application/eps, application/x-eps, image/eps, image/x-eps';
        return $existing_mimes;
}


/* ##### Ersätter standardtermen "Search: " vid sökningar ######### */
function default_archive_intro($output) {
	if (is_search()) {
		$intro = 'Sökresultat för';
		$output = str_replace('Search','' . $intro . '',$output);
	}

return $output;
}
add_filter('thesis_archive_intro','default_archive_intro');




?>