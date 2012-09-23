<?php
/**
 * @package nxcSocialNetworks
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    16 Sep 2012
 **/

$module = $Params['Module'];
$http   = eZHTTPTool::instance();
$ini    = eZINI::instance();

try{
	$hanlder = nxcSocialNetworksLoginHanlder::getInstanceByType( $Params['type'] );
} catch( Exception $e ) {
	eZDebug::writeError( $e->getMessage(), 'NXC Social Networks Login' );
	return $module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );
}

try{
	$remoteID = $hanlder->getUserRemoteID();
} catch( Exception $e ) {
	eZDebug::writeError( $e->getMessage(), 'NXC Social Networks Login' );
	return $module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );
}

$user = false;
if( $remoteID !== null ) {
	$object = eZContentObject::fetchByRemoteID( $remoteID );
	if( $object instanceof eZContentObject === false ) {
		try{
			$attributes = $hanlder->getUserData();
		} catch( Exception $e ) {
			eZDebug::writeError( $e->getMessage(), 'NXC Social Networks Login' );
			return $module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );
		}

		$userClassID = $ini->variable( 'UserSettings', 'UserClassID' );
		$userClass   = eZContentClass::fetch( $userClassID );
		if( $userClass instanceof eZContentClass === false ) {
			eZDebug::writeError( 'User calss does not exist', 'NXC Social Networks Login' );
			return $module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );
		}

		$object = eZContentFunctions::createAndPublishObject(
			array(
				'parent_node_id'   => $ini->variable( 'UserSettings', 'DefaultUserPlacement' ),
				'class_identifier' => $userClass->attribute( 'identifier' ),
				'creator_id'       => $ini->variable( 'UserSettings', 'UserClassID' ),
				'section_id'       => $ini->variable( 'UserSettings', 'DefaultSectionID' ),
				'remote_id'        => $remoteID,
				'attributes'       => $attributes
			)
		);

		if( isset( $attributes['image'] ) ) {
			unlink( $attributes['image'] );
		}

		if( $object instanceof eZContentObject === false ) {
			eZDebug::writeError( 'User`s object isn`t created.', 'NXC Social Networks Login' );
			return $module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );
		}
	}
}

if( $object instanceof eZContentObject ) {
	$user = eZUser::fetch( $object->attribute( 'id' ) );

	if( $user instanceof eZUser ) {
		$user->loginCurrent();

		if( $http->hasGetVariable( 'login_redirect_url' ) ) {
			$redirectURI = $http->getVariable( 'login_redirect_url' );
		} elseif( $http->hasSessionVariable( 'LastAccessesURI' ) && $http->sessionVariable( 'LastAccessesURI' ) ) {
			$redirectURI = $http->sessionVariable( 'LastAccessesURI' );
		} else {
			$redirectURI = $ini->variable( 'SiteSettings', 'DefaultPage' );
		}

		return $module->redirectTo( $redirectURI );
	}
}
?>
