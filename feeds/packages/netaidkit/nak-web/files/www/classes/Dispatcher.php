<?php

class Dispatcher
{
    protected $_allowed_controllers = array( 'IndexController',
                                             'SetupController',
                                             'AdminController',
                                             'UserController' );

    public function run($request)
    {
        $controller = $request->getController();
        $action     = $request->getAction();
    
        if (empty($controller) || empty($action))
            throw new Exception("Invalid request.");
    
        // Sanitize $controller_class before autoloading.
        $controller_class = ucfirst($controller) . 'Controller';
        if (!in_array($controller_class, $this->_allowed_controllers))
            throw new NotFoundException("Controller not defined.");
        
        ob_start();
        $controller_obj = new $controller_class($request);
        $controller_obj->do_action($action);
        $output = ob_get_clean();

        return $output;
    }
}
