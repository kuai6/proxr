<?php

namespace Application\Storage;

use Zend\Stdlib\ArrayUtils;
use Traversable;
use Zend\Cache\Storage;

/**
 * Class StorageFactory
 * @package Application\Storage
 */
abstract class StorageFactory
{
    /**
     * @param array|Traversable $config
     * @return Storage\StorageInterface
     * @throws Exception\InvalidArgumentException
     */
    public static function create($config)
    {
        if ($config instanceof Traversable) {
            $config = ArrayUtils::iteratorToArray($config);
        }

        if (!is_array($config)) {
            throw new Exception\InvalidArgumentException('Конфиг должен быть массивом');
        }

        if (empty($config['adapter'])) {
            throw new Exception\InvalidArgumentException('В конфиге не задан адаптер');
        }
        $adapter = $config['adapter'];
        $options = isset($config['options']) ? $config['options'] : [];
        if (substr(strtolower($adapter), 0, 4) === 'zend') {
            $adapterPluginManager = new Storage\AdapterPluginManager();
            $adapter = substr(strtolower($adapter), 4);
        } else {
            $adapterPluginManager = new AdapterPluginManager();
        }
        return $adapterPluginManager->get($adapter, $options);
    }
}
