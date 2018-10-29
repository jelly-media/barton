<?php

/**
 * Class A_AutoUpdate_Admin_Factory
 * @mixin C_Component_Factory
 * @adapts I_Component_Factory
 */
class A_AutoUpdate_Admin_Factory extends Mixin
{
    function autoupdate_admin_controller($context = null)
    {
        return new C_AutoUpdate_Admin_Controller();
    }
}
