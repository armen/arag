<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// | Smarty {rg_generate_report} plugin                                      |
// |                                                                         |
// | Type:    report execution function                                      |
// | Name:    rg_generate_report                                             |
// | Purpose: Generating report                                              |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_rg_generate_report($params, &$smarty)
{
    $ext      = Config::item('smarty.templates_ext');
    $template = '/component/generate_report';

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'name':
                $name = $_val;
                break;

            case 'template':
                $template = $_val;
                $template = text::strrtrim($template, '.'.$ext);
                break;

            default:
                $smarty->trigger_error("rg_generate_report: Unknown attribute '$_key'");
        }
    }

    if (!isset($name)) {
        $name = $smarty->get_template_vars('_report_generator');
    }

    if (is_array($name)) {
        // if name is array then we have to get just first element by default becuase name parameter
        // not specified
        list($name) = $name;
    } else if (!is_string($name)) {
        // if name is string then it setted as parameter in plugin but if not we have to trigger error
        $smarty->trigger_error('rg_generate_report: can not find name parameter or it is invalid!', E_USER_ERROR);
    }

    $rg = $smarty->get_template_vars($name);

    if (isset($rg)) {

        // Generate report
        $rg->generateReport();    

        // Get namespace
        $namespace = $smarty->get_template_vars($name.'_namespace');

        $smarty->assign('table', $rg->getTable());
        $smarty->assign('table_desc', $rg->getTable(True));
        $smarty->assign('fields', $rg->getFields(True));
        $smarty->assign('operators', $rg->getOperators(True));
        $smarty->assign('combines', $rg->getCombines(True));
        $smarty->assign('id', $rg->getReportId());
        $smarty->assign('uri', url::current());
        $smarty->assign('namespace', $namespace);        

        return $smarty->fetch(Arag::find_file('report_generator', 'views', $template, False, $ext));
    }

    return Null;
}

?>
