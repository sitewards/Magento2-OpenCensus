<?php
/**
 * Standard profiler driver that uses outputs for displaying profiling results.
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Sitewards\OpenCensus\Profiler\Driver;

use \OpenCensus\Trace\Exporter\ZipkinExporter;
use \OpenCensus\Trace\Span;
use \OpenCensus\Trace\Tracer;
use \Magento\Framework\Profiler\DriverInterface;

class OpenCensus implements DriverInterface
{
    const DEFAULT_APPLICATION_NAME = 'magento2';

    /**
     * @var array|null
     */
    private $config;

    /**
     * @var \OpenCensus\Trace\Span[]|array
     */
    private $spans = [];

    /**
     * @var \OpenCensus\Core\Scope[]|array
     */
    private $scopes = [];

    /**
     * Constructor
     *
     * @todo: Need something to export to, as well as a way to make it configurable.
     *
     * @param array|null $config
     */
    public function __construct(array $config = null)
    {
        // Starts the application. Seems to be in a static process -- not sure what to do about this, but i guess it
        // needs to be global to determine state (i.e. one thing is calling within another).
        //
        // Sets up a registry or? Somethingi happens here.
        //
        // Given that this class is a global, it might be possibile to use this non-statically.

        // Todo: This is now definitely direct object injection. lol. Need to check this somehow.
        Tracer::start(new $config['exporter']['type'](...$config['exporter']['args']));

        $this->config = $config;
    }

    public function clear($timerId = null)
    {
        if ($timerId) {
            unset($this->scopes[$timerId]);
        } else {
            $this->scopes = [];
        }
    }

    public function start($timerId, array $attributes = null)
    {
        // Magento expects this to be null, rather than an array *even though* it is type hinted against an array.
        // Accordingly, we coerce types here.
        if (!is_array($attributes)) {
            $attributes = [];
        }

        $this->scopes[$timerId] = Tracer::withSpan($this->getSpan($timerId, $attributes));
    }

    public function stop($timerId)
    {
        $this->scopes[$timerId]->close();
    }

    /**
     * Given a timer ID, returns a configured span. If there is no span, creates one.
     *
     * @param string $timerId    The ID of the property that is being profiled
     * @param array  $attributes An array of tags associated with this property.
     * @return Span
     */
    private function getSpan($timerId, array $attributes = [])
    {
        if (!isset($this->spans[$timerId])) {
            $this->spans[$timerId] = Tracer::startSpan(['name' => $timerId, 'attributes' => $attributes]);
        }

        return $this->spans[$timerId];
    }
}
