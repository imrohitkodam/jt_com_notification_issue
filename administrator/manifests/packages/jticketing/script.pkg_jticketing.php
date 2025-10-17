<?php
/**
 * @version    SVN: <svn_id>
 * @package    JTicketing
 * @copyright  Copyright (C) 2005 - 2017. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * Shika is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Application\CMSApplication;

jimport( 'joomla.application.component.helper' );

/**
 * JTicketing Installer
 *
 * @since  1.0.0
 */
class Pkg_jticketingInstallerScript
{
	/** @var array The list of extra modules and plugins to install */
	private $installation_queue = array(
		// @modules => { (folder) => { (module) => { (position), (published) } }* }*
		'modules' => array(
			'admin' => array(),
			'site' => array(
				'mod_jticketing_buy'       => 0,
				'mod_jticketing_event'     => 0,
				'mod_jticketing_menu'      => 0,
				'mod_jticketing_calendar'  => 0
			)
		),

		// @plugins => { (folder) => { (element) => (published) }* }*
		'plugins' => array(
			'actionlog' => array(
				'jticketing' => 1
			),
			'api' => array(
				'jticket' => 1,
				'jlike'   => 1
			),
			'community' => array(
				'addfields' => 0
			),
			'content' => array(
				'jlike_events' => 1,
				'tjintegration' => 0,
				'eventinfo' => 1,
				'schema_events' => 1
			),
			'jevents' => array(
				'addfields' => 0
			),
			'jticketingtax' => array(
				'jticketing_tax_default' => 0
			),
			'payment' => array(
				'2checkout'       => 0,
				'alphauserpoints' => 0,
				'authorizenet'    => 1,
				'bycheck'         => 1,
				'byorder'         => 1,
				'ccavenue'        => 0,
				'jomsocialpoints' => 0,
				'linkpoint'       => 1,
				'paypal'          => 1,
				'paypalpro'       => 0,
				'payu'            => 1,
				'amazon'          => 0,
				'ogone'           => 0,
				'paypal_adaptive_payment' => 0,
				'razorpay' => 0
			),
			'privacy' => array(
				'jticketing' => 1
			),
			'search' => array(
				'jtevents' => 1
			),
			'system' => array(
				'jticketing_j3'        => 1,
				'plug_sys_jticketing'  => 1,
				'tjassetsloader'       => 1,
				'jticketingactivities' => 1,
				'tjupdates'            => 1,
				'tjanalytics'          => 0
			),
			'tjevents' => array(
				'plug_tjevents_adobeconnect' => 1,
				'zoom' => 1,
				'jitsi' => 1,
				'jaas' => 1,
				'googlemeet' => 1,
			),
			'tjlmsdashboard' => array(
				'eventlist' => 1,
			),
			'tjsms' => array(
				'twilio' => 0,
				'mvaayoo' => 0
			),
			'tjreports' => array(
				'attendeereport'      => 1,
				'customerreport'      => 1,
				'eventcategoryreport' => 1,
				'eventreport'         => 1,
				'salesreport'         => 1
			),
			'jticketing' => array(

				'rsform'          => 0,
				'tjlesson'        => 0,
				'esgroup'         => 0,
				'jsgroup'         => 0,
				'joomlausergroup' => 0,
				'jteventreminder' => 0
			),
			'tjurlshortner' => array(
				'bitly'   => 0
			),
			'user' => array(
				'tjnotificationsmobilenumber' => 0
			),
			'finder' => array(
				'jticketingevents' => 0
			),
		),

		'applications' => array(
			'easysocial' => array(
				'jticketMyEvents'       => 0,
				'jticket_boughttickets' => 0,
				'jticketingtickettypes' => 1
			)
		),

		'libraries' => array(
			'activity'   => 1,
			'techjoomla' => 1,
			'lib_tjbarcode' => 1
		),

		'dynamics' => array(
			'jticketing' => 1
		)
	);

