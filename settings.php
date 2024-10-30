<?php
function brandregard_menu() {

	//create new top-level menu
	add_options_page('Brand Regard Settings', 'Brand Regard', 'administrator', __FILE__, 'brandregard_settings_page');

	//call register settings function
	add_action( 'admin_init', 'brandregard_settings' );
}


function brandregard_settings() {
	//register our settings
	register_setting( 'brandregard-plugin', 'brapikey' );
	register_setting( 'brandregard-plugin', 'brcompany' );
	register_setting( 'brandregard-plugin', 'brtk1' );
	register_setting( 'brandregard-plugin', 'brtk2' );
	register_setting( 'brandregard-plugin', 'briconsize' );
	register_setting( 'brandregard-plugin', 'brpage' );
	register_setting( 'brandregard-plugin', 'brdisplayname' );
}

function brandregard_settings_page() {

?>
<div class="wrap">
<h2>Brand Regard Plugin</h2>
<? if (get_option('brcompany') == "" && get_option('brapikey') == "" && get_option('brtk1') == "" && get_option('brtk2') == "") add_option('briconsize', 2); ?>
<form method="post" action="options.php">
    <?php settings_fields( 'brandregard-plugin' ); ?>
	<table class="form-table">
        <tr valign="top">
        <th scope="row">Your company name (company.brandregard.com)</th>
        <td><input type="text" name="brcompany" value="<?php echo get_option('brcompany'); ?>" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Brand Regard API key</th>
        <td><input type="text" name="brapikey" size="60" value="<?php echo get_option('brapikey'); ?>" /></td>
        </tr>

	
    <?
		$br_api = get_option('brapikey'); 
		$br_name = get_option('brcompany');
		$toolkits = array();
		if ($br_api != "" && $br_name != "") {

			$url = 'https://'.$br_name.'.brandregard.com/api/v1/toolkits.xml?api_key='.$br_api;
			try {
				if (@fopen($url, "r")) {
				
				$sm = new SimpleXMLElement($url, null, true);
				
					echo '<tr valign="top">';
					echo '<th scope="row">Toolkit 1</th><td>';
					echo '<select name="brtk1">';
					echo '<option value="EMPTY">EMPTY</option>';
					foreach($sm->children() as $u) 
					{
						if ($u == "401 Unauthorized") { echo ('API key invalid.'); exit; }
						$cnt = 0;
						$toolkit_id;
						foreach($u->children() as $c)
						{
							if ($cnt == 0 && get_option('brtk1') == $c) {
								array_push($toolkits, $c);
								echo '<option value="'.$c.'" selected="selected">';
							}
							elseif ($cnt == 0) {
								array_push($toolkits, $c);
								echo '<option value="'.$c.'">';
							}
							if ($cnt == 1) {
								echo $c.'</option>';
							}
							if ($cnt == 2) break;
							$cnt++;
													
						}
					}
					echo '</select></td></tr>';
					echo '<tr valign="top">';
					echo '<th scope="row">Toolkit 2</th><td>';
					echo '<select name="brtk2">';
					echo '<option value="EMPTY">EMPTY</option>';
					foreach($sm->children() as $u) 
					{
						if ($u == "401 Unauthorized") { echo ('API key invalid.'); exit; }
						$cnt = 0;
						$toolkit_id;
						foreach($u->children() as $c)
						{
							if ($cnt == 0 && get_option('brtk2') == $c) {
								array_push($toolkits, $c);
								echo '<option value="'.$c.'" selected="selected">';
							}
							elseif ($cnt == 0) {
								array_push($toolkits, $c);
								echo '<option value="'.$c.'">';
							}
							if ($cnt == 1) {
								echo $c.'</option>';
							}
							if ($cnt == 2) break;
							$cnt++;
													
						}
					}
				}						
				else
				{
					echo '<tr valign="top"><td>Your company name or API key is invalid.</td></tr>';
				}
			}
			catch (Exception $e)
			{
				echo 'Error: '.$e;
			}			
			
		}
		else
		{
			echo '<tr valign="top"><td>Toolkits menu will be added here once Brand Regard confirms that your company name and your API key is valid.</td></tr>';
		}
		?>
		
		<tr valign="top">
        <th scope="row">Thumbnail size</th>
        <td>
		<select name="briconsize">
		  <option value="0" <? if(get_option('briconsize') == 0) echo 'selected="selected"'; ?>>Tiny thumbnail</option>
		  <option value="1" <? if(get_option('briconsize') == 1) echo 'selected="selected"'; ?>>Small thumbnail</option>
		  <option value="2" <? if(get_option('briconsize') == 2 || get_option('briconsize') == "") echo 'selected="selected"'; ?>>Medium thumbnail</option>
		  <option value="3" <? if(get_option('briconsize') == 3) echo 'selected="selected"'; ?>>Large thumbnail</option>
		</select>
		</td>
        </tr>
		<tr valign="top">
        <th scope="row">Display name of assets</th>
        <td>
		<select name="brdisplayname">
		  <option value="1" <? if(get_option('brdisplayname') == 1) echo 'selected="selected"'; ?>>Yes</option>
		  <option value="0" <? if(get_option('brdisplayname') == 0 || get_option('brdisplayname') == "") echo 'selected="selected"'; ?>>No</option>
		</select>
		</td>
        </tr>
		<? 
			$page;
			if (get_option('brpage') == "") { 
				$page = "Hello world!";
			} else {
				$page = get_option('brpage');
			}
		?>
		<tr valign="top">
        <th scope="row">Page/Post name to display plugin</th>
        <td><input type="text" name="brpage" value="<?php echo $page; ?>" /></td>
        </tr>
    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>

</div>
<? } ?>