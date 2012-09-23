{ezcss_require( array( 'nxc_social_networks.css' ) )}

{if $error}
<div class="message-error">
	<h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span> {$error}</h2>
</div>
{/if}

{if $connected}
<div class="message-feedback">
	<h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span> {'Account connected.'|i18n( 'extension/nxc_social_networks' )}</h2>
</div>
{/if}

<div class="context-block">

	<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
		<h1 class="context-title">{'Connect social network account:'|i18n( 'extension/nxc_social_networks' )}</h1>
		<div class="header-subline"></div>
	</div></div></div></div></div></div>

	<div class="box-ml"><div class="box-mr"><div class="box-content">

		<div class="context-toolbar">
			<div class="block"></div>
		</div>

		<div class="content-navigation-childlist">
			<div class="nxc-social-networks-icon-container">
				{def $types = ezini( 'General', 'OAuth2', 'nxcsocailnetworks.ini' )}
				{foreach $types as $type => $class}
				<a id="nxc-{$type}-connect" href="{concat( 'nxc_social_network_token/authorize/', $type )|ezurl( 'no' )}">
					<img alt="{'Connect account'|i18n( 'extension/nxc_social_networks' )}" src="{concat( $type, '/connect.png' )|ezimage( 'no' )}" />
				</a>
				{/foreach}
				{undef $types}
			</div>
		</div>

		<div class="context-toolbar">
			<div class="block"></div>
		</div>

	</div></div></div>

	<div class="controlbar">
		<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
			<div class="block"></div>
		</div></div></div></div></div></div>
	</div>

</div>