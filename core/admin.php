<?php

/*
** PBCI Shopper Rewards FREE Plugin for Wordpress and WP-eCommercve
** Copyright (C) 2013  Pye Brook Company, Inc.
**
** This program is free software; you can redistribute it and/or
** modify it under the terms of the GNU General Public License
** as published by the Free Software Foundation; either version 2
** of the License ("GPLv2"), or (at your option) any later version.
**
** This program is also licensed under the Pye Brook Company, Inc.
** License, Version 1.0 (the "Pye Brook License"); And you may not
** use this file except in compliance with the License.
**
** For this program, Where terms of the GPLv2 and the Pye Brook
** License conflict, the terms of GPLv2 shall have precedence.
**
** You may obtain a copy of the Pye Brook License at
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
** GNU General Public License for more details.
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


function pbci_shopper_rewards_init() {
	$pluginfolder = get_bloginfo( 'url' ) . '/' . PLUGINDIR . '/' . dirname( pbci_shopper_rewards_plugin_dir() );

	$parent_slug = 'pbci_shopper_rewards_admin';
	$icon_url = pbci_shopper_rewards_plugin_url() . 'images/pye-brook-shopper-rewards-logo-16.png';
	$menupage = add_menu_page( __('Rewards', PBCIBP ), __('Rewards', PBCIBP ), 'manage_options',  $parent_slug, 'pbci_shopper_rewards_status',  $icon_url , 15.20 );

	if ( function_exists( 'points_details_list_page' ) )
		$menupage = add_submenu_page( $parent_slug,	__('Points Details', PBCIBP ), 	   __('Points Details', PBCIBP ), 	  'manage_options', 'points_details_list_page', 			'points_details_list_page' );

	if ( function_exists( 'pbci_shopper_rewards_redemption_levels' ) )
		$menupage = add_submenu_page( $parent_slug,	__('Redemption Levels', PBCIBP ),  __('Redemption Levels', PBCIBP ),  'manage_options', 'pbci_shopper_rewards_redemption_levels','pbci_shopper_rewards_redemption_levels' );

	if ( function_exists( 'pbci_shopper_rewards_categories' ) )
		$menupage = add_submenu_page( $parent_slug,	__('Exclude Categories', PBCIBP ), __('Exclude Categories', PBCIBP ), 'manage_options', 'pbci_shopper_rewards_categories', 		'pbci_shopper_rewards_categories' );

	if ( function_exists( 'pbci_shopper_rewards_settings' ) )
		$menupage = add_submenu_page( $parent_slug,	__('Settings', PBCIBP ), 		   __('Settings', PBCIBP ),			  'manage_options', 'pbci_shopper_rewards_settings', 		'pbci_shopper_rewards_settings' );

	if ( function_exists( 'pbci_shopper_rewards_import' ) )
		$menupage = add_submenu_page( $parent_slug,	__('Import', PBCIBP ), 			   __('Import', PBCIBP ),			  'manage_options', 'pbci_shopper_rewards_import', 			'pbci_shopper_rewards_import' );

	if ( class_exists( 'PBCI_Product_Manager_Client' ) ) {
		$manager = new PBCI_Product_Manager_Client('pbci-shopper-rewards-basic-for-wpec' , 'http://www.pyebrook.com/' );
		$manager->registration_page( $parent_slug, 'Upgrade to Basic' , 'Install Basic' , 'Update Available', 'Get Support');
	}

	/*
	 * Hide some items so make it easier to get to the menu pages
	*/
	$parent_slug = null;
	if ( function_exists( 'pbci_shopper_rewards_redeem' ) )
		$menupage = add_submenu_page( $parent_slug,	__('Redeem', PBCIBP ), 				__('Redeem', PBCIBP ),				'manage_options', 'pbci_shopper_rewards_redeem', 			'pbci_shopper_rewards_redeem' );

	if ( function_exists( 'pbci_shopper_rewards_get_coupon' ) )
		$menupage = add_submenu_page( $parent_slug,	__('Get Coupon', PBCIBP ), 			__('Get Coupon', PBCIBP ),			'manage_options', 'pbci_shopper_rewards_get_coupon',		'pbci_shopper_rewards_get_coupon' );

	/*
	 * Avoid the duplicate menu
	*/
	global $submenu;
	if ( isset( $submenu['pbci_shopper_rewards_admin'] ) )
		$submenu['pbci_shopper_rewards_admin'][0][0] = __( 'Welcome', PBCIBP );

}

add_action( 'admin_menu', 'pbci_shopper_rewards_init', 11 );

