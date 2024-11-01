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


function pbci_shopper_rewards_redemption_levels(){
	?><div class="wrap"><?php

	pbci_shopper_rewards_title_box( __( 'Shopper Rewards Redemption Levels', PBCIBP ) );

	$points_name               = pbci_shopper_rewards_name();
	$points_per_currency_unit  = pbci_shopper_rewards_per_currency_unit();
	$more_points_needed_format = pbci_shopper_rewards_more_points_needed_format();
	$days_to_use_coupon        = pbci_shopper_rewards_days_to_use_coupon();
	$redemption_labels         = pbci_shopper_rewards_redemption_section_labels();
	$redemption_options        = pbci_shopper_rewards_redemption_options();
	$redemption_choices        = pbci_shopper_rewards_redemption_choice_labels();

	if ( isset( $_POST['pbci_action'] ) &&  ($_POST['pbci_action'] == __('Add Option', PBCIBP ) ) && (isset( $_POST['pbci_shopper_rewards_new_option'] ) ) ) {
		$new                      = $_POST['pbci_shopper_rewards_new_option'];
		$new['points']            = intval($new['points']);
		$new['discount']          = intval($new['discount']);
		$new['productid']         = intval($new['productid']);
		$key                      = md5(serialize($new));
		$redemption_options[$key] = $new;
		update_option( 'pbci_shopper_rewards_redemption_options', $redemption_options );
	}

	if ( isset( $_POST['pbci_action_remove'] ) && is_array( $_POST['pbci_action_remove'] ) ) {
		$removes = $_POST['pbci_action_remove'];
		foreach ( $removes as $remove_index => $remove_operation ) {
			unset( $redemption_options[$remove_index] );
		}
		update_option( 'pbci_shopper_rewards_redemption_options', $redemption_options );
	}

	$redeemable_points = 0 ; // set to 0 so the sample message shows
	?>

	<br>
			<h2>Add Redemption Option</h2>
	<form id="redeem_points" method="post">
			<table class="points-settings">

				<tr class="redemption-points">
					<td class="label">
						Points required
					</td>
					<td class="value">
						<input type="number" min="1" max="100000" size="6" value="<?php echo esc_attr($points_per_currency_unit);?>" name="pbci_shopper_rewards_new_option[points]">
					</td>
				</tr>

				<tr class="redemption-type">
					<td class="label">
						What type of redemption?
					</td>
					<td class="value">
						<select name="pbci_shopper_rewards_new_option[type]">
						<?php $redemption_types = pbci_shopper_rewards_redemption_types(); ?>
						<?php foreach ( $redemption_types as $internal_name => $label ) { ?>
						  <option value="<?php echo esc_attr( $internal_name );?>"><?php echo esc_html( $label );?></option>
						<?php } ?>
						</select>
					</td>
				</tr>

				<tr class="redemption-amount">
					<td class="label">
						<?php _e( 'How many percentage points or currency units is the redemption value &nbsp;<i>(ignored for free product)</i>', PBCIBP ); ?>
					</td>
					<td class="value">
						<input type="number" min="0" size="5" value=""  name="pbci_shopper_rewards_new_option[discount]">
					</td>
				</tr>


				<tr class="redemption-product">
					<td class="label">
						<?php _e( 'Free product<br><i>(ignored for percentage and fixed discount options)</i>', PBCIBP ); ?>
					</td>
					<td class="value">
						<select name="pbci_shopper_rewards_new_option[productid]">
							<option value="0"></option>

							<?php $products = pbci_shopper_rewards_product_list(); ?>

							<?php foreach ( $products as $product ) { ?>
							  <option value="<?php echo esc_attr( $product['ID'] );?>"><?php echo esc_html( $product['post_title']  );?></option>
							<?php } ?>

						</select>
					</td>
				</tr>

				<tr class="redemption-description">
					<td class="label">
						<?php _e( 'Description for this option, leave blank to have a prompt created automatically', PBCIBP ); ?>
					</td>
					<td class="value">
						<input type="text" size="80" value="" name="pbci_shopper_rewards_new_option[description]">
					</td>
				</tr>

			</table>

			<p class="submit">
				<input type="submit" name="pbci_action" class="button-primary" value="Add Option" />
			</p>

			<hr>
			<?php
			// Percentage redemption options
			$discount_types = array( 'percent'=>0, 'fixed_amount' => 0, 'free_product' => 0 );
			// count each type of redemption option so we can display headings

			$redemption_options = pbci_sort_redemption_options( $redemption_options );

			foreach ( $redemption_options as $id => $level ) {
				if( empty( $discount_types[$level['type']] ) ) {
					$discount_types[$level['type']] = 1;
				} else {
					$discount_types[$level['type']]++;
				}
			}
			?>
			<h2 style="text-align:center">Preview of Your Redemption Form</h2>
			<hr>
		<?php
		if ( empty ( $redemption_options ) ) {
			?>
			<div class= "redemption-error">
				<?php _e('Currently there are not any redemption options available.  Check back soon!', PBCIBP );?>
			</div>
			<?php
			return;
		}
		?>
			<table>
			<tr>
				<td><h2><?php echo esc_html( pbci_shopper_rewards_form_heading() );?></h2></td>
				<td></td>
			</tr>

			<?php
			foreach ( $discount_types as $discount_type => $discount_type_count) {
				if ( $discount_type_count > 0 ) {
					?>
					<tr>
						<td>
							<h3><?php
							$heading = pbci_shopper_rewards_section_label( $discount_type );
							echo esc_html( $heading );
							?></h3>
						</td>
						<td></td>
					</tr>
						<?php
						foreach ( $redemption_options as $id => $level ) {

							if ( $level['type'] == $discount_type ) {
								if ( !empty( $level['description'] ) ) {
									$description = $level['description'];
								} else {
									$description = pbci_shopper_rewards_discount_label($discount_type,$level);
									if ( empty ( $description ) )
										continue;
								}
								?>
								<tr>
									<?php
									$disabled = ( (int)$level['points'] > (int)$redeemable_points ) ? 'disabled':'';
									?>
									<td>
										<input <?php echo $disabled;?> type="radio" name="redemption" value="<?php echo esc_attr( $id );?>"> <?php echo $description;?>
										<?php
										if ( (int)$level['points'] > (int)$redeemable_points ) {?>
											&nbsp;&nbsp;<i><?php echo pbci_shopper_rewards_more_points_prompt( (int)$level['points'] - (int)$redeemable_points );?></i>
										<?php
										}
										?>
									</td>
									<td>
										<input type="submit" name="pbci_action_remove[<?php echo $id;?>]" class="button-primary" value="<?php _e( 'Remove Option', PBCIBP ); ?>" />
									</td>

								</tr>
								<?php
							}
						}
					}
				}
			?>
			</table>
			<?php
			?>

        </form>

	</div><!-- end wrap -->

	<?php
	return true;
}
