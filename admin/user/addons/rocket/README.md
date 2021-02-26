Rocket automatically works out which channel entries each page uses, and refreshes its cache whenever those entries are updated in the control panel.

## Benefits:

Dramatically improve your TTFB metric.
Reduce server resource load.

## Potential Pitfalls:

If a page has any dynamic content, you will need to load it in via AJAX as the HTML output will load from the cache.
If your site has plugins which work with channel entries, Rocket might need some extra work to be compatible (currently supports: Low Reorder).
If your site is using HTTPAUTH, Rocket will be unable to create cache files

## Notes

Cache is purged whenever the addon settings are saved.
Rocket works with Apache/NGINX. Rocket does not work with the PHP built in server, because 2 request threads are required.

## More

Go even faster, add the following code to the top of index.php for super fast GET requests.
Note - this code ignores the 'Website online?' control panel setting. If a cache exists for a page it will be shown even if the site is 'offline'.

    // START ROCKET
    if (session_start() && !empty($_SESSION['ROCKET_CSRF']) && $_SERVER['REQUEST_METHOD'] != 'POST' && !isset($_GET['rocket_bypass'])) {
        $p = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'rocket_cache';
        if (file_exists($p . DIRECTORY_SEPARATOR . 'enabled')) {
            if (!isset($_COOKIE['loggedin']) || !file_exists($p . DIRECTORY_SEPARATOR . 'bypass')) {
                $f = $p . DIRECTORY_SEPARATOR . base64_encode($_SERVER['REQUEST_URI']) . '.html';
                if (file_exists($f)) {
                    die(str_replace(
                        '{{ROCKET_CSRF}}',
                        '<input type="hidden" name="csrf_token" value="'.$_SESSION['ROCKET_CSRF'].'" />',
                        file_get_contents($f)
                    ));
                }
            }
        }
    }
    // END ROCKET

## Changelog
1.4.1 - 2020-11-07
- Jump menu commands

1.4 - 2020-11-05
- support EE6
- add ability to toggle cache minification

1.3.2 - 2019-08-05
- tidy up the paths table whenever entries are saved
- optimise settings loading
- 'update on save' toggle

1.3.1 - 2019-07-30
- use a URL parameter for cache requests instead of enable/disable
- better regex pattern for CSRF matching
- the hook to update cache on channel entry save was missing
- **the index.php snippet has been updated**

1.3.0 - 2019-07-18
- fix tables not being dropped when uninstalled
- fix wildcard matching
- new mode option to include or exclude items in the list

1.2.1 - 2019-07-17
- Settings screen UX improvements

1.2.0 - 2019-07-11
- support pages with CSRF tokens (eg. forms)
- enable the update button on the add-on list page

1.1.1 - 2019-07-10
- suppress error so that 404 pages don't cause problems

1.1.0 - 2019-07-09
- allow form posts to work
- better way of including the setup file
- thanks to @litzinger for the assistance
- general tidy up
- toggle to bypass the cache if a member is logged in

1.0.0 - 2019-07-08
 - initial release

## Credits

[Icon by Freepik](https://www.freepik.com/) from [Flaticon](https://www.flaticon.com) is licensed by [CC 3.0 BY](http://creativecommons.org/licenses/by/3.0/)
