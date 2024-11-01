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
** use this file except in compliance with the Pye Brook License.
**
** For this program, Where terms of the GPLv2 and the Pye Brook
** License conflict, the terms of GPLv2 shall have precedence.
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

function pbci_shopper_rewards_status() {
	?>
	<div class="wrap">
	<?php
	pbci_shopper_rewards_title_box( pbci_shopper_rewards_name() . ' ' . __('Status', PBCIBP ) );

	if ( !empty ( $_POST ) ) {
		do_action( 'PBCI_SHOPPER_REWARDS_STATUS_POST', $_POST );
	}

	$points_name               = pbci_shopper_rewards_name();
	$points_per_currency_unit  = pbci_shopper_rewards_per_currency_unit();
	$more_points_needed_format = pbci_shopper_rewards_more_points_needed_format();
	$days_to_use_coupon        = pbci_shopper_rewards_days_to_use_coupon();
	$redemption_labels         = pbci_shopper_rewards_redemption_section_labels();
	$redemption_options        = pbci_shopper_rewards_redemption_options();
	$redemption_choices        = pbci_shopper_rewards_redemption_choice_labels();
	$terms        			   = pbci_shopper_rewards_redemption_terms();
	?>

	<br>
	<table class="points-status">

		<tr class="shopper_count">
			<td class="label">Shoppers with with points:</td>
			<td class="value"><?php echo pbci_shopper_rewards_shopper_count(); ?>
			</td>
		</tr>

		<?php
		$types = pbci_shopper_rewards_type_count();
		foreach ( $types as $type ) {
			?>
			<tr>
				<td class="label"><?php echo ucfirst( $type->name ); _e(' transcactions');?>:
				</td>
				<td class="value"><?php echo $type->count; ?>
				</td>
			</tr>
			<?php
			}
		?>

		<tr class="points_outstanding points">
			<td class="label">Points outstanding:</td>
			<td class="value"><?php echo pbci_shopper_rewards_points_outstanding(); ?>
			</td>
		</tr>

		<tr class="purchase_points points">
			<td class="label">All time points earned:</td>
			<td class="value"><?php echo pbci_shopper_rewards_purchase_points(); ?>
			</td>
		</tr>

		<tr class="coupon_count">
			<td class="label">Shopper rewards coupons</td>
			<td class="value"><?php echo pbci_shopper_rewards_coupons_existing(); ?>
			</td>
		</tr>

		<tr class="coupon_count">
			<td class="label">Shopper rewards coupons used:</td>
			<td class="value"><?php echo pbci_shopper_rewards_coupons_used(); ?>
			</td>
		</tr>

		<tr class="coupon_count">
			<td class="label">Shopper rewards coupons open:</td>
			<td class="value"><?php echo pbci_shopper_rewards_coupons_open(); ?>
			</td>
		</tr>
	</table>
	</div>

		<?php

		do_action( 'PBCI_SHOPPER_REWARDS_STATUS_AFTER_FORM', $_POST );
		return true;

}



function pbci_shopper_rewards_status_claim_keys() {
	if ( isset( $_POST['basic_key'] ) ) {
		$product_manager = new PBCI_Product_Manager_Client('pbci-shopper-rewards-basic-for-wpec');
		$product_manager->save_key( trim( $_POST['basic_key'] ) );
	}

	if ( isset( $_POST['premium_key'] ) ) {
		$product_manager = new PBCI_Product_Manager_Client('pbci-shopper-rewards-premium-for-wpec');
		$product_manager->save_key( trim( $_POST['premium_key'] ) );
	}

	if ( isset( $_POST['pbci_action_basic_get_key'] ) ) {
		$product_manager = new PBCI_Product_Manager_Client('pbci-shopper-rewards-basic-for-wpec');
		$basic_key = $product_manager->register();

		if (empty( $basic_key ) ) {
			$messages = $product_manager->messages();
			foreach ( $messages as $message ) {
				pbci_buyer_rewards_set_admin_message( $message );
			}
			$product_manager->deactivate();
		}
	}

	if ( isset( $_POST['pbci_action_premium_get_key'] ) ) {
		$product_manager = new PBCI_Product_Manager_Client('pbci-shopper-rewards-premium-for-wpec');
		$premium_key = $product_manager->register();

		if (empty( $premium_key ) ) {
			$messages = $product_manager->messages();
			foreach ( $messages as $message ) {
				pbci_buyer_rewards_set_admin_message( $message );
			}
		}
	}

	if ( isset( $_POST['pbci_action_basic_upgrade_check'] ) ) {
		$product_manager = new PBCI_Product_Manager_Client('pbci-shopper-rewards-basic-for-wpec');
		$product_manager->is_upgrade_available();
		$messages = $product_manager->messages();
		foreach ( $messages as $message ) {
			pbci_buyer_rewards_set_admin_message( $message );
		}

	}

	if ( isset( $_POST['pbci_action_premium_upgrade_check'] ) ) {
		$product_manager = new PBCI_Product_Manager_Client('pbci-shopper-rewards-premium-for-wpec');
		$product_manager->is_upgrade_available();
		$messages = $product_manager->messages();
		foreach ( $messages as $message ) {
			pbci_buyer_rewards_set_admin_message( $message );
		}

	}

	if ( isset( $_POST['pbci_action_basic_get_upgrade'] ) ) {
		$product_manager = new PBCI_Product_Manager_Client('pbci-shopper-rewards-basic-for-wpec');
		$product_manager->get_upgrade();
		$messages = $product_manager->messages();
		foreach ( $messages as $message ) {
			pbci_buyer_rewards_set_admin_message( $message );
		}

	}

	if ( isset( $_POST['pbci_action_premium_get_upgrade'] ) ) {
		$product_manager = new PBCI_Product_Manager_Client('pbci-shopper-rewards-premium-for-wpec');
		$product_manager->get_upgrade();
		$messages = $product_manager->messages();
		foreach ( $messages as $message ) {
			pbci_buyer_rewards_set_admin_message( $message );
		}

	}


}

add_action ( 'admin_notices', 'pbci_shopper_rewards_status_claim_keys' , 1 );





