<?php
/**
 * Licensed under The GPL-3.0 License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @since	 2.0.0
 * @author	 Christopher Castro <chris@quickapps.es>
 * @link	 http://www.quickappscms.org
 * @license	 http://opensource.org/licenses/gpl-3.0.html GPL-3.0 License
 */
use Cake\Cache\Cache;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\I18n\I18n;
use Cake\Routing\Router;
use Cake\Utility\Folder;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\ORM\TableRegistry;

/**
 * Stores some bootstrap-handy information into a persistent file.
 *
 * Information is stored in `SITE/tmp/snapshot.php` file, it contains
 * useful information such as installed languages, content types slugs, etc.
 *
 * You can read this information using `Configure::read()` as follow:
 *
 *     Configure::read('QuickApps.<option>');
 *
 * Available options are:
 *
 * - `node_types`: List of available content type slugs. e.g. ['article', 'page', ...]
 * - `plugins`: Array of plugin information indexed by Plugin Name
 * - `variables`: A set of useful environment variables
 *   - `url_locale_prefix`: 1 to enable language prefix in URL, 0 otherwise
 *   - `site_theme`: Frontend's theme name
 *   - `admin_theme`: Backend's theme name
 *   - `site_title`: Default title for every rendered page of your site
 *   - `site_description`: Default description for every rendered page of your site
 *   - `default_language`: Default site's language
 *
 * @param array $mergeWith Array to merge with snapshot array
 * @return void
 */
function snapshot($mergeWith = []) {
	$snapshot = [
		'node_types' => [],
		'plugins' => [],
		'variables' => [
			'url_locale_prefix' => 0,
			'site_theme' => null,
			'admin_theme' => null,
			'site_title' => null,
			'site_description' => null,
			'default_language' => 'en-us'
		],
		'languages' => [
			'en-us' => [
				'status' => 1,
				'name' => 'English',
				'native' => 'English',
				'direction' => 'ltr',
			]
		]
	];

	$Plugins = TableRegistry::get('Plugins')
		->find()
		->select(['Plugins.name', 'Plugins.status'])
		->all();
	$NodeTypes = TableRegistry::get('NodeTypes')
		->find()
		->select(['NodeTypes.slug'])
		->all();
	$Variables = TableRegistry::get('Variables')
		->find()
		->where([
			'Variables.name IN' => [
				'url_locale_prefix',
				'site_theme',
				'admin_theme',
				'site_title',
				'site_description',
				'default_language',
			]
		])
		->all();

	foreach ($NodeTypes as $nodeType) {
		$snapshot['node_types'][] = $nodeType->slug;
	}

	foreach ($Variables as $variable) {
		$snapshot['variables'][$variable->name] = $variable->value;
	}

	foreach ($Plugins as $plugin) {
		if ($plugin->status) {
			$pluginPath = false;

			foreach (App::path('Plugin') as $path) {
				if (is_dir($path . $plugin->name)) {
					$pluginPath = $path . $plugin->name;
					break;
				}
			}

			if (!$pluginPath) {
				continue;
			}

			$eventsPath =  $pluginPath . '/src/Event/';
			$isCore = (strpos(str_replace(['/', DS], '/', $pluginPath), str_replace(['/', DS], '/', APP)) !== false);
			$events = [
				'hooks' => [],
				'hooktags' => [],
				'fields' => [],
			];

			if (is_dir($eventsPath)) {
				$Folder = new Folder($eventsPath);
				foreach ($Folder->read(false, false, true)[1] as $classFile) {
					$className = basename(preg_replace('/\.php$/', '', $classFile));
					if (str_ends_with($className, 'Field')) {
						$events['fields']['Field\\' . $className] = [
							'namespace' => 'Field\\',
							'path' => dirname($classFile),
						];
					} elseif (str_ends_with($className, 'Hook')) {
						$events['hooks']['Hook\\' . $className] = [
							'namespace' => 'Hook\\',
							'path' => dirname($classFile),
						];
					} elseif (str_ends_with($className, 'Hooktag')) {
						$events['hooktags']['Hooktag\\' . $className] = [
							'namespace' => 'Hooktag\\',
							'path' => dirname($classFile),
						];
					}
				}
			}

			$snapshot['plugins'][$plugin->name] = [
				'name' => $plugin->name,
				'isTheme' => str_ends_with($plugin->name, 'Theme'),
				'isCore' => $isCore,
				'hasHelp' => file_exists($pluginPath . '/src/Template/Element/help.ctp'),
				'hasSettings' => file_exists($pluginPath . '/src/Template/Element/settings.ctp'),
				'events' => $events,
				'status' => $plugin->status,
				'path' => str_replace(['/', DS], '/', $pluginPath),
			];
		}
	}

	if (!empty($mergeWith)) {
		$snapshot = Hash::merge($snapshot, $mergeWith);
	}

	Configure::write('QuickApps', $snapshot);
	Configure::dump('snapshot.php', 'QuickApps', ['QuickApps']);
}

/**
 * Replace the first occurrence only.
 *
 * ### Example:
 *
 *     echo str_replace_once('A', 'a', 'AAABBBCCC');
 *     // out: aAABBCCCC
 *
 * @param string $search The value being searched for
 * @param string $replace The replacement value that replaces found search value
 * @param string $subject The string being searched and replaced on
 * @return string A string with the replaced value
 */
function str_replace_once($search, $replace, $subject) {
	if (strpos($subject, $search) !== false) {
		$occurrence = strpos($subject, $search);
		return substr_replace($subject, $replace, strpos($subject, $search), strlen($search));
	}

	return $subject;
}

/**
 * Check if $haystack string starts with $needle string.
 *
 * ### Example:
 *
 *     str_starts_with('lorem ipsum', 'lo'); // true
 *     str_starts_with('lorem ipsum', 'ipsum'); // false
 *
 * @param string $haystack
 * @param string $needle
 * @return boolean
 */
function str_starts_with($haystack, $needle) {
    return
    	$needle === '' ||
    	strpos($haystack, $needle) === 0;
}

/**
 * Check if $haystack string ends with $needle string.
 *
 * ### Example:
 *
 *     str_ends_with('lorem ipsum', 'm'); // true
 *     str_ends_with('dolorem sit amet', 'at'); // false
 *
 * @param string $haystack
 * @param string $needle
 * @return boolean
 */
function str_ends_with($haystack, $needle) {
	return
		$needle === '' ||
		substr($haystack, - strlen($needle)) === $needle;
}
