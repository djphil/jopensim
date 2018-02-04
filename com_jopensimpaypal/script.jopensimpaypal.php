<?php
/*
 * @component jOpenSimPayPal
 * @copyright Copyright (C) 2017 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

class com_jOpenSimPayPalInstallerScript {
	public function preflight( $type, $parent ) {
		$jversion = new JVersion();
		//check for minimum requirement
		// abort if the current Joomla release is older
		if( version_compare( $jversion->getShortVersion(), '3.4', 'lt' ) ) {
			Jerror::raiseWarning(null, 'Cannot install jOpenSimPayPal in a Joomla release prior to 3.4');
			return false;
		}
		$jopensim = JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_opensim";
		if(!is_dir($jopensim)) { // is jOpenSim installed?
			Jerror::raiseWarning(null, "This component requires jOpenSim! Please download and install the latest version at <a href='http:/"."/jopensim.com' target='_blank'>jopensim.com</a>!");
			return FALSE;
		} else { // lets look for the version
			$opensimclasspath = JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_opensim".DIRECTORY_SEPARATOR."includes".DIRECTORY_SEPARATOR."opensim.class.php";
			require_once($opensimclasspath);
			$jopensimVersion = opensim::$version;
			if(strnatcmp($jopensimVersion,"0.3.0") < 0) {
				Jerror::raiseWarning(null, "This component requires at least jOpenSim v0.3.0! Please download and install the latest version at <a href='http:/"."/jopensim.com' target='_blank'>jopensim.com</a>!");
				return FALSE;
			}
		}
	}
}
?>