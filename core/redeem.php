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


function pbci_shopper_rewards_redeem( $email = null, $target = null ){

	if ( empty ( $email ) ) {
		if ( !(isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'redeem') )
			return false;

		$email = $_REQUEST['email'];
	}

	if ( empty ( $email ) ) {
		$email = pbci_buyer_points_get_current_user_email();
	}

	if ( empty( $target) )
		$target = menu_page_url( 'pbci_shopper_rewards_get_coupon', false);

	$redeemable_points  = pbci_shopper_rewards_from_email( $email );

	$buyer_info = pbci_buyer_information( $email );
	?>
	<div class="wrap">
		<?php pbci_shopper_rewards_title_box( __( 'Redeem ', PBCIBP ).pbci_shopper_rewards_name() ); ?>

		<form id="redeem-points" method="post" action="<?php echo $target;?>">
			<?php wp_nonce_field('pbci_shopper_rewards_get_coupon' ); ?>

			<table class="points-earner">
				<tr class="email">
					<td class="label">
						<?php _e( 'Shopper eMail', PBCIBP );?>:&nbsp;
					</td>
					<td class="value">
						<?php echo esc_html( pbci_format_email( $email ) );?>
					</td>
				</tr>
				<tr>
					<td class="label">
						<?php _e( 'Available', PBCIBP );?> <?php echo pbci_shopper_rewards_name(); ?>&nbsp;<?php _e( 'Points', PBCIBP );?>:&nbsp;
					</td>
					<td class="value">
						<?php echo esc_html( $redeemable_points );?><br>
					</td>
				</tr>
			</table>
			<!-- points-earner -->
			<hr>
			<?php
			$redemption_options        = pbci_shopper_rewards_redemption_options();
			$redemption_choices        = pbci_shopper_rewards_redemption_choice_labels();

			if ( empty ( $redemption_options ) ) {
				?>
				<div class= "redemption-error">
					<?php _e('Currently there are not any redemption options available.  Check back soon!', PBCIBP );?>
				</div>
				<!-- redemption-error -->
				<?php
			} else {

			// count each type of redemption option so we can display headings
			$discount_types = array( 'percent'=>0, 'fixed_amount' => 0, 'free_product' => 0 );
			foreach ( $redemption_options as $id => $level ) {
				if( empty( $discount_types[$level['type']] ) ) {
					$discount_types[$level['type']] = 1;
				} else {
					$discount_types[$level['type']]++;
				}
			}
			?>
				<div class="redemption-choices">
					<div class="heading">
						<div class="left"><span><?php echo esc_html( pbci_shopper_rewards_form_heading() );?></span></div>
					</div>
					<!-- heading -->
					<?php
					foreach ( $discount_types as $discount_type => $discount_type_count) {
						if ( $discount_type_count > 0 ) {
							?>
							<div class="section">
									<?php
									$heading = pbci_shopper_rewards_section_label( $discount_type );
									echo esc_html( $heading );
									?>
							</div>
							<!-- section -->
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

								$disabled = ( (int)$level['points'] > (int)$redeemable_points ) ? 'disabled':'';
								?>
								<div class="choice <?php echo $disabled;?>">
								<?php
									?>
									<input <?php echo $disabled;?> type="radio" name="redemption" value="<?php echo esc_attr( $id );?>"> <?php echo $description;?>
									<?php
									if ( (int)$level['points'] > (int)$redeemable_points ) {?>
										<div class="note"><?php echo pbci_shopper_rewards_more_points_prompt( (int)$level['points'] - (int)$redeemable_points );?></div>
									<?php
									}
									?>
								</div>
								<!-- choice -->
								<?php
								}
							}
						}
					}
					?>

				<input  type="hidden" name="email" value="<?php echo esc_attr( $email );?>">
				<?php pbci_shopper_rewards_set_nonce($email); ?>

				<p class="submit">
					<input type="submit" name="pbci_action" class="button-primary" value="<?php _e( 'Get Coupon', PBCIBP ); ?>" />
				</p>

				</div>
				<?php }?>
				<!-- redemption-choices -->
			</form><!-- redeem-points -->
	</div>
	<!-- wrap -->
	<?php
	return true;
}


