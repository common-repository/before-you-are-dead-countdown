<?php
/**
 * Plugin Name: Before You Are Dead Countdown
 * Description: Creates a widget countdown with days, hours, minutes, seconds and optionally, years. And I wish you all a long life!
 * Version: 1.5.4
 * Author: David THOMAS
 * Author URI: http://www.smol.org/studio-de-creation-sympathique/habitants/anou
 * License: GPL2
 * Text Domain: byad-countdown
 * Domain Path: /languages
 */
/*
  Copyright 2014  David THOMAS  (email: anou@smol.org)
  
  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.
  
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
  
  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Load plugin textdomain.
 *
 * @since 1.0
 */
function byad_countdown_load_textdomain() {
  load_plugin_textdomain('byad-countdown', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );  
}
add_action( 'plugins_loaded', 'byad_countdown_load_textdomain' );

/* 
 * Before You Are Dead Countdown Widget
 */
require_once(sprintf("%s/byad-countdown_widget.php", dirname(__FILE__)));


if(!class_exists('Byad_Countdown')) {
  
  class Byad_Countdown {
    /**
     * Construct the plugin object
     */
    public function __construct() {
      // Initialize Settings
      require_once(sprintf("%s/byad-settings.php", dirname(__FILE__)));
      $Byad_Countdown_Settings = new Byad_Countdown_Settings();
            
      $plugin = plugin_basename(__FILE__);
      add_filter("plugin_action_links_$plugin", array( $this, 'plugin_settings_link' ));
      
			// This adds support for a "byad_countdown" shortcode
      add_shortcode( 'byad_countdown', array( $this, 'byad_countdown_shortcode_fn' ) );
      
    } // END public function __construct

    /**
     * Activate the plugin
     */
    public static function activate() {
        // Do nothing
    } // END public static function activate

    /**
     * Deactivate the plugin
     */     
    public static function deactivate() {
        // Do nothing
    } // END public static function deactivate
		// Add the settings link to the plugins page

		function plugin_settings_link($links) {
			$settings_link = '<a href="options-general.php?page=byad-countdown">' . __('Settings', 'byad-countdown') . '</a>';
			array_unshift($links, $settings_link);
			return $links;
		}

		// when the 'byad_countdown' shortcode is found, this function handles it
		function byad_countdown_shortcode_fn($atts, $content = NULL) {
      global $wp_widget_factory;
      
      $widget_class = 'Byad_Countdown_Widget';
      
      extract(shortcode_atts(array(
          'title' => __('The Final Countdown', 'byad-countdown'),
      ), $atts, 'byad_countdown'));
      
      $output = '';
      
      ob_start();
        the_widget($widget_class, array( 'title' => $title ));
        $output = ob_get_contents();
      ob_end_clean();

      return $output;
    }
  } // END class Byad_Countdown
} // END if(!class_exists('Byad_Countdown'))


if(class_exists('Byad_Countdown')) {
    // Installation and uninstallation hooks
    register_activation_hook(__FILE__, array('Byad_Countdown', 'activate'));
    register_deactivation_hook(__FILE__, array('Byad_Countdown', 'deactivate'));

    // instantiate the plugin class
    $byad_countdown = new Byad_Countdown();
}

