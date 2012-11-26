<?php
/**
 * Plugin Name: Accidentals
 * Description: Invoke jquery.accidentals.js to convert text representations of some musical symbols (sharps, flats, naturals, double sharps, and double flats) into actual symbols, depending on user's system capabilities.
 * Version: 0.1
 *
 * http://accidentals.bretpimentel.com
 *
 * License: GPL2
 *
 * Copyright (c) 2012, Bret Pimentel. 
 * This program is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU General Public License 
 * as published by the Free Software Foundation; either version 2 
 * of the License, or (at your option) any later version. 
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 * GNU General Public License for more details. 
 * You should have received a copy of the GNU General Public License 
 * along with this program; if not, write to the Free Software 
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA. 
 * 
 */

	$accidentals_version = '0.1';
	$accidentals_options_defaults = array (
		// 'accidentalClassBase' => 'accidental',
		// 'accidentalWrap' => '<span class="accidental" />',
		// 'caseSensitive' => false,
		// 'ignoreWithin' => '.no-accidentals',
		// 'inputTypes' => "['text']",
		// 'noteNames' => '[A-H]',
		// 'noteNameWrap' => '',
		// 'outerWrap' => '',
		'safeMode' => true,
		'selector' => 'body'
	);

	add_action('init', 'accidentals_init');

	function accidentals_init(){
		add_action( 'wp_enqueue_scripts', 'accidentals_add_scripts' );
		add_action( 'admin_menu', 'accidentals_menu' );
		add_action( 'admin_init', 'accidentals_register_settings' );
	}

	function accidentals_add_scripts() {
		wp_enqueue_script( 'accidentals',  accidentals_get_url( 'js/jquery.accidentals.min.js' ), array( 'jquery' ), $accidentals_version, true );
		wp_enqueue_script( 'accidentals-wordpress',  accidentals_get_url( 'js/accidentals-wordpress.min.js' ), array( 'jquery', 'accidentals' ), $accidentals_version, true );
		$options = get_option('accidentals_options');
		if ($options){
			wp_localize_script( 'accidentals-wordpress', 'accidentals', $options );
		}
	}

	function accidentals_menu() {
		add_options_page( 'Accidentals Options', 'Accidentals', 'manage_options', 'accidentals', 'accidentals_options' );
	}

	function accidentals_register_settings() {
		register_setting('accidentals-option-group', 'accidentals_options', 'accidentals_sanitize');
	}

	function accidentals_sanitize ($options) {
		global $accidentals_options_defaults;
		if( !is_array( $options ) || empty( $options ) || ( false === $options ) )
			return $accidentals_options_defaults;

		foreach( $accidentals_options_defaults as $option_name => $option_value ) {
			if ($option_name == "safeMode") { //checkbox
				 $options[$option_name] = isset($options[$option_name]) ? 1 : 0;
		 	} elseif ( !isset( $options[$option_name] ) || ( strlen($options[$option_name]) < 1 ) ) {
	 			$options[$option_name] = $accidentals_options_defaults[$option_name];
		 	}
	    }	

		return $options;
	}

	// Add settings link on plugin page
	function accidentals_options_settings_link($links) { 
		$links[] = '<a href="options-general.php?page=accidentals.php">Settings</a>'; 
		return $links; 
	}
	add_filter("plugin_action_links_".$plugin = plugin_basename(__FILE__), 'accidentals_options_settings_link' );

	function accidentals_get_url( $path = '' ) {
		return plugins_url( ltrim( $path, '/' ), __FILE__ );
	}

	function accidentals_options() {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		?>
		<div id="accidentals-wrap" class="wrap">
			<style>
				#accidentals-wrap td, #accidentals-wrap th {
					vertical-align: top;
				}
				#accidentals-wrap .accidentals-disabled {
					color: red;
					display: none;
				}
				#accidentals-wrap th {
					font-weight: bold;
				}
			</style>
			<?php screen_icon(); ?>
			<h2>Accidentals</h2>
			<p>This plugin converts certain text into musical symbols, if it can verify that the user's computer or device supports those symbols. If this cannot be verified, the plugin does not attempt conversions&mdash;this is a feature, to prevent unsightly boxes or other placeholders that some computers display instead of musical symbols. <a href="http://accidentals.bretpimentel.com/#moreaboutdisplayingspecialcharacters">More information</a></p>
			<h3>Usage</h3>
			<table class="form-table" id="accidentals-admin-demo">
				<tr>
					<th>Type this into posts or pages:</th>
					<th>It will be displayed <em>on the device you are currently using</em> as:</th>
				</tr>
				<tr>
					<td class="no-accidentals">A-natural</td>
					<td>A-natural</td>
				</tr>
				<tr>
					<td class="no-accidentals">A-flat</td>
					<td>A-flat</td>
				</tr>
				<tr>
					<td class="no-accidentals">A-sharp</td>
					<td>A-sharp</td>
				</tr>
				<tr>
					<td class="no-accidentals">A-double-flat</td>
					<td>A-double-flat</td>
				</tr>
				<tr>
					<td class="no-accidentals">A-double-sharp</td>
					<td>A-double-sharp</td>
				</tr>
			</table>				
			<p>Regardless of what you see here, your blog readers may see either symbols or the text as typed, depending on their operating systems, browsers, and installed fonts.</p>
			<h3>Styling and theme development</h3>
			<p>If you are familiar with <abbr title="cascading stylesheets">CSS</abbr>, symbols can be targeted for styling using the classes <code>accidental</code> (all symbols), <code>accidental-natural</code>, <code>accidental-flat</code>, <code>accidental-sharp</code>, <code>accidental-double-flat</code>, and <code>accidental-double-sharp</code>. Using these classes to apply <code>font-face</code> is not recommended in most cases, as the most common web fonts do not include these musical symbols. If you use CSS to change the <code>font-size</code>, <code>font-weight</code>, or other visual characteristics of the symbols, please test on a variety of systems to ensure that the desired results are being achieved.</p>
			<p>Text within containers having a class of <code>no-accidentals</code> will not be affected by the plugin.</p>
			<h3>Options</h3>
			<p>You can access more advanced options by disabling this plugin and using <a href="https://github.com/bpimentel/accidentals">jquery.accidentals.js</a> directly in your theme or plugin.</p>
			<form method="post" action="options.php">
				<?php
					settings_fields( 'accidentals-option-group' );
					do_settings_sections( 'accidentals-option-group' );
					$accidentals_options = get_option('accidentals_options');
				?>
			    <table class="form-table">
					<tr>
						<th>jQuery selector</th>
						<td><input type="text" name="accidentals_options[selector]" value="<?php echo $accidentals_options['selector']; ?>" /><br />Default value: <code>body</code></td>
						<td><p>This determines where on the page symbols are converted. The default (<code>body</code>) means that conversions will happen everywhere on the page (including post titles, widgets, header and footer, etc.). If you are jQuery-savvy, you may enter any jQuery selector to focus where the conversions occur.</p>
						<p>For many themes, you can restrict conversions to only post and page content by entering <code>.entry-content</code> (note the period).</p></td>
					</tr>
					<tr>
						<th>Safe mode</th>
						<td><input type="checkbox" name="accidentals_options[safeMode]" value="1"<?php checked($accidentals_options['safeMode']); ?> /><br />Default value: <code>checked</code></td>
						<td>Strongly recommended. If turned off, some users may see unsightly boxes or placeholder characters instead of musical symbols. Leave this checked to degrade gracefully to text <span class="no-accidentals">("A-flat")</span>.</td>
					</tr>
			    </table>
				<?php submit_button(); ?>
			</form>
		</div>
<?php	}
?>