<?php
/**
 *
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 1/9/14
 * @package default
 **/

/**
 * Class SwiftOtter_Blog_Model_Observer_Router
 * This class is acting as a hybrid router and observer class. This may not be the best practice, so I am open for ideas
 * to bring this to best practice standards.
 */

class SwiftOtter_Blog_Model_Observer_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    const FRONT_NAME = 'blog';
    protected $_allowedActions = array('subscribe');

    public function controllerFrontInitRouters($observer)
    {
        /* @var $front Mage_Core_Controller_Varien_Front */
        $front = $observer->getEvent()->getFront();

        $front->addRouter('blog', $this);
    }

    public function match(Zend_Controller_Request_Http $request)
    {
        $identifier = explode('/', trim($request->getPathInfo(), '/'));
        $frontName = self::FRONT_NAME;
        $actionName = "view";

        if ($identifier[0] !== $frontName) {
            return false;
        }

        if (in_array($request->getPost('action'), $this->_allowedActions)) {
            $actionName = $request->getPost('action');
        }

        $request->setModuleName('blog')
            ->setControllerName('index')
            ->setActionName($actionName)
            ->setParam('path', $request->getPathInfo());


        $request->setAlias(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS, implode('/', $identifier));

        return true;
    }

    protected function _getPrettyFrontName()
    {
        return Mage::helper('SwiftOtter_Blog')->getPrettyFrontName();
    }

    protected function _formatValue($input)
    {
        return strtolower(preg_replace('/[^A-Z0-9]/ui', '', $input));
    }

}