find . | grep -v .svn | grep 'model.php' | sed -e 's/model.php//' | xargs -I{} svn mv {}model.php {}.php

find . | grep -v .svn | grep -v 'webapp/templates' | grep -v 'webapp/components' | grep -e 'templates$' | xargs -I{} svn mv {} {}/../views

find . -type f | grep -v .svn | grep -v 'convert' | xargs grep -e "->item('Arag_" -l | xargs sed -i -e "s/->item('Arag_/->item('arag./"

find . -type f| grep -v .svn | grep -v 'convert' | xargs grep 'No direct script access allowed' -l | xargs sed -i -e "s/ if (\!defined('BASEPATH')) exit('No direct script access allowed');/defined('SYSPATH') or die('No direct script access.');/"

find . -type f | grep -v .svn | grep -v 'convert' | grep -v 'libraries' | grep -v docs.wiki | xargs grep 'parent::Arag_Controller' -l | xargs sed -i -e 's/parent::Arag_Controller/parent::__construct/'

find . -type f | grep -v .svn | grep -v 'convert' | grep -v 'libraries' | grep -v docs.wiki | xargs grep 'class [a-zA-Z]\+ extends Arag_Controller' -l | xargs sed -i -e 's/\(class [a-zA-Z]\+\) extends Arag_Controller/\1_Controller extends Controller/'

find . -type f | grep -v .svn | grep -v 'convert' | grep -v cache | grep -v 'libraries' | grep -v docs.wiki | xargs sed -e '/^.*Constructor/{:top;/^.*Constructor.*function.*parent::/!{N; b top;};s/function.*(\(.*\)parent::.*(/function __construct(\1parent::__construct(/};' -i

find . -type f | grep -v .svn | grep -v 'convert' | grep -v 'libraries' | grep -v docs.wiki | xargs grep -e '[A-Za-z]\+Model extends' -l | xargs sed -i -e 's/\([A-Za-z]\+\)Model extends/\1_Model extends/'

find . -type f | grep -v .svn | grep -v 'convert' | grep -v 'libraries' | grep -v docs.wiki | xargs grep -e 'class [a-zA-Z]\+ extends Backend[^a-zA-Z]*' -l | xargs sed -i -e 's/\(class [a-zA-Z]\+\) extends Backend\([^a-zA-Z]*\)/\1_Controller extends Backend_Controller\2/'

find . -type f | grep -v .svn | grep -v 'convert' | grep -v 'libraries' | grep -v docs.wiki | xargs grep -e '[a-zA-Z]\+Model' -l | xargs sed -i -e 's/\([a-zA-Z]\+\)Model/\1/g'

find . -type f | grep -v .svn | grep -v 'convert' | grep -v 'libraries' | grep -v docs.wiki | xargs grep -e '\$[a-zA-Z_0-9]\+->load->database()' -l | xargs sed -i -e 's/^.*\(\$[a-zA-Z_0-9]\+->load->database();\)/\/\/ \1/g'

find . -type f | grep -v .svn | grep -v 'convert' | grep -v 'libraries' | grep -v docs.wiki | xargs grep -e '{site_url' -l | xargs sed -i -e 's/{site_url/{url_site/g'

find . -type f | grep -v .svn | grep -v 'convert' | grep -v 'libraries' | grep -v docs.wiki | xargs grep -e 'site_url(' -l | xargs sed -i -e 's/site_url(/url::site(/g'

find . -type f | grep -v .svn | grep -v 'convert' | grep -v 'libraries' | grep -v docs.wiki | xargs grep -e '\$.*->load->helper(.*);' -l | xargs sed -i -e 's/^.*\($.*->load->helper(.*);\)/\/\/ \1/g'

find . -type f | grep -v .svn | grep -v 'convert' | grep -v 'libraries' | grep -v docs.wiki | xargs grep -e '{slot.*}' -l | xargs sed -i -e 's/{slot name="\(.*\)"}/{$\1|smarty:nodefaults}/g'

find . -type f | grep -v .svn | grep -v 'convert' | xargs grep -e "Config::item(.base_url" -l | xargs sed -i -e "s/Config::item(.base_url./url::base(/g"

find . -type f | grep -v .svn | grep -v 'convert' | xargs grep -e "\$[a-zA-Z_>-]*config->item(.base_url" -l | xargs sed -i -e "s/\$[a-zA-Z_>-]*config->item(.base_url./url::base(/g"

find . -type f | grep -v .svn | grep -v 'convert' | grep 'modules' | xargs grep -e "\$[a-zA-Z_>-]*config->set_item(" -l | xargs sed -i -e "s/\$[a-zA-Z_>-]*config->set_item(/Arag_Config::set(/g"

