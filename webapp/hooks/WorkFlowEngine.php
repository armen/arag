<?php defined('SYSPATH') or die('No direct script access.');

Event::add('workflow.initialize', array('WorkFlowEngine', 'initialize'));
Event::add('workflow.route', array('WorkFlowEngine', 'route'));
Event::add('workflow.resume', array('WorkFlowEngine', 'resume'));
Event::add('workflow.finalize', array('WorkFlowEngine', 'finalize'));

/**
 * Arag
 *
 * @package      Arag
 * @author       Armen Baghumian
 * @since        Version 0.1
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Loader Class
 *
 * Loads views and files
 *
 * @package     WorkFlowEngine
 * @subpackage  Hooks
 * @author      Armen Baghumian
 * @category    Hook
 */
class WorkFlowEngine extends WorkFlow {

    // {{{ initialize
    public static function initialize()
    {
        $session = new Session();
        $session->set('workflow', array('current' => Event::$data));

        Event::run(strtolower(Event::$data).'.initialize');        
    }
    // }}}
    // {{{ route
    public static function route()
    {
        $session  = new Session();
        $workflow = $session->get('workflow.current');

        Event::run(strtolower($workflow).'.route', Event::$data);
    }
    // }}}
    // {{{ resume
    public static function resume()
    {
        $session  = new Session();
        $workflow = $session->get('workflow.current');

        Event::run(strtolower($workflow).'.resume', Event::$data);
    }
    // }}}    
    // {{{ finalize
    public static function finalize()
    {
        $session  = new Session();
        $workflow = $session->get('workflow.current');
        $session->del('workflow');

        Event::run(strtolower($workflow).'.finalize', Event::$data);        
    }
    // }}}
}

?>
