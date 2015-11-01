<?php
namespace Application\Daemon;

use Zend\Stdlib\ArrayUtils;

/**
 * Class DaemonFactory
 * @package Application\Daemon
 */
abstract class DaemonFactory
{
    /**
     * @param array|\Traversable $cfg
     * @return AbstractDaemonLite
     */
    public static function create($cfg)
    {
        if ($cfg instanceof \Traversable) {
            $cfg = ArrayUtils::iteratorToArray($cfg);
        }

        if (!is_array($cfg)) {
            throw new Exception\InvalidArgumentException('Параметр $options должен быть массивом.');
        }

        if (empty($cfg['class'])) {
            throw new Exception\InvalidArgumentException('Не задан класс демона который необходимо запустить.');
        }

        $options = [];
        if (!empty($cfg['options'])) {
            if (!is_array($cfg['options'])) {
                throw new Exception\InvalidArgumentException('Опции для запуска демона должны быть массивом.');
            } else {
                $options = $cfg['options'];
            }
        }
        $daemon = new $cfg['class']($options);
        if (!$daemon instanceof DaemonInterface) {
            throw new Exception\RuntimeException('Попытка вызова класса не демона.');
        }
        return $daemon;
    }
}
