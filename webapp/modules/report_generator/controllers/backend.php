<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Backend_Controller extends ReportGenerator_Backend
{
    // {{{ properties

    private $additional_columns = Array();
    private $filters            = Array();

    // }}}
    // {{{ reports
    public function reports_any()
    {
        $rg      = new ReportGenerator_Model;
        $reports = new PList_Component('reports');

        $reports->setResource($rg->getReports());
        $reports->setLimit(Arag_Config::get('limit', 0));
        $reports->setEmptyListMessage(_("There is no report!"));
        $reports->addColumn('report_name', _("Name"));
        $reports->addColumn('report_desc', _("Description"));
        $reports->addColumn('table_name', _("Table Name"));
        $reports->addColumn('ReportGenerator.getCreateDate', _("Create Date"), PList_Component::VIRTUAL_COLUMN);
        $reports->addColumn('ReportGenerator.getModifyDate', _("Modify Date"), PList_Component::VIRTUAL_COLUMN);
        $reports->addAction('report_generator/backend/execute_report/#id#', 'Execute Report', 'view_action');
        $reports->addAction('report_generator/backend/delete_report/#id#', 'Delete Report', 'delete_action');

        $this->layout->content = new View('backend/reports');
    }
    // }}}
    // {{{ generate_report
    // {{{ generate_report_read
    public function generate_report_read()
    {
        $this->layout->content                 = new View('backend/get_table');
        $this->layout->content->allowed_tables = array_combine(Kohana::config('config.allowed_tables'), Kohana::config('config.allowed_tables'));
    }
    // }}}
    // {{{ generate_report_write
    public function generate_report_write()
    {
        $rg             = new ReportGenerator_Model;
        $actions        = $this->input->post('actions', Array());
        $parameter_name = $this->input->post('parameter_name', Null);
        $columns        = $this->input->post('columns', Array());
        $table_name     = $this->input->post('table_name', Null);
        $table          = $rg->describe($table_name);

        $this->layout->content                     = new View('backend/generate_report');
        $this->layout->content->table              = $table;
        $this->layout->content->columns            = !empty($table) ? array_combine(array_keys($table), array_keys($table)) : Array();
        $this->layout->content->selected_columns   = $columns;
        $this->layout->content->table_name         = $table_name;
        $this->layout->content->report_name        = $this->input->post('report_name', Null);
        $this->layout->content->report_description = $this->input->post('report_description', Null);
        $this->layout->content->additional_columns = $this->additional_columns;
        $this->layout->content->filters            = $this->filters;
        $this->layout->content->actions            = $actions;
        $this->layout->content->parameter_name     = $parameter_name;
        $date_fields_name                          = Kohana::config('config.date_field_names');

        // Generate report's list
        $report = new PList_Component('report');
        $report->setResource($rg->executeReport($table_name, $columns, $this->additional_columns, $this->filters));
        $report->setLimit(Arag_Config::get('limit', 0));
        $report->setEmptyListMessage(_("There is no record!"));

        foreach ($columns as $column) {
            if (in_array($column, $date_fields_name)) {
                $report->addColumn('ReportGenerator.getDate['.$column.']', $column, PList_Component::VIRTUAL_COLUMN);
            } else {
                $report->addColumn($column);
            }
        }

        foreach ($this->additional_columns as $label => $column) {
            $report->addColumn($label);
        }

        // Add report actions
        foreach ($actions as $action) {
            if (!empty($action['uri'])) {
                $group_action = (isset($action['group_action']) && $action['group_action'] == 'on')
                              ? PList_Component::GROUP_ACTION
                              : False;
                $report->addAction($action['uri'], $action['tooltip'], $action['class_name'], $group_action);
            }
        }

        if (!empty($parameter_name)) {
            $report->setGroupActionParameterName($parameter_name);
        }
    }
    // }}}
    // {{{ generate_report_validate_write
    public function generate_report_validate_write()
    {
        // TODO: validate actions

        $filters         = $this->input->post('filters', Array());
        $filter          = $this->input->post('filter');
        $filters_combine = $this->input->post('filters_combine', Array());
        $filter_combine  = $this->input->post('filter_combine');
        $formulas        = $this->input->post('formulas', Array());
        $formula         = $this->input->post('formula');
        $columns_label   = $this->input->post('columns_label', Array());
        $column_label    = $this->input->post('column_label');
        $result          = True;

        $rg      = new ReportGenerator_Model;
        $table   = $rg->describe($this->input->post('table_name'));
        $columns = !empty($table) ? array_combine(array_keys($table), array_keys($table)) : Array();

        // {{{ validate additional column

        if (!empty($formula)) {

            $sa = new ColumnSyntaxAnalyzer;

            // Add this form columns id as valid(defined) columns
            foreach ($columns as $column) {
                $sa->symbolTable->insert($column, ColumnLexicalAnalyzer::T_ID);
            }

            // Analyze input
            $sa->analyze($formula);

            // Check for errors
            if ($sa->hasErrors()) {

                // Fetch last error
                $errors     = $sa->getErrors();
                $last_error = end($errors);

                $this->layout->content->formula_splited_input = $last_error['params']['splitedinput'];
                $this->validation->add_error('formula', 'formula_error');
                $this->validation->message('formula_error', $last_error['message']);
                $result = False;

            } else {

                $formulas = array_merge($formulas, Array($formula));
                !empty($column_label) AND $columns_label = array_merge($columns_label, Array($column_label));
            }
        }

        (!empty($columns_label) AND !empty($formulas)) AND  $this->additional_columns = array_combine($columns_label, $formulas);

        // }}}

        // {{{ validate filter

        if (!empty($filter)) {

            $sa = new FilterSyntaxAnalyzer;

            // Add this form columns id as valid(defined) columns
            foreach ($columns as $column) {
                $sa->symbolTable->insert($column, FilterLexicalAnalyzer::T_ID);
            }

            // Analyze input
            $sa->analyze($filter);

            // Check for errors
            if ($sa->hasErrors()) {

                // Fetch last error
                $errors     = $sa->getErrors();
                $last_error = end($errors);

                $this->layout->content->formula_splited_input = $last_error['params']['splitedinput'];
                $this->validation->add_error('formula', 'formula_error');
                $this->validation->message('formula_error', $last_error['message']);
                $result = False;

            } else {

                $filters = array_merge($filters, Array($filter));
                !empty($filter_combine) AND $filters_combine = array_merge($filters_combine, Array($filter_combine));
            }
        }

        (!empty($filters_combine) AND !empty($filters)) AND $this->filters = array_combine($filters, $filters_combine);

        // }}}

        $this->validation->name('column_label', _("Column Label"))->add_rules('column_label', 'valid::id', 'depends_on[formula]')
                         ->post_filter('trim', 'column_label');
        $this->validation->name('formula', _("Formula"))->add_rules('formula', 'depends_on[column_label]');
        $result = $this->validation->validate();

        return $result;
    }
    // }}}
    // {{{ generate_report_validate_write_error
    public function generate_report_write_error()
    {
        $this->generate_report_write();
    }
    // }}}
    // }}}
    // {{{ save_report
    public function save_report_any()
    {
        $filters            = $this->input->post('filters', Array());
        $filters_combine    = $this->input->post('filters_combine', Array());
        $additional_columns = $this->input->post('formulas', Array());
        $columns_label      = $this->input->post('columns_label', Array());

        (!empty($filters_combine) AND !empty($filters)) AND $filters = array_combine($filters, $filters_combine);
        (!empty($columns_label) AND !empty($additional_columns)) AND  $additional_columns = array_combine($columns_label, $additional_columns);

        $rg = new ReportGenerator_Model;
        $rg->saveReport($this->input->post('table_name'),
                        $this->input->post('report_name'),
                        $this->input->post('report_description'),
                        $this->input->post('columns'),
                        $additional_columns,
                        $filters,
                        $this->input->post('actions'),
                        $this->input->post('parameter_name'));

        url::redirect('report_generator/backend/reports');
    }
    // }}}
    // {{{ delete_report
    // {{{ delete_report_read
    public function delete_report_read($id)
    {
        $rg = new ReportGenerator_Model;
        $this->global_tabs->setParameter('id', $id);

        $this->layout->content = new View('backend/delete_report', Array('id' => $id, 'name' => $rg->getReportName($id)));
    }
    // }}}
    // {{{ delete_report_validate_read
    public function delete_report_validate_read()
    {
        $this->validation->name(0, _("ID"))->add_rules(0, 'required', 'valid::numeric', array($this, '_check_report'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ delete_report_read_error
    public function delete_report_read_error()
    {
        $this->_invalid_request('report_generator/backend/reports', _("Invalid ID"));
    }
    // }}}
    // {{{ delete_report_write
    public function delete_report_write()
    {
        $rg = new ReportGenerator_Model;

        $rg->deleteReport($this->input->post('id'));

        url::redirect('report_generator/backend/reports');
    }
    // }}}
    // {{{ delete_report_validate_write
    public function delete_report_validate_write()
    {
        $this->validation->name('id', _("ID"))->add_rules('id', 'required', 'valid::numeric', array($this, '_check_report'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ delete_report_write_error
    public function delete_write_error()
    {
        $this->_invalid_request('report_generator/backend/reports', _("Invalid ID"));
    }
    // }}}
    // }}}
    // {{{ execute_report
    // {{{ execute_report_any
    public function execute_report_any($id = False)
    {
        ($id === False) AND $id = $this->input->post('id');

        $this->global_tabs->setParameter('id', $id);

        $report = new ReportGenerator_Component('report');
        $report->generateById($id);

        $this->layout->content = new View('backend/execute_report');
    }
    // }}}
    // {{{ execute_report_validate_read
    public function execute_report_validate_read()
    {
        $this->validation->name(0, _("ID"))->add_rules(0, 'required', 'valid::numeric', array($this, '_check_report'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ execute_report_validate_write
    public function execute_report_validate_write()
    {
        $this->validation->name('id', _("ID"))->add_rules('id', 'required', 'valid::numeric', array($this, '_check_report'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ execute_report_any_error
    public function execute_report_any_error()
    {
        $this->_invalid_request('report_generator/backend/reports', _("Invalid ID"));
    }
    // }}}
    // }}}
    // {{{ _check_report
    public function _check_report($id)
    {
        $rg = new ReportGenerator_Model;

        return $rg->hasReport($id);
    }
    // }}}
    // {{{ settings_read
    public function settings_read()
    {
        $data          = Array();
        $data['limit'] = Arag_Config::get("limit");
        $data['saved'] = $this->session->get_once('report_generator.limit_saved');

        $this->layout->content = new View('backend/settings_limit', $data);
    }
    // }}}
    // {{{ settings_write
    public function settings_write()
    {

        Arag_Config::set('limit', $this->input->post('limit'));

        $this->session->set('report_generator.limit_saved', true);

        $this->settings_read();

    }
    // }}}
    // {{{ settings_validate_write
    public function settings_validate_write()
    {
        $this->validation->name('limit', _("Limit"))->add_rules('limit', 'required', 'valid::numeric')->post_filter('trim', 'limit');

        return $this->validation->validate();
    }
    // }}}
    // {{{ settings_write_error
    public function settings_write_error()
    {
        $this->settings_read();
    }
    // }}}
}
