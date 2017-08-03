<?php
//Add default settings for popup
function wpis_plugin_settings() {
	if(!get_option('wpis_options')) {
		add_option('wpis_options', wpis_defaults());
	}
}

function wpis_defaults()
{
	$options = $_POST;
	    $update_val = array(
    	'arrow'    => 'true',
		'carrow'   => 'false',
		'zoom'     => 'true',
		'popup'    => 'true',
    	'autoplay' => 'true'
    );
	return $update_val;
}

//hook to add admin menu
add_action('admin_menu','wpis_plugin_admin_menu');
function wpis_plugin_admin_menu()
{
	add_menu_page('WPIS Settings','WPIS Settings','administrator','wpis','wpis_backend_menu','dashicons-layout',59);
}

// update the wpis options
if(isset($_POST['wpis_update']))
{
	update_option('wpis_options', wpis_updates());
}

function wpis_updates()
{
	$options = $_POST;

	$update_val = array(
		'arrow'   => $options['wpis_arrow'],
		'carrow'  => $options['wpis_carrow'],
		'zoom'    => $options['wpis_zoom'],
		'popup'   => $options['wpis_popup'],
		'autoplay'=> $options['wpis_autoplay']
    );
return $update_val;
}

// Setting Html
function wpis_backend_menu()
{
$options = get_option('wpis_options'); 
?>
<style>.wpis-table td{border:1px solid #ddd;}</style>
<div class="mainwrapper" id="wpis_admin">
	<h2><?php _e('WPIS ('.wpis_get_version().') Setting\'s','wpis'); ?></h2>
	<div>
		<form method="post">
			<table class="wpis-table" style="border:1px solid #ddd;" bgcolor="#fff" cellpadding="12" cellspacing="0">
				<tr>
					<td><?php _e('Slider Next/Prev Arrows (Enable/Disable)','wpis'); ?></td>
					<td>
						<div class="wpis_colwrap">
							<select name="wpis_arrow">
								<option value="true" <?php selected( $options['arrow'], 'true' ); ?>><?php _e('Enable','wpis'); ?></option>
								<option value="false" <?php selected( $options['arrow'], 'false' ); ?>><?php _e('Disable','wpis'); ?></option>
							</select>
						</div>
					</td>
				</tr>
				<tr>
					<td><?php _e('Carousel Next/Prev Arrows (Enable/Disable)','wpis'); ?></td>
					<td>
						<div class="wpis_colwrap">
							<select name="wpis_carrow">
								<option value="true" <?php selected( $options['carrow'], 'true' ); ?>><?php _e('Enable','wpis'); ?></option>
								<option value="false" <?php selected( $options['carrow'], 'false' ); ?>><?php _e('Disable','wpis'); ?></option>
							</select>
						</div>
					</td>
				</tr>
				<tr>
					<td><?php _e('Slider Image Zoom (Enable/Disable)','wpis'); ?></td>
					<td>
						<div class="wpis_colwrap">
							<select name="wpis_zoom">
								<option value="true" <?php selected( $options['zoom'], 'true' ); ?>><?php _e('Enable','wpis'); ?></option>
								<option value="false" <?php selected( $options['zoom'], 'false' ); ?>><?php _e('Disable','wpis'); ?></option>
							</select>
						</div>
					</td>
				</tr>
				<tr>
					<td><?php _e('Slider Image Popup (Enable/Disable)','wpis'); ?></td>
					<td>
						<div class="wpis_colwrap">
							<select name="wpis_popup">
								<option value="true" <?php selected( $options['popup'], 'true' ); ?>><?php _e('Enable','wpis'); ?></option>
								<option value="false" <?php selected( $options['popup'], 'false' ); ?>><?php _e('Disable','wpis'); ?></option>
							</select>
						</div>
					</td>
				</tr>
				<tr>
					<td><?php _e('Slider Autoplay (Enable/Disable)','wpis'); ?></td>
					<td>
						<div class="wpis_colwrap">
							<select name="wpis_autoplay">
								<option value="true" <?php selected( $options['autoplay'], 'true' ); ?>><?php _e('Enable','wpis'); ?></option>
								<option value="false" <?php selected( $options['autoplay'], 'false' ); ?>><?php _e('Disable','wpis'); ?></option>
							</select>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" value="<?php _e('Save Settings','wpis'); ?>" class="button-primary" id="wpis_update" name="wpis_update">	
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
<?php
}