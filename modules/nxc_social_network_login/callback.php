<?php
/**
 * @package nxcSocialNetworks
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    16 Sep 2012
 **/

$module = $Params['Module'];
$http   = eZHTTPTool::instance();
$ini    = eZINI::instance();

// Handling cancel button
if(
	$http->hasGetVariable( 'denied' )
	|| (
		$http->hasGetVariable( 'oauth_problem' )
		&& $http->getVariable( 'oauth_problem' ) == 'user_refused'
	) || (
		$http->hasGetVariable( 'error' )
		&& $http->getVariable( 'error' ) == 'access_denied'
	)
) {
	return $module->redirectTo( '/' );
}

// Get handler
try{
	$hanlder = nxcSocialNetworksLoginHanlder::getInstanceByType( $Params['type'] );
} catch( Exception $e ) {
	eZDebug::writeError( $e->getMessage(), 'NXC Social Networks Login' );
	return $module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );
}
// Get user`s remote ID
try{
	$remoteID = $hanlder->getUserRemoteID();
} catch( Exception $e ) {
	eZDebug::writeError( $e->getMessage(), 'NXC Social Networks Login' );
	return $module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );
}
// Get user`s attributes
try{
	$attributes = $hanlder->getUserData();
} catch( Exception $e ) {
	eZDebug::writeError( $e->getMessage(), 'NXC Social Networks Login' );
	return $module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );
}

// Trying to fetch current user from eZ Publish
$object = false;
$uniqueIdentifier = nxcSocialNetworksLoginHanlder::getUniqueIdentifier();
if( $uniqueIdentifier == 'email' ) {
	$account = explode( '|', $attributes['user_account'] );
	if( isset( $account[1] ) ) {
		$user = eZUser::fetchByEmail( $account[1] );
		if( $user instanceof eZUser ) {
			$object = $user->attribute( 'contentobject' );
		}
	}
} else {
	$object = eZContentObject::fetchByRemoteID( $remoteID );
}

if( $object instanceof eZContentObject === false ) {
	// There is no eZ publish user, so we are creating one
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
			'remote_id'        => $uniqueIdentifier == 'remote_id' ? $remoteID : null,
			'attributes'       => $attributes
		)
	);

	if( $object instanceof eZContentObject === false ) {
		eZDebug::writeError( 'User`s object isn`t created.', 'NXC Social Networks Login' );
		return $module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );
	}
} else {
	// There is also eZ Publish user, so we are updating it, if it is needed
	$isUpdateNeeded = false;
	$dataMap        = $object->attribute( 'data_map' );
	// We are not updating user_account attribute
	unset( $attributes['user_account'] );

	foreach( $attributes as $identifier => $value ) {
		if( isset( $dataMap[ $identifier ] ) ) {
			$storedContent = $dataMap[ $identifier ]->toString();
			if( $identifier == 'image' ) {
				// We are comparing image sizes (stored in the eZ Publish vs social media avatar)
				$storedContent = explode( '|', $storedContent );
				$storedFile    = eZClusterFileHandler::instance( $storedContent[0] );
				if( $storedFile->metaData['size'] !== filesize( $attributes['image'] ) ) {
					$isUpdateNeeded = true;
					break;
				}
			} else {
				// We are comparing the content of rest attributes
				if( $storedContent != $value ) {
					$isUpdateNeeded = true;
					break;
				}
			}
		}
	}

	if( $isUpdateNeeded ) {
		// User should be logged in before update his profile
		$user = eZUser::fetch( $object->attribute( 'id' ) );
		if( $user instanceof eZUser ) {
			$user->loginCurrent();
		}

		eZContentFunctions::updateAndPublishObject(
			$object,
			array( 'attributes' => $attributes )
		);
	}
}

// Removing social media avatar (it was stored locally)
if( isset( $attributes['image'] ) ) {
	unlink( $attributes['image'] );
}

// Logging in into eZ Publish
if( $object instanceof eZContentObject ) {
	$user = eZUser::fetch( $object->attribute( 'id' ) );

	if( $user instanceof eZUser ) {
		$user->loginCurrent();

		if( $http->hasGetVariable( 'login_redirect_url' ) ) {
			$redirectURI = $http->getVariable( 'login_redirect_url' );
		} elseif( $http->hasGetVariable( 'state' ) ) {
			$redirectURI = base64_decode( $http->getVariable( 'state' ) );
		} elseif( $http->hasSessionVariable( 'LastAccessesURI' ) && $http->sessionVariable( 'LastAccessesURI' ) != "" ) {
			$redirectURI = $http->sessionVariable( 'LastAccessesURI' );
		} elseif($_SERVER['HTTP_REFERER'] != '' ) {
			$redirectURI = $_SERVER['HTTP_REFERER'];
		} else {
			$redirectURI = $ini->variable( 'SiteSettings', 'DefaultPage' );
		}

		return $module->redirectTo( $redirectURI );
	}
}
?>
