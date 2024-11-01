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

function pbci_sort_redemption_options( $a ) {
	uasort($a, 'pbci_compare_redemption_options');
	return $a;
}

// Comparison function
function pbci_compare_redemption_options($a, $b) {
	if ($a['type'] == $b['type']) {
		if ($a['points'] == $b['points']) {
			return 0;
		} else {
			return ($a['points']  < $b['points'] ) ? -1 : 1;
		}
	}
	return ($a['type'] < $b['type']) ? -1 : 1;
}

function pbci_shopper_rewards_delete_options() {
	$result = delete_option( 'pbci_shopper_rewards_name' );
	$result = delete_option( 'pbci_shopper_rewards_more_points_needed_format' );
	$result = delete_option( 'pbci_shopper_rewards_days_to_use_coupon' );
	$result = delete_option( 'pbci_shopper_rewards_redemption_labels' );
	$result = delete_option( 'pbci_shopper_rewards_points_per_currency_unit' );
	$result = delete_option( 'pbci_shopper_rewards_terms_format' );
}

function pbci_shopper_rewards_form_heading_format() {
	return '%pointsname% Redemption Options';
}

function pbci_shopper_rewards_coupon_heading_format() {
	return '%pointsname% Coupon';
}

function pbci_shopper_rewards_redemption_types() {
	$redemption_types = get_option( 'pbci_shopper_rewards_redemption_types' ,
			array( 'percent' => __( 'percent', PBCIBP ), 'fixed_amount' => __( 'fixed amount off', PBCIBP ), 'free_product' => __( 'free product', PBCIBP ) ) );
	return $redemption_types;
}

function pbci_shopper_rewards_name() {
	$points_name = get_option( 'pbci_shopper_rewards_name', __( 'Rewards', PBCIBP ) );
	return $points_name;
}

function pbci_shopper_rewards_per_currency_unit() {
	$points_per_currency_unit = get_option( 'pbci_shopper_rewards_per_currency_unit', 1 );
	return (int)$points_per_currency_unit;
}

function pbci_shopper_rewards_more_points_needed_format() {
	$more_points_needed_format = get_option( 'pbci_shopper_rewards_more_points_needed_format', __( '(%points% more %pointsname% to redeem this option)', PBCIBP ) );
	return $more_points_needed_format;
}

function pbci_shopper_rewards_days_to_use_coupon() {
	$days_to_use_coupon = get_option( 'pbci_shopper_rewards_days_to_use_coupon', 0 );
	return (int)$days_to_use_coupon;
}

function pbci_shopper_rewards_redemption_options() {
	$redemption_options = get_option( 'pbci_shopper_rewards_redemption_options', array() );
	$redemption_options = pbci_sort_redemption_options( $redemption_options );
	return $redemption_options;
}

function pbci_shopper_rewards_redemption_section_labels() {

	$redemption_labels = get_option( 'pbci_shopper_rewards_redemption_labels' ,
			array(
					'percent'      => __( '%pointsname% can be redeemed for a percent discount off of a future purchase', PBCIBP ),
					'fixed_amount' => __( '%pointsname% can be redeemed for a fixed amount off of a future purchase', PBCIBP ),
					'free_product' => __( '%pointsname% can be redeemed for a free product with a future purchase', PBCIBP ),
			)
	);

	return $redemption_labels;
}

function pbci_shopper_show_powered_by() {
	return get_option( 'pbci_shopper_rewards_powered_by',true );
}

function pbci_shopper_rewards_redemption_terms () {
	$terms_format = get_option( 'pbci_shopper_rewards_terms_format', __( 'Redeem your %pointsname% at %siteurl%', PBCIBP ) );
	return $terms_format;
}

function pbci_shopper_rewards_redemption_choice_labels() {

	$redemption_labels = get_option( 'pbci_shopper_rewards_redemption_labels' ,
			array(
					'percent'      => __( 'Redeem %points% %pointsname% for %discount%% off any one item', PBCIBP ),
					'fixed_amount' => __( 'Redeem %points% %pointsname% for %currency% off your next purchase', PBCIBP ),
					'free_product' => __( 'Redeem %points% %pointsname% for a free <b>"%productname%"</b> with your next purchase', PBCIBP ),
			)
	);

	return $redemption_labels;
}

function pbci_shopper_rewards_get_replacements_desc() {

	$replacements = array (
			'%pointsname%'    => __( 'will be replaces by the name of your points program', PBCIBP ),
			'%points%'        => __( 'will be replaced by the number of points', PBCIBP ),
			'%productname%'   => __( 'will be replaced by the name of the product', PBCIBP ),
			'%discount%'      => __( 'will be replaced by the discount percentage', PBCIBP ),
			'%currency%'      => __( 'will be replaced by the discount displayed in your local currency format', PBCIBP ),
			'%siteurl%'       => __( 'will be replaced by your web site url', PBCIBP ),
			'%redeemurl%'     => __( 'will be replaced by your redemption url', PBCIBP ),
			'%shopperpoints%' => __( 'will be replaced by your the current shoppers points', PBCIBP ),

	);

	return $info;
}

function pbci_shopper_rewards_get_replacements(  $points = '', $discount = '', $productid = 0 ) {

	if ( $productid != 0 ) {
		$productname = get_the_title( $productid );
	} else {
		$productname = '';
	}

	if ( !empty( $productid ) ) {
		$productname = get_the_title( $productid );
	} else {
		$productname = '';
	}

	$currency = wpsc_currency_display( $discount );

	$replacements = array (
			'%pointsname%'    => pbci_shopper_rewards_name(),
			'%points%'        => $points,
			'%productname%'   => $productname,
			'%discount%'      => $discount,
			'%currency%'      => $currency,
			'%siteurl%'       => '<a href="'.site_url().'">'.site_url().'</a>',
			'%redeemurl%'     => pbci_shopper_points_redeem_url(),
			'%shopperpoints%' => pbci_shopper_rewards_current_points(),

	);

	return $replacements;

}



