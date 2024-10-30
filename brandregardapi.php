<?php
/*
Plugin Name: Brand Regard Plugin
Plugin URI: http://www.brandregard.com/
Description: The plugin will display toolkits from your Brand Regard account
Version: 2.0.4
Author: Magnus Sigurbjornsson - Transmit ehf.
Author URI: http://www.brandregard.com/
License: GPLv2

Copyright 2011 Transmit ehf. - Brand Regard (email : magnus@transmit.is)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/
include("settings.php");
add_action('admin_menu', 'brandregard_menu');


function brandRegardPlugin($plugindata) {
libxml_use_internal_errors(true); 
$br_api = get_option('brapikey'); 
$br_name = get_option('brcompany');
$tk1 = get_option('brtk1');
$tk2 = get_option('brtk2');
$iconsize = get_option('briconsize');
$page = get_option('brpage');
$display_name = get_option('brdisplayname');

if (is_page($page) || is_single($page))
{
	if ($br_name == "" || $br_api == "") {
		echo 'Please go to Settings > Brand Regard and configure the plugin.';
	} 
	else
	{
		$url = 'https://'.$br_name.'.brandregard.com/api/v2/toolkits.xml?api_key='.$br_api;
		try {
				if (@fopen($url, "r"))
				{
					$tm = array();
					if ($tk1 != "" && $tk1 != "EMPTY") {
						$url = 'https://'.$br_name.'.brandregard.com/api/v2/toolkits/'.$tk1.'?api_key='.$br_api;
						$tm[0] = new SimpleXMLElement($url, null, true);
					}
					if ($tk2 != "" && $tk2 != "EMPTY") {
						$url = 'https://'.$br_name.'.brandregard.com/api/v2/toolkits/'.$tk2.'?api_key='.$br_api;
						$tm[1] = new SimpleXMLElement($url, null, true);
					}
					$content .= '<div id="toolkit_container"> ';
					$name;
					// Each toolkit (up to two)
					foreach ($tm as $m)
					{
						$content .= '<div class="toolkit"> ';
						// Retrieving title
						foreach($m->children() as $u)
						{
							$content .= '<h2>'.$u.'</h2> ';
							// All assets within a toolkit
							foreach($u->children() as $c)
							{
							$counter = 0;
								
								// Each asset
								foreach ($c->children() as $q)
								{
								if ($counter == 1) $name = $q; 
								if ($counter == 3) $download = $q['href'];
								$counter++;
								$b = 0;		

								$cnt = 0;
									// Dig into previews
									foreach($q->children() as $a)
									{
										$width = $a['width'];
										$height = $a['height'];
										if ($cnt == $iconsize) 
										{
											for($i = 0;$i <= 3;$i++) 
											{
												if ($iconsize == $i)
												{
													$size;
													$defpad;
													if ($iconsize == 0)
													{
														$size = 30;
														$defpad = 10;
														$content .= '<div class="asset_container_tiny">';
													}
													else if ($iconsize == 1)
													{
														$size = 50;
														$defpad = 15;
														$content .= '<div class="asset_container_small">';
													}
													else if ($iconsize == 2)
													{
														$size = 65;
														$defpad = 20;
														$content .= '<div class="asset_container_medium">';
													}
													else if ($iconsize == 3)
													{
														$size = 150;
														$defpad = 40;
														$content .= '<div class="asset_container_large">';
													}
													$content .= '<div class="asset">';
													//$content .= $download;
													$content .= '<a href="'.$download.'">';															
													if ($width == $size && $height == $size)
													{
														$content .= '<img style="padding: '. $defpad .'px;" src="'.$a['href'].'" alt="'.$name.'" />';
													}
													else
													{
														$warr = array($size, $width);	
														$harr = array($size, $height);
														sort($harr);
														sort($warr);
														$wmis = ($warr[1] - $warr[0]) / 2;
														$hmis = ($harr[1] - $harr[0]) / 2;
														$defpadh = $defpad - $hmis;
														$defpadw = $defpad - $wmis;
														$defpadhp = $defpad + $hmis;
														$defpadwp = $defpad + $wmis;
														
														if ($width > $size) 
														{ 
															if ($height > $size) $content .= '<img style="padding-top:'. ceil($defpadh) .'px;padding-bottom:'. floor($defpadh) .'px;padding-right:'.ceil($defpadw).'px;padding-left:'.floor($defpadw).'px;"  src="'.$a['href'].'" alt="'.$name.'" />'; 
															else if ($height < $size) $content .= '<img style="padding-top:'.ceil($defpadhp).'px;padding-bottom:'.floor($defpadhp).'px;padding-right:'.ceil($defpadw).'px;padding-left:'.floor($defpadw).'px;"  src="'.$a['href'].'" alt="'.$name.'" />';
															else $content .= '<img style="padding-top:'.$defpad.'px;padding-bottom:'.$defpad.'px;padding-right:'.ceil($defpadw).'px;padding-left:'.floor($defpadw).'px;"  src="'.$a['href'].'" alt="'.$name.'" />';					 
														}
														else if ($width < $size)
														{
															if ($height > $size) $content .= '<img style="padding-top:'.ceil($defpadh).'px;padding-bottom:'.floor($defpadh).'px;padding-right:'.ceil($defpadwp).'px;padding-left:'.floor($defpadwp).'px;"  src="'.$a['href'].'" alt="'.$name.'" />';
															else if ($height < $size) $content .= '<img style="padding:' . ceil($defpadhp) . 'px ' . floor($defpadwp) . 'px ' . ceil($defpadhp) . 'px ' . floor($defpadwp) . 'px;"   src=' . $a['href'] . ' alt='. $name . ' />';
															else $content .= '<img style="padding-top:'.$defpad.'px;padding-bottom:'.$defpad.'px;padding-right:'.ceil($defpadwp).'px;padding-left:'.floor($defpadwp).'px;"  src="'.$a['href'].'" alt="'.$name.'" />';					 
														}
														else
														{
															if ($height > $size) $content .= '<img style="padding-top:'.ceil($defpadh).'px;padding-bottom:'.floor($defpadh).'px;padding-right:'.$defpad.'px;padding-left:'.$defpad.'px;"  src="'.$a['href'].'" alt="'.$name.'" />';
															else if ($height < $size) $content .= '<img style="padding-top:'.ceil($defpadhp).'px;padding-bottom:'.floor($defpadhp).'px;padding-right:'.$defpad.'px;padding-left:'.$defpad.'px;"  src="'.$a['href'].'" alt="'.$name.'" />';
															else $content .= '<img style="padding-top:'.$defpad.'px;padding-bottom:'.$defpad.'px;padding-right:'.$defpad.'px;padding-left:'.$defpad.'px;"  src="'.$a['href'].'" alt="'.$name.'" />';					 
														}
														
													}
													$content .= '</a>';
													$content .= ' </div> ';
													if ($iconsize == 0 && $display_name == 1) $content .= '<div class="name_tiny">'. $name .'</div>';
													else if ($iconsize == 1 && $display_name == 1) $content .= '<div class="name_small">'. $name .'</div>';
													else if ($iconsize == 2 && $display_name == 1) $content .= '<div class="name_medium">'. $name .'</div>';
													else if ($iconsize == 3 && $display_name == 1) $content .= '<div class="name_large">'. $name .'</div>';
													$content .= '</div>';
												}
												
											}													
										}
										$cnt++;
									}	
								}
							}	
						}
					$content .= '</div>';
					}
				$content .= '</div>';
				return $content;
				}
				else { 
					echo 'Error. Please configure your Brand Regard plugin in Settings > Brand Regard'; 
				}
		}
		catch (ErrorException $e)
		{
			echo 'Error: '.$e;
		}
		catch (Exception $e)
		{
			echo 'Error: '.$e;
		}
	}
	
}
return $plugindata;
}	
function the_css() {
echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') .'/wp-content/plugins/brand-regard-plugin/style.css" />' . "\n";
}

add_filter ('the_content', 'brandRegardPlugin');
add_action ('wp_head', 'the_css');
?>