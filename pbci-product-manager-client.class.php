<?php

if ( !class_exists( 'PBCI_Product_Manager_Client' ) ) {
	class PBCI_Product_Manager_Client {

		function PBCI_Product_Manager_Client ( $main_file_or_plugin_base_name , $update_uri = '' ) {

			// we typically get the plugin main file name, but we can also handle
			// getting the plugin name
			$main_file = $main_file_or_plugin_base_name;

			if ( !file_exists ( $main_file ) ) {
				$root = $this->plugin_root_directory($main_file);
				$main_file = $root . $main_file . '.php';
			}

			$this->plugin_base_name = basename( dirname( $main_file ) );
			$this->installed        = file_exists( $main_file );
			$this->plugin_main_file = $main_file;
			$this->update_uri        = $update_uri;

			if ( file_exists( $main_file ) ) {
				$data = get_plugin_data( $main_file, false, false );
				$this->parse_plugin_info( $data );
				$this->installed_version( $data['Version']);
			} else {
				$this->get_info(); // get the data from the remote if it isn't local
			}

			$this->messages = array();

		}

		private $plugin_main_file = '';
		private $plugin_base_name = '';
		private $installed        = false;
		private $messages;

		private $installed_version = '0.0';
		private $version           = '';
		private $name              = '';
		private $author            = '';
		private $author_uri        = '';
		private $description       = '';
		private $title             = '';
		private $plugin_uri        = '';
		private $update_uri        = '';

		function base_name( $base_name = null ) {
			if ( isset( $base_name ) ) {
				$this->plugin_base_name = $base_name;
			}

			return $this->plugin_base_name;
		}

		function name( $name = null ) {
			if ( isset( $name ) )
				$this->name = $name;

			return $this->name;
		}

		function title( $title = null ) {
			if ( isset( $title ) )
				$this->title = $title;

			return $this->title;
		}

		function author( $author = null ) {
			if ( isset( $author ) )
				$this->author = $author;

			return $this->author;
		}

		function description( $description = null ) {
			if ( isset( $description ) )
				$this->description = $description;

			return $this->description;
		}

		function author_uri( $author_uri = null ) {
			if ( isset( $author_uri ) )
				$this->author_uri = $author_uri;

			return $this->author_uri;
		}

		function version( $version = null ) {
			if ( isset( $version ) )
				$this->version = $version;

			return $this->version;
		}

		function installed_version( $installed_version = null ) {
			if ( isset( $installed_version ) )
				$this->installed_version = $installed_version;

			return $this->installed_version;
		}


		function plugin_uri( $plugin_uri = null ) {
			if ( isset( $plugin_uri ) )
				$this->plugin_uri = $plugin_uri;

			return $this->plugin_uri;
		}

		function domain() {
			return self::clean_domain( site_url() );
		}

		function parse_plugin_info ( $data ) {
			if ( !is_array( $data ) )
				$data = (array)$data;

			$this->version( 	isset( $data['Version'] ) 		? $data['Version'] 		: '0.0' );
			$this->name( 		isset( $data['Name'] )    		? $data['Name'] 		: '' );
			$this->title( 		isset( $data['Title'] )   		? $data['Title']  		: '' );
			$this->description( isset( $data['Description'] ) 	? $data['Description'] 	: '' );
			$this->author( 		isset( $data['Author'] ) 		? $data['Author']  		: '' );
			$this->author_uri( 	isset( $data['AuthorURI'] ) 	? $data['AuthorURI'] 	: '' );
			$this->plugin_uri( 	isset( $data['PluginURI'] ) 	? $data['PluginURI'] 	: '' );
		}

		function messages() {
			$m = $this->messages;
			$this->nessages = array();
			return $m;
		}

		function save_message( $msg = null ) {
			if ( isset( $msg ) ) {
				$this->messages[] = $msg;
			}
		}

		function deactivate( $product = '' ) {
			if ( empty ( $product ) ) {
				$product = $this->plugin_base_name;
			}

			deactivate_plugins( $this->product_main_file( $product ) );
		}

		function product_is_installed ( $product = '' ) {
			$installed = false;

			if ( empty ( $product ) ) {
				$product = $this->$plugin_base_name;
			}

			$installed = file_exists( $this->product_main_file( $product ) );

			return $installed;
		}

		function product_main_file( $product = '' ) {
			if ( empty ( $product ) ) {
				$product = $this->$plugin_base_name;
			}

			$root = $this->plugin_root_directory($product);
			$main_file = $root . $product . '.php';

			return $main_file;
		}

		/*
		* functions that handle product keys
		*/
		private function key_option_name() {
			return $this->plugin_base_name.'_key';
		}

		function get_key() {
			return get_option( $this->key_option_name() , '' );
		}

		function verify_key( $force = false ) {
			$verified = false;

			$key = $this->get_key();

			if ( !empty( $key ) ) {

				$already_verified = false;//get_transient ( keys_option_name() );

				if ( !($already_verified === true) ) {
					$result = $this->post( 'pbci_verify_key' );
					$verified = isset( $result->key_is_valid ) ? $result->key_is_valid : false;

					if ( $verified ) {
						set_transient ( $this->key_option_name(), true, (60 * 60 * 24) );
					}
				}
			}

			return $verified;
		}

		function save_key( $value = null ) {
			if ( empty( $value ) ) {
				delete_option( $this->key_option_name() );
			} elseif ( !empty( $value ) ) {
				update_option( $this->key_option_name(), $value );
			}

			return $value;
		}

		function clear_product_key() {
			$this->save_key( null);
			$this->post( 'clear_product_key' );
		}

		/*
		 * register a product
		 *
		 */
		function register( $product_name = '', $email = null , $purchase_id = null ) {
			if ( empty ( $product_name ) ) {
				$product_name = $this->plugin_base_name;
			}

			$result = $this->post( 'pbci_register', $product_name, null, array( 'email'=>$email,'purchase_id'=>$purchase_id) );
			$this->save_key( $result->product_key );

			if ( !empty( $result->product_key ) ) {
				$this->deactivate($product_name);
			}

			return $result->product_key;
		}

		/*
		* communicate with the registration server
		*/
		private function post_url() {
			return $this->update_uri . 'wp-admin/admin-ajax.php';
		}

		function is_upgrade_available() {
			$result =  $this->post( 'pbci_is_upgrade_available' );
			return $result->upgrade_available;
		}

		function get_upgrade() {
			$this->post( 'pbci_get_upgrade' , null, array( &$this, 'unpack_upgrade_files' ) );
		}

		function get_info ( $product_name = null ) {
			if ( empty ( $product_name ) ) {
				$product_name = $this->plugin_base_name;
			}

			$data = $this->post( 'pbci_get_info', $product_name );
			$this->parse_plugin_info( $data );
			return $data;
		}

		private function unpack_upgrade_files( $result ) {

			if ( !isset( $result->upgrade_files ) )
				return;

			$message = __('processing upgrade files for ', PBCILM) . $this->plugin_base_name;
			$upgrade_files = $result->upgrade_files;

			if ( !empty ( $upgrade_files ) ) {
				$new_tag = md5( serialize ( $upgrade_files ) );

				$current_dir = $this->plugin_root_directory( $result->plugin_base_name );
				if ( !file_exists ( $current_dir ) ) {
					mkdir( $current_dir );
					$message .= __( 'created directory ', PBCILM ) .  $current_dir . '<br>';
					$archive_dir = null;
				} else {

					$archive_dir = $current_dir . '/archive/';
					if ( !file_exists ( $archive_dir ) ) {
						mkdir( $archive_dir );
						$message .= __( 'created archive directory ', PBCILM ) .  $archive_dir . '<br>';
					}

					$archive_dir .= ($this->version . '/');
					if ( !file_exists ( $archive_dir ) ) {
						mkdir( $archive_dir );
						$message .= __( 'created archive directory ', PBCILM ) .  $archive_dir . '<br>';
					}

					$archive_dir .=  ($new_tag . '/');
					if ( !file_exists ( $archive_dir ) ) {
						mkdir( $archive_dir );
						$message .= __( 'created archive directory ', PBCILM ) .  $archive_dir . '<br>';
					}
				}

				foreach ( $upgrade_files as $upgrade_file ) {
					if ( $upgrade_file->type == 'dir' ) {

						if ( $archive_dir != null ) {
							$the_archive_path = $archive_dir . $upgrade_file->name;
							if ( !file_exists ( $the_archive_path ) ) {
								mkdir( $the_archive_path );
								$message .= __( 'created archive directory ', PBCILM ) .  $the_archive_path . '<br>';
							}
						}

						$the_path = $current_dir.$upgrade_file->name;
						if ( !file_exists( $the_path ) ) {
							mkdir( $the_path );
							$message .= __( 'created archive directory ', PBCILM ) .  $the_path . '<br>';
						}
					}
				}

				foreach ( $upgrade_files as $upgrade_file ) {
					if ( $upgrade_file->type == 'file' ) {
						$the_path = $current_dir.$upgrade_file->name;
						if ( ( $archive_dir != null ) && file_exists( $the_path ) ) {
							$the_archive_path = $archive_dir . $upgrade_file->name;
							rename ( $the_path, $the_archive_path );
							$message .= __( 'moved plugin file  to archive ', PBCILM ) .  $the_path . ' => ' . $the_archive_path . '<br>';
						}

						$data = base64_decode ( $upgrade_file->data );
						file_put_contents($the_path, $data );
						$message .= __( 'created product file ', PBCILM ) .  $the_path . '<br>';
					}
				}

				$message = __('Completed installing files for <b>', PBCILM) . $this->name() . '</b><br>';
				$this->save_message( $message );
				$this->installed = file_exists( $this->plugin_main_file );

				if ( $this->installed ) {
					$data = get_plugin_data( $this->plugin_main_file, false, false );
					$this->parse_plugin_info( $data );
					$this->installed_version( $data['Version']);
				} else {
					$this->get_info(); // get the data from the remote if it isn't local
				}
			}
		}

		/*
		 * communicate with the registration server
	     */
		function post( $action, $product = '', $callback = null, $extra_args = null ) {

			$args = $this->get_params($action, $product);

			if ( !empty( $extra_args ) && is_array( $extra_args ) )  {
				$args = array_merge($args,$extra_args);
			}

			$response = wp_remote_post(
					$this->post_url(),
					array(
							'method'      => 'POST',
							'timeout'     => 5,
							'redirection' => 2,
							'httpversion' => '1.0',
							'blocking'    => true,
							'headers'     => array(),
							'body'        => $args,
							'cookies'     => array()
					)
			);

			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				error_log( 'Something went wrong: '.$error_message );
				error_log( 'url: '.$this->post_url() );
				error_log( print_r($response,true) );
				$result = null;
			} else {
				$result = json_decode( $response['body'] );
				if ( !empty ( $result->message ) ) {
					$this->save_message( $result->message );
				}

				if ( $callback != null ) {
					call_user_func ( $callback, $result );
				}
			}

			return $result;
		}

		function product_info_table() {

			?>
			<table class="product_info">
				<tr class="name">
					<td class="label">Name:</td>
					<td class="value"><?php echo esc_html( $this->name() );?></td>
				</tr>

				<tr class="title">
					<td class="label">Title:</td>
					<td class="value"><?php echo esc_html( $this->title() );?></td>
				</tr>

				<tr class="description">
					<td class="label">Description:</td>
					<td class="value"><?php echo esc_html( $this->description() );?></td>
				</tr>

				<tr class="author">
					<td class="label">Author:</td>
					<td class="value"><?php echo esc_html($this->author() );?></td>
				</tr>

				<tr class="authoruri">
					<td class="label">Author URI:</td>
					<td class="value"><?php echo esc_html($this->author_uri() );?></td>
				</tr>

				<tr class="version">
					<td class="label">Version:</td>
					<td class="value"><?php echo esc_html($this->version() );?></td>
				</tr>

				<tr class="pluginuri">
					<td class="label">Plugin URI:</td>
					<td class="value"><?php echo esc_html($this->plugin_uri() );?></td>
				</tr>

				<tr class="domain">
					<td class="label">Registered to Domain:</td>
					<td class="value"><?php echo esc_html($this->domain() );?></td>
				</tr>

				<tr class="product_key">
					<td class="label">Product Key:</td>
					<td class="value"><?php echo esc_html( $this->get_key() );?>
					<input type="submit" name="<?php echo esc_attr( $this->get_arg_name() );?>[license][action]" class="button-primary" value="Clear Key" />
					</td>
				</tr>

			</table>
			<?php
		}

		function registration_form( $product_name = '' ) {
			if ( empty ( $product_name ) ) {
				$product_name = $this->plugin_base_name;
			}

			$email ="";
			$purchase_id = "";
			?>
			<h2>Register <?php echo esc_html( $this->name() );?> </h2>

			<?php if ( isset( $info->Description ) && !empty( $info->Description ) )?>
				<p><?php echo esc_html( $this->description() );?> </p>
				<br>

		    <hr>
			<div>
				<h2><?php _e('License Manager', 'PBCILM' )?></h2>
				<form id="register_plugin" method="post">
					<table class="registration">
						<tr class="email">
							<td class="label">
								Enter the email address you purchased with:
							</td>
							<td class="value">
								<input type="text" size="60" value="<?php echo esc_attr($email);?>" name="<?php echo esc_attr( $this->get_arg_name() );?>[register][email]">
							</td>
						</tr>

						<tr class="purchase_id">
							<td class="label">
								Enter the purchase id from your receipt:
							</td>
							<td class="value">
								<input type="text" size="60" value="<?php echo esc_attr($purchase_id);?>" name="<?php echo esc_attr( $this->get_arg_name() );?>[register][purchase_id]">
							</td>
						</tr>

						<tr class="confirm">
							<td  colspan="2" class="label">
								<input type="submit" name="<?php echo esc_attr( $this->get_arg_name() );?>[register][action]" class="button-primary" value="Register Product" />
							</td>
						</tr>

					</table>
						<input type="hidden" size="60" value="<?php echo esc_attr($product_name);?>" name="product_manager_product_name">
						<input type="hidden" size="60" value="<?php echo esc_attr($product_name);?>" name="<?php echo esc_attr( $this->get_arg_name() );?>[status][product_name]">
		        </form>
			</div>
	        <?php
		}

		function status_form( $product_name = '' ) {
			if ( empty ( $product_name ) ) {
				$product_name = $this->plugin_base_name;
			}

			?>
			<div>
				<h2><?php echo esc_html( $this->name() );?> <?php _e('Status and Support', 'PBCILM' )?></h2>
				<hr>
				<form id="register_plugin" method="post">
					<?php $this->product_info_table(); ?>

					<hr>
					<h3><?php _e('Support Request', PBCILM);?></h3>
					<p><?php _e('If you have an issue you want to bring to our attention or if you have a suggestion for improving this you can send us an email.',PBCILM);?></p><br>

					<textarea rows="15" cols="120"  name="<?php echo esc_attr( $this->get_arg_name() );?>[status][msg]"><?php
					?><?php _e('Sending you a note becuase I need some help with the plugin or I have a suggestion. The details are as follows:', PBCILM);?>
					</textarea>
					<br>

					<input type="submit" name="<?php echo esc_attr( $this->get_arg_name() );?>[status][action]" class="button-primary" value="Send Email" />
					<input type="hidden" size="60" value="<?php echo esc_attr($product_name);?>" name="<?php echo esc_attr( $this->get_arg_name() );?>[status][product_name]">
					<input type="hidden" size="60" value="<?php echo esc_attr($product_name);?>" name="product_manager_product_name">
		        </form>
			</div>
			<hr>
	        <?php
		}

		function install_form( $product_name = '' ) {
			if ( empty ( $product_name ) ) {
				$product_name = $this->plugin_base_name;
			}

			?>
			<div>
				<h2><?php _e('Ready To Install', 'PBCILM' )?> <?php echo esc_html( $this->name() );?></h2>
				<form id="install_plugin" method="post">
					<?php $this->product_info_table(); ?>
					<input type="hidden" size="60" value="<?php echo esc_attr($product_name);?>" name="<?php echo esc_attr( $this->get_arg_name() );?>[install][product_name]">
					<input type="hidden" size="60" value="<?php echo esc_attr($product_name);?>" name="product_manager_product_name">
					<p>
					<input type="submit" name="<?php echo esc_attr( $this->get_arg_name() );?>[install][action]" class="button-primary" value="Install Product" />
					</p>

		        </form>
			</div>
			<hr>
			<?php
		}

		function upgrade_form( $product_name = '' ) {
			if ( empty ( $product_name ) ) {
				$product_name = $this->plugin_base_name;
			}
			?>

			<div>
				<h2><?php _e('An Upgrade Is Available for ', 'PBCILM' )?> <?php echo esc_html( $this->name() );?></h2>
				<form id="install_plugin" method="post">
					<?php $this->product_info_table(); ?>
					<input type="hidden" size="60" value="<?php echo esc_attr($product_name);?>" name="<?php echo esc_attr( $this->get_arg_name() );?>[install][product_name]">
					<input type="hidden" size="60" value="<?php echo esc_attr($product_name);?>" name="product_manager_product_name">
					<p>
					<input type="submit" name="<?php echo esc_attr( $this->get_arg_name() );?>[install][action]" class="button-primary" value="Install Upgrade" />
					</p>

		        </form>
			</div>
			<hr>
			<?php

		}

		function registration_page( $admin_menu_handle = null,  $register_menu_title = null, $install_menu_title = null ,  $update_menu_title = null, $status_menu_title = null ) {

			switch ( $this->get_next_user_action() ) {

				case 'register':
					$admin_menu_name = isset( $register_menu_title ) ? $register_menu_title : __( 'Register', PBCILM );
					break;

				case 'install':
					$admin_menu_name = isset( $install_menu_title ) ? $install_menu_title : __( 'Install', PBCILM );
					break;

				case 'upgrade':
					$admin_menu_name = isset( $upgrade_menu_title ) ? $upgrade_menu_title : __( 'Upgrade', PBCILM );
					break;

				default:
					$admin_menu_name = isset( $status_menu_title ) ? $status_menu_title : __( 'Support', PBCILM );
					break;
			}

			$menupage = add_submenu_page(
											$admin_menu_handle,
											$admin_menu_name,
											$admin_menu_name,
											'manage_options',
											'registration_page_callback',
											array( $this, 'registration_page_callback' )
										);
			return $menupage;
		}

		function registration_page_callback(  ) {

			?>
			<div class="wrap">
			<?php

			$info = $this->get_info();

			if ( isset( $info->Name ) )
				$name_for_title =  $info->Name;
			else
				$name_for_title =  $this->plugin_base_name;

			switch ( $this->get_next_user_action() ) {

				case 'register':
					$this->registration_form( $this->plugin_base_name );
					break;

				case 'install':
					$this->install_form( $this->plugin_base_name );
					break;

				case 'upgrade':
					$this->upgrade_form( $this->plugin_base_name );
					break;

				default:
					$this->status_form( $this->plugin_base_name );
					break;
			}

			?>
			</div>
			<?php
			return true;

		}

		private function get_next_user_action() {

			// if no key then we need to register
			if ( !$this->verify_key() ) {
				return 'register';
			}

			// if no key then we need to register
			if ( !$this->installed ) {
				return 'install';
			}

			// if no key then we need to register
			if ( $this->is_upgrade_available() ) {
				return 'upgrade';
			}

			return 'status';
		}

		private function get_arg_name() {
			$product_name = $this->plugin_base_name;
			$argname = $product_name. '_action';
			return $argname;
		}


		function process_post_args() {

			if ( !isset( $_POST[ $this->get_arg_name() ] ) )
				return;

			$args = $_POST[$this->get_arg_name()] ;

			if ( isset( $args['register'] ) ) {
				$register = $args['register'];
				$result = $this->register( $register['product_name'], $register['email'], $register['purchase_id'] );
				pbci_set_admin_message( $this->messages());
			}

			if ( isset( $args['install'] ) ) {
				$register = $args['install'];
				$result = $this->get_upgrade();
				pbci_set_admin_message( $this->messages());
			}

			if ( isset( $args['upgrade'] ) ) {
				$register = $args['upgrade'];
				$result = $this->get_upgrade();
				pbci_set_admin_message( $this->messages());
			}

			if ( isset( $args['status'] ) ) {
				$register = $args['status'];
				pbci_set_admin_message( $this->messages());
			}

			if ( isset( $args['license'] ) ) {
				$register = $args['license'];
				$result = $this->clear_product_key();
				pbci_set_admin_message( $this->messages());
			}

			return true;

		}

		private function get_params ( $action = '' , $product = '' ) {
			if ( empty ( $product ) ) {
				$product = $this->plugin_base_name;
			}

			$params = array (
					'product'		    => $product,
					'installed' => $this->product_is_installed($product),
					'domain'            => $this->domain(),
					'action'            => empty( $action ) ? 'checkin' : $action,
					'site_url'          => site_url(),
					'admin_email'       => get_option('admin_email', ''),
					'key'               => $this->get_key(),
					'server_name'       => isset( $_SERVER['SERVER_NAME'] ) ? $_SERVER['SERVER_NAME'] : '',
					'plugin_base_name'  => $this->base_name(),
					'name'              => $this->name(),
					'version'           => $this->version(),
					'installed_version' => $this->installed_version(),
			);

			return $params;
		}

		private function trim_slashes( $string ) {
			$string = rtrim($string, '/');
			$string = rtrim($string, '\\');
			return $string;
		}

		static function clean_domain ( $domain ) {
			$info = parse_url( $domain );
			$domain = empty( $info['host'] ) ? $info['path']:$info['host'];
			return $domain;
		}

		private function plugin_root_directory( $plugin_base_name = '' ) {

			$basename = plugin_basename(__FILE__);
			$tok = strtok( $basename , '/' );
			$dirpath = plugin_dir_path( __FILE__ );
			$i = stripos( $dirpath, $tok );
			$root = substr( $dirpath, 0, $i );
			if ( !empty ( $plugin_base_name ) )
				$root .= ($plugin_base_name . '/');

			return $root;
		}
	}
}


