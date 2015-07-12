<?php
function dcl_upgrade0_9_5RC23()
{
    global $phpgw_setup;

    $phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='files' WHERE dcl_config_name='DCL_FILE_PATH' AND dcl_config_varchar = '.'");
    $phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.5RC24' WHERE dcl_config_name='DCL_VERSION'");

    $setup_info['dcl']['currentver'] = '0.9.5RC24';
    return $setup_info['dcl']['currentver'];
}