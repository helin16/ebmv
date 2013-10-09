<?php
/**
 * Menu template
 *
 * @package    Web
 * @subpackage Layout
 * @author     lhe
 */
class Menu extends TTemplateControl
{
    public function logout($sender, $params)
    {
        $auth = $this->getApplication()->Modules['auth'];
        $auth->logout();
        $this->Response->Redirect("/admin/");
    }
}