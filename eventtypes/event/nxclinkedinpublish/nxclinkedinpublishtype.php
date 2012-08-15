<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcLinkedInPublishType
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    09 Dec 2010
 **/

class nxcLinkedInPublishType extends eZWorkflowEventType
{
	const TYPE_ID = 'nxclinkedinpublish';

	public static $eventParams = array(
		'classAttributes'     => 'data_text1',
		'publishOnlyOnCreate' => 'data_int1',
		'includeURL'          => 'data_int2'
	);

	public function __construct() {
		$this->eZWorkflowEventType( self::TYPE_ID, 'Publishes message to LinkedIn account' );

		$this->Attributes['current_class_id'] = 0;
	}

	public function execute( $process, $event ) {
		eZDebug::createAccumulatorGroup( 'nxc_linkedin', 'NXC LinkedIn' );

		$processParams = $process->attribute( 'parameter_list' );

		$object  = eZContentObject::fetch( $processParams['object_id'] );
		$dataMap = $object->attribute( 'data_map' );

		$iniAPI     = eZINI::instance( 'nxclinkedin.ini' );
		$connection = new LinkedIn(
			array(
				'appKey'      => $iniAPI->variable( 'LinkedinAPI', 'Key' ),
				'appSecret'   => $iniAPI->variable( 'LinkedinAPI', 'Secret' ),
				'callbackUrl' => null
			)
		);
		$iniOAuthToken = eZINI::instance( 'nxclinkedinaccesstoken.ini' );
		$connection->setTokenAccess(
			array(
				'oauth_token'        => $iniOAuthToken->variable( 'AccessToken', 'Token' ),
				'oauth_token_secret' => $iniOAuthToken->variable( 'AccessToken', 'Secret' )
			)
		);

		if(
			(bool) $event->attribute( self::$eventParams['publishOnlyOnCreate'] )
			&& $object->attribute( 'current_version' ) != 1
		) {
			return eZWorkflowType::STATUS_ACCEPTED;
		}

		$selectedClassAttributes = explode( ',', $event->attribute( self::$eventParams['classAttributes'] ) );
		foreach( $dataMap as $objectAttribute ) {
			if( in_array( $objectAttribute->attribute( 'contentclassattribute_id' ), $selectedClassAttributes ) ) {
				eZDebug::accumulatorStart( 'nxc_linkedin_publish', 'nxc_linkedin', 'Publishing message to LinkedIn' );

				$content = $objectAttribute->attribute( 'content' );
				$message = ( is_string( $content ) && !empty( $content ) ) ? $content : false;

				if( $message !== false ) {
					$share = array(
						'title'       => $object->attribute( 'name' ),
						'description' => $message
					);
					if( (bool) $event->attribute( self::$eventParams['includeURL'] ) ) {
						$url = $object->attribute( 'main_node' )->attribute( 'url_alias' );
						eZURI::transformURI( $url, true, 'full' );
						$share['submitted-url'] = $url;
					}

					$response = $connection->share( 'new', $share, false );
					if( (bool) $response['success'] === false ) {
						eZDebug::writeError( $response['error'], 'NXC NXC LinkedIn Publish' );
					}
				}

				eZDebug::accumulatorStop( 'nxc_linkedin_publish' );
			}
		}

		return eZWorkflowType::STATUS_ACCEPTED;
	}

	public function attributes() {
		return array_merge(
			array(
				'contentclass_list',
				'contentclass_attribute_list',
				'current_class_id'
			),
			eZWorkflowEventType::attributes()
		);
	}

	public function hasAttribute( $attr ) {
		return in_array( $attr, $this->attributes() );
	}

	public function attribute( $attr ) {
		switch( $attr ) {
			case 'contentclass_list': {
				return eZContentClass::fetchList( eZContentClass::VERSION_STATUS_DEFINED, true );
			}
			case 'contentclass_attribute_list': {
				$classList = $this->attribute( 'contentclass_list' );
				if( $this->attribute( 'current_class_id' ) !== 0 ) {
					$classID = $this->attribute( 'current_class_id' );
				} else {
					$classID = isset( $classList[0] ) ? $classList[0]->attribute( 'id' ) : false;
				}

				return is_numeric( $classID ) ? eZContentClassAttribute::fetchListByClassID( $classID ) : array();
			}
			default:
				return eZWorkflowEventType::attribute( $attr );
		}
	}

	public function customWorkflowEventHTTPAction( $http, $action, $event ) {
		$selectedClassAttributes = explode( ',', $event->attribute( self::$eventParams['classAttributes'] ) );
		if( $selectedClassAttributes[0] == '' ) {
			unset( $selectedClassAttributes[0] );
		}

		switch( $action ) {
			case 'load_class_attribute_list': {
				$var = 'WorkflowEvent_event_nxclinkedinpublish_class_' . $event->attribute( 'id' );
				if( $http->hasPostVariable( $var ) ) {
					$this->setAttribute( 'current_class_id', $http->postVariable( $var ) );
				} else {
					eZDebug::writeError( 'No class selected' );
				}
				break;
			}
			case 'new_class_attribute': {
				$var = 'WorkflowEvent_event_nxclinkedinpublish_class_attribute_' . $event->attribute( 'id' );
				if( $http->hasPostVariable( $var ) ) {
					$selectedClassAttributes[] = (int) $http->postVariable( $var );
					$selectedClassAttributes   = array_unique( $selectedClassAttributes );
					$event->setAttribute( self::$eventParams['classAttributes'], implode( ',', $selectedClassAttributes ) );
				} else {
					eZDebug::writeError( 'No class attribute selected' );
				}
			}
			case 'remove_class_attribute': {
				$var = 'WorkflowEvent_event_nxclinkedinpublish_remove_class_attribute_' . $event->attribute( 'id' );
				if( $http->hasPostVariable( $var ) ) {
					$removeAttributeIDs = (array) $http->postVariable( $var );
					foreach( $selectedClassAttributes as $index => $classAttributeID ) {
						if( in_array( $classAttributeID, $removeAttributeIDs ) ) {
							unset( $selectedClassAttributes[ $index ] );
						}
					}
					$event->setAttribute( self::$eventParams['classAttributes'], implode( ',', $selectedClassAttributes ) );
				} else {
					eZDebug::writeError( 'No class attributes selected' );
				}
				break;
			}
			default:
				eZDebug::writeError( 'Unknown custom HTTP action: ' . $action );
		}
	}

	public function fetchHTTPInput( $http, $base, $event ) {
		if( $http->hasPostVariable( 'StoreButton' ) ) {
			$publishOnlyOnCreate = $base . '_data_nxclinkedinpublish_publish_only_on_create_' . $event->attribute( 'id' );
			$event->setAttribute( self::$eventParams['publishOnlyOnCreate'], (int) $http->hasPostVariable( $publishOnlyOnCreate ) );

			$includeURL = $base . '_data_nxclinkedinpublish_include_url_' . $event->attribute( 'id' );
			$event->setAttribute( self::$eventParams['includeURL'], (int) $http->hasPostVariable( $includeURL ) );
		}
	}
}

eZWorkflowEventType::registerEventType( nxcLinkedInPublishType::TYPE_ID, 'nxcLinkedInPublishType' );
?>
