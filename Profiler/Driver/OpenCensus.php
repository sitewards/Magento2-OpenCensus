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
     * @var array
     */
    private $spans = [];

    /**
     * @var array
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
        // Todo: This should be a function
        $applicationName = isset($config['application_name'])
            ? $config['application_name']
            : self::DEFAULT_APPLICATION_NAME;


        // Starts the application. Seems to be in a static process -- not sure what to do about this, but i guess it
        // needs to be global to determine state (i.e. one thing is calling within another).
        //
        // Sets up a registry or? Somethingi happens here.
        //
        // Given that this class is a global, it might be possibile to use this non-statically.
        Tracer::start(new ZipkinExporter('magento2', 'http://dockercompose_jaeger-collector_1:9411/api/v2/spans'));

        $this->config = $config;
    }

    public function clear($timerId = null)
    {
        unset($this->scopes[$timerId]);
    }

    public function start($timerId, array $tags = null)
    {
        $this->scopes[$timerId] = Tracer::withSpan($this->getSpan($timerId));
    }

    public function stop($timerId)
    {
        $this->scopes[$timerId]->close();
    }

    /**
     * Given a timer ID, returns a configured span. If there is no span, creates one.
     *
     * @param string $timerId
     * @return Span
     */
    private function getSpan($timerId)
    {
        if (!isset($this->spans[$timerId])) {
            $this->spans[$timerId] = Tracer::startSpan(['name' => $timerId]);
        }

        return $this->spans[$timerId];
    }
}
