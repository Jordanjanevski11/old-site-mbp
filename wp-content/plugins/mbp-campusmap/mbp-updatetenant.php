<?php

/** Namnet p塤atabasen du vill anv寤a f򲠗ordPress */
define('DB_NAME', '201787-a4campus');

/** MySQL-databasens anv寤arnamn */
define('DB_USER', '201787_wc94351');

/** MySQL-databasens l򳥮ord */
define('DB_PASSWORD', 'ZtA7bMJ*35p');

/** MySQL-server */
define('DB_HOST', 'localhost');

if($_POST['update_value'] && $_POST['update_value'] != "" && $_POST['update_value'] != " ")
{
	$data = explode("_", $_POST['element_id']);

	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	mysql_select_db(DB_NAME, $link);

	$sql = "UPDATE tenants SET ".mysql_real_escape_string($data[0], $link)." = '".mysql_real_escape_string(utf8_decode($_POST['update_value']), $link)."' WHERE id = '".mysql_real_escape_string(intval($data[1]), $link)."'";
	$res = mysql_query($sql);

	if( $res )
	{
		echo $_POST['update_value'];
	}
	else
	{
		echo $_POST['original_value'];
	}
}
elseif($_POST['addempty'] == "true")
{
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	mysql_select_db(DB_NAME, $link);

	$sql = "INSERT INTO tenants (name, address, house, phone, contact, email, webpage) VALUES (' ', ' ', ' ', ' ', ' ', ' ', ' ')";
	$res = mysql_query($sql);
}
elseif($_POST['remove_tenant'] == "true" && isset($_POST['id']))
{
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	mysql_select_db(DB_NAME, $link);

	$sql = "DELETE FROM tenants WHERE id = '". mysql_real_escape_string(intval($_POST['id']), $link). "'";
	$res = mysql_query($sql);
}
else
{
	echo $_POST['original_value'];
}


?>