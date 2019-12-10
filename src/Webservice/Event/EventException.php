<?php
namespace Efrogg\Webservice\Event;

use Symfony\Component\EventDispatcher\Event;

class EventException extends Event
{
    const EVENT_EXCEPTION = 'EVENT_EXCEPTION';

    private $exception;

    /**
     * EventException constructor.
     * @param $exception
     */
    public function __construct($exception)
    {
        $this->exception = $exception;
    }

    /**
     * @return mixed
     */
    public function getException()
    {
        return $this->exception;
    }


}
