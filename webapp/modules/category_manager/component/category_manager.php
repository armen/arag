<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Peyman Karimi <peykar@gmail.com>                                |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

/*
 * Class for managing categories
 *
 * @author  Peyman Karimi <peykar@gmail.com>
 * @since   PHP 5
 */

class Category_Manager_Component extends Component
{
    // {{{ Properties
    private $message = Null;

    // }}}
    // {{{ Constructor
    public function __construct($name, $namespace, $module = Null, $appname = Null)
    {
        parent::__construct($namespace, $module);

        // Loading Models
        $this->categoryManager = new Category_Manager_Model;

        $this->name      = $name;
        $this->namespace = $namespace;
        $this->appname   = $appname ? $appname : APPNAME;
        $this->module    = $module;
        $this->plistName = $this->namespace.'_categories';

        // Set default URI
        $uri = trim(Router::$routed_uri, '/').'/'; // Add trailing slash
        if (Router::$method == 'index' && strpos($uri, 'index') === False) {
            $uri .= 'index/';
        }

        if (preg_match('/action_(delete|delete_confirm)_'.$namespace.'\/([0-9]+)\//', $uri, $matches)) {
            // We will not handle actions here, because we need $this->currenctCategoryURI in confirm_delete action handler
            // so we must make this property first and then do our action handling
            $matched_uri = array_shift($matches);
            $action      = $matches;

            // We must remove the "action part" of uri from it.
            $uri         = str_replace($matched_uri, null, $uri);
        }

        if (preg_match('/category_'.$namespace.'\/([0-9]+)\//', $uri, $matches)) {
            // Check if page parameter is already in uri
            $this->currentCategory = $matches[1];
            $uri                   = str_replace($matches[0], null, $uri);
        } else {
            if ($parent = $this->categoryManager->getCategory(null, $namespace, $name, $module, $this->appname)) {
                $this->currentCategory = $parent['id'];
            } else {
                $this->currentCategory = $this->categoryManager->createCategory($namespace, $name, $module, $this->appname);
            }
            $uri = str_replace("category_{$namespace}/", null, $uri);
        }

        $this->currentCategory = $this->categoryManager->getCategory($this->currentCategory, null, null, $this->module, $this->appname);

        // Check if requested category is is not for current appname or does not exist
        if (!$this->currentCategory) {
            Kohana::instance()->_invalid_request();
        }

        $this->baseURI            = $uri;
        $this->currentCategoryURI = $this->baseURI.'category_'.$this->namespace.'/'.$this->currentCategory['id'].'/';

        // Handle Get Actions
        if (isset($action)) {
            $this->_handleAction($action[0], $action[1]);
        }

        // Handle Post Actions. (Add, Edit)
        $this->_handleForms();

        $categories = $this->categoryManager->getCategories($this->currentCategory['id']);
        $plist = new Plist_Component($this->plistName);
        $plist->setResource($categories);
        $plist->addColumn('name', _("Name"));
        $plist->addColumn('label', _("Label"));
        $plist->addAction($this->baseURI.'category_'.$this->namespace.'/'.'#id#/', _('View sub categories'), 'view_tree');
        $plist->addAction($this->currentCategoryURI.'action_delete_confirm_'.$this->namespace.'/'.'#id#/', _('Delete'), 'delete_action');

        // Prepare breadcrumb's feed
        $this->_prepareBreadcrumb();
    }
    // }}}
    // {{{ _prepareBredcrumb
    private function _prepareBreadcrumb() {
        $this->path     = array();
        $pathCategories = $this->categoryManager->getPath($this->currentCategory['id']);
        foreach($pathCategories as $pathCategory) {
            $category = $this->categoryManager->getCategory($pathCategory);
            $this->path[] = array('title' => $category['label'],
                                  'uri'   => $this->baseURI.'category_'.$this->namespace.'/'.$category['id']);
        }
        $this->breadcrumbName = $this->namespace.'_breadcrumb';
        $this->breadcrumb = new BreadCrumb_Component($this->breadcrumbName, true);
    }
    // }}}
    // {{{ _handleAction
    private function _handleAction($action, $id) {
        if ($action == 'delete') {
            $this->categoryManager->deleteCategory($id, $this->appname);
        } elseif ($action == 'delete_confirm') {
            $session  = Session::instance();
            $key      = sha1(microtime());
            $yes_uri  = $this->currentCategoryURI.'action_delete_'.$this->namespace.'/'.$id.'/';
            $no_uri   = $this->currentCategoryURI;

            $category = $this->categoryManager->getCategory($id);
            if ($category) {
                $session->set_flash('category_manager.'.$key.'.yes_uri', $yes_uri);
                $session->set_flash('category_manager.'.$key.'.no_uri', $no_uri);
                $session->set_flash('category_manager.'.$key.'.entity_name', $category['label']);
            }
            url::redirect('category_manager/backend/confirm_delete/'.urlencode($key));
        }
    }
    // }}}
    // {{{ _handleFroms
    private function _handleForms() {
        if ($this->_validateForms()) {
            $input  = Input::instance();
            $action = $input->post('action');

            if ($action == 'add') {
                $name  = $input->post('name_add');
                $label = $input->post('label_add');

                $this->categoryManager->createCategory($name, $label, $this->module, $this->appname, $this->currentCategory['id']);

                $this->message = _('Category has been added successfully');

            } elseif ($action == 'edit') {
                // Check if it is Grand-parent. We should not change name of grand-parent categories. Grand-parent categories are those categories that
                // their parents are null
                $name  = $this->currentCategory['parent'] ? $input->post('name_edit') : $this->currentCategory['name'];
                $label = $input->post('label_edit');

                $this->categoryManager->editCategory($this->currentCategory['id'], $name, $label, $this->module, $this->appname,
                                                     $this->currentCategory['parent']);

                // because of edit, we must update $this->currentCategory
                $this->currentCategory = $this->categoryManager->getCategory($this->currentCategory['id']);
                $this->message = _('Category has been edited successfully');
            }
        }
    }
    // }}}
    // {{{ getMessage()
    public function getMessage()
    {
        return $this->message;
    }
    // }}}
    // {{{ _validateForms
    private function _validateForms() {
        $input      = Input::instance();
        $controller = Kohana::instance();
        $validation = $controller->validation;
        $action     = $input->post('action');

        // Validation Messages
        $validation->message('alpha', _("%s should be ordinary text."));
        $validation->message('required', _("%s is required"));

        if ($action == 'add') {
            $validation->name('name_add', _("Name"))->add_rules('name_add', 'required', 'alpha');
            $validation->name('label_add', _("Label"))->add_rules('label_add', 'required');
        } elseif ($action == 'edit') {
            $this->currentCategory['parent'] && $validation->name('name_edit', _("Name"))->add_rules('name_edit', 'required', 'alpha');
            $validation->name('label_edit', _("Label"))->add_rules('label_edit', 'required');
        }
        return $validation->validate();
    }
    // }}}
}
