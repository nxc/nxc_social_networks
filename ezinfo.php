<?php
/**
 * @package nxcSocialNetworks
 * @class   nxc_social_networksInfo
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    15 Aug 2012
 **/

class nxc_social_networksInfo
{
	public static function info() {
		return array(
			'Name'      => '<a href="http://projects.ez.no/nxc_social_networks">NXC Social Networks Integration</a>',
			'Version'   => '1.1',
			'Author'    => 'SD / NXC International SA / Brookins Consulting',
			'Copyright' => array( 'Copyright &copy; ' . date( 'Y' ). ' <a href="http://nxc.no" target="blank">NXC Consulting</a>',
								  'Copyright &copy; 1999 - ' . date( 'Y' ) . ' <a href="http://brookinsconsulting.com" target="blank">Brookins Consulting</a>' ),
			'License' => "GNU General Public License",
			'info_url' => "https://github.com/nxc/nxc_social_networks"
		);
	}
}
?>