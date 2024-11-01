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

function pbci_buyer_information( $email ) {

	$wp_user = get_user_by( 'email', $email );

	global $wpdb;
	$sql = 'SELECT log_id FROM ' . WPSC_TABLE_SUBMITTED_FORM_DATA .' WHERE value = "' . $email . '" ORDER BY id DESC LIMIT 1';
	$purchase_log_id = $wpdb->get_var( $sql );

	$buyer_info = array();

	if ( !empty ( $purchase_log_id ) ) {
		$sql = 'SELECT unique_name, value FROM ' . WPSC_TABLE_SUBMITTED_FORM_DATA .' d, ' .WPSC_TABLE_CHECKOUT_FORMS . ' f  WHERE log_id = ' . $purchase_log_id . ' AND f.id = d.form_id';
		$buyer_info_rows = $wpdb->get_results( $sql, OBJECT   );

		foreach ( $buyer_info_rows as $row ) {
			$buyer_info[$row->unique_name] = $row->value;
		}
	}

	return $buyer_info;
}

if ( !function_exists( 'pbci_format_email' ) ) {
	function pbci_format_email( $email ) {
		$email = strtolower( $email );
		$email = apply_filters( 'pbci_format_email', $email);
		return $email;
	}
}

if ( !function_exists( 'pbci_format_name' ) ) {
	function pbci_format_name( $name ) {
		$name = strtolower( $name );
		if ( isset( $name[0] ) ) {
			$name = ucfirst ( $name );
		}
		$name = apply_filters( 'pbci_format_name', $name);
		return $name;
	}
}

function pbci_shopper_rewards_db_init() {

	global $wpdb;
	global $charset_collate;

	$sql =
"CREATE TABLE IF NOT EXISTS " . pbci_shopper_rewards_table_name() . "(
ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
wp_user_id bigint(20) NOT NULL DEFAULT '0',
shopper_id bigint(20) NOT NULL DEFAULT '0',
email varchar(255) DEFAULT NULL,
points int(11) NOT NULL DEFAULT '0',
description varchar(255) DEFAULT NULL,
couponcode varchar(255) DEFAULT NULL,
nonce varchar(255) DEFAULT NULL,
purchase_log_id bigint(20) DEFAULT NULL,
timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
type enum('info','add','redeem','deduct','transfer', 'purchase', 'social') NOT NULL,
PRIMARY KEY  (id),
KEY email_key (email(191)),
KEY timestamp_key (timestamp),
KEY couponcode_key (couponcode),
KEY purchase_log_id_key (purchase_log_id),
KEY type_key (type),
KEY wp_user_id_key (wp_user_id),
KEY shopper_id_key (shopper_id),
KEY nonce_key (nonce)
) " . $charset_collate;

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}



/**
 * The name of the meta table for a specific meta object type.
 *
 *
 * @param string $meta_object_type Type of object metadata is for (e.g., variation. cart, etc)
 	* @return string Name of the custom meta table
 */
function pbci_shopper_rewards_table_name() {
	global $wpdb;
	return $wpdb->prefix.'shopper_rewards';
}

/**
 * @param unknown $purchase_log_id
 * @return points accumulated for purchase, NULL if purchase id not found
 */
function pbci_shopper_rewards_from_purchase( $purchase_log_id ) {
	global $wpdb;
	$sql = 'SELECT SUM(points) FROM ' . pbci_shopper_rewards_table_name() .' WHERE purchase_log_id = ' . $purchase_log_id;
	$purchase_points = $wpdb->get_var( $sql );
	return $purchase_points;
}

/**
 * @param unknown $purchase_log_id
 * @return points accumulated for purchase, 0 if purchase id not found
 */
function pbci_shopper_rewards_from_email( $email = null ) {
	global $wpdb;

	if ( empty ( $email ) ) {
		$email = pbci_buyer_points_get_current_user_email();
	}

	$sql = 'SELECT SUM(points) FROM ' . pbci_shopper_rewards_table_name() .' WHERE email = "' . $email . '"';
	$purchase_points = $wpdb->get_var( $sql );
	if ( empty( $purchase_points ) ||  ( $purchase_points < 0 ))
		$purchase_points = 0;

	return $purchase_points;
}

