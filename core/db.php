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

function pbci_shopper_rewards_shopper_count() {
	global $wpdb;

	if ( empty ( $email ) ) {
		$email = pbci_buyer_points_get_current_user_email();
	}

	$sql = 'SELECT COUNT( DISTINCT email) FROM ' . pbci_shopper_rewards_table_name();
	$result = $wpdb->get_var( $sql );
	if ( empty( $result ) ||  ( $result < 0 ))
		$result = 0;

	return $result;
}

function pbci_shopper_rewards_type_count() {
	global $wpdb;

	if ( empty ( $email ) ) {
		$email = pbci_buyer_points_get_current_user_email();
	}

	$sql = 'SELECT type as name, COUNT( ID ) as count FROM ' . pbci_shopper_rewards_table_name() . ' GROUP BY type';
	$result = $wpdb->get_results( $sql );

	return $result;
}

function pbci_shopper_rewards_points_outstanding() {
	global $wpdb;

	if ( empty ( $email ) ) {
		$email = pbci_buyer_points_get_current_user_email();
	}

	$sql = 'SELECT SUM( points ) FROM ' . pbci_shopper_rewards_table_name();
	$result = $wpdb->get_var( $sql );
	if ( empty( $result ) ||  ( $result < 0 ))
		$result = 0;

	return $result;
}

function pbci_shopper_rewards_purchase_points() {
	global $wpdb;

	if ( empty ( $email ) ) {
		$email = pbci_buyer_points_get_current_user_email();
	}

	$sql = 'SELECT SUM( points ) FROM ' . pbci_shopper_rewards_table_name() . ' WHERE type = "purchase"';
	$result = $wpdb->get_var( $sql );
	if ( empty( $result ) ||  ( $result < 0 ))
		$result = 0;

	return $result;
}

function pbci_shopper_rewards_coupons_existing() {
	global $wpdb;

	if ( empty ( $email ) ) {
		$email = pbci_buyer_points_get_current_user_email();
	}

	$sql = 'SELECT count(*) FROM ' . pbci_shopper_rewards_table_name() . ', '. WPSC_TABLE_COUPON_CODES . ' WHERE coupon_code = couponcode';
	$result = $wpdb->get_var( $sql );
	if ( empty( $result ) ||  ( $result < 0 ))
		$result = 0;

	return $result;
}



function pbci_shopper_rewards_coupons_used() {
	global $wpdb;

	if ( empty ( $email ) ) {
		$email = pbci_buyer_points_get_current_user_email();
	}

	$sql = 'SELECT count(*) FROM ' . pbci_shopper_rewards_table_name() . ', '. WPSC_TABLE_COUPON_CODES . ' WHERE `coupon_code` = `couponcode` AND `is-used` = 1';
	$result = $wpdb->get_var( $sql );
	if ( empty( $result ) ||  ( $result < 0 ))
		$result = 0;

	return $result;
}



function pbci_shopper_rewards_coupons_open() {
	global $wpdb;

	if ( empty ( $email ) ) {
		$email = pbci_buyer_points_get_current_user_email();
	}

	$sql = 'SELECT count(*) FROM ' . pbci_shopper_rewards_table_name() . ', '. WPSC_TABLE_COUPON_CODES . ' WHERE `coupon_code` = `couponcode` AND `is-used` = 0' ;
	$result = $wpdb->get_var( $sql );
	if ( empty( $result ) ||  ( $result < 0 ))
		$result = 0;

	return $result;
}







