<?php

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\App as Laravel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Stopwatch\Stopwatch;

if (! function_exists('stopwatch')) {
    /**
     * @param  string  $name
     * @param  callable|string $callback
     * @param  array  $parameters
     * @param  null|string  $category
     * @param  bool  $morePrecision
     * @param  null|\Psr\Log\LoggerInterface  $logger
     *
     * @return mixed
     */
    function stopwatch(string $name, $callback, array $parameters = [], string $category = null, bool $morePrecision = false, LoggerInterface $logger = null)
    {
        $stopwatch = new Stopwatch($morePrecision);

        $logger or $logger = Laravel::make(LoggerInterface::class);

        $stopwatch->start($name, $category) and $logger->info("Start stopwatch [$name]");

        $called = Laravel::call($callback, $parameters);

        $stopwatchEvent = $stopwatch->stop($name) and $logger->info("End stopwatch [$name] [$stopwatchEvent]");

        return $called;
    }
}

if (! function_exists('wrap_query_log')) {
    /**
     * @param callable|string $callback
     * @param ...$parameter
     *
     * @return array
     */
    function wrap_query_log($callback, ...$parameter)
    {
        return (new Pipeline())
            ->send($callback)
            ->through(function ($callback, $next) {
                DB::enableQueryLog();
                DB::flushQueryLog();

                return $next($callback);
            })
            ->then(function ($callback) use ($parameter) {
                Laravel::call($callback, $parameter);

                return DB::getQueryLog();
            });
    }
}

if (! function_exists('dumpa')) {
    function dumpa(...$vars)
    {
        foreach ($vars as $var) {
            ($var instanceof Arrayable or method_exists($var, 'toArray')) ? dump($var->toArray()) : dump($var);
        }
    }
}

if (! function_exists('dda')) {
    function dda(...$vars)
    {
        dumpa(...$vars);
        exit(1);
    }
}

if (! function_exists('array_reduces')) {
    /**
     * @param  array  $array
     * @param  callable  $callback
     * @param  null  $carry
     *
     * @return null|mixed
     */
    function array_reduces(array $array, callable $callback, $carry = null)
    {
        foreach ($array as $key => $value) {
            $carry = call_user_func($callback, $carry, $value, $key);
        }

        return $carry;
    }
}

if (! function_exists('array_maps')) {
    /**
     * @param  callable  $callback
     * @param  array  $array
     *
     * @return array
     */
    function array_maps(callable $callback, array $array)
    {
        $arr = [];
        foreach ($array as $key => $value) {
            $arr[$key] = call_user_func($callback, $value, $key);
        }

        return $arr;
    }
}
