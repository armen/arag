{* Smarty *}
{*
    vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
    File: $Id: index.tpl 53 2007-10-11 18:38:57Z sasan $
*}
{arag_load_script src="scripts/mootools.js"}
{arag_load_script src="scripts/mootools-more.js"}
{arag_load_script src="modpub/user_profile/change_cities.js"}
<script type="text/javascript">
    var defaultCountry = {$defaults.country}
    {literal}
        window.addEvent('domready', function() {
            $('country').addEvent('change', function(e){
                e.stop();

                var list_of_classes = $$('.cities_province');
                var countryValue    = $('country').getProperty('value');

                if (countryValue != defaultCountry) {
                    list_of_classes.each(function(myDiv) {
                        var divToHide = new Fx.Slide(myDiv);
                        divToHide.slideOut();
                    });
                } else {
                    list_of_classes.each(function(myDiv) {
                        if (myDiv.getStyle('display') == 'none') {
                            myDiv.erase('style');
                        }
                        var divToShow = new Fx.Slide(myDiv);
                        divToShow.slideIn();
                    });
                }
            });


            $('province').addEvent('change', function(e) {
                e.stop();
                var jsonUrl = '{/literal}{$arag_base_url|smarty:nodefaults}{literal}index.php/user_profile/get_cities/get_cities_of/' +
                               $('province').getProperty('value');
                var request = new Request.JSON({
                    url: jsonUrl,
                    onComplete: function(jsonObj) {
                        changeCities(jsonObj.cities);
                    }
                }).send();
            });
        });
    {/literal}
</script>
{assign var=uri value="user_profile/%section%/index"}
{arag_validation_errors}
{if !$isset_profile}
    {arag_block align="left" template="warning"}
        _("You hadn't enter your personal information yet")
    {/arag_block}
{/if}
{if $flagsaved}
    {arag_block align="left" template="info"}
        _("Profile edited successfuly!")
    {/arag_block}
{else}
    {arag_block}
        {arag_form uri=$uri|replace:"%section%":$section}
            <table border="0" dir="{dir}">
                <tr>
                    <td align="{right}">
                        _("Username"):
                    </td>
                    <td align="{left}">
                        {$username|smarty:nodefaults|default:null}
                    </td>
                </tr>
                <tr>
                    <td align="{right}">
                        _("Name"):
                    </td>
                    <td align="{left}">
                        {$name|smarty:nodefaults|default:null}
                    </td>
                </tr>
                <tr>
                    <td align="{right}">
                        _("Last Name"):
                    </td>
                    <td align="{left}">
                        {$lastname|smarty:nodefaults|default:null}
                    </td>
                </tr>
                <tr>
                    <td align="{right}">
                        _("Email"):
                    </td>
                    <td align="{left}">
                        {$email|smarty:nodefaults|default:null}
                    </td>
                </tr>
                <tr>
                    <td align="{right}" colspan="2">
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td align="{right}">
                        _("Phone"):{asterisk}
                    </td>
                    <td align="{left}">
                        <input type="text" name="phone" value="{$phone|smarty:nodefaults|default:null}" dir="ltr" />
                    </td>
                </tr>
                <tr>
                    <td align="{right}">
                        _("Cellphone"):
                    </td>
                    <td align="{left}">
                        <input type="text" name="cellphone" value="{$cellphone|smarty:nodefaults|default:null}" dir="ltr" />
                    </td>
                </tr>
                <tr>
                    <td align="{right}" colspan="2">
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td align="{right}">
                        _("Country"):{asterisk}
                    </td>
                    <td align="{left}">
                        <select name="country" style="width:180px" id="country">
                            {foreach from=$countries item=item}
                                {if !$isset_profile && $item.id == $defaults.country}
                                    <option value="{$item.id}" selected="selected">
                                {elseif $isset_profile && $item.id == $country}
                                    <option value="{$item.id}" selected="selected">
                                {else}
                                    <option value="{$item.id}">
                                {/if}
                                    {$item.country}
                                </option>
                            {/foreach}
                        </select>
                    </td>
                </tr>
                <tr>
                    <td align="{right}">
                        {if $isset_profile && $country != $defaults.country}
                            <div class="cities_province" style="display: none">
                        {else}
                            <div class="cities_province">
                        {/if}
                            _("Province"):{asterisk}
                        </div>
                    </td>
                    <td align="{left}">
                        {if $isset_profile && $country != $defaults.country}
                            <div class="cities_province" style="display: none">
                        {else}
                            <div class="cities_province">
                        {/if}
                            <select name="province" style="width:180px" id="province">
                                {foreach from=$provinces item=item}
                                    {if !$isset_profile && $item.id == $defaults.province}
                                        <option value="{$item.id}" selected="selected">
                                    {elseif $isset_profile && $item.id == $province}
                                        <option value="{$item.id}" selected="selected">
                                    {else}
                                        <option value="{$item.id}">
                                    {/if}
                                        {$item.province}
                                    </option>
                                {/foreach}
                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td align="{right}">
                        {if $isset_profile && $country != $defaults.country}
                            <div class="cities_province" style="display: none">
                        {else}
                            <div class="cities_province">
                        {/if}
                            _("City"):{asterisk}
                        </div>
                    </td>
                    <td align="{left}" id="cities">
                        {if $isset_profile && $country != $defaults.country}
                            <div class="cities_province" style="display: none">
                        {else}
                            <div class="cities_province">
                        {/if}
                        <select name="city" style="width:180px" id="city">
                            {foreach from=$cities item=item}
                                {if !$isset_profile && $item.code == $defaults.city}
                                    <option value="{$item.code}" selected="selected">
                                {elseif $isset_profile && $item.code == $city}
                                    <option value="{$item.code}" selected="selected">
                                {else}
                                    <option value="{$item.code}">
                                {/if}
                                    {$item.city}
                                </option>
                            {/foreach}
                        </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td align="{right}" colspan="2">
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td align="{right}">
                        _("Address"):{asterisk}
                    </td>
                    <td align="{left}">
                        <input type="text" name="address" value="{$address|smarty:nodefaults|default:null}" />
                    </td>
                </tr>
                <tr>
                    <td align="{right}">
                        _("Postal Code"):{asterisk}
                    </td>
                    <td align="{left}">
                        <input type="text" name="postal_code" value="{$postal_code|smarty:nodefaults|default:null}" dir="ltr" />
                    </td>
                </tr>
                <tr>
                    <td align="{right}" colspan="2">
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td align="{right}">
                        &nbsp;
                    </td>
                    <td align="{left}">
                        <input type="submit" name="submit" value={quote}_("Submit"){/quote} />
                    </td>
                </tr>
            </table>
        {/arag_form}
    {/arag_block}
{/if}
