<?php

namespace ZendDeveloperTools\Controller;

use Zend\Mvc\MvcEvent,
    Zend\EventManager\EventDescription as Event,
    Zend\EventManager\EventManager,
    Zend\EventManager\EventCollection,
    Zend\Http\Response as HttpResponse,
    Zend\Stdlib\RequestDescription as Request,
    Zend\Stdlib\ResponseDescription as Response,
    Zend\Stdlib\IsAssocArray,
    Zend\Stdlib\Dispatchable,
    ZendDeveloperTools\Service\DeveloperTools as DevToolsService,
    ArrayObject;

class DeveloperToolsController implements Dispatchable
{
    protected $event;
    protected $events;

    public function indexAction()
    {
        $execTime = DevToolsService::$stopTime - DevToolsService::$startTime;
        return array('execTime' => round($execTime, 5));
    }

    public function execute(MvcEvent $e)
    {
        $actionResponse = $this->indexAction();

        if (!is_object($actionResponse)) {
            if (IsAssocArray::test($actionResponse)) {
                $actionResponse = new ArrayObject($actionResponse, ArrayObject::ARRAY_AS_PROPS);
            }
        }

        $e->setResult($actionResponse);
        return $actionResponse;
    }

    public function dispatch(Request $request, Response $response = null, Event $e = null)
    {
        $this->request = $request;
        if (!$response) {
            $response = new HttpResponse();
        }
        $this->response = $response;
        $this->event = $e;

        $result = $this->events()->trigger('dispatch', $e, function($test) {
            return ($test instanceof Response);
        });

        if ($result->stopped()) {
            return $result->last();
        }
        return $e->getResult();
    }
    /**
     * Get the request object
     * 
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get the response object
     * 
     * @return Response
     */
    public function getResponse()
    {
        if (null === $this->response) {
            $this->response = new HttpResponse();
        }
        return $this->response;
    }

    /**
     * Set the event manager instance used by this context
     * 
     * @param  EventCollection $events 
     * @return AppContext
     */
    public function setEventManager(EventCollection $events)
    {
        $this->events = $events;
        return $this;
    }

    /**
     * Retrieve the event manager
     *
     * Lazy-loads an EventManager instance if none registered.
     * 
     * @return EventCollection
     */
    public function events()
    {
        if (!$this->events instanceof EventCollection) {
            $this->setEventManager(new EventManager(array(
                'Zend\Stdlib\Dispatchable',
                __CLASS__, 
                get_called_class()
            )));
            $this->attachDefaultListeners();
        }
        return $this->events;
    }

    /**
     * Set an event to use during dispatch
     *
     * By default, will re-cast to MvcEvent if another event type is provided.
     * 
     * @param  Event $e 
     * @return void
     */
    public function setEvent(Event $e)
    {
        if ($e instanceof Event && !$e instanceof MvcEvent) {
            $eventParams = $e->getParams();
            $e = new MvcEvent();
            $e->setParams($eventParams);
            unset($eventParams);
        }
        $this->event = $e;
    }

    /**
     * Get the attached event
     *
     * Will create a new MvcEvent if none provided.
     * 
     * @return Event
     */
    public function getEvent()
    {
        if (!$this->event) {
            $this->setEvent(new MvcEvent());
        }
        return $this->event;
    }

    /**
     * Set locator instance
     * 
     * @param  Locator $locator 
     * @return void
     */
    public function setLocator(Locator $locator)
    {
        $this->locator = $locator;
    }

    /**
     * Retrieve locator instance
     * 
     * @return Locator
     */
    public function getLocator()
    {
        return $this->locator;
    }

    /**
     * Get plugin broker instance
     *
     * @return Zend\Loader\Broker
     */
    public function getBroker()
    {
        if (!$this->broker) {
            $this->setBroker(new PluginBroker());
        }
        return $this->broker;
    }

    /**
     * Set plugin broker instance
     *
     * @param  string|Broker $broker Plugin broker to load plugins
     * @return Zend\Loader\Pluggable
     */
    public function setBroker($broker)
    {
        if (!$broker instanceof Broker) {
            throw new Exception\InvalidArgumentException('Broker must implement Zend\Loader\Broker');
        }
        $this->broker = $broker;
        if (method_exists($broker, 'setController')) {
            $this->broker->setController($this);
        }
        return $this;
    }

    /**
     * Get plugin instance
     *
     * @param  string     $plugin  Name of plugin to return
     * @param  null|array $options Options to pass to plugin constructor (if not already instantiated)
     * @return mixed
     */
    public function plugin($name, array $options = null)
    {
        return $this->getBroker()->load($name, $options);
    }

    /**
     * Method overloading: return plugins
     * 
     * @param mixed $method 
     * @param mixed $params 
     * @return void
     */
    public function __call($method, $params)
    {
        $options = null;
        if (0 < count($params)) {
            $options = array_shift($params);
        }
        return $this->plugin($method, $options);
    }

    /**
     * Register the default events for this controller
     * 
     * @return void
     */
    protected function attachDefaultListeners()
    {
        $events = $this->events();
        $events->attach('dispatch', array($this, 'execute'));
    }

    /**
     * Transform an action name into a method name
     * 
     * @param  string $action 
     * @return string
     */
    public static function getMethodFromAction($action)
    {
        $method  = str_replace(array('.', '-', '_'), ' ', $action);
        $method  = ucwords($method);
        $method  = str_replace(' ', '', $method);
        $method  = lcfirst($method);
        $method .= 'Action';
        return $method;
    }

}
