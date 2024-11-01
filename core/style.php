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

if ( !is_admin() ) {
	function pbci_shopper_rewards_style_sheet() {
		// figure out the css file name
		$css_file_name = 'css/style.css';
		$css_file_path =   pbci_shopper_rewards_plugin_dir() . $css_file_name;

		// if the file name exists then queue it up
		if ( file_exists( $css_file_path ) ) {
			$filetimestamp = filemtime( $css_file_path );
			$version = date("Y.m.d.G.i.s");
			$script_handle = 'shopper_rewards-style';
			$css_file_url = pbci_shopper_rewards_plugin_url() . $css_file_name;
			wp_register_style( $script_handle, $css_file_url, null, $version, false );
			wp_enqueue_style( $script_handle );
		}
	}

	add_action( 'wp_enqueue_scripts', 'pbci_shopper_rewards_style_sheet' );

} else {
	function pbci_shopper_rewards_admin_register_head() {
		$url =  pbci_shopper_rewards_plugin_url() . 'css/style.css';
		echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
	}

	add_action( 'admin_head', 'pbci_shopper_rewards_admin_register_head' );
}