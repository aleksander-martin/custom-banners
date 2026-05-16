<?php
include("cbkg.php");

function isValidCBKey(){
	
	// FIX: changed $_GLOBALS to $GLOBALS to prevent PHP 8 errors
	if ( !isset($GLOBALS['is_valid_cb_result']) )
	{		
		$email = get_option('custom_banners_registered_name');
		$webaddress = get_option('custom_banners_registered_url');
		$key = get_option('custom_banners_registered_key');
		
		$keygen = new CBKG();
		$computedKey = $keygen->computeKey($webaddress, $email);
		$computedKeyEJ = $keygen->computeKeyEJ($email);

		if ($key == $computedKey || $key == $computedKeyEJ) {
			$GLOBALS['is_valid_cb_result'] = true;
			return true;
		} else {
			$plugin = "custom-banners-pro/custom-banners-pro.php";
			
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			if(is_plugin_active($plugin)){
				$GLOBALS['is_valid_cb_result'] = true;
				return true;
			}
			
			$GLOBALS['is_valid_cb_result'] = false;
			return false;
		}
	}
	
	return $GLOBALS['is_valid_cb_result'];
}
