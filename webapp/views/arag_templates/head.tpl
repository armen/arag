{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id$
*}

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="icon" href="{$arag_base_url|smarty:nodefaults}{$theme}/images/favicon.ico" type="image/x-icon" />
<link rel="stylesheet" media="all" type="text/css" href="{$arag_base_url|smarty:nodefaults}modpub/{$arag_current_module}/styles.css" />
<link rel="stylesheet" media="all" type="text/css" href="{$arag_base_url|smarty:nodefaults}styles/styles.css" />
<link rel="stylesheet" media="all" type="text/css" href="{$arag_base_url|smarty:nodefaults}scripts/JalaliJSCalendar/skins/calendar-blue.css" />
{arag_load_script src="scripts/mask.js"}
<link rel="stylesheet" media="all" type="text/css" href="{$arag_base_url|smarty:nodefaults}scripts/MoodalBox/css/moodalbox.css" />
{literal}
<script type="text/javascript">
blank_image = '{/literal}{$arag_base_url}{literal}images/misc/blank.gif';
</script>
{/literal}
<style type="text/css">
{literal}
body { behavior: url('{/literal}{$arag_base_url}{literal}/scripts/whateverhover.htc') }
/* * { behavior: url('{/literal}{$arag_base_url}{literal}/scripts/iepngfix.htc') } */
{/literal}
</style>
{foreach item='header' from=$headers|smarty:nodefaults}
    {$header|smarty:nodefaults}
{/foreach}