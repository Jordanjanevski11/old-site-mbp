<?php
/*
Plugin Name: MBP-Campusmap
Plugin URI: http://minabastapolare.se
Description: Plugin for the maps over campus
Version: 0.1
Author: Pontus Persson
Author URI: http://minabastapolare.se
*/

// constants
if(!defined(MBP_PLUGIN_DIR))
	define(MBP_PLUGIN_DIR, ABSPATH.'wp-content/plugins/');

if(!defined(MBP_CUSTOM_IMAGES))
	define(MBP_CUSTOM_IMAGES, ABSPATH.'wp-content/themes/thesis/custom/images/');

if(!defined(MBP_CAMPUSMAP_DIR))
	define(MBP_CAMPUSMAP_DIR, ABSPATH.'wp-content/plugins/mbp-campusmap/');

if(!defined(MBP_CAMPUSMAP_PATH))
	define(MBP_CAMPUSMAP_PATH, WP_PLUGIN_URL.'/mbp-campusmap/');

// add scripts 
add_action('init', 'admin_scripts');
function admin_scripts() 
{
	if( is_admin() )
	{
		wp_enqueue_script( "editinplace", (MBP_CAMPUSMAP_PATH."jquery.editinplace.js"), array( 'jquery' ) );
	}
}

// hook the menu
add_action('admin_menu', 'mbp_add_menu');

// function for adding menus
function mbp_add_menu()
{
	// Add a new top-level menu
	add_menu_page('MBP Settings', 'MBP Settings', 'manage_options', 'mbp-toplevel-menu', 'mbp_sublevel_page_1');
	// Add first submenu
	add_submenu_page('mbp-toplevel-menu','Hyresgäster','Hyresgäster','manage_options','mbp-toplevel-menu','mbp_sublevel_page_1'); //add_submenu_page('mbp-toplevel-menu', 'Hyresgäster', 'Hyresgäster', 'manage_options', 'mbp-sub-page-1', 'mbp_sublevel_page_1');
	// Add sub menu
	add_submenu_page('mbp-toplevel-menu', 'Kartor/Filer', 'Kartor/Filer', 'manage_options', 'mbp-sub-page-2', 'mbp_sublevel_page_2');
}

