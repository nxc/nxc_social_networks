<?php
/**
 * @package nxcSocialNetworks
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    16 Sep 2012
 **/

$module = $Params['Module'];

try{
	$hanlder = nxcSocialNetworksLoginHanlder::getInstanceByType( $Params['type'] );
} catch( Exception $e ) {
	eZDebug::writeError( $e->getMessage(), 'NXC Social Networks Login' );
	return $module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );
}

try{
	$scopes      = $hanlder->getScopes();
	$redirectURL = $hanlder->getCallbackURL();

	header( 'Location: ' . $hanlder->getLoginURL( $scopes, $redirectURL ) );
	eZExecution::cleanExit();
} catch( Exception $e ) {
	eZDebug::writeError( $e->getMessage(), 'NXC Social Networks Login' );
	return $module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );
}
?>
