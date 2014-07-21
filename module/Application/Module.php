<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
		
		$sm = $e->getApplication()->getServiceManager();
		
		$userService = $sm->get('zfcuser_user_service');
		$userService->getEventManager()->attach('register.post', 
		function(\Zend\EventManager\Event $e) use ($sm) {
			$userForm = $e->getParam('user');
			$objectManager = $sm->get('doctrine.entitymanager.orm_default');
			$user = $objectManager->getRepository('Application\Entity\User')->find($userForm->getId());
					 
			// role : user
			$role = $objectManager->getRepository('Application\Entity\Role')->find(1);
			$user->addRole($role);
			
			$objectManager->flush();
		});	
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