	/** @var array Obsolete files and folders to remove*/
	private $removeFilesAndFolders = array(
		'files' => array(
			'administrator/components/com_jticketing/views/orders/tmpl/all.php',
			'administrator/components/com_jticketing/views/orders/tmpl/my.php',
			'administrator/components/com_jticketing/views/orders/tmpl/my.xml',
			'administrator/components/com_jticketing/views/events/tmpl/all_list.php',
			'administrator/components/com_jticketing/views/events/tmpl/create.php',
			'administrator/components/com_jticketing/views/events/tmpl/single.php',
			'plugins/payment/authorizenet/authorizenet/com_jticketing_authorizenet.log',
			'plugins/payment/2checkout/2checkout/com_jticketing_2checkout.log',
			'plugins/payment/alphauserpoints/alphauserpoints/com_jticketing_alphauserpoints.log',
			'plugins/payment/ccavenue/ccavenue/com_jticketing_ccavenue.log',
			'plugins/payment/ogone/ogone/com_jticketing_ogone.log',
			'plugins/payment/paypal/paypal/com_jticketing_paypal.log',
			'plugins/payment/amazon/amazon/com_jticketing_amazon.log',
			'plugins/payment/paypalpro/paypalpro/com_jticketing_paypalpro.log',
			'plugins/payment/payu/payu/com_jticketing_payu.log',
			'plugins/payment/transfirst/transfirst/com_jticketing_transfirst.log',
			'components/com_jticketing/views/event/tmpl/default_location.php',
			'components/com_jticketing/views/events/tmpl/eventpin.php',
			'components/com_jticketing/views/events/tmpl/filters.php',
			'components/com_jticketing/views/eventform/tmpl/default_attendeefields.php',
			'components/com_jticketing/views/eventform/tmpl/default_tickettypes.php',
			'components/com_jticketing/assets/js/fblike.js',
			'components/com_jticketing/assets/js/masonry.pkgd.min.js',
			'plugins/payment/razorpay/razorpay/com_jticketing_razorpay.log',
		),
		'folders' => array(
			'components/com_jticketing/bootstrap',
			'components/com_jticketing/views/createevent',
			'components/com_jticketing/views/checkout',
			'components/com_jticketing/views/eventl',
			'components/com_jticketing/views/buy',
			'components/com_jticketing/views_bs2',
			'components/com_jticketing/models/createevent',
			'components/com_jticketing/models/eventl',
			'components/com_jticketing/helpers/dompdf/lib/ttf2ufm',
			'administrator/components/com_jticketing/views/dashboard',
			'administrator/components/com_jticketing/views/settings',
			'administrator/components/com_jticketing/models/settings',
			'administrator/components/com_jticketing/controllers/settings',
		)
	);

	public function preflight ($type, $parent) {

	}

	public function update($parent)
	{
		return true;
	}

	public function install($parent)
	{
		return true;
	}

	public function uninstall($parent)
	{
		?>

		<?php $rows = 1;?>

		<table class="table-condensed table">
			<thead>
				<tr>
					<th class="title" colspan="2">Extension</th>
					<th width="30%">Status</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="3"></td>
				</tr>
			</tfoot>
			<tbody>
				<tr class="row0">
					<td class="key" colspan="2"><h4>TjFields component</h4></td>
					<td><strong style="color: red">Uninstalled</strong></td>
				</tr>
			</table>
		<?php
		return true;
	}

