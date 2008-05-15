<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

/*
 * Class for create Reports
 * 
 * @author  Armen Baghumian <armen@OpenSourceClub.org>
 * @since   PHP 5
 */

class ReportGenerator_Component extends Component
{
    // {{{ properties
    
    private $report    = Null;
    private $rg        = Null;
    private $fields    = Null;
    private $operators = Null;
    private $combines  = Null;
    private $table     = Null;

    // }}}
    // {{{ Constructor
    public function __construct($namespace = Null)
    {
        parent::__construct($namespace, 'report_generator');

        $this->rg = new ReportGenerator_Model;
    }
    // }}}
    // {{{ generateById
    public function generateById($id)
    {
        $this->report = $this->rg->getReport($id);     
    }
    // }}}
    // {{{ generateByName
    public function generateByName($name)
    {
        $this->report = $this->rg->getReportByName($name);
    }
    // }}}
    // {{{ generateReport
    public function generateReport()
    {
        $input           = new Input;
        $this->fields    = $input->post('fields');
        $this->operators = $input->post('operators');
        $this->combines  = $input->post('combines');
        $this->table     = $this->rg->describe($this->report['table_name']);
        $where           = $this->rg->constructWhere($this->fields, $this->operators, $this->combines);
        $result          = $this->rg->executeReport($this->report['table_name'], $this->report['columns'], 
                                              $this->report['additional_columns'], $this->report['filters'], 
                                              $where);

        $list = new PList_Component('report');
        $list->setResource($result);
        $list->setLimit(Arag_Config::get('limit', 0));
        $list->setEmptyListMessage(_("There is no record!"));

        foreach ($this->report['columns'] as $column) {
            $list->addColumn($column);
        }

        foreach ($this->report['additional_columns'] as $label => $column) {
            $list->addColumn($label);
        }

        // Add this->report actions
        foreach ($this->report['actions'] as $action) {
            if (is_array($action) && !empty($action['uri'])) {
                $group_action = (isset($action['group_action']) && $action['group_action'] == 'on') 
                              ? PList_Component::GROUP_ACTION 
                              : False;
                $list->addAction($action['uri'], $action['tooltip'], $action['class_name'], $group_action);
            }
        }

        if (isset($this->report['actions']['parameter_name']) && !empty($this->report['actions']['parameter_name'])) {
            $list->setGroupActionParameterName($this->report['actions']['parameter_name']);
        }
    }
    // }}}
    // {{{ getTable
    public function getTable($jason = False)
    {
        return $jason ? json_encode($this->table) : $this->table;
    }
    // }}}
    // {{{ getOperators
    public function getOperators($jason = False)
    {
        return $jason ? json_encode($this->operators) : $this->operators;
    }
    // }}}
    // {{{ getCombines
    public function getCombines($jason = False)
    {
        return $jason ? json_encode($this->combines) : $this->combines;
    }
    // }}}    
    // {{{ getFields
    public function getFields($jason = False)
    {
        return $jason ? json_encode($this->fields) : $this->fields;
    }
    // }}}
    
}