// 
function mbp_sublevel_page_1()
{
	global $wpdb;

?>	<div class="wrap">
		<div id="icon-options-general" class="icon32"><br></div>
		<h2>Inställningar för hyresgäster</h2>
		<p>Klicka på texten för att ändra den</p>
	<?php
	
	$tenants = $wpdb->get_results( "SELECT * FROM tenants ORDER BY name ASC" ); // .mysql_real_escape_string(intval($_GET['page']), $wpdb->dbh) );
	if( !$tenants )
	{
		// If tenants empty create one without data just to have something to edit.
		$tenants = $wpdb->get_results( "INSERT INTO tenants (name, address, house, phone, contact, email, webpage) VALUES (' ', ' ', ' ', ' ', ' ', ' ', ' ')" );
		$tenants = $wpdb->get_results( "SELECT * FROM tenants ORDER BY name ASC" );
		if( !$tenants )
		{
			// Something is wrong
			die("Kan inte hämta från databasen");
		}
	}
	
	?>
	<script type="text/javascript">
	jQuery(document).ready( function(jQuery) {
		jQuery(".edit_name").editInPlace(
		{
		      url: "/wp-content/plugins/mbp-campusmap/mbp-updatetenant.php", bg_over: "#FFFBCC", bg_out: "transparent", default_text : "Klicka för att ange namn", saving_text: "Sparar..."
		});
		jQuery(".edit_address").editInPlace(
		{
		      url: "/wp-content/plugins/mbp-campusmap/mbp-updatetenant.php", bg_over: "#FFFBCC", bg_out: "transparent", default_text : "Klicka för att ange adress", saving_text: "Sparar..."
		});
		jQuery(".edit_house").editInPlace(
		{
		      url: "/wp-content/plugins/mbp-campusmap/mbp-updatetenant.php", bg_over: "#FFFBCC", bg_out: "transparent", default_text : "Klicka för att ange hus-kod", saving_text: "Sparar..."
		});
		jQuery(".edit_phone").editInPlace(
		{
		      url: "/wp-content/plugins/mbp-campusmap/mbp-updatetenant.php", bg_over: "#FFFBCC", bg_out: "transparent", default_text : "Klicka för att ange tel", saving_text: "Sparar..."
		});
		jQuery(".edit_contact").editInPlace(
		{
		      url: "/wp-content/plugins/mbp-campusmap/mbp-updatetenant.php", bg_over: "#FFFBCC", bg_out: "transparent", default_text : "Klicka för att ange kontakt", saving_text: "Sparar..."
		});
		jQuery(".edit_email").editInPlace(
		{
		      url: "/wp-content/plugins/mbp-campusmap/mbp-updatetenant.php", bg_over: "#FFFBCC", bg_out: "transparent", default_text : "Klicka för att ange email", saving_text: "Sparar..."
		});
		jQuery(".edit_webpage").editInPlace(
		{
		      url: "/wp-content/plugins/mbp-campusmap/mbp-updatetenant.php", bg_over: "#FFFBCC", bg_out: "transparent", default_text : "Klicka för att ange webbsida", saving_text: "Sparar..."
		});
	});
	
	function add_tenant()
	{
		jQuery.post("/wp-content/plugins/mbp-campusmap/mbp-updatetenant.php", { addempty: "true" }, function(data)
		{ 
			location.reload();
		});
	}
	function remove_tenant(tenantid)
	{
		var answer = confirm("Vill du verkligen ta bort hyresgästen?");
		if (answer)
		{
			jQuery.post("/wp-content/plugins/mbp-campusmap/mbp-updatetenant.php", { remove_tenant: "true", id: tenantid }, function(data)
			{
				location.reload();
			});
		}
	}
	</script>
	
	<p><a class="button-secondary submit-add-to-menu" href="javascript:add_tenant();">Lägg till hyresgäst</a></p>
	<table class="wp-list-table widefat fixed pages" cellspacing="0">
	<thead>
		<tr style="font-weight:bold;background-color:#DFDFDF;">
			<th width="50">Ta bort</td>
			<th>Namn</td>
			<th>Adress</td>
			<th>Beläget</td>
			<th>Tel</td>
			<th>Kontaktperson</td>
			<th>E-post</td>
			<th>Webbsida</td>
		</tr>
	</thead>
	<tbody id="the-list">
	<?php
	foreach($tenants as $tenant)
	{
		if( !($tenant->name) )
		{
?>		<tr style="background-color:#ffffee;"><?php
		}
		else
		{
?>		<tr><?php
		}
		?>
			<th width="50"><a href="javascript:remove_tenant(<?php echo $tenant->id; ?>);">Ta bort</a></td>
			<td><span id="name_<?php echo $tenant->id; ?>" class="edit_name"><?php echo $tenant->name; ?></span></td>
			<td><span id="address_<?php echo $tenant->id; ?>" class="edit_address"><?php echo $tenant->address; ?></td>
			<td><span id="house_<?php echo $tenant->id; ?>" class="edit_house"><?php echo $tenant->house; ?></td>
			<td><span id="phone_<?php echo $tenant->id; ?>" class="edit_phone"><?php echo $tenant->phone; ?></td>
			<td><span id="contact_<?php echo $tenant->id; ?>" class="edit_contact"><?php echo $tenant->contact; ?></td>
			<td><span id="email_<?php echo $tenant->id; ?>" class="edit_email"><?php echo $tenant->email; ?></td>
			<td><span id="webpage_<?php echo $tenant->id; ?>" class="edit_webpage"><?php echo $tenant->webpage; ?></td>
		</tr>
		<?php
	}
	?>
	<tfoot>
		<tr style="font-weight:bold;background-color:#DFDFDF;">
			<th colspan="8">
			&nbsp;
			</th>
		</tr>
	</tfoot>
	</tbody>
	</table>
	</div>
<?php
}

