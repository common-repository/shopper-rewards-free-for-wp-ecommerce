<?php
/*
** PBCI Shopper Rewards FREE Plugin for Wordpress and WP-eCommercve
** Copyright (C) 2013  Pye Brook Company, Inc.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
** GNU General Public License for more details.
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
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor,
** Boston, MA  02110-1301, USA.
**
*/



function msg_text_free_thank_you_with_upgrade_to_premium() {
	return _( '
Thank you for using the FREE version of our <b>Buyer Rewards for WP-eCommerce FREE</b> plugin.
We have put a lot of work into the plugin and hope it helps you increase both the visits to your store and your sales.
<br>
<br>
To cost to license the <b style="color:green;">Buyer Rewards for WP-eCommerce PREMIUM</b> plugin is a very modest <b style="color:green;">$20.00</b>.
<div style="clear:both; color:green; margin-top:0.7em; margin-bottom:0.7em; margin-left:30px; margin-right:30px; font-style:italic;">
Buyer Rewards for WP-eCommerce should help you increase your sales. At this modest price, just one additional can cover the cost for the license fee.
This small amount of money gives us the ability to keep it up to date as Wordpress and WP-eCommerce add new features.
</div>
After you register, the license we provide will give you all of the features of the PREMIUM version.  This includes the ability give points to your visitors when they share your products and pages on their Facebook and Twitter accounts.
', PBCIBP);
}


function msg_text_free_thank_you_with_upgrade_to_basic() {
return _( '
Thank you for using the FREE version of our <b>Buyer Rewards for WP-eCommerce Free</b> plugin.
We have put a lot of work into the plugin and hope it helps you increase both the visits to your store and your sales.
<br>
<br>
To cost to license the <b style="color:green;">Buyer Rewards for WP-eCommerce Basic</b> plugin is a very modest <b style="color:green;">$20.00</b>.
<div style="clear:both; color:green; margin-top:0.7em; margin-bottom:0.7em; margin-left:30px; margin-right:30px; font-style:italic;">
Buyer Rewards for WP-eCommerce should help you increase your sales. At this modest price, just one additional can cover the cost for the license fee.
This small amount of money gives us the ability to keep it up to date as Wordpress and WP-eCommerce add new features.
</div>
After you register, the license we provide will give you all of the features of the BASIC version.  This includes the ability to change
all of the options on the settings tab and excluding product categories from earning points.
', PBCIBP);
}

function msg_text_basic_thank_you() {
return __( '
Thank you for using the BASIC version of our <b>Buyer Rewards for WP-eCommerce Free</b> plugin.
We have put a lot of work into the plugin and hope it helps you increase both the visits to your store and your sales.
<br>
', PBCIBP);
}


function msg_text_basic_enter_license_key() {
return sprintf( __( 'Be sure to enter your license key(s) on the <a href="%s">Welcome</a> page', PBCIBP ) , menu_page_url( 'pbci_shopper_rewards_status' , false) );
}

