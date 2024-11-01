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


function pbci_shopper_rewards_add_replacement_args ( $args, $purchase_log_notification ) {
	$replacements = pbci_shopper_rewards_get_replacements();
	$new = array();

	foreach ( $replacements as $key => $value ) {
		$key = str_replace( '%', '', $key );
		$new[$key] = $value;
	}

	$args = array_merge( $args, $new );
	return $args;
}

add_filter ( 'wpsc_purchase_log_notification_html_args', 'pbci_shopper_rewards_add_replacement_args', 10, 2 );



function pbci_shopper_rewards_purchase_complete( $id, $current_status, $previous_status, $purchase_log  ) {
	pbci_shopper_rewards_add_purchase( $purchase_log->get( 'id' ) );
}

add_action ( 'wpsc_update_purchase_log_status' , 'pbci_shopper_rewards_purchase_complete' , 10 , 4);
