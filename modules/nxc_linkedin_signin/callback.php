<?php
/**
 * @package nxcSocialNetworks
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    12 Jan 2011
 **/

$http    = eZHTTPTool::instance();
$ini     = eZINI::instance( 'nxclinkedin.ini' );
$module  = $Params['Module'];
$siteIni = eZINI::instance();

$connection = new LinkedIn(
	array(
		'appKey'      => $ini->variable( 'LinkedinAPI', 'Key' ),
		'appSecret'   => $ini->variable( 'LinkedinAPI', 'Secret' ),
		'callbackUrl' => eZSys::serverURL() . eZSys::indexDir() . '/nxc_linkedin_api/callback'
	)
);
$request = $http->hasSessionVariable( 'linkedin_request_token' )
	? $http->sessionVariable( 'linkedin_request_token' )
	: array( 'oauth_token' => null );

if(
	$http->hasGetVariable( 'oauth_token' ) &&
	( $http->getVariable( 'oauth_token' ) !== $request['oauth_token'] )
) {
	return $module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );
}

try{
	$response = $connection->retrieveTokenAccess(
		$request['oauth_token'],
		$request['oauth_token_secret'],
		$http->getVariable( 'oauth_verifier' )
	);
} catch( Exception $e ) {
	return $module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );
}

$profile = $connection->profile( '~:(id,first-name,last-name,picture-url,email-address)' );
$profile = (array) new SimpleXMLElement( $profile['linkedin'] );
if( isset( $profile['id'] ) === false ) {
	return $module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );
}

$remoteID = 'linkedin_user_' . $profile['id'];
$object = eZContentObject::fetchByRemoteID( $remoteID );
if( $object instanceof eZContentObject ) {
	$user = eZUser::fetch( $object->attribute( 'id' ) );
} else {
	$userClassID = $siteIni->variable( 'UserSettings', 'UserClassID' );
	$userClass   = eZContentClass::fetch( $userClassID );
	if( $userClass instanceof eZContentClass  === false ) {
		eZDebug::writeError( 'Content class with ID ' . $userClassID . ' doesn\'t exist.', 'NXC LinkedIn Signin' );
		return array();
	}

	$login = 'LinkedInUser_' . $profile['id'];
	$email = $profile['email-address'];
	if(
		empty( $email )
		|| eZUser::fetchByEmail( $email ) instanceof eZUser
	) {
		$email = $profile['id'] . '@nospam.linkedin.com';
	}

	$password     = eZUser::createPassword( 8 );
	$passwordHash = eZUser::createHash( $login, $password, eZUser::site(), eZUser::hashType() );
	$account      = $login . '|' . $email . '|' . $passwordHash . '|' . eZUser::passwordHashTypeName( eZUser::hashType() );

	$attributes = array(
		'user_account' => $account,
		'first_name'   => $profile['first-name'],
		'last_name'    => $profile['last-name']
	);

	// Downloading user profile image
	if( $profile['picture-url'] ) {
		$picture = 'var/cache/linkdedin_profile_' .  $profile['id'] . '.jpg';
		$fp = fopen( $picture, 'w' );
		$ch = curl_init( $profile['picture-url'] );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
		curl_setopt( $ch, CURLOPT_FILE, $fp );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_exec( $ch );
		curl_close( $ch );
		fclose( $fp );
		$attributes['image'] = $picture;
	}

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
	if( $attributes['image'] ) {
		@unlink( $picture );
	}

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
		|| $redirectURI === '/nxc_linkedin_signin/callback'
		|| $redirectURI === '/nxc_linkedin_signin/signin'
	) {
		$redirectURI = $siteIni->variable( 'SiteSettings', 'DefaultPage' );
	}

	return $module->redirectTo( $redirectURI );
}
?>
