<?php /* #?ini charset="utf-8"?

[General]
# It could be email or remote_id
UniqueUserIdentifier=email

OAuth2[facebook]=nxcSocialNetworksOAuth2Facebook
OAuth2[twitter]=nxcSocialNetworksOAuth2Twitter
OAuth2[linkedin]=nxcSocialNetworksOAuth2LinkedIn
OAuth2[google]=nxcSocialNetworksOAuth2Google
OAuth2[instagram]=nxcSocialNetworksOAuth2Instagram

LoginHandlers[facebook]=nxcSocialNetworksLoginHandlerFacebook
LoginHandlers[twitter]=nxcSocialNetworksLoginHandlerTwitter
LoginHandlers[linkedin]=nxcSocialNetworksLoginHandlerLinkedIn
LoginHandlers[google]=nxcSocialNetworksLoginHandlerGoogle

PublishHandlers[facebook]=nxcSocialNetworksPublishHandlerFacebook
PublishHandlers[twitter]=nxcSocialNetworksPublishHandlerTwitter
PublishHandlers[linkedin]=nxcSocialNetworksPublishHandlerLinkedIn

LinkShortenHandlerToUse=is.gd
LinkShortenHandlers[is.gd]=nxcSocialNetworksLinkShortenHandlerIsgd
LinkShortenHandlers[to.ly]=nxcSocialNetworksLinkShortenHandlerToly
LinkShortenHandlers[v.gd]=nxcSocialNetworksLinkShortenHandlerVgd
LinkShortenHandlers[tinyurl.com]=nxcSocialNetworksLinkShortenHandlerTinyurl
LinkShortenHandlers[goo.gl]=nxcSocialNetworksLinkShortenHandlerGoogl
LinkShortenHandlers[bit.ly]=nxcSocialNetworksLinkShortenHandlerBitly
LinkShortenHandlers[ow.ly]=nxcSocialNetworksLinkShortenHandlerOwly

# New application can be created at https://developers.facebook.com/apps
# Set the following application settings
# - Basic info > App Domains = yoursite.com
# - Website with Facebook Login > Site URL = yoursite.com
[FacebookApplication]
Key=143260445738456
Secret=a00a3aec0c7bebb97b57f93800b2dab1

# New application can be created at https://dev.twitter.com/apps/new
# Set the following application settings
# - Access level = Read and write
[TwitterApplication]
Key=cRACdvBvd74Bw4gtqtlQ
Secret=jD81UH24wXP9f8UscgQbqaqVzbHIxKagadb5BZCwcg

# New application can be created at https://www.linkedin.com/secure/developer
# Set the following application settings
# - Application Info > Website URL = yoursite.com
[LinkedInApplication]
Key=54b5bpmfccwh
Secret=AcfsXn0pKjntiXzV

$ 1. Go to https://code.google.com/apis/console > Services and enable "Google+ API"
# 2. Go to https://code.google.com/apis/console > API Access  and click on "Create an OAuth2 client ID"
# Set the following application setting
# - Client ID for web applications > Redirect URIs:
# http://path_to_your_ezp_admin_siteaccess/nxc_social_network_token/get_access_token/google
# http://yoursite.com/nxc_social_network_login/callback/google ogle
[GoogleApplication]
Key=888150503182-2cqci1lfo3tjacej6mnd0rk95nkur7u7.apps.googleusercontent.com
Secret=iM6a7XhOF1fClnUoNvyEOqkH

# http://instagram.com/developer/clients/manage/
# 'CLIENT ID' and	'CLIENT SECRET' are 'Key' and 'Secret'
[InstagramApplication]
Key=3c83de65fba447a0a2966f2db0645d9f
Secret=ed8b5921241845a1b8c325e1dacf4aaf

[LinkShortenHandlerGoogl]
ApiKey=Dxx-MxSoVcbDXTbZ3FPgn7nQAIzaSyDKdBUy54T

[LinkShortenHandlerBitly]
GenericAccessToken=63e589dd207b324aa19aa9169fa7d7be14c103ca

[LinkShortenHandlerOwly]
ApiKey=CfsrIVeqilTuOYTNtrl2v

*/ ?>
