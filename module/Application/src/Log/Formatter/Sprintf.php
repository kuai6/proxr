<?php
namespace Application\Log\Formatter;

use Zend\Log\Formatter\Base;
use Zend\Log\Formatter\Simple;

/**
 * Class Sprintf formatter
 *
 * Example:
 *   $writer = new Stream($this->getLogFile());
 *   $writer->setFormatter(new Sprintf("[%timestamp% | %6s:pid%] %message%"));
 *   $logger->info("Hello, World", array('pid' => 777));
 *   // output: [2001-09-11 00:00:00|    777] Hello, World
 *
 * @package Application\Log\Formatter
 */
class Sprintf extends Simple
{
    protected function baseFormat($event)
    {
        $baseFormatter = new Base();
        return $baseFormatter->format($event);
    }

    /**
     * format: "%timestamp% %% %message%"
     *
     * @param array $event
     * @return string|void
     */
    public function format($event)
    {
        $output = $this->format;
        $vars = [''];

        $event = $this->baseFormat($event);

        if (isset($event['extra']) && is_array($event['extra'])) {
            $event = array_merge($event, $event['extra']);
        }

        if (preg_match_all('/%([^%]+)%/', $output, $matches)) {
            foreach ($matches[1] as $k => $match) {
                @list($format, $name) = explode(':', $match);

                if (!$name) {
                    $name = $format;
                    $format = '%s';
                } else {
                    $format = '%' . $format;
                }

                $value = isset($event[$name]) ? $event[$name] : '';

                $output = preg_replace("/{$matches[0][$k]}/", $format, $output, 1);
                $vars[$k + 1] = $value;
            }
            $vars[0] = $output;
        }
        $output = call_user_func_array('sprintf', $vars);

        return $output;
    }

    /**
     * The __toString method allows a class to decide how it will react when it is converted to a string.
     *
     * @return string
     * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
     */
    public function __toString()
    {
        return get_class($this);
    }
}
