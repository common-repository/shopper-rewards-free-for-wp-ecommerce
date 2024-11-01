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

function pbci_shopper_rewards_import() {
	?>
	<div class="wrap">
		<?php pbci_shopper_rewards_title_box( __('Create Points from Purchase History', PBCIBP ) ); ?>

		<?php

		if ( isset( $_POST['pbci_action'] ) &&  ($_POST['pbci_action'] == __( 'Import Purchases', PBCIBP ) ) ) {
			pbci_shopper_rewards_import_purchases();
		}

		?>
		<form method="post">
			<hr>
			<h2><?php _e('This is a manual import option used to create points for existing purchases', PBCIBP ); ?></h2>

			<p>
				<?php _e('Normally you would only have to do an import one time.  Use it after you install the plugin to give your past customers points.', PBCIBP ); ?></p>

			<p>
				<?php _e('If you do an import more than once it should not have have any negative affect. During the import process purchase IDs are checked. Purchase IDs that already have points awarded do not receive points a second time.', PBCIBP ); ?>
			</p>

			<p>
				<?php _e('If you have turned off the plugin for a period of time and want to make sure points are awarded for purchases during that interval you can click import again.', PBCIBP ); ?>
			</p>

			<p class="submit">
				<input type="submit" name="pbci_action" class="button-primary" value="<?php _e( 'Import Purchases', PBCIBP ); ?>" />
			</p>
			<hr>
		</form>
		</div>
	<?php
}

function pbci_shopper_rewards_import_purchases() {
	global $wpdb;

	$sql = 'SELECT id FROM ' . WPSC_TABLE_PURCHASE_LOGS ;
	$purchase_log_ids = $wpdb->get_col( $sql , 0 );

	$count = 0;
	foreach ($purchase_log_ids as $purchase_log_id ) {
		$added = pbci_shopper_rewards_add_purchase( $purchase_log_id );
		if ( $added ) {
			$count++;
		}

		$msg = sprintf ( __('%d transactions checked, %d have been imported', PBCIBP ) , count( $purchase_log_ids ), $count );
		pbci_buyer_rewards_set_admin_message( $msg );
	}
}
