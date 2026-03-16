<?php

use InvalidArgumentException;
use Xmf\Request;

const PREFIX_INVALID_CHAR_PATTERN = '/[^0-9A-Za-z_-]/';

/**
 * Validate a DB prefix: reject if it contains characters outside [A-Za-z0-9_-].
 * Returns the validated prefix unchanged.
 *
 * @throws InvalidArgumentException if the prefix contains invalid characters
 */
function validatePrefix(string $raw): string
{
    if (preg_match(PREFIX_INVALID_CHAR_PATTERN, $raw)) {
        throw new InvalidArgumentException('Invalid prefix: contains disallowed characters');
    }
    return $raw;
}

include XOOPS_ROOT_PATH . '/include/cp_header.php';
include __DIR__ . '/admin_header.php';
require_once dirname(__DIR__) . '/class/gtickets.php';
$db = XoopsDatabaseFactory::getDatabaseConnection();

// COPY TABLES
if (Request::hasVar('copy', 'POST') && Request::hasVar('old_prefix', 'POST')) {
    try {
        $new_prefix = validatePrefix(Request::getString('new_prefix', '', 'POST'));
        $old_prefix = validatePrefix(Request::getString('old_prefix', '', 'POST'));
    } catch (InvalidArgumentException $e) {
        redirect_header(XOOPS_URL . '/modules/protector/admin/prefix_manager.php', 3, $e->getMessage());
    }

    // Ticket check
    if (!$xoopsGTicket->check(true, 'protector_admin')) {
        redirect_header(XOOPS_URL . '/', 3, $xoopsGTicket->getErrors());
    }

    $new_prefix = empty($new_prefix) ? 'x' . substr(bin2hex(random_bytes(4)), -5) : $new_prefix;

    $sql = 'SHOW TABLE STATUS FROM `' . XOOPS_DB_NAME . '`';
    $srs = $db->query($sql);
    if (!$db->isResultSet($srs)) {
        throw new \RuntimeException(
            \sprintf(_DB_QUERY_ERROR, $sql) . $db->error(),
            E_USER_ERROR,
        );
    }

    if (!$db->getRowsNum($srs)) {
        die('You are not allowed to copy tables');
    }

    $count = 0;
    while (false !== ($row_table = $db->fetchArray($srs))) {
        ++$count;
        $old_table = $row_table['Name'];
        if (substr($old_table, 0, strlen($old_prefix) + 1) !== $old_prefix . '_') {
            continue;
        }

        $new_table = $new_prefix . substr($old_table, strlen($old_prefix));

        $sql = 'SHOW CREATE TABLE ' . $old_table;
        $crs = $db->query($sql);
        if (!$db->isResultSet($crs)) {
            throw new \RuntimeException(
                \sprintf(_DB_QUERY_ERROR, $sql) . $db->error(),
                E_USER_ERROR,
            );
        }

        if (!$db->getRowsNum($crs)) {
            echo "error: SHOW CREATE TABLE ($old_table)<br>\n";
            continue;
        }
        $row_create = $db->fetchArray($crs);
        $create_sql = preg_replace("/^CREATE TABLE `$old_table`/", "CREATE TABLE `$new_table`", $row_create['Create Table'], 1);

        $crs = $db->exec($create_sql);
        if (!$crs) {
            echo "error: CREATE TABLE ($new_table)<br>\n";
            continue;
        }

        $irs = $db->exec("INSERT INTO `$new_table` SELECT * FROM `$old_table`");
        if (!$irs) {
            echo "error: INSERT INTO ($new_table)<br>\n";
            continue;
        }
    }

    $_SESSION['protector_logger'] = $xoopsLogger->dump('queries');

    redirect_header('index.php?page=prefix_manager', 1, _AM_MSG_DBUPDATED);
    exit;

    // DUMP INTO A LOCAL FILE
} elseif (Request::hasVar('backup', 'POST') && Request::hasVar('prefix', 'POST')) {
    try {
        $prefix = validatePrefix(Request::getString('prefix', '', 'POST'));
    } catch (InvalidArgumentException $e) {
        redirect_header(XOOPS_URL . '/modules/protector/admin/prefix_manager.php', 3, $e->getMessage());
    }

    // Ticket check
    if (!$xoopsGTicket->check(true, 'protector_admin')) {
        redirect_header(XOOPS_URL . '/', 3, $xoopsGTicket->getErrors());
    }

    // get table list
    $sql = 'SHOW TABLE STATUS FROM `' . XOOPS_DB_NAME . '`';
    $srs = $db->query($sql);
    if (!$db->isResultSet($srs)) {
        throw new \RuntimeException(
            \sprintf(_DB_QUERY_ERROR, $sql) . $db->error(),
            E_USER_ERROR,
        );
    }
    if (!$db->getRowsNum($srs)) {
        die('You are not allowed to delete tables');
    }

    $exportString = '';
    $rowLimit = 100;

    while (false !== ($row_table = $db->fetchArray($srs))) {
        $table = $row_table['Name'];
        if (substr($table, 0, strlen($prefix) + 1) !== $prefix . '_') {
            continue;
        }
        $sql = "SHOW CREATE TABLE `$table`";
        $drawCreate = $db->query($sql);
        if (!$db->isResultSet($drawCreate)) {
            throw new \RuntimeException(
                \sprintf(_DB_QUERY_ERROR, $sql) . $db->error(),
                E_USER_ERROR,
            );
        }

        $create = $db->fetchRow($drawCreate);
        $db->freeRecordSet($drawCreate);

        $exportString .= "\nDROP TABLE IF EXISTS `$table`;\n{$create[1]};\n\n";
        $sql      = "SELECT * FROM `$table`";
        $result = $db->query($sql);
        if (!$db->isResultSet($result)) {
            throw new \RuntimeException(
                \sprintf(_DB_QUERY_ERROR, $sql) . $db->error(),
                E_USER_ERROR,
            );
        }
        $fieldCount  = $db->getFieldsNum($result);

        $insertValues = '';

        if ($db->getRowsNum($result) > 0) {
            $fieldInfo = [];
            $insertNames = "INSERT INTO `$table` (";
            for ($j = 0; $j < $fieldCount; ++$j) {
                $field = $result->fetch_field_direct($j);
                $fieldInfo[$field->name] = $field;
                $insertNames .= ((0 === $j) ? '' : ', ') . $field->name;
            }
            $insertNames .= ")\nVALUES\n";

            $rowCount = 0;
            $insertValues = $insertNames;
            while (false !== ($row = $db->fetchArray($result))) {
                if ($rowCount >= $rowLimit) {
                    $insertValues .= ");\n\n" . $insertNames;
                    $rowCount = 0;
                }
                $insertValues .= (0 === $rowCount++) ? '(' : "),\n(";
                $firstField = true;
                foreach ($fieldInfo as $name => $field) {
                    if (null === $row[$name]) {
                        $value = 'null';
                    } else {
                        switch ($field->type) {
                            case MYSQLI_TYPE_NULL:
                                $value = 'NULL';
                                break;
                            case MYSQLI_TYPE_DECIMAL:
                            case MYSQLI_TYPE_NEWDECIMAL:
                            case MYSQLI_TYPE_BIT:
                            case MYSQLI_TYPE_TINY:
                            case MYSQLI_TYPE_SHORT:
                            case MYSQLI_TYPE_LONG:
                            case MYSQLI_TYPE_FLOAT:
                            case MYSQLI_TYPE_DOUBLE:
                            case MYSQLI_TYPE_LONGLONG:
                            case MYSQLI_TYPE_INT24:
                                $value = $row[$name];
                                break;
                            default:
                                $value = $db->quote($row[$name]);
                                break;
                        }
                    }
                    $insertValues .= ($firstField ? '' : ', ') . $value;
                    $firstField = false;
                }
            }
            $insertValues .= ");\n\n";
        }

        $exportString .= $insertValues;
        if ($db->isResultSet($result)) {
            $db->freeRecordSet($result);
        }
    }

    header('Content-Type: Application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $prefix . '_' . date('YmdHis') . '.sql"');
    header('Content-Length: ' . strlen($exportString));
    set_time_limit(0);
    echo $exportString;
    exit;

    // DROP TABLES
} elseif (Request::hasVar('delete', 'POST') && Request::hasVar('prefix', 'POST')) {
    try {
        $prefix = validatePrefix(Request::getString('prefix', '', 'POST'));
    } catch (InvalidArgumentException $e) {
        redirect_header(XOOPS_URL . '/modules/protector/admin/prefix_manager.php', 3, $e->getMessage());
    }

    // Ticket check
    if (!$xoopsGTicket->check(true, 'protector_admin')) {
        redirect_header(XOOPS_URL . '/', 3, $xoopsGTicket->getErrors());
    }

    // check if prefix is working
    if ($prefix == XOOPS_DB_PREFIX) {
        die("You can't drop working tables");
    }

    // check if prefix_xoopscomments exists
    $check_rs = $db->query("SELECT 1 FROM `{$prefix}_xoopscomments` LIMIT 1");
    if (!$check_rs) {
        die('This is not a prefix for XOOPS');
    }
    $db->freeRecordSet($check_rs);

    // get table list
    $sql = 'SHOW TABLE STATUS FROM `' . XOOPS_DB_NAME . '`';
    $srs = $db->query($sql);
    if (!$db->isResultSet($srs)) {
        throw new \RuntimeException(
            \sprintf(_DB_QUERY_ERROR, $sql) . $db->error(),
            E_USER_ERROR,
        );
    }
    if (!$db->getRowsNum($srs)) {
        die('You are not allowed to delete tables');
    }

    while (false !== ($row_table = $db->fetchArray($srs))) {
        $table = $row_table['Name'];
        if (substr($table, 0, strlen($prefix) + 1) !== $prefix . '_') {
            continue;
        }
        $drs = $db->exec("DROP TABLE `$table`");
    }

    $_SESSION['protector_logger'] = $xoopsLogger->dump('queries');

    redirect_header('index.php?page=prefix_manager', 1, _AM_MSG_DBUPDATED);
    exit;
}

// begin of Output
xoops_cp_header();
include __DIR__ . '/mymenu.php';

// query
$sql = 'SHOW TABLE STATUS FROM `' . XOOPS_DB_NAME . '`';
$srs = $db->query($sql);
if (!$db->isResultSet($srs)) {
    throw new \RuntimeException(
        \sprintf(_DB_QUERY_ERROR, $sql) . $db->error(),
        E_USER_ERROR,
    );
}
if (!$db->getRowsNum($srs)) {
    die('You are not allowed to copy tables');
    xoops_cp_footer();
    exit;
}

// search prefixes
$tables   = [];
$prefixes = [];
while (false !== ($row_table = $db->fetchArray($srs))) {
    if (substr($row_table['Name'], -6) === '_users') {
        $prefixes[] = [
            'name'    => substr($row_table['Name'], 0, -6),
            'updated' => $row_table['Update_time'],
        ];
    }
    $tables[] = $row_table['Name'];
}

// table
echo '
<h3>' . _AM_H3_PREFIXMAN . "</h3>
<table class='outer' width='95%'>
    <tr>
        <th>" . _AM_PROTECTOR_PREFIX . '</th>
        <th>' . _AM_PROTECTOR_TABLES . '</th>
        <th>' . _AM_PROTECTOR_UPDATED . '</th>
        <th>' . _AM_PROTECTOR_COPY . '</th>
        <th>' . _AM_PROTECTOR_ACTIONS . '</th>
    </tr>
';

foreach ($prefixes as $prefix) {

    // count the number of tables with the prefix
    $table_count       = 0;
    $has_xoopscomments = false;
    foreach ($tables as $table) {
        if ($table == $prefix['name'] . '_xoopscomments') {
            $has_xoopscomments = true;
        }
        if (substr($table, 0, strlen($prefix['name']) + 1) === $prefix['name'] . '_') {
            ++$table_count;
        }
    }

    // check if prefix_xoopscomments exists
    if (!$has_xoopscomments) {
        continue;
    }

    $prefix4disp  = htmlspecialchars($prefix['name'], ENT_QUOTES | ENT_HTML5);
    $ticket_input = $xoopsGTicket->getTicketHtml(__LINE__, 1800, 'protector_admin');

    if ($prefix['name'] == XOOPS_DB_PREFIX) {
        $del_button   = '';
        $style_append = 'background-color:#FFFFFF';
    } else {
        $del_button   = "<input type='submit' name='delete' value='delete' onclick='return confirm(\"" . _AM_CONFIRM_DELETE . "\")' />";
        $style_append = '';
    }

    echo "
    <tr>
        <td class='odd' style='$style_append;'>$prefix4disp</td>
        <td class='odd' style='text-align:right;$style_append;'>$table_count</td>
        <td class='odd' style='text-align:right;$style_append;'>{$prefix['updated']}</td>
        <td class='odd' style='text-align:center;$style_append;' nowrap='nowrap'>
            <form action='?page=prefix_manager' method='POST' style='margin:0;'>
                $ticket_input
                <input type='hidden' name='old_prefix' value='$prefix4disp' />
                <input type='text' name='new_prefix' size='8' maxlength='16' />
                <input type='submit' name='copy' value='copy' />
            </form>
        </td>
        <td class='odd' style='text-align:center;$style_append;'>
            <form action='?page=prefix_manager' method='POST' style='margin:0;'>
                $ticket_input
                <input type='hidden' name='prefix' value='$prefix4disp' />
                $del_button
                <input type='submit' name='backup' value='backup' onclick='this.form.target=\"_blank\"' />
            </form>
        </td>
    </tr>\n";
}

echo '
</table>
<p>' . sprintf(_AM_TXT_HOWTOCHANGEDB, XOOPS_VAR_PATH, XOOPS_DB_PREFIX) . '</p>

';

// Display Log if exists
if (!empty($_SESSION['protector_logger'])) {
    echo $_SESSION['protector_logger'];
    $_SESSION['protector_logger'] = '';
    unset($_SESSION['protector_logger']);
}

xoops_cp_footer();
