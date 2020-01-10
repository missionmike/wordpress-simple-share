<?php
/*
Plugin Name: DT's Simple Share
Plugin URI: https://dtweb.design/simple-share/
Description: Simple social media and email sharebar. Specify platforms and location, or use shortcode [dts_sharebar] wherever you want them to show up!
Version: 0.5.2
Author: Michael R. Dinerstein
Author URI: https://www.linkedin.com/in/michaeldinerstein/
License: GPL2
*/
   
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

include( 'includes/setup.php' );	// Standard init and setup processing

include( 'includes/data.php' );		// All share URL data and URL formatting

include( 'includes/enqueue.php' );	// Front-end and back-end styles + scripts

include( 'includes/utility.php' ); 	// Utility and helper functions

include( 'includes/ajax.php' );		// AJAX functions

include( 'includes/output.php' );	// HTML output and shortcode processing
