<?php
/**
 * @package nxcSocialNetworks
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    30 Nov 2010
 **/

$http = eZHTTPTool::instance();
$ini  = eZINI::instance( 'nxclinkedin.ini' );

$connection = new LinkedIn(
	array(
		'appKey'      => $ini->variable( 'LinkedinAPI', 'Key' ),
		'appSecret'   => $ini->variable( 'LinkedinAPI', 'Secret' ),
		'callbackUrl' => eZSys::serverURL() . eZSys::indexDir() . '/nxc_linkedin_api/callback'
	)
);

$response = $connection->retrieveTokenRequest( array( 'rw_nus' ) );
if( $response['success'] === true ) {
	$http->setSessionVariable( 'linkedin_request_token', $response['linkedin'] );
	header( 'Location: ' . LinkedIn::_URL_AUTH . $response['linkedin']['oauth_token'] );
	eZExecution::cleanExit();
} else {
	eZDebug::writeError( 'Request token retrieval failed.', 'NXC LinkedIn API' );
}
?>
