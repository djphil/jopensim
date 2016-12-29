<?php
if(!defined("DS")) define("DS",DIRECTORY_SEPARATOR);
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2016 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$document = JFactory::getDocument();
$document->addStyleSheet('components/com_opensim/assets/opensim.css');

// See if we can get the icon from here :)
$style = ".menu-icon-16-opensim {background-image: url(\"components/com_opensim/assets/images/icon-16-opensim.png\") no-repeat !important; background-position:left !important;}";
$document->addStyleDeclaration($style);


global $mainframe;
$mainframe = JFactory::getApplication();
 
// require the basic opensim class
require_once(JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'opensim.class.php');

// Require the base controller
 
require_once( JPATH_COMPONENT.DIRECTORY_SEPARATOR.'controller.php' );
$controller = JFactory::getApplication()->input->get('view');

// Require specific controller if requested
if($controller = JFactory::getApplication()->input->get('view')) {
    $path = JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$controller.'.php';
    if (file_exists($path)) {
        require_once $path;
    } else {
        $controller = '';
    }
}
 
// Create the controller
$classname    = 'OpenSimController'.$controller;
$controller   = new $classname();
/*$controller->opensim = $opensim;*/

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();