function pbci_shopper_rewards_current_points( ) {
	return pbci_shopper_rewards_from_email( pbci_buyer_points_get_current_user_email() );
}

function pbci_shopper_rewards_row_count() {
	global $wpdb;
	$sql = 'SELECT COUNT(ID) FROM ' . pbci_shopper_rewards_table_name();
	$count = $wpdb->get_var( $sql );
	return $count;
}

function pbci_buyer_points_form_key() {
	if ( class_exists ( 'PBCI_Product_Manager_Client' ) ) {
		$client = new PBCI_Product_Manager_Client( 'pbci-shopper-rewards-basic-for-wpec' );
		if ( $client->verify_key() )
			return '';
	}

	// need to make sure the edit controls are disabled unless the companion plugin
	// is installed, that's where the logic that handles the form and the values it
	// sets lives.
	echo 'DISABLED';
}

function pbci_shopper_rewards_product_is_eligible( $product_id ) {

	// get the categories that don't earn points
	$pbci_shopper_rewards_ignore_categories = get_option( 'pbci_shopper_rewards_ignore_categories', array() );

	$eligible = true;

	if ( has_term( $pbci_shopper_rewards_ignore_categories, 'wpsc_product_category', $product_id ) ) {
		$eligible = false;
	}

	$eligible = apply_filters( 'pbci_shopper_rewards_product_is_eligible', $product_id, $eligible );

	return $eligible;
}

function pbci_shopper_rewards_add_purchase( $purchase_log_id ) {

	$points_per_dollar = get_option( 'pbci_shopper_rewards_per_per_dollar', 1.0 );

	$purchase_log = new WPSC_Purchase_log( $purchase_log_id );

	// only add points for transactions that resulted in purchases
	if ( !wpsc_check_purchase_processed ( $purchase_log->get( 'processed' ) ) )
		return false;

	// check to see if this purchase log has already added points
	if ( pbci_shopper_rewards_from_purchase( $purchase_log_id ) !== NULL)
		return false;

	// loop through all of the products in the cart and if the product is in one of the categories then
	// deduct the amount from the total
	$total_earning_points = 0.0;
	$cart_items = $purchase_log->get_cart_contents();
	foreach ( $cart_items as $cart_item ) {
		if ( !empty($cart_item->prodid) ) {
			$product_id = $cart_item->prodid;
			if ( pbci_shopper_rewards_product_is_eligible( $product_id ) ) {
				$item_amount = ($cart_item->price * $cart_item->quantity);
				$total_earning_points += $item_amount;
			}
		}
	}

	// the total less the shipping and tax is the starting number, it already has discount built in so no
	// need to worry about coupons
	$total_earning_points -= $purchase_log->get( 'discount_value' );

	// only add points when the eligible purchase amount is positive
	if ( $total_earning_points > 0 ) {
		$points      = (int) ($total_earning_points * $points_per_dollar);
		$description = 'Purchase #'.$purchase_log_id.' on ' . format_purchase_date( $purchase_log->get( 'date' ) );
		$email       = wpsc_get_buyers_email( $purchase_log->get( 'id' ) );

		pbci_shopper_rewards_insert ( $email, $points, $description, 'purchase', null, $purchase_log_id );
	}

	return true;
}

function format_purchase_date( $timestamp ) {
	$datestring = date( 'Y/m/d g:i A' , $timestamp );
	return $datestring;
}

function pbci_shopper_rewards_insert( $email , $points = 0, $description = '', $type = 'info', $couponcode = '', $purchase_log_id = '' ) {

	// email is the key value to have
	if ( empty( $email ) )
		return;

	// validate the type of entry
	if ( $type != 'info' && $type != 'add' && $type != 'redeem' && $type != 'deduct'  && $type != 'transfer' && $type != 'social' && $type != 'purchase')
		return false;

	$values = array();

	$formats = array();

	if ( !empty( $email ) ) {
		$values['email'] = $email;
		$formats[] = '%s';
	}

	if ( !empty( $points ) ) {
		$values['points'] = $points;
		$formats[] = '%d';
	}

	if ( !empty( $type ) ) {
		$values['type'] = $type;
		$formats[] = '%s';
	}

	if ( !empty( $description ) ) {
		$values['description'] = $description;
		$formats[] = '%s';
	}

	if ( !empty( $couponcode ) ) {
		$values['couponcode'] = $couponcode;
		$formats[] = '%s';
	}

	if ( !empty( $purchase_log_id ) ) {
		$values['purchase_log_id'] = $purchase_log_id;
		$formats[] = '%d';
	}

	global $wpdb;
	$result = $wpdb->insert( pbci_shopper_rewards_table_name(), $values, $formats );
	return $result;
}

