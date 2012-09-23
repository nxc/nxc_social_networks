{ezcss_require( array( 'nxc_social_networks.css' ) )}
<div class="nxc-social-networks-login">
{def $login_handlers = ezini( 'General', 'LoginHandlers', 'nxcsocailnetworks.ini' )}
{foreach $login_handlers as $type => $handler}
<a class="nxc-{$type}-signin" href="{concat( 'nxc_social_network_login/redirect/', $type )|ezurl( 'no' )}">{'Sign in'|i18n( 'extension/nxc_social_networks' )}</a>
{/foreach}
</div>
{undef $login_handlers}
