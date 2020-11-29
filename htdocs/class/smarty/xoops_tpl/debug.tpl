{* Smarty *}
{* debug.tpl, last updated version 2.1.0 *}
{* @author     debianus *}
{* @copyright  http://www.impresscms.org/ The ImpressCMS Project *}
{* @license	   https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL) *}
{assign_debug_info}
{capture assign=debug_output}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <title>Smarty Debug Console</title>
{literal}
<style type="text/css">
/* <![CDATA[ */
body, h1, h2, td, th, p {
    font-family: sans-serif;
    font-weight: normal;
    font-size: 0.9em;
    margin: 1px;
    padding: 0;
}

h1 {
  background-color: #2b5071;
  color: #f3f3f3;
  font-size: 1.2em;
  font-weight: bold;
  margin: 0;
  padding: 3px;
  text-align: left;
}

h2 {
    background: none repeat scroll 0 0 #428bca;
    border: medium none;
    color: #fff;
    text-align: left;
    font-weight: bold;
    padding: 4px;
}

body {
    background: #fff;
}

p {
    margin: 0;
    font-style: italic;
    text-align: center;
}

table {
    width: 100%;
}
th {width:30%}
td {width: 70%}
th, td {
    padding: 4px;
    font-family: monospace;
    vertical-align: top;
    text-align: left;
     border: 1px solid #ddd;
}

td {
    color: green;
}
.odd {
  background-color: #f7f7f7;
}

.even {
    background-color: #fafafa;
}
tr:hover {background-color:#ededed; transition: all 0.2s ease-out 0s;}

.exectime {
    font-size: 0.8em;
    font-style: italic;
}

#table_assigned_vars th {
   color:#1d1f5b
}

#table_config_vars th {
    color: maroon;
}
#Included > div {
    padding: 15px;
}
#Included > div p{
    text-align:left;
    font-style: normal
}
/* Tabs */
        ul#Tabs {
            font-size: 12px;
            font-weight: bold;
            list-style-type: none;
            padding-bottom: 28px;
            border-bottom: 1px solid #dcdcdc;
            margin-bottom: 12px;
            z-index: 1;
        }
        #Tabs li.Tab {
            float: left;
            height: 25px;
            background-color: #efefef;
            margin: 2px 0px 0px 5px;
            border: 1px solid #dcdcdc;
            border-radius: 4px 4px 0 0;
        }
        #Tabs li.Tab a {
            float: left;
            padding: 5px 12px 0;
            display: block;
            color: #666666;
            text-decoration: none
        }
        #Tabs li.Tab a:hover {
            background-color: #e9e9e9;
            border:none
        }
        /* Selected Tab */
        #Tabs li.SelectedTab {
            float: left;
            height: 25px;
            margin: 0 4px 0 8px;
            padding: 2px 10px 0;
            background-color: #fff;
            border: 1px solid #f3f3f3;
            border-radius: 4px 4px 0 0;
        }
        #Tabs li.SelectedTab a {
            float: left;
            display: block;
            color: #666666;
            text-decoration: none;
            padding: 5px 12px 0;
            cursor: default;
            border:none
        }
/* ]]> */
</style>
<script type="text/javascript">
        function included() {
            // Tab
            document.getElementById('includedTab').className = 'SelectedTab';
            document.getElementById('assignedTab').className = 'Tab';
            document.getElementById('outerTab').className = 'Tab';
            // Page
            document.getElementById('Included').style.display= 'block';
            document.getElementById('Assigned').style.display = 'none';
            document.getElementById('Outer').style.display = 'none';
        }
        function assigned() {
            // Tab
            document.getElementById('includedTab').className = 'Tab';
            document.getElementById('assignedTab').className = 'SelectedTab';
            document.getElementById('outerTab').className = 'Tab';
            // Page
            document.getElementById('Included').style.display = 'none';
            document.getElementById('Assigned').style.display = 'block';
            document.getElementById('Outer').style.display = 'none';
        }
        function outer() {
            // Tab
            document.getElementById('includedTab').className = 'Tab';
            document.getElementById('assignedTab').className = 'Tab';
            document.getElementById('outerTab').className = 'SelectedTab';
            // Page
            document.getElementById('Included').style.display= 'none';
            document.getElementById('Assigned').style.display = 'none';
            document.getElementById('Outer').style.display = 'block';
        }
    </script>
{/literal}
</head>
<body>
<div id="Container">
    <div><h1>Smarty Debug Console</h1></div>
    <ul id="Tabs">
        <li id="includedTab" class="SelectedTab"><a href="#Included" onclick="included(); return false;" title="included">Included</a></li>
        <li id="assignedTab" class="Tab"><a href="#Assigned" onclick="assigned(); return false;" title="assigned">Assigned</a></li>
        <li id="outerTab" class="Tab"><a href="#Outer" onclick="outer(); return false;" title="outer">Outer</a></li>
    </ul>
    <div id="Content">
        <div id="Included">
            <h2>Included templates &amp; config files (load time in seconds)</h2>
            <div>
                {section name=templates loop=$_debug_tpls}
                    {section name=indent loop=$_debug_tpls[templates].depth}&nbsp;&nbsp;&nbsp;{/section}
                    <p style="color:{if $_debug_tpls[templates].type eq "template"}#666{elseif $_debug_tpls[templates].type eq "insert"}black{else}green{/if}">
                        {$_debug_tpls[templates].filename|escape:html}
                    {if isset($_debug_tpls[templates].exec_time)}
                        <span class="exectime">
                        ({$_debug_tpls[templates].exec_time|string_format:"%.5f"})
                        {if %templates.index% eq 0}(total){/if}
                        </span>
                    {/if}</p>
                {sectionelse}
                <p>No templates included</p>
                {/section}

            </div>
        </div>
        <div id="Assigned" style="display: none;">
            <h2>Assigned template variables</h2>
            <table id="table_assigned_vars">
            {section name=vars loop=$_debug_keys}
                <tr class="{cycle values="odd,even"}">
                    <th>{ldelim}${$_debug_keys[vars]|escape:'html'}{rdelim}</th>
                    <td>{$_debug_vals[vars]|@debug_print_var:0:400}</td></tr>
            {sectionelse}
                <tr><td><p>No template variables assigned</p></td></tr>
            {/section}
            </table>
        </div>
        <div id="Outer" style="display: none;">
            <h2>Assigned config file variables (outer template scope)</h2>
            <table id="table_config_vars">
            {section name=config_vars loop=$_debug_config_keys}
                <tr class="{cycle values="odd,even"}">
                    <th>{ldelim}#{$_debug_config_keys[config_vars]|escape:'html'}#{rdelim}</th>
                    <td>{$_debug_config_vals[config_vars]|@debug_print_var}</td></tr>
            {sectionelse}
                <tr><td><p>no config vars assigned</p></td></tr>
            {/section}
            </table>
        </div>
    </div>
</div>
</body>
</html>
{/capture}
{if isset($_smarty_debug_output) and $_smarty_debug_output eq "html"}
    {$debug_output}
{else}
<script type="text/javascript">
// <![CDATA[
    if ( self.name == '' ) {ldelim}
       var title = 'Console';
    {rdelim}
    else {ldelim}
       var title = 'Console_' + self.name;
    {rdelim}
    _smarty_console = window.open("",title.value,"width=680,height=600,resizable,scrollbars=yes");
    _smarty_console.document.write('{$debug_output|escape:'javascript'}');
    _smarty_console.document.close();
// ]]>
</script>
{/if}
