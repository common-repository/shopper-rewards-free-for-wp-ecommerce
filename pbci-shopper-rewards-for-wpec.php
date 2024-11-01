<?php
/*
Plugin Name: Shopper Rewards FREE for WPEC
Plugin URI: http://www.pyebrook.com/shopper-rewards-plugin-for-wpec/
Description: Buyers can accumulate and redeem reward points earned my making purchases in your WPEC store
Author: Pye Brook Company, Inc / Jeffrey Schutzman
Version: 2013.07.15.1
Author URI: http://www.pyebrook.com/
License: http://www.pyebrook.com/
*/

/*
** PBCI Shopper Rewards FREE Plugin for Wordpress and WP-eCommercve
** Copyright (C) 2013  Pye Brook Company, Inc.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
** GNU General Public License for more details.
**
** This program is free software; you can redistribute it and/or
** modify it under the terms of the GNU General Public License
** as published by the Free Software Foundation; either version 2
** of the License ("GPLv2"), or (at your option) any later version.
**
** This program is also licensed under the Pye Brook Company, Inc.
** License, Version 1.0 (the "Pye Brook License"); And you may not
** use this file except in compliance with the Pye Brook License.
**
** For this program, Where terms of the GPLv2 and the Pye Brook
** License conflict, the terms of GPLv2 shall have precedence.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor,
** Boston, MA  02110-1301, USA.
**
*/


/*
** Copyright 2013, Pye Brook Company, Inc.
**
** Licensed under the Pye Brook Company, Inc. License,
** Version 1.0 (the "Pye Brook License"); you may not
** use this file except in compliance with the License.
** You may obtain a copy of the Pye Brook License at
**
**     http://www.pyebrook.com/
**
** This software is not free may not be distributed, and
** should not be shared.  It is governed by the Pye Brook
** License included in its original distribution
** (license.pdf and/or license.txt) and by the license found
** at www.pyebrook.com.
**
** This software is copyrighted and the property of Pye Brook
** Company, Inc.
**
** See the License for the specific language governing permissions and
** limitations under the License.
**
** Contact Pye Brook Company, Inc. at info@pyebrook.com for more information.
*/

if ( !defined( 'PBCIBP' ) )
	define( 'PBCIBP', 'pbcibp' );

/*
 * include the core files
 */
require_once ( dirname( __FILE__ ) . '/core/wpsc-hooks-and-filters.php' );
require_once ( dirname( __FILE__ ) . '/core/style.php'                  );
require_once ( dirname( __FILE__ ) . '/core/util.php'                   );
require_once ( dirname( __FILE__ ) . '/core/db.php'                     );
require_once ( dirname( __FILE__ ) . '/core/transactions.php'           );
require_once ( dirname( __FILE__ ) . '/core/redeem.php'                 );
require_once ( dirname( __FILE__ ) . '/core/options.php'                );
require_once ( dirname( __FILE__ ) . '/core/userprofile.php'            );
require_once ( dirname( __FILE__ ) . '/core/messages.php'            );

if ( is_admin() ) {
	require_once ( dirname( __FILE__ ) . '/core/admin.php' );
	require_once ( dirname( __FILE__ ) . '/core/list-totals.php'   );
	require_once ( dirname( __FILE__ ) . '/core/list-details.php'  );
	require_once ( dirname( __FILE__ ) . '/core/levels.php'        );
	require_once ( dirname( __FILE__ ) . '/core/settings.php'      );
	require_once ( dirname( __FILE__ ) . '/core/import.php'        );
	require_once ( dirname( __FILE__ ) . '/core/status.php'        );
	require_once ( dirname( __FILE__ ) . '/core/categories.php'    );
	require_once ( dirname( __FILE__ ) . '/core/loginbyemail.php'  );
	require_once ( dirname( __FILE__ ) . '/core/loginbyemail.php'  );
}

/*
 * Localization setup
 */
function pbci_optional_file_include( $filename ) {
	if ( file_exists( $filename ) )
		include $filename;
}

register_activation_hook( __FILE__, 'pbci_shopper_rewards_db_init' );

function pbci_shopper_rewards_load_textdomain() {
	// Retrieve the directory for the localization files
	$lang_dir = get_bloginfo( 'url' ) . '/' . PLUGINDIR . '/' . dirname( plugin_basename( __FILE__ ) ) .  '/lang' ;

	// Set the theme's text domain using the unique identifier from above
	load_theme_textdomain( PBCIBP, $lang_dir);

} // end custom_theme_setup

add_action('after_setup_theme', 'pbci_shopper_rewards_load_textdomain');

/*
 * Utility routines for this plugin
 */
function pbci_shopper_rewards_plugin_dir() {
	$d = dirname( __FILE__ );
	if ( !pbci_shopper_rewards_string_ends_with( $d, '/' ) ) {
		$d .= '/';
	}
	return $d;
}

function pbci_shopper_rewards_plugin_url() {
	return plugin_dir_url( __FILE__ );
}


if ( file_exists( $path = dirname( __FILE__ ) .'/pbci-product-manager-client.class.php' ) ) {
	include (  $path );
}



