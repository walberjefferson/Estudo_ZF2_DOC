<?php

namespace Livraria;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use Livraria\Service\Categoria as CategoriaService;
use Livraria\Service\Livro as LivroService;
use LivrariaAdmin\Form\Livro as LivroFrm;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
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
                    __NAMESPACE__.'Admin' => __DIR__ . '/src/' . __NAMESPACE__.'Admin',
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig(){

        return array(
            'factories' => array(
                'Livraria\Service\Categoria' => function($service){
                    return new CategoriaService($service->get('Doctrine\ORM\EntityManager'));
                },
                'Livraria\Service\Livro' => function($service){
                    return new LivroService($service->get('Doctrine\ORM\EntityManager'));
                },
                'LivrariaAdmin\Form\Livro' => function($service) {
                    $em = $service->get('Doctrine\ORM\EntityManager');
                    $repository = $em->getRepository('Livraria\Entity\Categoria');
                    $categorias = $repository->fetchPairs();
                    return new LivroFrm(null, $categorias);
                },

            ),
        );
    }
}