function mbp_sublevel_page_2()
{

	// Check if trying to upload a file
	if( $_POST['upload'] == 'true' && $_FILES)
	{
		// Array with all the errors we can detect
		$errorarr = array(	"No error",
						"The uploaded file exceeds the upload_max_filesize directive in php.ini.",
						"The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.",
						"The uploaded file was only partially uploaded.",
						"No file was uploaded.",
						"",
						"Missing a temporary folder.",
						"Failed to write file to disk.",
						"A PHP extension stopped the file upload." );
		
		// Check for errors with upload
		if( $_FILES['uploadedfile']['error'] > 0 )
		{
			die( "File upload error: ".$errorarr[$_FILES['uploadedfile']['error']] );
		}

		$target_path = MBP_CUSTOM_IMAGES."uploads/".basename( $_FILES['uploadedfile']['name']);
		$target_url = "/wp-content/themes/thesis/custom/images/uploads/".basename( $_FILES['uploadedfile']['name']);
		
		if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path))
		{
		    echo "Filen '".$target_url."' har laddats upp";
		    if( $_POST['type'] == "parkmap" )
		    {
			update_option( 'parkimgurl', $target_url );
		    }
		    elseif( $_POST['type'] == "areamap" )
		    {
			update_option( 'mapimgurl', $target_url );
		    }
		    else
		    {
			update_option( 'svmapurl', $target_url );
		    }
		}
		else
		{
		    echo "There was an error uploading the file, please try again!";
		}
	}


	global $wpdb;
	
	 //echo get_option('mapimgurl');
	 
?>	<div class="wrap">
		<div id="icon-options-general" class="icon32"><br></div>
		<h2>Ladda upp kartor/filer</h2>
		<p>Byt ut filer som finns för nerladdning genom att ladda upp nya här.</p>
		<p><i>Notera att filnamn med mellanslag, å ä ö eller ovanliga tecken kan fungera dåligt.</i></p>
	</div>
	
	<form enctype="multipart/form-data" action="" method="POST">
	<input type="hidden" name="MAX_FILE_SIZE" value="3000000" />
	<input type="hidden" name="upload" value="true" />
	Områdeskarta <input type="radio" name="type" value="areamap"> 
	Parkeringskarta <input type="radio" name="type" value="parkmap"> 
	Svartvit <input type="radio" name="type" value="svmap"> 
	<br>
	<input class="button tagadd" name="uploadedfile" type="file" />
	<input type="submit" value="Skicka" />
	</form>
<?php


}

