<?php
/*
Plugin Name: MBP News Sidebar Widget
Version: 1.0
Description: A simple sidebar widget to display newsflow in sidebar
Author: Pontus Persson
Author URI: http://minabastapolare.se
*/

class MBPNewsWidget extends WP_Widget
{

	function MBPNewsWidget()
	{
		$widget_ops = array( 'classname' => 'mbpnewswidget', 'description' => 'A simple sidebar widget to display newsflow in sidebar' );
		$control_ops = array( 'width' => 200, 'height' => 350);

		$this->WP_Widget( 'mbpnewswidget', 'MBP News Sidebar Widget', $widget_ops, $control_ops );
	}


	function widget( $args, $instance )
	{
		extract( $args );
		$title = apply_filters('widget_title', $instance['title'] );
		$count = apply_filters('widget_title', $instance['count'] );

		echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;
		
		// Content - BEGIN
		$filename = "http://www.newsmachine.com/external/a4campus_feed.xml";
		$xml = url_get_contents( $filename );

		$feed = produce_XML_object_tree($xml);
	
		//$feed->agents->agent[0] as $agent; $agent->resultsets->resultset[0] as $result; $result->results->result;
		
		$counter = 0;
		foreach ($feed->agents->agent as $entry)
		{
			foreach($entry->resultsets->resultset as $item)
			{
				if( !empty($item->documents->document->title) && !empty($item->documents->document->source) )
				{
					if( $counter >= $count )
					{
						break;
					}
					$maxchars = 100;
					
					$content = (strlen(strip_tags($item->results->result)) > $maxchars) ? substr(strip_tags($item->results->result), 0, $maxchars)."..." : strip_tags($item->results->result);
					
					echo '<div class="widget_newspost">';
					echo '<a target="_BLANK" href="'.$item->documents->document->url.'">'.$item->documents->document->title.'</a><br>';
					//echo $item->documents->document->title. "<br>";
					echo '<p class="press_widget_date">'.$item->documents->document->source.' '.date("Y-m-d", strtotime($item->documents->document->date) ).'</p>';
					
					echo '<p class="press_widget_content">'.$content.'</p>';
					echo '</div>';
					$counter++;
				}
			}
		}
		echo '<a class="puff_link" href="/pressarkiv">Visa alla pressklipp</a>';
		// Content - END

		echo $after_widget;
	}


	function update( $new_instance, $old_instance )
	{
		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['count'] = strip_tags( $new_instance['count'] );
		return $instance;
	}


	function form($instance)
	{
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
		$instance['title'] = strip_tags( $instance['title'] );
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>">Titel: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id('count'); ?>">Antal nyheter att visa: 
		<select id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>">
		<?php
		for ($i = 1; $i < 11; $i++)
		{
			?>
			<option <?php if($i == $instance['count']) { echo 'selected="selected" '; } ?>value="<?php echo $i; ?>"><?php echo $i; ?></option>
			<?php
		}
		?>
		</select>
		</label></p>
		<?php
	}
}

add_action('widgets_init', create_function('', 'return register_widget("MBPNewsWidget");'));