<?php
/**
 * Created by PhpStorm.
 * User: Ppito
 * Date: 11/15/2016
 * Time: 8:26 PM
 *
 * @link      https://github.com/Ppito/zf3-whoops for the canonical source repository
 * @copyright Copyright (c) 2016 Mickael TONNELIER.
 * @license   https://github.com/Ppito/zf3-whoops/blob/master/LICENSE.md The MIT License
 */

namespace WhoopsErrorHandler\Handler;

use Interop\Container\ContainerInterface;
use Whoops\Handler\JsonResponseHandler as WhoopsAjaxHandler;

class AjaxHandler extends HandlerAbstract
    implements HandlerInterface {


    /**
     * AjaxHandler constructor.
     *
     * @param \Interop\Container\ContainerInterface $container
     * @param array                                 $options
     * @return self
     */
    public function __construct(ContainerInterface $container, $options = []) {

        parent::__construct($container, $options);
        $this->handler = new WhoopsAjaxHandler();
        $this->configure();
        return $this;
    }

    /**
     * Inject an editor into the whoops configuration.
     *
     * @throws \InvalidArgumentException for an invalid show trace option.
     */
    public function configure() {
        /** @var \Whoops\Handler\JsonResponseHandler $handler */
        $handler = $this->getHandler();
        $handler->setJsonApi(true);

        if (!isset($this->options['show_trace']) || !isset($this->options['show_trace']['ajax_display'])) {
            return;
        }

        $show_trace = $this->options['show_trace']['ajax_display'];

        if (! is_bool($show_trace)) {
            throw new \InvalidArgumentException(sprintf(
                'Whoops show trace option must be a boolean; received "%s"',
                (is_object($show_trace) ? get_class($show_trace) : gettype($show_trace))
            ));
        }
        $handler->addTraceToOutput($show_trace);
    }
}
