<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2018 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
echo '<?x'.'ml version="1.0" encoding="utf-8"?>';
?>
<AuthorizationResponse xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
	<IsAuthorized>false</IsAuthorized>
	<Message><![CDATA[<?php echo $this->message; ?>]]></Message>
</AuthorizationResponse>