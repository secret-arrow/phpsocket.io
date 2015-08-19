<?php
namespace Event;
class Emitter
{
    /**
     * [event=>[[listener1, once?], [listener2,once?], ..], ..]
     */
    protected $_eventListenerMap = array();
    
    public function on($event_name, $listener)
    {
        $this->emit('newListener', array($event_name, $listener));
        $this->_eventListenerMap[$event_name][] = array($listener, 0); 
        return $this;
    }

    public function once($event_name, $listener)
    {
        $this->_eventListenerMap[$event_name][] = array($listener, 1);
        return $this;
    }
   
    public function removeListener($event_name, $listener)
    {
        if(!isset($this->_eventListenerMap[$event_name]))
        {
            return $this;
        }
        foreach($this->_eventListenerMap[$event_name] as $key=>$item)
        {
            if($item[0] === $listener)
            {
                $this->emit('removeListener', array($event_name, $listener));
                unset($this->_eventListenerMap[$event_name][$key]);
            }
        }
        if(empty($this->_eventListenerMap[$event_name]))
        {
            unset($this->_eventListenerMap[$event_name]);
        }
        return $this;
    }

    public function removeAllListeners($event_name)
    {
        $this->emit('removeListener', array($event_name));
        unset($this->_eventListenerMap[$event_name]);
        return $this;
    }

    public function listeners($event_name)
    {
        if(empty($this->_eventListenerMap[$event_name]))
        {
            return array();
        }
        $listeners = array();
        foreach($this->_eventListenerMap[$event_name] as $item)
        {
            $listeners[] = $item[0];
        }
        return $listeners;
    }

    public function emit($event_name, $args = array())
    {
        if(empty($this->_eventListenerMap[$event_name]))
        {
            return false;
        }
        foreach($this->_eventListenerMap[$event_name] as $key=>$item)
        {
             call_user_func_array($item[0], $args);
             // once ?
             if($item[1])
             {
                 unset($this->_eventListenerMap[$event_name][$key]);
                 if(!$this->_eventListenerMap[$event_name])
                 {
                     unset($this->_eventListenerMap[$event_name]);
                 }
             }
        }
        return true;
    }
}