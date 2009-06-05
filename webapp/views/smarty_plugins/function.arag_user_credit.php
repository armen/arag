<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_arag_user_credit($params, &$smarty)
{
    $transactionManager = Model::load('TransactionManager', 'transaction_manager');
    $credit             = $transactionManager->getCredit(Session::instance()->get('user.username'));

    if (isset($params['exact_number']) && $params['exact_number']) {
        return $credit;
    }

    return _("Your credit: ").format::money($credit, 0, ".", ",", True);
}