function pbci_shopper_rewards_get_coupon() {
	?>
	<div class="wrap">

		<?php pbci_shopper_rewards_title_box( pbci_shopper_rewards_name() . ' Coupon' ); ?>

		<?php
		$input_arguments_error = false;

		if ( !(isset( $_REQUEST['pbci_action'] ) && $_REQUEST['pbci_action'] == 'Get Coupon') || !isset( $_REQUEST['redemption'] ) ) {
			pbci_buyer_show_error( 'missing action or parameter');
			$input_arguments_error = true;
		}

		$nonce = pbci_shopper_rewards_get_nonce();

		// redemption post arg is a requirement
		if ( !isset( $_REQUEST['redemption'] ) || !isset( $nonce ) ) {
			pbci_buyer_show_error( 'missing or invalid redemption option or parameter');
			$input_arguments_error = true;
		}

		// an email address is a requirement
		if ( !isset( $_REQUEST['email'] ) || empty( $_REQUEST['email'] ) ) {
			pbci_buyer_show_error( 'missing, redemption email address');
			$input_arguments_error = true;
		} else {
			$email = $_REQUEST['email'];
		}

		// if the redemption post arg is something we don't understand don't do anything
		// redemption post arg is a requirement
		$redemption_options = pbci_shopper_rewards_redemption_options();
		if ( !array_key_exists( $_REQUEST['redemption'], $redemption_options ) ) {
			_e( 'You must select an available redemption option to get your coupon!', PBCIBP );
			$input_arguments_error = true;
		}

		// at this point we either have everything we need to start to process the request or we don't
		if ( $input_arguments_error ) {
			pbci_buyer_show_error( 'Unable to process redemption request');
			?>
			<div class= "redemption-error">
				<?php _e( 'The request to exchange your rewards for a coupon did not work.  Please go back to the redemption page and make sure you have selected the option you want.' , PBCIBP );?>
			</div>
			<?php
			pbci_buyer_back_link();
			return false;
		}

		$redemption = pbci_buyer_redemption_from_nonce( $nonce );
		if ( !empty ( $redemption ) ) {
			pbci_buyer_show_error( 'redemption already processed');
			?>
			<h5>
				<?php echo sprintf( __( 'Coupon code <b>%s</b> previously created for <b>%s</b>' , PBCIBP ), esc_html( $redemption->couponcode ), esc_html( $email ) );	?>
			</h5>
			<?php
			pbci_buyer_back_link();
			return false;
		}

		$option             = $redemption_options[$_REQUEST['redemption']];
		$description        = pbci_shopper_rewards_discount_label($option['type'], $option);
		$redeemable_points  = pbci_shopper_rewards_from_email( $email );

		$is_percent = ($option['type'] == 'percent') || ($option['type'] == 'free_product');

		if ( $option['type'] == 'free_product' ) {
			$amount       = 100;
			$product_name = get_the_title( $option['productid'] );
		} else {
			$amount       = $option['discount'];
			$product_name = '';
		}

		$days_valid  = pbci_shopper_rewards_days_to_use_coupon();
		$coupon_code = pbci_make_new_coupon($amount, $is_percent, $days_valid, $product_name);
		$points      = $option['points'];

		pbci_shopper_rewards_record_redempton( $email, $coupon_code, $points, $description, $nonce );

		/*
		 *  Display the coupon code
		*/
		$redemption = pbci_buyer_rdemption_from_nonce( $nonce );
		$days_valid = pbci_shopper_rewards_days_to_use_coupon();
		$terms      = pbci_shopper_rewards_terms_html();
		$coupon     = new wpsc_coupons( $redemption->couponcode );
		?>
		<div class="coupon">
			<h1 style="title"><?php echo esc_html( pbci_shopper_rewards_coupon_heading() );?></h1><br>
			<div class="description"><?php echo $redemption->description;?></div><br>
			<div class="use-coupon-code">use coupon code</div><br>
			<div class="coupon-code"><?php echo esc_html( $redemption->couponcode );?></div><br>
			<div class="use-coupon-code">at checkout</div><br>

			<?php if ( $days_valid > 0 ) { ?>
				<div class="valid-for">Coupon valid through <?php echo esc_html( date('D M j', strtotime( $coupon->end_date ) ) );?></div><br>
			<?php }?>

			<?php if ( !empty ( $terms ) ) { ?>
				<div class="terms"><?php echo $terms;?></div><br>
			<?php } ?>
		</div> <!-- end coupon -->
	</div> <!-- end wrap -->
	<?php

	return true;
}

function pbci_shopper_rewards_history( $email ) {
	global $wpdb;

	$where = ' WHERE email = "'.$email.'"';
	$sql   = "SELECT * FROM `" . pbci_shopper_rewards_table_name() . "` {$where} ORDER BY timestamp DESC  ";
	$rows  = $wpdb->get_results( $sql );

	pbci_shopper_rewards_title_box( pbci_shopper_rewards_name() . ' History' );

	?><table class="points-history">
		<tr>
			<th class="timestamp">Date</th>
			<th class="action">Action</th>
			<th class="points">Points</th>
			<th class="description">Description</th>
		</tr>
		<?php


		foreach ( $rows as $row ) {
			?>
			<tr>
				<td class="timestamp"><?php echo esc_html( date('M j Y', strtotime( $row->timestamp ) ) );?></td>
				<td class="action"><?php echo esc_html( $row->type );?></td>
				<td class="points"><?php echo esc_html( $row->points );?></td>
				<td class="description"><?php echo $row->description;?></td>
			</tr>
			<?php
		}
	?></table><?php

}

