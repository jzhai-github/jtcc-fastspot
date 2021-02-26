<?php
/**
* The software is provided "as is", without warranty of any
* kind, express or implied, including but not limited to the
* warranties of merchantability, fitness for a particular
* purpose and noninfringement. in no event shall the authors
* or copyright holders be liable for any claim, damages or
* other liability, whether in an action of contract, tort or
* otherwise, arising from, out of or in connection with the
* software or the use or other dealings in the software.
* -----------------------------------------------------------
* Amici Infotech - Super Restricted Entries
*
* @package      SuperRestrictedEntries
* @author       Mufi
* @copyright    Copyright (c) 2019, Amici Infotech.
* @link         http://expressionengine.amiciinfotech.com/super-restricted-entries
* @filesource   ./system/user/addons/super_restricted_entries/config.php
*/

if ( ! defined('SUPER_RESTRICTED_ENTRIES_VER'))
{
	define('SUPER_RESTRICTED_ENTRIES_NAME', 'Super Restricted Entries');
	define('SUPER_RESTRICTED_ENTRIES_DESC', 'Module to restrict channel entry from members or member groups');
	define('SUPER_RESTRICTED_ENTRIES_VER', '1.0.5');
	define('SUPER_RESTRICTED_ENTRIES_MOD', 'Super_restricted_entries');

	define('SUPER_RESTRICTED_ENTRIES_DOCS_URL', 'https://docs.amiciinfotech.com/expressionengine/super-restricted-entries');
	define('SUPER_RESTRICTED_ENTRIES_AUTHOR', 'Mufi');
	define('SUPER_RESTRICTED_ENTRIES_AUTHOR_URL', 'https://amiciinfotech.com/');
}

$config['super_restricted_entries_name'] = SUPER_RESTRICTED_ENTRIES_NAME;
$config['super_restricted_entries_ver']	 = SUPER_RESTRICTED_ENTRIES_VER;