find . -type f | grep -v .svn | grep -v 'convert' | grep 'modules' | xargs grep -e "\$[a-zA-Z_>-]*config->item(" -l | xargs sed -i -e "s/\$[a-zA-Z_>-]*config->item(\([^,]\+\),\s*\([^,]\+\),\s*\([^)]\+\))/Arag_Config::get(\1, \3)/g"

find . -type f | grep -v .svn | grep -v 'convert' | grep 'modules' | xargs grep -e "\$[a-zA-Z_>-]*config->item(" -l | xargs sed -i -e "s/\$[a-zA-Z_>-]*config->item(/Arag_Config::get(/g"

find . -type f | grep -v .svn | grep -v 'convert' | xargs grep -e "\$[a-zA-Z_>-]*config->item(" -l | xargs sed -i -e "s/\$[a-zA-Z_>-]*config->item(/Config::item(/g"

find . -type f | grep -v .svn | grep -v 'convert' | xargs grep -e "redirect(" -l | xargs sed -i -e "s/\sredirect(\(.*\))/ url::redirect(\1)/g"

find . -type f | grep -v .svn | grep -v 'convert' | grep -v 'libraries' | grep -v docs.wiki | xargs grep -e '{anchor.*}' -l | xargs sed -i -e 's/{anchor\(.*\)}/{html_anchor\1}/g'

find . -type f | grep -v .svn | grep -v 'convert' | grep -v docs.wiki | xargs grep -e '->result_array()' -l | xargs sed -i -e 's/->result_array()/->result(False)/g'

find . -type f | grep -v .svn | grep -v 'convert' | grep -v docs.wiki | xargs grep -e '->result_array(False)' -l | xargs sed -i -e 's/->result_array(False)/->result(False)/g'

find . -type f | grep -v .svn | grep -v 'convert' | grep -v docs.wiki | xargs grep -e "->row()" -l | xargs sed -i -e "s/->row()/->current()/g"

find . -type f | grep -v .svn | grep -v 'convert' | grep -v 'libraries' | grep -v docs.wiki | xargs grep -e '->session->userdata(.*)' -l | xargs sed -i -e 's/->[Ss]ession->userdata(\(.*\))/->session->get(\1)/g'

find . -type f | grep -v .svn | grep -v 'convert' | grep -v 'libraries' | grep -v docs.wiki | xargs grep -e '->session->set_userdata(.*)' -l | xargs sed -i -e 's/->[Ss]ession->set_userdata(\(.*\))/->session->set(\1)/g'

find . -type f | grep -v .svn | grep -v 'convert' | grep -v 'libraries' | grep -v docs.wiki | xargs grep -e '->session->unset_userdata(.*)' -l | xargs sed -i -e 's/->[Ss]ession->unset_userdata(\(.*\))/->session->del(\1)/g'

find . -type f | grep -v .svn | grep -v 'convert' | grep -v 'libraries' | grep -v docs.wiki | xargs grep -e '->sess_destroy(' -l | xargs sed -i -e 's/->sess_destroy(/->destroy(/g'

find . -type f | grep -v .svn | grep -v 'convert' | grep -v docs.wiki | xargs grep -e '->result_array()' -l | xargs sed -i -e 's/->result_array()/->result(False)/g'

find . -type f | grep -v .svn | grep -v 'convert' | grep -v docs.wiki | xargs grep -e 'load->model(Array([^,]\+, [^)]\+).*)' -i -l | xargs sed -i -e "s/load->model(Array(.\([^,]\+\)., .\([^)]\+\).)\(.*\))/load->model('\1'\3, '\2')/ig"

find . -type f | grep -v .svn | grep -v 'convert' | grep -v docs.wiki | grep -e 'modules/.*/models' | xargs grep -e 'CI =& get_instance' -l | xargs sed -i -e 's/CI =& get_instance/controller = Kohana::instance/g'

find . -type f | grep -v .svn | grep -v 'convert' | grep -v docs.wiki | grep -e 'modules/.*/models' | xargs grep -e '$CI' -l | xargs sed -i -e 's/$CI/$controller/g'

find . -type f | grep -v .svn | grep -v 'convert' | grep -v docs.wiki | grep -e 'modules/.*/models' | xargs grep -e '->row_array()' -l | xargs sed -i -e 's/=\( .*->\)row_array()/= (Array)\1current()/g'

find . -type f | grep -v .svn | grep -v 'convert' | grep -v docs.wiki | grep -e 'modules/.*/models' | xargs grep -e 'db->dbprefix' -l | xargs sed -i -e 's/db->dbprefix/tablePrefix/g'

# validators should be converted by hand
# 'numeric' in validator shuold be changed to valid_type
# get validators started from 0 instead 1
# routes should be converted by hand
# add a $tablePrefix vriable to your model
# cleanup all '^//'es 
