<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksPublishType
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    20 Sep 2012
 **/

class nxcSocialNetworksPublishType extends eZWorkflowEventType
{
	const TYPE_ID = 'nxcsocialnetworkspublish';

	private static $classAttributes = array();
	private static $handlerNames    = array();

	public function __construct() {
		$this->eZWorkflowEventType( self::TYPE_ID, 'Publish to Social Networks' );
	}

	public function execute( $process, $event ) {
		eZDebug::createAccumulatorGroup( 'nxc_social_networks_publish', 'NXC Social Networks Publish' );

		$processParams = $process->attribute( 'parameter_list' );
		$classIDs      = unserialize( $event->attribute( 'data_text1' ) );
		$object        = eZContentObject::fetch( $processParams['object_id'] );
		if( in_array( $object->attribute( 'contentclass_id' ), $classIDs ) === false ) {
			return eZWorkflowType::STATUS_ACCEPTED;
		}

		$dataMap  = $object->attribute( 'data_map' );
		$hanlders = $event->attribute( 'handlers' );
		foreach( $hanlders as $hanlder ) {
			$options = $hanlder->getOptions();
			if(
				isset( $options['publish_only_on_create'] )
				&& (bool) $options['publish_only_on_create'] === true
				&& $object->attribute( 'current_version' ) != 1
			) {
				continue;
			}

			$classAttributeIDs = $hanlder->attribute( 'classattribute_ids' );
			foreach( $classAttributeIDs as $classAttributeID ) {
				$classAttribute = eZContentClassAttribute::fetch( $classAttributeID, false );
				if( $classAttribute['contentclass_id'] != $object->attribute( 'contentclass_id' ) ) {
					continue;
				}

				$attributeContent = false;
				foreach( $dataMap as $objectAttribute ) {
					if( $objectAttribute->attribute( 'contentclassattribute_id' ) == $classAttributeID ) {
						$attributeContent = $objectAttribute->attribute( 'content' );
						break;
					}
				}
				if( $attributeContent === false ) {
					continue;
				}

				$accumulator = 'nxc_social_networks_publish_to_' . $hanlder->attribute( 'type' );
				eZDebug::accumulatorStart(
					$accumulator,
					'nxc_social_networks_publish',
					'Publishing message to ' . $hanlder->attribute( 'name' )
				);

				try{
					$hanlder->publish( $object, $attributeContent );
				} catch( Exception $e ) {
					eZDebug::writeError( $e->getMessage(), 'NXC Social Networks Publish' );
				}

				eZDebug::accumulatorStop( $accumulator );
			}
		}

		return eZWorkflowType::STATUS_ACCEPTED;
	}

	public function typeFunctionalAttributes() {
		return array(
			'handlers',
			'available_handler_names',
			'contentclass_attribute_list',
			'affected_class_ids'
		);
	}

	public function attributeDecoder( $event, $attr ) {
		switch( $attr ) {
			case 'handlers': {
				return nxcSocialNetworksPublishHanlder::fetchList( $event->attribute( 'id' ) );
			}
			case 'available_handler_names': {
				if( count( self::$handlerNames ) === 0 ) {
					$types = nxcSocialNetworksPublishHanlder::getTypes();
					foreach( $types as $type => $hanlderClass ) {
						try {
							$handler = new $hanlderClass( array() );
							self::$handlerNames[ $type ] = $handler->attribute( 'name' );
							unset( $handler );
						} catch( Exception $e ) {}
					}
				}

				return self::$handlerNames;
			}
			case 'contentclass_attribute_list': {
				if( count( self::$classAttributes ) === 0 ) {
					self::$classAttributes = eZContentClassAttribute::fetchList(
						true,
						array(
							'data_type' => array(
								array(
									eZStringType::DATA_TYPE_STRING,
									eZTextType::DATA_TYPE_STRING
								)
							)
						)
					);
				}

				return self::$classAttributes;
			}
			case 'affected_class_ids': {
				return unserialize( $event->attribute( 'data_text1' ) );
			}
			default:
				return null;
		}
	}

