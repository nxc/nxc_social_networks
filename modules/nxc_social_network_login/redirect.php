<?php
/**
 * @package nxcSocialNetworks
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    16 Sep 2012
 **/

$http   = eZHTTPTool::instance();
$module = $Params['Module'];

try{
	$handler = nxcSocialNetworksLoginHandler::getInstanceByType( $Params['type'] );
} catch( Exception $e ) {
	eZDebug::writeError( $e->getMessage(), 'NXC Social Networks Login' );
	return $module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );
}


if( $http->hasVariable( 'after_login_url' ) ) {
	$http->setSessionVariable( 'AfterLoginURI', $http->variable( 'after_login_url' ) );
}

try{
	$scopes      = $handler->getScopes();
	$redirectURL = $handler->getCallbackURL();

	header( 'Location: ' . $handler->getLoginURL( $scopes, $redirectURL ) );
	eZExecution::cleanExit();
} catch( Exception $e ) {
	eZDebug::writeError( $e->getMessage(), 'NXC Social Networks Login' );
	return $module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );
}
?>