/**
 * The letter l (lowercase L) and the number 1
 * should be removed, as they can be mistaken
 * for each other.
 */
function pbci_get_new_coupon_code() {
	$string = md5(uniqid(mt_rand(), true));
	return strtoupper( substr( $string, 0, 4 ) . '-' . substr( $string, 4, 4 ) );
}

function pbci_make_new_coupon( $discount, $is_percent, $days_valid = 0, $product_name ='' ) {
	global $wpdb;

	$rules = null;

	if ( !empty ( $product_name ) ) {
		$rule = array ( 'property' => 'item_name', 'logic' =>	'equal',  'value' => $product_name, 'operator' => '' );
		$rules = array ( $rule );
	}

	$coupon_code   = pbci_get_new_coupon_code();
	$use_once      = (int)true;
	$every_product = (int)false;
	$is_active     = (int)true;
	$start_date    = date( 'Y-m-d') . " 00:00:00";
	$end_date      = null;

	if ( isset( $days_valid ) && ($days_valid > 0) ) {
		$days_string = '+' . $days_valid . ' days';
		$end_date  = date('Y-m-d', strtotime($days_string) ) . ' 23:59:59';
	}

	$insert = $wpdb->insert(
			WPSC_TABLE_COUPON_CODES,
			array(
					'coupon_code'   => $coupon_code,
					'value'         => $discount,
					'is-percentage' => $is_percent,
					'use-once'      => $use_once,
					'is-used'       => 0,
					'active'        => $is_active,
					'every_product' => $every_product,
					'start'         => $start_date,
					'expiry'        => $end_date,
					'condition'     => serialize( $rules )
			),
			array(
					'%s',
					'%f',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
			)
	);

	return $coupon_code;
}

function pbci_shopper_rewards_record_redempton( $email, $couponcode, $points = 0, $description = '' , $nonce) {
	global $wpdb;

	if ( empty ( $email ) || empty( $couponcode ) ) {
		return false;
	}

	if ( empty ( $description ) ) {
		$description = 'Redeeming '.$points.' points on ' . date( 'Y/m/d g:i A' );
	}

	$insert_id = $wpdb->insert(
			pbci_shopper_rewards_table_name(),
			array(
					'email'       => $email,
					'couponcode'  => $couponcode,
					'description' => $description,
					'points'      => -intval($points),
					'type'        => 'redeem',
					'nonce'       => $nonce,
			),
			array(
					'%s',
					'%s',
					'%s',
					'%d',
					'%s',
					'%s',
			)
	);

	return ($insert_id !== false);
}

/**
 * @param unknown $purchase_log_id
 * @return points accumulated for purchase, NULL if purchase id not found
 */
function pbci_buyer_redemption_from_nonce( $nonce ) {
	global $wpdb;
	$sql = 'SELECT * FROM ' . pbci_shopper_rewards_table_name() .' WHERE nonce = "' . $nonce. '"';
	$row = $wpdb->get_row( $sql );
	return $row;
}

function pbci_buyer_points_get_current_user_email() {

	if ( is_user_logged_in() ) {
		global $current_user;
		$email = $current_user->user_email;
	} else {
		$email = '';
	}

	return $email;
}

function pbci_shopper_rewards_set_nonce( $email = null ) {
	if ( empty ( $email ) ) {
		$email = pbci_buyer_points_get_current_user_email();
	}

	$key  = $email.microtime();
	$hash = md5($key);
	?><input  type="hidden" name="pbcibuyerpointsnonce" value="<?php echo esc_attr( $hash );?>"><?php
	return $hash;
}

function pbci_shopper_rewards_get_nonce() {
	return isset( $_REQUEST['pbcibuyerpointsnonce'] ) ? $_REQUEST['pbcibuyerpointsnonce']  : null;
}
