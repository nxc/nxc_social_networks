#!/usr/bin/env php
<?php
/**
 * @package nxcSocialNetworks
 * @script  nxcLinkShortenTest.php
 * @author  Brookins Consulting <info@brookinsconsulting.com>
 * @date    01 Sep 2014
 **/

/** Script autoloads initialization **/

require 'autoload.php';

/** Script startup and initialization **/

$cli = eZCLI::instance();
$script = eZScript::instance( array( 'description' => ( "NXC Social Networks Link Shortening Service Test Script\n\n" .
														"nxcLinkShortenTest.php --url=https://google.com --service=goo.gl" ),
									 'use-session' => false,
									 'use-modules' => true,
									 'use-extensions' => true,
									 'user' => true ) );

$script->startup();

$options = $script->getOptions( "[url;][service;][list-handlers;]",
								"",
								array( 'url' => 'Use this parameter to specify which url string to shorten. Example: ' . "'--url=https://google.com'" . ' is an optional parameter which defaults to string https://google.com',
									   'service' => 'Use this parameter to specify which url shortening service handler to use. Use the ' . "'--list-handlers'" . ' parameter for a list of handlers available. Example: ' . "'--service=goo.gl'" . ' is an optional parameter which defaults to value specified by the LinkShortenHandlerDefault ini setting ',
									   'list-handlers' => 'Use this parameter to display which url shortening service handlers are available for use. Example: ' . "'--list-handlers'" . ' is an optional parameter which defaults to false'),
								false,
								array( 'user' => true ) );
$script->initialize();

/** Test for required script arguments **/

$url = isset( $options['url'] ) ? $options['url'] : 'https://google.com';

$service = ( isset( $options['service'] ) && $options['service'] != ''
			 && $options['service'] != 1 ) ? $options['service'] : eZINI::instance( 'nxcsocialnetworks.ini' )->variable( 'General', 'LinkShortenHandlerDefault' );

$listHandlers = isset( $options['list-handlers'] ) ? true : false;

/** Test to determin action to take: display handlers or shorten url **/

if( $listHandlers )
{
	/** Dispaly a list of all url shortening service handlers available **/
	$handlers = eZINI::instance( 'nxcsocialnetworks.ini' )->variable( 'General', 'LinkShortenHandlers' );

	$cli->output( "\nUrl shortening service handlers available:\n" );

	foreach( $handlers as $handlerNameShort => $handlerClassName ) {
		$cli->output( 'Service: ' . $handlerNameShort . "\n" );
	}
} else {
	/** Perform url service shorten test **/

	$shortUrl = nxcSocialNetworksLinkShortenHandler::instance( $service )->shorten( $url );

	$cli->output( "\nUrl shortening test complete!\n" );

	$cli->output( 'Service: ' . $service . "\n" );

	$cli->output( 'Url: ' . $url . "\n" );

	$cli->output( 'Short Url: ' . $shortUrl . "\n" );
}

/** Shutdown script **/
$script->shutdown();

?>