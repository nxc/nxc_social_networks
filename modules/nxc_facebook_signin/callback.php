<?php
/**
 * @package nxcSocialNetworks
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    26 Nov 2010
 **/

$module  = $Params['Module'];
$http    = eZHTTPTool::instance();
$ini     = eZINI::instance( 'nxcfacebook.ini' );
$siteIni = eZINI::instance();

$connection = new Facebook(
	array(
		'appId'  => $ini->variable( 'FacebookAPI', 'AppID' ),
		'secret' => $ini->variable( 'FacebookAPI', 'Secret' )
	)
);
$uid = (int) $connection->getUser();
if( $uid == 0 ) {
	return $module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );
}

$remoteID = 'fb_user_' . $uid;
$object = eZContentObject::fetchByRemoteID( $remoteID );
if( $object instanceof eZContentObject ) {
	$user = eZUser::fetch( $object->attribute( 'id' ) );
} else {
	$userClassID = $siteIni->variable( 'UserSettings', 'UserClassID' );
	$userClass   = eZContentClass::fetch( $userClassID );
	if( $userClass instanceof eZContentClass  === false ) {
		eZDebug::writeError( 'Content class with ID ' . $userClassID . ' doesn\'t exist.', 'NXC Facebook Signin' );
		return array();
	}

	// Downloading user profile image
	$picture = 'var/cache/fb_profile_' .  $uid . '.jpg';
	$fp = fopen( $picture, 'w' );
	$ch = curl_init( BaseFacebook::$DOMAIN_MAP['graph'] . '/' . $uid . '/picture?type=large' );
	curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
	curl_setopt( $ch, CURLOPT_FILE, $fp );
	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
	curl_exec( $ch );
	curl_close( $ch );
	fclose( $fp );

	$data = $connection->api( '/' . $uid );

	// Creating user account
	$login = $data['username'];
	$email = $data['email'];
	if(
		empty( $login )
		|| eZUser::fetchByName( $login ) instanceof eZUser
	) {
		$login = 'FacebookUser_' . $uid;
	}
	if(
		empty( $email )
		|| eZUser::fetchByEmail( $email ) instanceof eZUser
	) {
		$email = $uid . '@nospam.facebook.com';
	}
	$password     = eZUser::createPassword( 8 );
	$passwordHash = eZUser::createHash( $login, $password, eZUser::site(), eZUser::hashType() );
	$account      = $login . '|' . $email . '|' . $passwordHash . '|' . eZUser::passwordHashTypeName( eZUser::hashType() );

	$attributes = array(
		'image'        => $picture,
		'user_account' => $account,
		'first_name'   => $data['first_name'],
		'last_name'    => $data['last_name']
	);

	$object = eZContentFunctions::createAndPublishObject(
		array(
			'remote_id'        => $remoteID,
			'parent_node_id'   => $siteIni->variable( 'UserSettings', 'DefaultUserPlacement' ),
			'class_identifier' => $userClass->attribute( 'identifier' ),
			'creator_id'       => $siteIni->variable( 'UserSettings', 'UserClassID' ),
			'section_id'       => $siteIni->variable( 'UserSettings', 'DefaultSectionID' ),
			'attributes'       => $attributes
		)
	);
	@unlink( $picture );

	$user = eZUser::fetchByName( $login );
}

if( $user instanceof eZUser ) {
	$user->loginCurrent();

	$redirectURI = false;
	if( $http->hasGetVariable( 'login_redirect_url' ) ) {
		$redirectURI = $http->getVariable( 'login_redirect_url' );
	} elseif( $http->hasSessionVariable( 'LastAccessesURI' ) && $http->sessionVariable( 'LastAccessesURI' ) ) {
		$redirectURI = $http->sessionVariable( 'LastAccessesURI' );
	}

	if(
		$redirectURI === false
		|| $redirectURI === '/nxc_facebook_signin/callback'
	) {
		$redirectURI = $siteIni->variable( 'SiteSettings', 'DefaultPage' );
	}

	return $module->redirectTo( $redirectURI );
}
?>
