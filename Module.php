<?php

namespace ZendModuleTest;

use Zend\ModuleManager\ModuleManager;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManager;
use Zend\Mvc\ModuleRouteListener;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface, ServiceProviderInterface
{

	public function onBootstrap (\Zend\Mvc\MvcEvent $e)
	{
		/* @formatter:off */


		/* @var $application \Zend\Mvc\Application */
		$application = $e->getApplication();

		/* @var $serviceManager \Zend\ServiceManager\ServiceManager */
		$serviceManager = $application->getServiceManager();

		/* @var $em \Zend\EventManager\EventManager */
		$eventManager = $application->getEventManager();

		/* @var $sharedManager \Zend\EventManager\SharedEventManager */
		$sharedManager = $eventManager->getSharedManager();

		/**
		 * These are the event hooks for the Zend\Mvc\Application
		 */
		$eventManager->attach(array('route','dispatch','render','finish'), function  ($event)
		{
			/* @var $event \Zend\Mvc\MvcEvent */
			/* @var $target \Zend\Mvc\Application */
			/* @var $route \Zend\Mvc\Router\Http\RouteMatch */

			$target = $event->getTarget();
			\Zend\Debug\Debug::dump(__NAMESPACE__ . ' ' . get_class($target) . ' ' . $event->getName());

			// change the template of all the modules
			// $viewModel = $event->getViewModel();
			// $viewModel->setTemplate('layout/error');

			//$route = $target->getMvcEvent()->getRouteMatch();
		});

		/**
		 * These are the event hooks for the Zend\Mvc\Controller\AbstractActionController
		 */
		$sharedManager->attach('Zend\Mvc\Controller\AbstractActionController', array('dispatch'), function($event)
		{
			/* @var $event \Zend\Mvc\MvcEvent */
			/* @var $target \Zend\Mvc\Controller\AbstractActionController */

			$target = $event->getTarget();
			\Zend\Debug\Debug::dump(__NAMESPACE__ . ' ' . get_class($target) . ' ' . $event->getName());

			//$target->getEvent()->getRouteMatch()->setParam('company_id', 100001);
		});


		/**
		 * These are the event hooks for the Zend\View\View
		 */
		$viewManager = $serviceManager->get('ViewManager')->getView()->getEventManager();
		$viewManager->attach(array('renderer','response'), function  ($event)
		{
			/* @var $event \Zend\View\ViewEvent */
			/* @var $target \Zend\View\View */

			$target = $event->getTarget();
			\Zend\Debug\Debug::dump(__NAMESPACE__ . ' ' . get_class($target) . ' ' . $event->getName());
		});

		/* @formatter:on */
	}

	/**
	 * Get core configuration array
	 *
	 * @return array
	 */
	public function getConfig ()
	{
		return include __DIR__ . '/config/module.config.php';
	}

	/**
	 * Service Manager config for models, tables, etc
	 *
	 * @return array
	 */
	public function getServiceConfig ()
	{
		return include __DIR__ . '/config/module.service.php';
	}

	/**
	 * Service Manager config for the Controllers
	 *
	 * @return array
	 */
	public function getControllerConfig ()
	{
		return include __DIR__ . '/config/module.controller.php';
	}

	/**
	 * Autoloader configuration
	 *
	 * @see \Zend\ModuleManager\Feature\AutoloaderProviderInterface::getAutoloaderConfig()
	 */
	public function getAutoloaderConfig ()
	{
		return array(
			'Zend\Loader\ClassMapAutoloader' => array(
				__DIR__ . '/autoload_classmap.php'
			),
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
					// if we're in a namespace deeper than one level we need to fix the \
					// in the path
					__NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/', __NAMESPACE__)
				)
			)
		);
	}
}