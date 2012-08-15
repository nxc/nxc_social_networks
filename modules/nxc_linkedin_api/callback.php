<?php
/**
 * @package nxcSocialNetworks
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    30 Nov 2010
 **/

$http   = eZHTTPTool::instance();
$ini    = eZINI::instance( 'nxclinkedin.ini' );
$module = $Params['Module'];

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
	return $module->redirectTo( '/nxc_linkedin_api/settings' );
}

$response = $connection->retrieveTokenAccess(
	$request['oauth_token'],
	$request['oauth_token_secret'],
	$http->getVariable( 'oauth_verifier' )
);
if( $response['success'] === true ) {
	$http->removeSessionVariable( 'linkedin_request_token' );

	$ini = eZINI::instance( 'nxclinkedinaccesstoken.ini' );
	$ini->setVariable( 'AccessToken', 'Token', $response['linkedin']['oauth_token'] );
	$ini->setVariable( 'AccessToken', 'Secret', $response['linkedin']['oauth_token_secret'] );
	$result = $ini->save(
		'nxclinkedinaccesstoken.ini.append.php',
		false,
		false,
		false,
		'settings/override',
		false,
		true
	);

	if( $result === false ) {
		eZDebug::writeError( 'Cannot update persistent token info at settings file', 'NXC LinkedIn API' );
	}
}

return $module->redirectTo( '/nxc_linkedin_api/settings/1' );
?>
