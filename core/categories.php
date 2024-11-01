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

function pbci_shopper_rewards_categories() {

	if ( isset( $_POST['pbci_action'] ) &&  ($_POST['pbci_action'] == 'Save Options') ){
		$pbci_shopper_rewards_ignore_categories = isset( $_POST['pbci_shopper_rewards_ignore_categories'] ) ?  $_POST['pbci_shopper_rewards_ignore_categories'] : array();
		$pbci_shopper_rewards_ignore_categories = array_keys( $pbci_shopper_rewards_ignore_categories );
		update_option ( 'pbci_shopper_rewards_ignore_categories' , $pbci_shopper_rewards_ignore_categories );
	} else {
		$pbci_shopper_rewards_ignore_categories = get_option( 'pbci_shopper_rewards_ignore_categories', array() );
	}

	?>
	<div class="wrap">
	<?php pbci_shopper_rewards_title_box( __('Buyer Points for WPEC - Exclude Categories', PBCIBP ) ); ?>

	<?php do_action( 'PBCI_ADMIN_MESSAGES' ); ?>

	<form method="post">
		<h3><?php _e('Points are earned for purchases from all product categories <b>EXCEPT</b> when the purchased products are in any of the categories checked below', PBCIBP );?></h3>
		<?php
		$categories = get_terms( 'wpsc_product_category', array( 'orderby' => 'name' ) );

		foreach ($categories as $category) {
			?>
			<input <?php echo in_array($category->term_id, $pbci_shopper_rewards_ignore_categories)?"checked":"";?> type="checkbox" name="pbci_shopper_rewards_ignore_categories[<?php echo $category->term_id;?>]" value="true" <?php echo pbci_buyer_points_form_key();?>/> <?php echo $category->name;?><br/>
			<?php
		}
		?>
		<p class="submit">
			<input type="submit" name="pbci_action" class="button-primary" value="<?php _e('Save Options');?>" <?php echo pbci_buyer_points_form_key();?>/>
		</p>
	</form>
	<hr>
	<em>If you are a developer you can create your own rules to control when points are earned for purchases.  All you need is a filter that looks like the example below in your functions.php file!.</em><br><br>
	<code>
		/*<br>
		** product_id identifies the product, you can test anything related to your product, including price, size, categories, terms, ...<br>
		** eligible is true if the product was determined to be eligible to earn discounts, override it if you like<br>
		** @return true if you want the product purchase to earn points, false if you don't want the product to earn points<br>
		*/<br>
		function my_shopper_rewards_product_is_eligible( $product_id, $eligible ) {<br>
<br>
			// As an example we are only going to enable points to be earned when the purchase is made on a Tuesday<br>
			$day = date('l');<br>
			if ( $l !== 'Tuesday' )<br>
				$eligible = false;<br>
<br>
			return $eligible;<br>
		}<br>
<br>
		add_filter( 'pbci_shopper_rewards_product_is_eligible', 'my_shopper_rewards_product_is_eligible', 10 , 2 );<br><br>
	</code>
</div>
	<?php
}