/*
 * Message handling
 */
if ( !function_exists( 'pbci_dismiss_admin_message' ) ) {
	function pbci_dismiss_admin_message( $id ) {
		$messages = get_option( 'pbci_messages' , array() );

		if ( isset($messages[$id] ) ) {
			unset( $messages[$id] );
			update_option( 'pbci_messages',$messages );
		}
	}
}

if ( !function_exists( 'pbci_set_admin_message' ) ) {
	function pbci_set_admin_message( $new_messages ) {

		if ( empty ( $new_messages ) )
			return;

		if ( is_string( $new_messages ) ) {
			$new_messages = array ( $new_messages );
		}

		$messages = get_option( 'pbci_messages' , array() );

		foreach ( $new_messages as $new_message ) {
			// using the hash key is an easy way of preventing duplicate messages
			$id = md5( $new_message );

			if ( !isset($messages[$id] ) ) {
				$messages[$id] = $new_message;
			}
		}

		update_option( 'pbci_messages', $messages );

		if ( did_action( 'admin_menu') ) {
			pbci_show_admin_messages();
		}
	}
}


if ( !function_exists( 'pbci_show_admin_messages' ) ) {


	function pbci_show_admin_messages() {

		// do not display messages until after the admin menu has been processed and other notices are being shown
		if ( !did_action( 'admin_notices') )
			return;

		$messages = get_option( 'pbci_messages' , array() );

		static $already_sent_javascript = false;

		if ( !$already_sent_javascript && count( $messages ) ) {
		?>
		<script>
		jQuery(document).ready( function() {
			function dismiss_msg(id) {
				jQuery.ajax({
					type : "post",
					dataType : "text",
					url : "<?php echo admin_url( 'admin-ajax.php' );?>",
					data : {action: "pbci_dismiss_message", id : id},
					success: function (response) {
						;
					},
					error: function (response) {
						;
					},
				});
				jQuery( "#pbci-admin-message-"+id ).hide();
			}

			jQuery(".pbci-admin-message-dismiss").click(function(event) {
			    	dismiss_msg(event.target.id);
			    	return false;
			    });
			});
		</script>
		<?php
		$already_sent_javascript = true;
		}


		static $already_displayed = array();

		foreach ( $messages as $id=>$message ) {
			if ( in_array($id, $already_displayed) )
				continue;

			$already_displayed[] = $id;


			?>
			<div class="pbci-admin-message" id="pbci-admin-message-<?php echo esc_attr( $id );?>">
				<div class="message-text">
					<p>
						<?php echo $message; ?>
					</p>
				</div>
				<div class="pbci-admin-message-action" style="width: 100%; text-align: right;">
					<a class="pbci-admin-message-dismiss" id="<?php echo esc_attr( $id );?>"><?php _e( 'Dismiss', PBCIBP )?></a>
				</div>
			</div>
			<?php
		}
	}
}

