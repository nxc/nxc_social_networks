<?php
/**
 * @package nxcSocialNetworks
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    26 Nov 2010
 **/

$module  = $Params['Module'];
$http    = eZHTTPTool::instance();
$ini     = eZINI::instance( 'nxctwitter.ini' );
$siteIni = eZINI::instance();

$connection = new TwitterOAuth(
	$ini->variable( 'TwitterAPI', 'Key' ),
	$ini->variable( 'TwitterAPI', 'Secret' ),
	$http->sessionVariable( 'twitter_request_token' ),
	$http->sessionVariable( 'twitter_request_token_secret' )
);
$accessToken = $connection->getAccessToken( $http->getVariable( 'oauth_verifier' ) );

$http->removeSessionVariable( 'twitter_request_token' );
$http->removeSessionVariable( 'twitter_request_token_secret' );

if( (int) $connection->http_code !== 200 ) {
	return $module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );
}

$this->twitterAPI = new TwitterOAuth(
	$ini->variable( 'TwitterAPI', 'Key' ),
	$ini->variable( 'TwitterAPI', 'Secret' ),
	$accessToken['oauth_token'],
	$accessToken['oauth_token_secret']
);

$remoteID = 'twitter_user_' . $accessToken['user_id'];
$object   = eZContentObject::fetchByRemoteID( $remoteID );
if( $object instanceof eZContentObject ) {
	$user = eZUser::fetch( $object->attribute( 'id' ) );
} else {
	$userClassID = $siteIni->variable( 'UserSettings', 'UserClassID' );
	$userClass   = eZContentClass::fetch( $userClassID );

	if( $userClass instanceof eZContentClass ) {
		$userInfo = $this->twitterAPI->get( 'users/show', array( 'user_id' => $accessToken['user_id'] ) );
		$nameArr  = explode( ' ', $userInfo->name );

		$login = $accessToken['screen_name'];
		if(
			empty( $login )
			|| eZUser::fetchByName( $login ) instanceof eZUser
		) {
			$login = 'TwitterUser_' . $accessToken['user_id'];
		}

		$email        = $login . '@nospam.twitter.com';
		$password     = eZUser::createPassword( 8 );
		$passwordHash = eZUser::createHash( $login, $password, eZUser::site(), eZUser::hashType() );

		$attributes = array(
			'first_name'   => $nameArr[0],
			'last_name'    => isset( $nameArr[1] ) ? $nameArr[1] : '',
			'user_account' => $login . '|' . $email . '|' . $passwordHash . '|' . eZUser::passwordHashTypeName( eZUser::hashType() ),
			'signature'    => $userInfo->description
		);

		$filename = 'var/cache/'. substr( strrchr( $userInfo->profile_image_url, '/' ), 1 );
		if( copy( $userInfo->profile_image_url, $filename ) ) {
			$attributes['image'] = $filename;
		};

		$object = eZContentFunctions::createAndPublishObject(
			array(
				'parent_node_id'   => $siteIni->variable( 'UserSettings', 'DefaultUserPlacement' ),
				'class_identifier' => $userClass->attribute( 'identifier' ),
				'creator_id'       => $siteIni->variable( 'UserSettings', 'UserClassID' ),
				'section_id'       => $siteIni->variable( 'UserSettings', 'DefaultSectionID' ),
				'remote_id'        => $remoteID,
				'attributes'       => $attributes
			)
		);

		if( isset( $attributes['image'] ) ) {
			unlink( $filename );
		}

		if( $object instanceof eZContentObject === false ) {
			eZDebug::writeError( 'User`s object isn`t created.', 'NXC Twitter Signin' );
		}

		$user = eZUser::fetchByName( $login );
	} else {
		eZDebug::writeError( 'Content class with ' . $userClassID . ' doesn\'t exist.', 'NXC Twitter Signin' );
	}
}

if( $user instanceof eZUser ) {
	$user->loginCurrent();

	if( $http->hasGetVariable( 'login_redirect_url' ) ) {
		$redirectURI = $http->getVariable( 'login_redirect_url' );
	} elseif( $http->hasSessionVariable( 'LastAccessesURI' ) && $http->sessionVariable( 'LastAccessesURI' ) ) {
		$redirectURI = $http->sessionVariable( 'LastAccessesURI' );
	} else {
		$redirectURI = $siteIni->variable( 'SiteSettings', 'DefaultPage' );
	}

	return $module->redirectTo( $redirectURI );
}
?>
