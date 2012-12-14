<?php

/*
Plugin Name: Litecoin WordPress
Description: Add Litecoin stuff to your WordPress blog
Author: Fanquake
Version: 1.0
Revision Date: May 21, 2012
Requires at least: WP 3.2.1, PHP 5.3
Tested up to: WP 3.4, PHP 5.3.6
*/

function getLitecoinPrice()
{
	 // Fetch the current rate from MtGox
	$ch = curl_init('https://mtgox.com/api/0/data/ticker.php');
	curl_setopt($ch, CURLOPT_REFERER, 'Mozilla/5.0 (compatible; MtGox PHP client; '.php_uname('s').'; PHP/'.phpversion().')');
	curl_setopt($ch, CURLOPT_USERAGENT, "CakeScript/0.1");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$mtgoxjson = curl_exec($ch);
	curl_close($ch);

	// Decode from an object to array
	$output_mtgox = json_decode($mtgoxjson);
	$output_mtgox_1 = get_object_vars($output_mtgox);
	$mtgox_array = get_object_vars($output_mtgox_1['ticker']);

	echo '
			<ul>
			<li><strong>Last:</strong>&nbsp;&nbsp;', $mtgox_array['last'], '</li>
			<li><strong>High:</strong>&nbsp;', $mtgox_array['high'], '</li>
			<li><strong>Low:</strong>&nbsp;&nbsp;', $mtgox_array['low'], '</li>
			<li><strong>Avg:</strong>&nbsp;&nbsp;&nbsp;', $mtgox_array['avg'], '</li>
			<li><strong>Vol:</strong>&nbsp;&nbsp;&nbsp;&nbsp;', $mtgox_array['vol'], '</li>
			</ul>';
}

class litecoin_widget extends WP_Widget {

// Constructor //

	function litecoin_widget()
	{
		$widget_ops = array( 'classname' => 'litecoin_widget', 'description' => 'Show some litecoin stuff' ); // Widget Settings
		$control_ops = array( 'id_base' => 'litecoin_widget' ); // Widget Control Settings
		$this->WP_Widget( 'litecoin_widget', 'Litecoins!', $widget_ops, $control_ops ); // Create the widget
	}

// Extract Args //

	function widget($args, $instance)
	{
		extract( $args );

		$title 		= apply_filters('widget_title', $instance['title']); // The widget title
		$show_price	= isset($instance['show_price']) ? $instance['show_price'] : false; // Show the Litecoin price
		$donate		= isset($instance['donate_litecoins']) ? $instance['donate_litecoins'] : false; // Get some litecoins for your blog
		$donation_address	= isset($instance['donation_address']) ? $instance['donation_address'] : false; // Donation address

// Before widget //

		echo $before_widget;

// Title of widget //

		if ($title)
			echo $before_title, $title, $after_title;

// Widget output //

		if ($show_price)
			getLitecoinPrice();

		if ($donate)
			echo '
		<p style="font-size:10px;">
			Send me some Litecoins! ', $donation_address, '
		</p>';

	// After widget //

		echo $after_widget;
	}

// Update Settings //

	function update($new_instance, $old_instance)
	{
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['show_price'] = $new_instance['show_price'];
		$instance['donate_litecoins'] = $new_instance['donate_litecoins'];
		$instance['donation_address'] = $new_instance['donation_address'];
		return $instance;
	}

// Widget Control Panel //

	function form($instance)
	{
		$defaults = array( 'title' => 'Litecoins!', 'show_price' => 'on', 'donate_litecoins' => 'on', 'donation_address' => 'xxx'  );
		$instance = wp_parse_args((array) $instance, $defaults);

		echo '
	<p>
		<label for="', $this->get_field_id('title'), '">Title:</label>
		<input class="widefat" id="', $this->get_field_id('title'), '" name="', $this->get_field_name('title'), '" type="text" value="', $instance['title'], '" />
	</p>
	<p>
		<label for="', $this->get_field_id('show_price'), '">', _e('Show the Litecoin price?'), '</label>
		<input type="checkbox" class="checkbox" ', checked( $instance['show_price'], 'on' ), ' id="', $this->get_field_id('show_price'), '" name="', $this->get_field_name('show_price'), '" />
	</p>
	<p>
		<label for="', $this->get_field_id('donate_litecoins'), '">', _e('Add a Litecoin donation address?'), '</label>
		<input type="checkbox" class="checkbox" ', checked( $instance['donate_litecoins'], 'on' ), ' id="', $this->get_field_id('donate_litecoins'), '" name="', $this->get_field_name('donate_litecoins'), '" />
	</p>
	<p>
		<label for="', $this->get_field_id('donation_address'), '">', _e('Donation Address:'), '</label>
		<input class="widefat" id="', $this->get_field_id('donation_address'), '" name="', $this->get_field_name('donation_address'), '" type="text" value="', $instance['donation_address'], '" />
	</p>';
	}
}

// End class litecoin_widget
add_action('widgets_init', create_function('', 'return register_widget("litecoin_widget");'));
?>