	private function _installStraper($parent)
	{
		$src = $parent->getParent()->getPath('source');

		// Install the FOF framework
		$source = $src.'/tj_strapper';
		$target = JPATH_ROOT.'/media/techjoomla_strapper';

		$haveToInstallStraper = false;
		if(!file_exists($target)) {
			$haveToInstallStraper = true;
		} else {
			$straperVersion = array();
			if(File::exists($target.'/version.txt')) {
				$rawData = file_get_contents($target.'/version.txt');
				$info = explode("\n", $rawData);
				$straperVersion['installed'] = array(
					'version'	=> trim($info[0]),
					'date'		=> new Date(trim($info[1]))
				);
			} else {
				$straperVersion['installed'] = array(
					'version'	=> '0.0',
					'date'		=> new Date('2011-01-01')
				);
			}
			$rawData = file_get_contents($source.'/version.txt');
			$info = explode("\n", $rawData);
			$straperVersion['package'] = array(
				'version'	=> trim($info[0]),
				'date'		=> new Date(trim($info[1]))
			);

			$haveToInstallStraper = $straperVersion['package']['date']->toUNIX() > $straperVersion['installed']['date']->toUNIX();
		}

		$installedStraper = false;
		if($haveToInstallStraper) {
			$versionSource = 'package';
			$installer = new Installer;
			$installedStraper = $installer->install($source);
		} else {
			$versionSource = 'installed';
		}

		if(!isset($straperVersion)) {
			$straperVersion = array();
			if(File::exists($target.'/version.txt')) {
				$rawData = file_get_contents($target.'/version.txt');
				$info = explode("\n", $rawData);
				$straperVersion['installed'] = array(
					'version'	=> trim($info[0]),
					'date'		=> new Date(trim($info[1]))
				);
			} else {
				$straperVersion['installed'] = array(
					'version'	=> '0.0',
					'date'		=> new Date('2011-01-01')
				);
			}
			$rawData = file_get_contents($source.'/version.txt');
			$info = explode("\n", $rawData);
			$straperVersion['package'] = array(
				'version'	=> trim($info[0]),
				'date'		=> new Date(trim($info[1]))
			);
			$versionSource = 'installed';
		}

		if(!($straperVersion[$versionSource]['date'] instanceof Date)) {
			$straperVersion[$versionSource]['date'] = new Date();
		}

		return array(
			'required'	=> $haveToInstallStraper,
			'installed'	=> $installedStraper,
			'version'	=> $straperVersion[$versionSource]['version'],
			'date'		=> $straperVersion[$versionSource]['date']->format('Y-m-d'),
		);
	}

