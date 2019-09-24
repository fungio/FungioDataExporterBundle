<?php

namespace Fungio\DataExporterBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package Fungio\DataExporterBundle\DependencyInjection
 *
 * @author Pierrick AUBIN <pierrick.aubin@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('fungio_data_exporter');
        $rootNode    = $treeBuilder->getRootNode();

        return $treeBuilder;
    }
}
