<?php

namespace FriendsOfBehat\PerformanceExtension\ServiceContainer;

use Behat\Testwork\Call\Handler\RuntimeCallHandler;
use Behat\Testwork\Call\ServiceContainer\CallExtension;
use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use FriendsOfBehat\PerformanceExtension\Testwork\Call\Handler\RuntimeVariadicsCallHandler;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class PerformanceExtension implements Extension
{
    /**
     * {@inheritdoc}
     */
    public function getConfigKey()
    {
        return 'fob_performance';
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ExtensionManager $extensionManager)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $definition = new Definition(RuntimeVariadicsCallHandler::class, [E_ALL | E_STRICT]);
        $definition->addTag(CallExtension::CALL_HANDLER_TAG, ['priority' => 100]);
        $container->setDefinition(CallExtension::CALL_HANDLER_TAG . '.runtime_variadics', $definition);
    }

    /**
     * Uses the error handling defined for vanilla call handler.
     *
     * @see RuntimeCallHandler
     *
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $errorReporting = $container->getDefinition(CallExtension::CALL_HANDLER_TAG . '.runtime')->getArgument(0);

        $container
            ->getDefinition(CallExtension::CALL_HANDLER_TAG . '.runtime_variadics')
            ->replaceArgument(0, $errorReporting)
        ;
    }
}
