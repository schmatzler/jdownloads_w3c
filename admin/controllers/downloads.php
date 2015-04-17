<?php
/**
 * @package jDownloads
 * @version 2.5  
 * @copyright (C) 2007 - 2013 - Arno Betz - www.jdownloads.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * 
 * jDownloads is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controlleradmin'); 

/**
 * jDownloads list downloads controller class.
 *
 */
class jdownloadsControllerdownloads extends JControllerAdmin
{
	/**
	 * Constructor
	 *
	 */
	function __construct()
	{
		parent::__construct();
        
        // Register Extra task
        $this->registerTask('delete',    'delete');
	}

                                                
    /**
     * Proxy for getModel.
     */
    public function getModel($name = 'download', $prefix = 'jdownloadsModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    } 
	
    
   /**
    * Removes an download item in db table.
    *
    * @return  void
    *
    */    
    public function delete()
    {
        $jinput = JFactory::getApplication()->input;
		$cid = $jinput->get('cid', 0, 'array');
		$error          = '';
        $message        = '';
        
        // run the model methode
        $model = $this->getModel();
        
        if(!$model->delete($cid))
        {
            $msg = JText::_( 'COM_JDOWNLOADS_ERROR_RESULT_MSG' );
            $error = 'error';
        } else {                             
            $this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
        }
        $this->setRedirect( 'index.php?option=com_jdownloads&view=downloads', $msg, $error);       
    }
    	
}
?>