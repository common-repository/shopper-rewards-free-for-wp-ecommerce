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

function pbci_shopper_rewards_settings() {
	?>
	<div class="wrap">
	<?php pbci_shopper_rewards_title_box( 'Redemption Settings and Prompts' ); ?>
	<?php

	pbci_buyer_rewards_show_free_version_upgrade_message ();

	do_action( 'PBCI_BUYER_REWARDS_SETTINGS', $_POST );

	$points_name               = pbci_shopper_rewards_name();
	$points_per_currency_unit  = pbci_shopper_rewards_per_currency_unit();
	$more_points_needed_format = pbci_shopper_rewards_more_points_needed_format();
	$days_to_use_coupon        = pbci_shopper_rewards_days_to_use_coupon();
	$redemption_labels         = pbci_shopper_rewards_redemption_section_labels();
	$redemption_options        = pbci_shopper_rewards_redemption_options();
	$redemption_choices        = pbci_shopper_rewards_redemption_choice_labels();
	$terms        			   = pbci_shopper_rewards_redemption_terms();
	$show_powered_by		   = pbci_shopper_show_powered_by();
	?>
		<br>
			<form id="redeem_points" method="post">
				<table class="points-settings">

					<tr class="points_name">
						<td class="label">
							What do you call your points
						</td>
						<td class="value">
							<input type="text" size="80" value="<?php echo esc_attr($points_name);?>" name="pbci_shopper_rewards_name" <?php echo pbci_buyer_points_form_key();?>>
						</td>
					</tr>

					<tr class="points_per_currency_unit">
						<td class="label">
							Points for each <b><?php echo wpsc_currency_display( 1.00 );?></b> spent
						</td>
						<td class="value">
							<input type="number" min="1" max="1000" size="6" value="<?php echo esc_attr($points_per_currency_unit);?>" name="pbci_shopper_rewards_per_currency_unit" <?php echo pbci_buyer_points_form_key();?>>
						</td>
					</tr>

					<tr class="more_points_needed_format">
						<td class="label">
							<b>More Points Needed</b> message format:
						</td>
						<td class="value">
							<input type="text" size="100" value="<?php echo esc_attr($more_points_needed_format);?>" name="pbci_shopper_rewards_more_points_needed_format" <?php echo pbci_buyer_points_form_key();?>>
						</td>
					</tr>

					<tr class="days_to_use_coupon">
						<td class="label">
							<b>How many days</b> does shopper have<br/> to use the coupon before it expires:
						</td>
						<td class="value">
							<input type="number" min="0" size="5" value="<?php echo esc_attr($days_to_use_coupon);?>" name="pbci_shopper_rewards_days_to_use_coupon" <?php echo pbci_buyer_points_form_key();?>>&nbsp;<i>'0' means forever</i>
						</td>
					</tr>


					<?php foreach ( $redemption_labels as $internal_name => $label ) {?>
						<tr class= "section_labels">
							<td class="label">
								Label for <b><?php echo esc_html( $internal_name );?></b> section
							</td>
							<td class="value">
								<input type="text" size="100" value="<?php echo esc_attr($label);?>" name="pbci_shopper_rewards_redemption_labels[<?php echo esc_attr( $internal_name )?>]" <?php echo pbci_buyer_points_form_key();?>>
							</td>
						</tr>
					<?php }	?>

					<?php foreach ( $redemption_choices as $internal_name => $label ) {?>
						<tr class="choice_labels">
							<td class="label">
								Prompt for <b><?php echo esc_html( $internal_name );?></b> redemption option
							</td>
							<td class="value">
								<input type="text" size="100" value="<?php echo esc_attr($label);?>" name="pbci_shopper_rewards_choice_labels[<?php echo esc_attr( $internal_name )?>]" <?php echo pbci_buyer_points_form_key();?>>
							</td>
						</tr>
					<?php }	?>

					<tr class="terms">
						<td class="label">
							<b>Terms</b><br>(html ok)
						</td>
						<td class="value">
							<textarea cols="100" rows="5" name="pbci_shopper_rewards_terms_format" <?php echo pbci_buyer_points_form_key();?>><?php echo esc_textarea($terms);?></textarea>
						</td>
					</tr>

					<tr class="power_by">
						<td class="label">
							<b>Show Powered by Shopper Rewards FREE plugin</b>
						</td>
						<td class="value">
							<input <?php echo $show_powered_by?'CHECKED':''?> type="checkbox" size="100" value="<?php echo esc_attr($label);?>" name="pbci_shopper_rewards_powered_by" <?php echo pbci_buyer_points_form_key();?>>
						</td>
					</tr>


				</table>

				<p class="submit">
					<input type="submit" name="pbci_action" class="button-primary" value="Restore Defaults" <?php echo pbci_buyer_points_form_key();?>/>
					<input type="submit" name="pbci_action" class="button-primary" value="Save Settings" <?php echo pbci_buyer_points_form_key();?>/>
				</p>

	        </form>
	        <br>
	        <hr>
	        <i>
		        <b>%pointsname%</b>&nbsp;<?php _e( 'will be replaces by the name of your points program', PBCIBP ); ?><br>
				<b>%points%</b>&nbsp;<?php _e( 'will be replaced by the number of points', PBCIBP ); ?><br>
				<b>%productname%</b>&nbsp;<?php _e( 'will be replaced by the name of the product', PBCIBP ); ?><br>
				<b>%discount%</b>&nbsp;<?php _e( 'discount will be replaced by the discount percentage', PBCIBP ); ?><br>
				<b>%currency%</b>&nbsp;<?php _e( 'will be replaced by the discount displayed in your local currency format', PBCIBP ); ?><br>
				<b>%siteurl%</b>&nbsp;<?php _e( 'will be replaced by your web site url', PBCIBP ); ?><br>
	        </i>

		</div>
		<?php
		return true;

}
