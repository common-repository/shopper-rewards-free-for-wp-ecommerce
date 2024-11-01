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

/*
 * Do any text substitutions in utility routines
*/
function pbci_shopper_rewards_do_replacements( $target, $points = '', $discount = '', $productid = 0 ) {
	$replacements = pbci_shopper_rewards_get_replacements( $points, $discount, $productid );
	$replaced = str_replace( array_keys( $replacements ), array_values( $replacements ), $target );
	return $replaced;
}

function pbci_shopper_rewards_coupon_heading() {
	$format = pbci_shopper_rewards_coupon_heading_format();
	return pbci_shopper_rewards_do_replacements( $format );
}

function pbci_shopper_rewards_form_heading() {
	$format = pbci_shopper_rewards_form_heading_format();
	return pbci_shopper_rewards_do_replacements( $format );
}

function pbci_shopper_rewards_terms_html() {
	$terms = pbci_shopper_rewards_redemption_terms();
	return pbci_shopper_rewards_do_replacements( $terms );
}

function pbci_shopper_rewards_section_label( $discount_type = '' ) {
	$choices = pbci_shopper_rewards_redemption_section_labels();

	if ( empty( $discount_type ) || empty($choices[$discount_type]) )
		return '';

	return pbci_shopper_rewards_do_replacements( $choices[$discount_type] );
}

function pbci_shopper_rewards_discount_label($discount_type = '', $level = '' ) {
	$choices = pbci_shopper_rewards_redemption_choice_labels();

	if ( !is_array( $level ) || empty( $discount_type ) ) {
		$description = '';
	} elseif ( !empty(  $choices[$discount_type] ) ) {
		$points    = isset( $level['points'] ) ?  $level['points'] : null;
		$discount  = isset( $level['discount'] ) ?  $level['discount'] : null;
		$productid = isset( $level['productid'] ) ?  $level['productid'] : null;

		$description = pbci_shopper_rewards_do_replacements( $choices[$discount_type], $points, $discount, $productid );
	} else {
		$description = '';
	}

	return $description;
}

function pbci_shopper_rewards_more_points_prompt( $points = '' ) {
	if ( empty( $points ) )
		return '';

	$format = pbci_shopper_rewards_more_points_needed_format();
	return pbci_shopper_rewards_do_replacements( $format , $points);

}

function pbci_shopper_rewards_product_list() {
	global $wpdb;
	$products = $wpdb->get_results(	"SELECT ID, post_title FROM $wpdb->posts WHERE post_status = 'publish' AND post_parent=0 AND post_type = 'wpsc-product' ORDER BY post_title ", ARRAY_A );
	return $products;
}

function pbci_shopper_rewards_title_box( $title ) {
	?>
	<div class="buyer-points-titlebox">
		<div class="icon"> </div>
		<div class="text"> <?php echo esc_html ( $title );?></div>
	</div>
	<?php

	do_action( 'PBCI_ADMIN_MESSAGES' );
}

function pbci_buyer_show_notice ( $msg ) {
	if ( is_admin() ) {
	?><div class="updated"><p><?php echo esc_html($msg);?></p></div><?php
	}
}

function pbci_buyer_show_error ( $msg ) {
	if ( is_admin() ) {
	?><div class="error"><p><?php echo esc_html($msg);?></p></div><?php
	}
}

function pbci_buyer_back_link () {
	$url = htmlspecialchars( $_SERVER['HTTP_REFERER'] );
	?><a href='$url'>back</a><?php
}


function pbci_buyer_rewards_show_free_version_upgrade_message () {
	if ( class_exists ( 'pbci-shopper-rewards-basic-for-wpec' ) ) {
		$product_manager = new PBCI_Product_Manager_Client( 'pbci-shopper-rewards-basic-for-wpec' );
		$key_valid = $product_manager->verify_key();
	} else {
		$key_valid = false;
	}

	if ( !$key_valid ) {
		pbci_set_admin_message( msg_text_free_thank_you_with_upgrade_to_basic() );
	} else {
		if ( class_exists ( 'PBCI_Product_Manager_Client' ) ) {
			$product_manager = new PBCI_Product_Manager_Client( 'pbci-shopper-rewards-basic-for-wpec' );
			$key_valid = $product_manager->verify_key();
		} else {
			$key_valid = false;
		}

		if ( !$key_valid ) {
			pbci_set_admin_message( msg_text_free_thank_you_with_upgrade_to_premium() );
		}
	}
}


function pbci_buyer_rewards_show_registration_message () {
	if ( !function_exists( 'pbci_shopper_rewards_basic_version' ) ) {
		pbci_buyer_rewards_set_admin_message( msg_text_free_thank_you_with_upgrade_to_basic() );
	} else {
		pbci_buyer_rewards_set_admin_message( msg_text_basic_thank_you() );
		$need_key = true;
		$need_key = !pbci_check_key( 'BASIC' );
		if ( $need_key ) {
			pbci_buyer_rewards_set_admin_message( msg_text_basic_enter_license_key() );
		}
	}
}

function pbci_shopper_rewards_string_ends_with($haystack, $needle){
	$length = strlen($needle);
	if ($length == 0) {
		return true;
	}

	return (substr($haystack, -$length) === $needle);
}


function pbci_buyer_rewards_set_admin_message($msg) {
	pbci_set_admin_message( $msg );
}




