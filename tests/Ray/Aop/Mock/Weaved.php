<?php

namespace Ray\Aop\Mock;

/**
 * Test class for Ray.Aop
 */
class Weaved extends Mock
{
    private $rayAopIntercept = true;

    public function ___postConstruct(\Ray\Aop\Bind $bind)
    {
        $this->bind = $bind;
    }

    public function returnSame($a)
    {
        // direct call
        if (!$this->rayAopIntercept || !isset($this->bind[__FUNCTION__])) {
            return call_user_func_array('parent::' . __FUNCTION__, func_get_args());
        }

        $this->rayAopIntercept = true;

        // interceptor weaved call
        $interceptors = $this->bind[__FUNCTION__];
        $annotation = (isset($this->bind->annotation[__FUNCTION__])) ? $this->bind->annotation[__FUNCTION__] : null;
        $invocation = new \Ray\Aop\ReflectiveMethodInvocation([
            $this,
            __FUNCTION__
        ], func_get_args(), $interceptors, $annotation);

        return $invocation->proceed();
    }
}
