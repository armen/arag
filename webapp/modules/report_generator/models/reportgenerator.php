<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class ReportGenerator_Model extends Model 
{
    // {{{ Properties

    private $config    = array();
    private $tableName = 'report_generator_reports';

    // }}}
    // {{{ constructor
    public function __construct()
    {
        $this->config           = defined('MASTERAPP') ? Config::item('database.default') : Config::item('sites/'.APPNAME.'.database');
        $this->config['object'] = False;
        
        parent::__construct(new Database($this->config));
    }
    // }}}
    // {{{ describe
    public function describe($table)
    {
        try {
            $table = $this->db->query("Describe ".$this->config['table_prefix'].$table)->result_array();
        } catch(Kohana_Database_Exception $e) {
            return Array();
        }

        $date_fields_name = Config::item('config.date_field_names');
        $result           = Array();
        
        foreach ($table as $key => $column) {
            preg_match('/([a-z]+)(?:\(([0-9]+)\))?/', $column['Type'], $matches);

            $type  = $matches[1];
            $field = $column['Field'];
           
            switch ($type) {
                case 'clob':
                case 'longclob';
                case 'mediumclob':
                case 'blob':
                case 'longblob';
                case 'mediumblob':
                case 'text':
                case 'longtext':
                case 'varchar':
                    $type = 'text';
                    break;

                case 'int':
                case 'tinyint':
                case 'bigint':
                    $type = in_array($field, $date_fields_name) ? 'date' : 'numeric';
                    break;

                default:
                    $type = 'not_implemented';
            }

            $result[$field]['field']  = $field;
            $result[$field]['type']   = $type;
            $result[$field]['length'] = isset($matches[2]) ? $matches[2] : 0;
        }

        return $result;
    }
    // }}}
    // {{{ executeReport
    public function executeReport($table_name, $columns, $additionalColumns, $filters, $where = Null)
    {
        empty($columns) AND $columns = Array();

        $resource = $this->db->select(implode(',', $columns))->from($table_name);

        foreach ($additionalColumns as $label => $column) {
            $this->db->select('('.$column.') AS '.$this->db->escape_table($label));
        }

        if (!empty($filters)) {

            $combines = array_values($filters);        
            $filters  = array_keys($filters);

            // Add an opening parenthese at the begining of filters
            $filters[0] = '('.$filters[0];

            // Add a closing parenthese at the end of filters
            $filters[count($filters)-1] = $filters[count($filters)-1].')';

            // Inject an empty field at the begining of filters_combine because the first condition
            // should be ignored, pop it to make it same length as $filters to combine it
            array_unshift($combines, Null);
            array_pop($combines);

            $filters = array_combine($filters, $combines);
        }

        // Add conditions
        foreach ($filters as $filter => $combine) {
            if ($combine === 'AND') {
                $resource->where($filter);
            } else {
                $resource->orwhere($filter);
            }
        }

        // Add optional conditions
        !empty($where) AND $resource->where('('.$where.')');

        try {
            $resource = $resource->get()->result(False);
        } catch(Kohana_Database_Exception $e) {
            // Shit there is an sql error log it!
            Log::add('error', "There is an SQL during execution of report: '".$this->db->last_query()."'");
            exit;
        }

        return $resource;
    }
    // }}}
    // {{{ constructWhere
    public function constructWhere($fields, $operators, $combines)
    {
        $where = Null;

        if (empty($fields)) {
            return $fields;
        }

        foreach ($fields as $field => $values) {
            
            $escaped_field = $this->db->escape_table($field);

            foreach ($values as $index => $value) {

                $where .= ' '.$combines[$field][$index].' '.$escaped_field.' ';
                
                switch ($operators[$field][$index]) {
                    case '<':
                    case '>':
                    case '=':
                    case '!=':
                    case '<=':
                    case '>=':
                        !is_numeric($value) AND $value = $this->db->escape($value);
                        $where .= $operators[$field][$index].' '.$value;
                        break;

                    case '$':
                        $where .= 'LIKE '.$this->db->escape('%'.$value);
                        break;

                    case '^':
                        $where .= 'LIKE '.$this->db->escape($value.'%');
                        break;

                    case '~':
                        $where .= 'LIKE '.$this->db->escape('%'.$value.'%');
                        break;

                    case '!~':
                        $where .= 'NOT LIKE '.$this->db->escape('%'.$value.'%');
                        break;
                }
            }
        }
        
        return $where;
    }
    // }}}
    // {{{ saveReport
    public function saveReport($tableName, $reportName, $reportDesc, $columns, $additionalColumns, $filters, $actions, $parameter_name)
    {
        $actions['parameter_name'] = $parameter_name;

        $row = Array(
                      'table_name'         => $tableName, 
                      'report_name'        => $reportName, 
                      'report_desc'        => $reportDesc,
                      'columns'            => serialize($columns),
                      'additional_columns' => serialize($additionalColumns),
                      'filters'            => serialize($filters),
                      'actions'            => serialize($actions),
                      'create_date'        => time(),
                      'modify_date'        => 0
                    );

        $this->db->insert($this->tableName, $row);
    }
    // }}}
    // {{{ getReports
    public function getReports()
    {
        return $this->db->select('id, table_name, report_name, report_desc, columns, additional_columns, filters, actions, create_date, modify_date')
                        ->from($this->tableName)
                        ->get()->result(False);
    }
    // }}}
    // {{{ deleteReport
    public function deleteReport($id)
    {
        $this->db->delete($this->tableName, Array('id' => $id));
    }
    // }}}
    // {{{ hasReport
    public function hasReport($id)
    {
        $result = $this->db->select('count(id) as count')->getwhere($this->tableName, Array('id' => $id))->current();

        return (boolean)$result['count'];
    }
    // }}}    
    // {{{ getReportName
    public function getReportName($id)
    {
        $result = $this->db->select('report_name')->getwhere($this->tableName, Array('id' => $id))->current();
        return $result['report_name'];
    }
    // }}}
    // {{{ getReport
    public function getReport($id)
    {
        $result = $this->db->select('id, table_name, report_name, report_desc, columns, additional_columns, filters, actions, create_date, modify_date')
                           ->getwhere($this->tableName, Array('id' => $id))->current();

        $result['columns']            = unserialize($result['columns']);
        $result['filters']            = unserialize($result['filters']);
        $result['actions']            = unserialize($result['actions']);
        $result['additional_columns'] = unserialize($result['additional_columns']);

        empty($result['columns']) AND $result['columns'] = Array();
        empty($result['filters']) AND $result['filters'] = Array();
        empty($result['actions']) AND $result['actions'] = Array();
        empty($result['additional_columns']) AND $result['additional_columns'] = Array();

        return $result;
    }
    // }}}
    // {{{ getReportByName
    public function getReportByName($name)
    {
        $result = $this->db->select('id, table_name, report_name, report_desc, columns, additional_columns, filters, actions, create_date, modify_date')
                           ->getwhere($this->tableName, Array('report_name' => $name))->current();

        $result['columns']            = unserialize($result['columns']);
        $result['filters']            = unserialize($result['filters']);
        $result['actions']            = unserialize($result['actions']);
        $result['additional_columns'] = unserialize($result['additional_columns']);

        empty($result['columns']) AND $result['columns'] = Array();
        empty($result['filters']) AND $result['filters'] = Array();
        empty($result['actions']) AND $result['actions'] = Array();
        empty($result['additional_columns']) AND $result['additional_columns'] = Array();

        return $result;
    }
    // }}}    
    // {{{ List callbacks
    // {{{ getCreateDate
    public function getCreateDate($row)
    {
        return format::date($row['create_date']);
    }
    // }}}
    // {{{ getModifyDate
    public function getModifyDate($row)
    {
        return format::date($row['modify_date']);
    }
    // }}}
    // }}}
}