	public function customWorkflowEventHTTPAction( $http, $action, $event ) {
		if( preg_match( '/add_handler_class_attribute_([0-9]+)/', $action, $matches ) ) {
			$handlerID = (int) $matches[1];
			$var = 'WorkflowEvent_event_nxcsocialnetworkspublish_handler_new_class_attributes' . $event->attribute( 'id' );
			if( $http->hasPostVariable( $var ) ) {
				$newAttributeIDs = (array) $http->postVariable( $var );

				if( isset( $newAttributeIDs[ $handlerID ] ) ) {
					try{
						$handler = nxcSocialNetworksPublishHanlder::fetch( $handlerID );
						$handler->addClassAttributeID( (int) $newAttributeIDs[ $handlerID ] );
						$handler->store();
					} catch( Exception $e ) {
						eZDebug::writeError( $e->getMessage() );
					}
				}
			} else {
				eZDebug::writeError( 'No new attribute selected' );
			}
		} else {
			switch( $action ) {
				case 'new_handler': {
					$var = 'WorkflowEvent_event_nxcsocialnetworkspublish_new_handler_' . $event->attribute( 'id' );
					if( $http->hasPostVariable( $var ) ) {
						$type = $http->postVariable( $var );

						try{
							$handler = nxcSocialNetworksPublishHanlder::newInstance( $type );
							$handler->setAttribute( 'workflow_event_id', $event->attribute( 'id' ) );
							$handler->store();
						} catch( Exception $e ) {
							eZDebug::writeError( $e->getMessage() );
						}
					} else {
						eZDebug::writeError( 'No publish handler selected' );
					}
					break;
				}
				case 'remove_handler': {
					$var = 'WorkflowEvent_event_nxcsocialnetworkspublish_remove_handler_' . $event->attribute( 'id' );
					if( $http->hasPostVariable( $var ) ) {
						$handler = nxcSocialNetworksPublishHanlder::fetch( $http->postVariable( $var ) );
						if(
							$handler instanceof nxcSocialNetworksPublishHanlder
							&& (int) $handler->attribute( 'workflow_event_id' ) === (int) $event->attribute( 'id' )
						) {
							$handler->remove();
						}
					} else {
						eZDebug::writeError( 'No publish handler selected' );
					}
					break;
				}
				case 'remove_handler_class_attribute': {
					$var = 'WorkflowEvent_event_nxcsocialnetworkspublish_remove_handler_class_attribute_' . $event->attribute( 'id' );
					if( $http->hasPostVariable( $var ) ) {
						$data        = explode( '|', $http->postVariable( $var ) );
						$handlerID   = $data[0];
						$attributeID = $data[1];
						$handler     = nxcSocialNetworksPublishHanlder::fetch( $handlerID );
						if(
							$handler instanceof nxcSocialNetworksPublishHanlder
							&& (int) $handler->attribute( 'workflow_event_id' ) === (int) $event->attribute( 'id' )
						) {
							$handler->removeClassAttributeID( $attributeID );
							$handler->store();
						}
					} else {
						eZDebug::writeError( 'No publish handler selected' );
					}
					break;
				}
				default:
					eZDebug::writeError( 'Unknown custom HTTP action: ' . $action );
			}
		}
	}

	public function fetchHTTPInput( $http, $base, $event ) {
		if( $http->hasPostVariable( 'StoreButton' ) === false ) {
			return true;
		}

		$affectedClassIDs = array();

		$optionNames = array( 'publish_only_on_create', 'include_url' );
		$options     = array();
		$var         = 'WorkflowEvent_event_nxcsocialnetworkspublish_handler_options';
		if( $http->hasPostVariable( $var ) ) {
			$options = (array) $http->postVariable( $var );
		}

		$handlers = $event->attribute( 'handlers' );
		foreach( $handlers as $handler ) {
			$handlerOptionNames = $optionNames;
			if( $handler->attribute( 'has_extra_options' ) ) {
				$handlerOptionNames = array_merge( $optionNames, $handler->getExtraOptionNames() );

			}

			foreach( $handlerOptionNames as $optionName ) {
				$handler->setOptions(
					$optionName,
					isset( $options[ $optionName ][ $handler->attribute( 'id' ) ] )
						? $options[ $optionName ][ $handler->attribute( 'id' ) ]
						: 0
				);
			}
			$handler->store();

			$handlerClassIDs = array();
			foreach( $handler->attribute( 'classattribute_ids' ) as $classAttrbitueID ) {
				$attribute = eZContentClassAttribute::fetch( $classAttrbitueID, false );
				if( is_array( $attribute ) ) {
					$handlerClassIDs[] = $attribute['contentclass_id'];
				}
				$affectedClassIDs = array_merge( $affectedClassIDs, $handlerClassIDs );
			}
			$event->setAttribute( 'data_text1', serialize( array_unique( $affectedClassIDs ) ) );
		}
	}
}

eZWorkflowEventType::registerEventType( nxcSocialNetworksPublishType::TYPE_ID, 'nxcSocialNetworksPublishType' );
?>