function mbp_hoverscript()
{
	global $wpdb;

	$houses = $wpdb->get_results( "SELECT DISTINCT tenants.house FROM tenants ORDER BY name ASC" ); // .mysql_real_escape_string(intval($_GET['page']), $wpdb->dbh) );
	$tenants = $wpdb->get_results( "SELECT * FROM tenants ORDER BY name ASC" );
	
?>
		<script type="text/javascript">
	
			var images = new Array();
			var tenantinfo = new Array();
			var isOpen = new Boolean();
			var lastLetter = new String();
			
			if (document.images)
			{
<?php			// only load images that actually have tenants
				foreach($houses as $house)
				{
					if( strtolower($house->house) != "-")
					{
?>
					images['<?php echo strtolower(trim($house->house)); ?>'] = "/wp-content/themes/thesis/custom/images/houses/<?php echo strtolower(trim($house->house)); ?>.gif";
					images['<?php echo strtolower(trim($house->house)); ?>_white'] = "/wp-content/themes/thesis/custom/images/houses/<?php echo strtolower(trim($house->house)); ?>_white.gif";
					var imageObj<?php echo strtolower(trim($house->house)); ?> = new Image();
					imageObj<?php echo strtolower(trim($house->house)); ?>.src="/wp-content/themes/thesis/custom/images/houses/<?php echo strtolower(trim($house->house)); ?>.gif";
<?php				}
				}
?>
			}


<?php		// put all the info for each tenant in an array
			foreach($tenants as $tenant)
			{
				$strTenantInfo = "<h4>".$tenant->name."</h4>";
				$strTenantInfo .= "<div style=\"margin-bottom:4px;\"><b>Besöksadress</b><br>".$tenant->address."</div>";
				$strTenantInfo .= "<div style=\"margin-bottom:4px;\"><b>Beläget</b><br>Hus ".$tenant->house."</div>";
				$strTenantInfo .= "<div style=\"margin-bottom:4px;\"><b>Telefonnummer</b><br>".$tenant->phone."</div>";
				$strTenantInfo .= "<div style=\"margin-bottom:4px;\"><b>Kontaktperson</b><br>".$tenant->contact."</div>";
							
				if ( $tenant->email != "" )
				{
					$strTenantInfo .= "<div style=\"margin-bottom:4px;\"><b>E-post</b><br><a href=\"mailto:".$tenant->email."\" style=\"color:#515151\">".$tenant->email."</a></div>";
				}
				
				if ( $tenant->webpage != "" && $tenant->webpage != "n/n" )
				{
					$strTenantInfo .= "<div style=\"margin-bottom:4px;\"><b>Webbsida</b><br><a href=\"http://".$tenant->webpage."\" target=\"_blank\" style=\"color:#515151\">".$tenant->webpage."</a></div>";
				}
?>
				tenantinfo['<?php echo $tenant->id; ?>'] = '<?php echo $strTenantInfo	?>';
<?php
			}
?>
			jQuery(function(){jQuery(".house_hover").hover(
				function()
				{
					if(isOpen)
					{
						var letter =  $(this).attr('id').split('_')[1]
						jQuery(".house_"+letter).addClass('houses_hover');
						jQuery("#post_"+letter).attr('src', images[letter] );
					}
				}, 
				function()
				{
					if(isOpen)
					{
						var letter =  $(this).attr('id').split('_')[1]
						jQuery(".house_"+letter).removeClass('houses_hover');
						jQuery("#post_"+letter).attr('src', images[letter+'_white'] );
					}
				}
			);});
			
			//~ var fading = false;

			//~ $(".houses").hoverIntent({    
				//~ over: function()
				//~ {
					//~ if(isOpen)
					//~ {
						//~ if( fading )
						//~ {
							//~ //jQuery("#infobox").clearqueue();
						//~ }
						//~ fading = true;
						//~ var letter =  $(this).attr('id').split('_')[0]
						//~ var id =  $(this).attr('id').split('_')[1]
						//~ jQuery("#post_"+letter).attr('src', images[letter] );
						//~ jQuery("#infobox").html( tenantinfo[id] );
						//~ jQuery("#infobox").fadeIn(200, function(){ fading = false; } );
					//~ }
				//~ },
				//~ timeout: 0, 
				//~ out: function()
				//~ {
					//~ //alert ('hej');
					//~ if(isOpen)
					//~ {
						//~ fading = true;
						//~ var letter =  $(this).attr('id').split('_')[0]
						//~ var id =  $(this).attr('id').split('_')[1]
						//~ jQuery("#post_"+letter).attr('src', images[letter+'_white'] );
						//~ jQuery("#infobox").fadeOut(200, function(){ fading = false; } );
					//~ }
				//~ }
			//~ });
			
			
			var fadingIn = false;
			var x;
			
			function sara( id, letter)
			{
				fadingIn = true;
				jQuery("#post_"+letter).attr('src', images[letter] );
				jQuery("#infobox").html( tenantinfo[id] );
				jQuery("#infobox").fadeIn(200, function(){ fadingIn = false; x = null; });
			}

			jQuery(function(){jQuery(".houses").hover(
				function()
				{
					if(isOpen)
					{
						var letter =  $(this).attr('id').split('_')[0];
						var id =  $(this).attr('id').split('_')[1];
						x = setTimeout('sara("'+id+'", "' +letter+ '")', 150);
					}
				}, 
				function()
				{
					if(isOpen)
					{
						//~ var id =  $(this).attr('id').split('_')[1];
						//~ jQuery("#post_"+letter).attr('src', images[letter+'_white'] );
						jQuery('.house_hover').each(function()
						{
							if(jQuery(this).attr('src').indexOf('_white') == -1)
							{
								var letter =  $(this).attr('id').split('_')[1];
								jQuery(this).attr('src', images[letter+'_white'] );
							}
						});
						
						if(x == null)
						{
							jQuery("#infobox").fadeOut(200);
						}
						else
						{
							if( fadingIn )
							{
								jQuery("#infobox").hide();
							}
							clearTimeout(x);
						}
					}
				}
			);});
			
			
			
			jQuery(".houses").click(function(e) 
			{
				if(isOpen)
				{
					var letter =  $(this).attr('id').split('_')[0]
					var id =  $(this).attr('id').split('_')[1]
					jQuery(this).addClass('houses_stick');
					jQuery("#post_"+letter).attr('src', images[letter] );
					jQuery("#infobox").html( tenantinfo[id] );
					jQuery("#infobox").show();
					lastLetter = letter; isOpen = false;
				}
				else
				{
					var id =  $(this).attr('id').split('_')[1]
					jQuery(".houses").removeClass('houses_stick');
					jQuery("#post_"+lastLetter).attr('src', images[lastLetter+'_white'] );
					jQuery("#infobox").hide();
					isOpen = true;
				}
			});
		</script>
	<?php
}
add_action('wp_footer','mbp_hoverscript');