	/**
	 * Installs subextensions (modules, plugins) bundled with the main extension
	 *
	 * @param JInstaller $parent
	 * @return JObject The subextension installation status
	 */
	private function _installSubextensions($parent)
	{
		$src = $parent->getParent()->getPath('source');

		$db = Factory::getDbo();

		$status = new CMSObject();
		$status->modules = array();
		$status->plugins = array();

		// Modules installation
		if(count($this->installation_queue['modules'])) {
			foreach($this->installation_queue['modules'] as $folder => $modules) {
				if(count($modules)) foreach($modules as $module => $modulePreferences) {
					// Install the module
					if(empty($folder)) $folder = 'site';
					$path = "$src/modules/$folder/$module";
					if(!is_dir($path)) {
						$path = "$src/modules/$folder/mod_$module";
					}
					if(!is_dir($path)) {
						$path = "$src/modules/$module";
					}
					if(!is_dir($path)) {
						$path = "$src/modules/mod_$module";
					}
					if(!is_dir($path)) continue;
					// Was the module already installed?
					$sql = $db->getQuery(true)
						->select('COUNT(*)')
						->from('#__modules')
						->where($db->qn('module').' = '.$db->q('mod_'.$module));
					$db->setQuery($sql);
					$count = $db->loadResult();
					$installer = new Installer;
					$result = $installer->install($path);
					$status->modules[] = array(
						'name'=>'mod_'.$module,
						'client'=>$folder,
						'result'=>$result
					);
					// Modify where it's published and its published state
					if(!$count) {
						// A. Position and state
						list($modulePosition, $modulePublished) = $modulePreferences;
						if($modulePosition == 'cpanel') {
							$modulePosition = 'icon';
						}
						$sql = $db->getQuery(true)
							->update($db->qn('#__modules'))
							->set($db->qn('position').' = '.$db->q($modulePosition))
							->where($db->qn('module').' = '.$db->q('mod_'.$module));
						if($modulePublished) {
							$sql->set($db->qn('published').' = '.$db->q('1'));
						}
						$db->setQuery($sql);
						$db->execute();

						// B. Change the ordering of back-end modules to 1 + max ordering
						if($folder == 'admin') {
							$query = $db->getQuery(true);
							$query->select('MAX('.$db->qn('ordering').')')
								->from($db->qn('#__modules'))
								->where($db->qn('position').'='.$db->q($modulePosition));
							$db->setQuery($query);
							$position = $db->loadResult();
							$position++;

							$query = $db->getQuery(true);
							$query->update($db->qn('#__modules'))
								->set($db->qn('ordering').' = '.$db->q($position))
								->where($db->qn('module').' = '.$db->q('mod_'.$module));
							$db->setQuery($query);
							$db->execute();
						}

						// C. Link to all pages
						$query = $db->getQuery(true);
						$query->select('id')->from($db->qn('#__modules'))
							->where($db->qn('module').' = '.$db->q('mod_'.$module));
						$db->setQuery($query);
						$moduleid = $db->loadResult();

						$query = $db->getQuery(true);
						$query->select('*')->from($db->qn('#__modules_menu'))
							->where($db->qn('moduleid').' = '.$db->q($moduleid));
						$db->setQuery($query);
						$assignments = $db->loadObjectList();
						$isAssigned = !empty($assignments);
						if(!$isAssigned) {
							$o = (object)array(
								'moduleid'	=> $moduleid,
								'menuid'	=> 0
							);
							$db->insertObject('#__modules_menu', $o);
						}
					}
				}
			}
		}

		// Plugins installation
		if(count($this->installation_queue['plugins'])) {
			foreach($this->installation_queue['plugins'] as $folder => $plugins) {
				if(count($plugins)) foreach($plugins as $plugin => $published) {
					$path = "$src/plugins/$folder/$plugin";
					if(!is_dir($path)) {
						$path = "$src/plugins/$folder/plg_$plugin";
					}
					if(!is_dir($path)) {
						$path = "$src/plugins/$plugin";
					}
					if(!is_dir($path)) {
						$path = "$src/plugins/plg_$plugin";
					}
					if(!is_dir($path)) continue;

					// Was the plugin already installed?
					$query = $db->getQuery(true)
						->select('COUNT(*)')
						->from($db->qn('#__extensions'))
						->where($db->qn('element').' = '.$db->q($plugin))
						->where($db->qn('folder').' = '.$db->q($folder));
					$db->setQuery($query);
					$count = $db->loadResult();

					$installer = new Installer;
					$result = $installer->install($path);

					$status->plugins[] = array('name'=>'plg_'.$plugin,'group'=>$folder, 'result'=>$result);

					if($published && !$count) {
						$query = $db->getQuery(true)
							->update($db->qn('#__extensions'))
							->set($db->qn('enabled').' = '.$db->q('1'))
							->where($db->qn('element').' = '.$db->q($plugin))
							->where($db->qn('folder').' = '.$db->q($folder));
						$db->setQuery($query);
						$db->execute();
					}
				}
			}
		}

		if(isset($this->installation_queue['libraries']) and count($this->installation_queue['libraries'])) {
			foreach($this->installation_queue['libraries']  as $folder=>$status1) {

					$path = "$src/libraries/$folder";
					if(file_exists($path))
					{
						$query = $db->getQuery(true)
							->select('COUNT(*)')
							->from($db->qn('#__extensions'))
							->where('( '.($db->qn('name').' = '.$db->q($folder)) .' OR '. ($db->qn('element').' = '.$db->q($folder)) .' )')
							->where($db->qn('folder').' = '.$db->q($folder));
						$db->setQuery($query);
						$count = $db->loadResult();

						$installer = new Installer;
						$result = $installer->install($path);

						$status->libraries[] = array('name'=>$folder,'group'=>$folder, 'result'=>$result,'status'=>$status1);
						//print"<pre>"; print_r($status->plugins); die;

						if($published && !$count) {
							$query = $db->getQuery(true)
								->update($db->qn('#__extensions'))
								->set($db->qn('enabled').' = '.$db->q('1'))
								->where('( '.($db->qn('name').' = '.$db->q($folder)) .' OR '. ($db->qn('element').' = '.$db->q($folder)) .' )')
								->where($db->qn('folder').' = '.$db->q($folder));
							$db->setQuery($query);
							$db->execute();
						}
					}
			}
		}

		//Application Installations
		if (count($this->installation_queue['applications'])) {
			foreach ($this->installation_queue['applications'] as $folder => $applications) {
				if (count($applications)) {
					foreach ($applications as $app => $published) {
						$path = "$src/applications/$folder/$app";
						if (!is_dir($path)) {
							$path = "$src/applications/$folder/plg_$app";
						}
						if (!is_dir($path)) {
							$path = "$src/applications/$app";
						}
						if (!is_dir($path)) {
							$path = "$src/applications/plg_$app";
						}
						if (!is_dir($path)) continue;

						if (file_exists(JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/installer/installer.php'))
						{
							require_once JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/installer/installer.php';

							$installer     = new SocialInstaller;
							// The $path here refers to your application path
							$installer->load($path);
							$installer->install();
							//$status->app_install[] = array('name'=>'easysocial_camp_plg','group'=>'easysocial_camp_plg', 'result'=>$plg_install,'status'=>'1');
							$status->applications[] = array('name'=>$app,'group'=>$folder, 'result'=>$result,'status'=>$published);
						}
					}
				}
			}
		}

		//Dynamics Installations
		if (count($this->installation_queue['dynamics'])) {
			foreach ($this->installation_queue['dynamics'] as $folder => $dynamics) {
				if ($dynamics) {
					$path = "$src/dynamics/$folder";
					// check sorce and destination folder is exist
					if (is_dir($path) && is_dir(JPATH_ADMINISTRATOR . "/components/com_acym/dynamics")) {
						$src = "$src/dynamics/$folder";

						$dst = JPATH_ADMINISTRATOR . "/components/com_acym/dynamics/jticketing";

						// Check folder is exist or not
						if (!is_dir($dst)) {
							mkdir($dst, 0755, true);  // Create the destination folder with necessary permissions
						}

						// delete old files if exist
						$files = array_diff(scandir($dst), array('.', '..'));  // Get all files except . and ..
						foreach ($files as $file) {
							$filePath = $dst . '/' . $file;
							unlink($filePath);  // Delete the file
						}

						// Copy new files
						$files = array_diff(scandir($src), array('.', '..'));  // Get all files except . and ..
						foreach ($files as $file) {
							$srcFilePath = $src . '/' . $file;
							$dstFilePath = $dst . '/' . $file;

							copy($srcFilePath, $dstFilePath);  // Copy the file
						}

						$status->dynamics[] = array('name'=>$folder,'group'=> "Acymailing", 'result'=>$result,'status'=> 1);
					}
				}
			}
		}


		return $status;
	}


	/**
	 * Removes obsolete files and folders
	 *
	 * @param array $removeFilesAndFolders
	 */
	private function _removeObsoleteFilesAndFolders($removeFilesAndFolders)
	{
		// Remove files
		if(!empty($removeFilesAndFolders['files'])) foreach($removeFilesAndFolders['files'] as $file) {
			$f = JPATH_ROOT.'/'.$file;
			if(!File::exists($f)) continue;
			File::delete($f);
		}
		// Remove folders
		if(!empty($removeFilesAndFolders['folders'])) foreach($removeFilesAndFolders['folders'] as $folder) {
			$f = JPATH_ROOT.'/'.$folder;
			if(!file_exists($f)) continue;
				Folder::delete($f);
		}
	}

			/**
	 * Renders the post-installation message
	 */
	private function _renderPostInstallation($straperStatus,$status, $parent, $msgBox=array())
	{
		$document = Factory::getDocument();
		?>
		<?php $rows = 1;?>

		<link rel="stylesheet" type="text/css" href=""/>
		<div class="techjoomla-bootstrap" >
		<div class="alert alert-info">
			<div class="row-fluid">
				<strong>In order to complete the Update please make sure you do ALL the following steps.</strong>
			</div>
			<div class="row-fluid">
				1.Go to the JTicketing Control Panel and look for the <strong>Migrate Data</strong> button in the top left corner. Click that. If all goes well you should get various success messages.
			</div>
			<div class="row-fluid">
				2.If you had setup the Reminder cron replace it with the new Jlike reminder cron.
			</div>
			<div class="row-fluid">
				3. A lot of the HTML in this release has been optimised and rewritten introducing structural changes and new elements on most of the pages. Any overrides you have done should be reviewed and redone for the extension to work correctly and to get full benefit of new features. To see the new UI you need to remove your overrides.
			</div>
		</div>
		<div class="techjoomla-bootstrap" >
		<table class="table-condensed table">
			<thead>
				<tr>
					<th class="title" colspan="2">Extension</th>
					<th width="30%">Status</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="3"></td>
				</tr>
			</tfoot>
			<tbody>
				<tr class="row0">
					<td class="key" colspan="2">
						<strong>TechJoomla Strapper <?php echo $straperStatus['version']?></strong> [<?php echo $straperStatus['date'] ?>]
					</td>
					<td><strong>
						<span style="color: <?php echo $straperStatus['required'] ? ($straperStatus['installed']?'green':'red') : '#660' ?>; font-weight: bold;">
							<?php echo $straperStatus['required'] ? ($straperStatus['installed'] ?'Installed':'Not Installed') : 'Already up-to-date'; ?>
						</span>
					</strong></td>
				</tr>
				<tr class="row0">
					<td class="key" colspan="2"><h4>TjFields component</h4></td>
					<td><strong style="color: green">Installed</strong></td>
				</tr>
				<tr class="row0">
					<td class="key" colspan="2">JTicketing component</td>
					<td><strong style="color: green">Installed</strong></td>
				</tr>
				<tr class="row0">
					<td class="key" colspan="2"><h4>TjActivityStream component</h4></td>
					<td><strong style="color: green">Installed</strong></td>
				</tr>

				<?php if (count($status->modules)) : ?>
				<tr>
					<th>Module</th>
					<th>Client</th>
					<th></th>
				</tr>
				<?php foreach ($status->modules as $module) : ?>
				<tr class="row<?php echo ($rows++ % 2); ?>">
					<td class="key"><?php echo $module['name']; ?></td>
					<td class="key"><?php echo ucfirst($module['client']); ?></td>
					<td><strong style="color: <?php echo ($module['result'])? "green" : "red"?>"><?php echo ($module['result'])?'Installed':'Not installed'; ?></strong></td>
				</tr>
				<?php endforeach;?>
				<?php endif;?>
				<?php if (count($status->plugins)) : ?>
				<tr>
					<th>Plugin</th>
					<th>Group</th>
					<th></th>
				</tr>
				<?php foreach ($status->plugins as $plugin) : ?>
				<tr class="row<?php echo ($rows++ % 2); ?>">
					<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
					<td class="key"><?php echo ucfirst($plugin['group']); ?></td>
					<td><strong style="color: <?php echo ($plugin['result'])? "green" : "red"?>"><?php echo ($plugin['result'])?'Installed':'Not installed'; ?></strong></td>
				</tr>
				<?php endforeach; ?>
				<?php endif; ?>
				<?php if (!empty($status->libraries) and count($status->libraries)) : ?>
				<tr class="row1">
					<th>Library</th>
					<th></th>
					<th></th>
					</tr>
				<?php foreach ($status->libraries as $libraries) : ?>
				<tr class="row2 <?php //echo ($rows++ % 2); ?>">
					<td class="key"><?php echo ucfirst($libraries['name']); ?></td>
					<td class="key"></td>
					<td><strong style="color: <?php echo ($libraries['result'])? "green" : "red"?>"><?php echo ($libraries['result'])?'Installed':'Not installed'; ?></strong>
					<?php
						if(!empty($libraries['result'])) // if installed then only show msg
						{
						echo $mstat=($libraries['status']? "<span class=\"label label-success\">Enabled</span>" : "<span class=\"label label-important\">Disabled</span>");

						}
					?>

					</td>
				</tr>
				<?php endforeach;?>
				<?php endif;?>

				<?php if (!empty($status->applications) and count($status->applications)) :
				 ?>
				<tr class="row1">
					<th>EasySocial App</th>
					<th></th>
					<th></th>
					</tr>
				<?php foreach ($status->applications as $app_install) : ?>
				<tr class="row2 <?php  ?>">
					<td class="key"><?php echo ucfirst($app_install['name']); ?></td>
					<td class="key"></td>
					<td><strong style="color: <?php echo ($app_install['result'])? "green" : "red"?>"><?php echo ($app_install['result'])?'Installed':'Not installed'; ?></strong>
					<?php
						if(!empty($app_install['result'])) // if installed then only show msg
						{
							echo $mstat=($app_install['status']? "<span class=\"label label-success\">Enabled</span>" : "<span class=\"label label-important\">Disabled</span>");

						}
					?>

					</td>
				</tr>
				<?php endforeach;?>
				<?php endif;?>

			</tbody>
		</table>

	</div>
		<?php
	}


	public function _addLayout($parent)
	{

		$src = $parent->getParent()->getPath('source');
		$JTsubformlayouts = $src . "/layouts/JTsubformlayouts";

		if (Folder::exists(JPATH_SITE . '/layouts/JTsubformlayouts/layouts'))
		{
			Folder::delete(JPATH_SITE . '/layouts/JTsubformlayouts/layouts');
		}

		Folder::copy($JTsubformlayouts, JPATH_SITE . '/layouts/JTsubformlayouts/layouts');
	}

	/*
	 * enable the plugins
	 */

	public function postflight($type, $parent)
	{

		// Install subextensions
		$status = $this->_installSubextensions($parent);

		// Remove obsolete files and folders
		$removeFilesAndFolders = $this->removeFilesAndFolders;
		$this->_removeObsoleteFilesAndFolders($removeFilesAndFolders);

		echo '<br/>' . '
		<div align="center"> <div class="alert alert-success" style="background-color:#DFF0D8;border-color:#D6E9C6;color: #468847;padding: 8px;"> <div style="font-weight:bold;"></div> <h4><a href="https://techjoomla.com/table/extension-documentation/documentation-for-jticketing/" target="_blank">'.JText::_('COM_JTICKETING_PRODUCT_DOC').'</a> | <a href="https://techjoomla.com/documentation-for-jticketing/jticketing-faqs.html" target="_blank">'.JText::_('COM_JTICKETING_PRODUCT_FAQ').'</a></h4> </div> </div>';

		// Install Techjoomla Straper
		$straperStatus = $this->_installStraper($parent);

		// Show the post-installation page
		$this->_renderPostInstallation($straperStatus,$status, $parent);

		if($type=='install')
		{
			echo '<p><strong style="color:green">' . Text::_('COM_JTICKETING_' . $type . '_TEXT') . '</p></strong>';
		}
		else
		{
			echo '<p><strong style="color:green">' . Text::_('COM_JTICKETING_' . $type . '_TEXT') . '</p></strong>';
		}
		$this->_addLayout($parent);

		// Install default reports from tjreports extension
		$this->_installDefaultTJReportsPlugin();
	}

	/**
	 * Install default tjReports
	 * and install them with extension
	 *
	 * @return  void
	 */
	public function _installDefaultTJReportsPlugin()
	{
		BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjreports/models');
		$model = BaseDatabaseModel::getInstance('Reports', 'TjreportsModel');
		$installed = 0;

		if ($model)
		{
			$installed = $model->addTjReportsPlugins();
		}

		return $installed;
	}
}
