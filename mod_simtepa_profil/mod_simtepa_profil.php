<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_simtepa_profil
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/helper.php';

$pegawai = ModSimtepaProfilHelper::getPegawai($params);
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx', ''), ENT_COMPAT, 'UTF-8');

require JModuleHelper::getLayoutPath('mod_simtepa_profil', $params->get('layout', 'default'));
