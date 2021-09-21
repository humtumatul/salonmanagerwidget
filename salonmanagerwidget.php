<?php
/**
 * @copyright	Copyright (c) 2021 salonmanagerwidget. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * system - Salon Manager Widget Plugin
 *
 * @package		Joomla.Plugin
 * @subpakage	salonmanagerwidget.SalonManagerWidget
 */
class plgsystemSalonManagerWidget extends JPlugin {

	/**
	 * Constructor.
	 *
	 * @param 	$subject
	 * @param	array $config
	 */
	function __construct(&$subject, $config = array()) {
		// call parent constructor
		parent::__construct($subject, $config);
	}

	 public function onAfterRender()
  {
      $app = JFactory::getApplication();
      
      $id=$this->params->get('salon_widget_id');
      $key=$this->params->get('salon_widget_key');
      // only insert the script in the frontend
      if ($app->isClient('site')) {

          // retrieve all the response as an html string
          $html = $app->getBody();

          // replace the closing body tag with your scripts appending to the closing body tag
          $scripts = [
              'https://widgets.salonmanager.com/loader.js',
            
          ];

          $tags = "";
          foreach($scripts as $s){
              $tags .= '<script src="' . $s . '" data-sm="' . $key . '" ></script>';
          }

          $html = str_replace('</body>',$tags . '</body>',$html);

          // override the original response
          $app->setBody($html);
        }
    }
    
public function onContentPrepareForm($form, $data)
{
    $db = JFactory::getDBO();
	$session = JFactory::getSession();
	$widgetid = $session->get('widgetid');
	if($widgetid!=""){
	$extension  = new JTableExtension($db);
	$ext_id = $this->getPlgId(); 

	// get the existing extension data
	$extension->load($ext_id);
	$this->params->set('salon_widget_key', $widgetid);
	$extension->bind( array('params' => $this->params->toString()) );

	// check and store 
	if (!$extension->check()) {
		$this->setError($extension->getError());
		return false;
	}
	if (!$extension->store()) {
		$this->setError($extension->getError());
		return false;
	}
	}

}
private function getPlgId(){
        // stupid hack since there doesn't seem to be another way to get plugin id
        $db = JFactory::getDBO();
        $sql = 'SELECT `extension_id` FROM `#__extensions` WHERE `element` = "salonmanagerwidget" AND `folder` = "system"'; // check the #__extensions table if you don't know your element / folder
        $db->setQuery($sql);
        if( !($plg = $db->loadObject()) ){
            return false;
        } else {
            return (int) $plg->extension_id;
        }
    }

}

