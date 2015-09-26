<?php
/*
 * Copyright 2014 Empodex PHP Framework.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @copyright 2014-2015 Empoddy Labs.
 * @author Prabhat Shankar <prabhat.singh88[at]gmail.com>
 */

class AdminDashboardController extends EFCController {

	var $tpl_name = 'admin_dashboard.tpl';

	public function __construct() {


	}
	

	public function control() {

        $config = Config::getInstance();
        $this->setViewTemplate($this->tpl_name);
        $this->addToView('enable_bootstrap', true);
        $this->addToView('developer_log', $config->getValue('is_log_verbose'));
        $this->addToView('efc_application_url', Utils::getApplicationURL());

        if ($this->shouldRefreshCache() ) {
            if (isset($_GET['u']) && isset($_GET['n']) && isset($_GET['d']) && isset($_GET['s'])) {
                $this->displayIndividualInsight();
                if (isset($_GET['share'])) {
                    $this->addToView('share_mode', true);
                }
            } else {
                if (!$this->displayPageOfInsights()) {
                    $controller = new LoginController(true);
                    return $controller->go();
                }
            }
            if ($this->isLoggedIn()) {
                //Populate search dropdown with service users and add thinkup_api_key for desktop notifications.
                $owner_dao = DAOFactory::getDAO('OwnerDAO');
                $owner = $owner_dao->getByEmail($this->getLoggedInUser());
                $this->addToView('efc_api_key', $owner->api_key);
                $this->addHeaderJavaScript('assets/js/notify-insights.js');

                $instance_dao = DAOFactory::getDAO('InstanceDAO');
                $instances = $instance_dao->getByOwnerWithStatus($owner);
                $this->addToView('instances', $instances);
                $saved_searches = array();
                if (sizeof($instances) > 0) {
                    $instancehashtag_dao = DAOFactory::getDAO('InstanceHashtagDAO');
                    $saved_searches = $instancehashtag_dao->getHashtagsByInstances($instances);
                }
                $this->addToView('saved_searches', $saved_searches);

                //Start off assuming connection doesn't exist
                //$connection_status = array('facebook'=>'inactive', 'twitter'=>'inactive', 'instagram'=>'inactive');
                foreach ($instances as $instance) {

                }
                $this->addToView('connection_status', $connection_status['efc']);
            }
        }

        $this->addToView('tpl_path', EFC_WEBAPP_PATH.'plugins/insightsgenerator/view/');
        if ($config->getValue('image_proxy_enabled') == true) {
            $this->addToView('image_proxy_sig', $config->getValue('image_proxy_sig'));
        }
        return $this->generateView();
    }

	}


}