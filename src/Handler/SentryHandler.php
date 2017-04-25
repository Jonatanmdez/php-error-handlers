<?php
namespace Josegonzalez\ErrorHandlers\Handler;

use Josegonzalez\ErrorHandlers\Handler\AbstractHandler;
use Raven_Client;

class SentryHandler extends AbstractHandler implements HandlerInterface
{
    /**
     * Handles a given exception
     *
     * @param Throwable|Exception $exception A Throwable or Exception instance
     * @return void
     */
    public function handle($exception)
    {
        $exception = call_user_func($this->config('exceptionCallback'), $exception);
        if (!$exception) {
            return;
        }
        $client = $this->client();
        $client = call_user_func($this->config('clientCallback'), $client);
        if ($client) {
            $client->captureException($exception);
        }
    }

    /**
     * Returns a client
     *
     * @return \Raven_Client
     */
    protected function client()
    {
        $dsn = $this->config('dsn');
        if (!$dsn) {
            return null;
        }

        $client = new Raven_Client($dsn);
        
        /* Include the release for Sentry */
        
        $release = $this->config('release');
        if($release){
            $client->setRelease($release);
        }
       
          
        return $client;
    }
}
