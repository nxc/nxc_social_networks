#!/usr/bin/env php
<?php

require_once 'autoload.php';

$handler = new nxcSocialNetworksPublishHandlerTwitter( array() );

$url = 'http://blog.nxcgroup.com/';

$shortUrl = $handler->shorten( $url );

print_r( 'Url: ' . $url ."\n");
print_r( 'Short: ' . $shortUrl ."\n");

?>