if ( !function_exists( 'pbci_dismiss_admin_message_action' ) ) {
	function pbci_dismiss_admin_message_action() {
		if ( isset ( $_POST['id'] ) ) {
			$id = $_POST['id'];
			pbci_dismiss_admin_message( $id );
		}
		exit( 'message id '.$id.' dismissed' );
	}
}

if ( !has_action( 'admin_notices' , 'pbci_show_admin_messages') ) {
	add_action( 'admin_notices', 'pbci_show_admin_messages');
}

if ( !has_action("wp_ajax_pbci_dismiss_message", 'pbci_dismiss_admin_message_action') ) {
	add_action("wp_ajax_pbci_dismiss_message", 'pbci_dismiss_admin_message_action' );
}

if ( !has_action('wp_ajax_nopriv_pbci_dismiss_message', 'pbci_dismiss_admin_message_action') ) {
	add_action('wp_ajax_nopriv_pbci_dismiss_message', 'pbci_dismiss_admin_message_action' );
}

add_action( 'admin_menu', 'process_post_args_wrapper', 1);

function process_post_args_wrapper() {
	if ( isset( $_POST['product_manager_product_name'] ) ) {
		$server = new PBCI_Product_Manager_Client($_POST['product_manager_product_name']);
		$server->process_post_args();
	}
}

