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
		/* @var $application \Zend\Mvc\Application */
		$application = $e->getApplication();

		/* @var $serviceManager \Zend\ServiceManager\ServiceManager */
		$serviceManager = $application->getServiceManager();

		/**
		 * These are the event hooks for the Zend\Mvc\Application
		 */
		/* @var $em \Zend\EventManager\EventManager */
		$eventManager = $application->getEventManager();
		$eventHooks = array(
			'route',
			'dispatch',
			'render',
			'finish'
		);
		$eventManager->attach($eventHooks, function  ($evt)
		{
			/* @var $evt \Zend\Mvc\MvcEvent */
			\Zend\Debug\Debug::dump('\Zend\Mvc\Application ' . $evt->getName());

			// change the template of all the modules
			// $evt->getViewModel()->setTemplate('layout/1column');
		});

		/**
		 * These are the event hooks for the Zend\View\View
		 */
		/* @var $view \Zend\View\View */
		$view = $serviceManager->get('ViewManager')
			->getView();
		$vm = $view->getEventManager();
		$eventHooks = array(
			'renderer',
			'response'
		);
		$vm->attach($eventHooks, function  ($eventView)
		{
			/* @var $eventView \Zend\View\ViewEvent */
			\Zend\Debug\Debug::dump('\Zend\View\View ' . $eventView->getName());
		});
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