// function to 
function show_campusmap()
{
	global $wpdb;

	$tenants = $wpdb->get_results( "SELECT * FROM tenants ORDER BY name ASC" ); // .mysql_real_escape_string(intval($_GET['page']), $wpdb->dbh) );
	$num_tenants = $wpdb->num_rows;
	$houses = $wpdb->get_results( "SELECT DISTINCT tenants.house FROM tenants ORDER BY name ASC" );
	
	
	?>
	<div style="float:left;width:500px;">
		<div style="position:relative;z-index:1; margin-left:-80px; width: 621px; height:662px; background:url('/wp-content/themes/thesis/custom/images/houses2/a4campus-karta.png'); background-position:3px 0;">
			<?php
			foreach( $houses as $house )
			{
				if( strtolower($house->house) != "-" )
				{
					echo '<img class="house_hover" id="post_'.strtolower(trim($house->house)).'" src="/wp-content/themes/thesis/custom/images/houses/'.strtolower(trim($house->house)).'_white.gif">';
				}
			}
			?>
			<div id="infobox">
			</div>
		</div>
		<div id="download_content">
			<h1 class="title_underlined">Nerladdningsbara filer</h1>
			<div class="download_text">
				<a href="<?php echo get_option('mapimgurl'); ?>">Områdeskarta</a><br>
			</div>
				<div class="seperatorline"></div>
			<div class="download_text">
				<a href="<?php echo get_option('parkimgurl'); ?>">Parkeringskarta</a><br>
			</div>
				<div class="seperatorline"></div>
			<div class="download_text">
				<a href="<?php echo get_option('svmapurl'); ?>">Parkeringskarta (svartvit)</a><br>
			</div>
		</div>
	</div>
	<div style="position:relative;z-index:2;width:430px;float:right;padding-top:0;padding-left:10px;">
		<h1>Hyresgäster på Campusområdet</h1>
		<table cellspacing="0" style="float:left;margin-top:10px;">
		<?php
		$i = 0;
		foreach($tenants as $tenant)
		{
			if( $i == intval($num_tenants / 2) )
			{
			?>
				</table>
				<table cellspacing="0" style="float:right;margin-top:10px;">
			<?php
			}
			$i++;
			
			?>
			<tr>
				<td><div id="<?php echo strtolower($tenant->house); ?>_<?php echo $tenant->id; ?>" class="houses house_<?php echo strtolower($tenant->house); ?>"><?php echo $tenant->name; ?></div></td>
			</tr>
			<?php
		}
		?>
		</table>
		</div>
	<?php
}
add_shortcode('campus_karta', 'show_campusmap');

?>