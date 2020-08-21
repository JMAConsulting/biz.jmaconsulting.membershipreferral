<?php

require_once 'membershipreferral.civix.php';
use CRM_Membershipreferral_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function membershipreferral_civicrm_config(&$config) {
  _membershipreferral_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function membershipreferral_civicrm_xmlMenu(&$files) {
  _membershipreferral_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function membershipreferral_civicrm_install() {
  _membershipreferral_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function membershipreferral_civicrm_postInstall() {
  _membershipreferral_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function membershipreferral_civicrm_uninstall() {
  _membershipreferral_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function membershipreferral_civicrm_enable() {
  _membershipreferral_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function membershipreferral_civicrm_disable() {
  _membershipreferral_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function membershipreferral_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _membershipreferral_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function membershipreferral_civicrm_managed(&$entities) {
  _membershipreferral_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function membershipreferral_civicrm_caseTypes(&$caseTypes) {
  _membershipreferral_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function membershipreferral_civicrm_angularModules(&$angularModules) {
  _membershipreferral_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function membershipreferral_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _membershipreferral_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_entityTypes
 */
function membershipreferral_civicrm_entityTypes(&$entityTypes) {
  _membershipreferral_civix_civicrm_entityTypes($entityTypes);
}

function membershiprelation_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  if ($objectName == "Membership" && in_array($objectRef->membership_type_id, [1, 2])) {
    $referralCode = civicrm_api3('Membership', 'getvalue', ['id' => $objectId, 'return' => 'custom_' . _getReferralCode()]);
    if (!$referralCode) {
      //TODO
    }
  }
}

function _getReferralCode() {
  $return = civicrm_api3('custom_field', 'getvalue', [
    'name' => 'referral_code',
    'return' => 'id',
  ]);
}

function membershipreferral_civicrm_pre1($op, $objectName, $objectId, &$params) {
   if ($op == "edit" && $objectName == "Membership") {
     if (!empty($params['membership_type_id'])) {
       if (in_array($params['membership_type_id'], [1,2])) {
         $membershipObj = new CRM_Member_DAO_Membership();
         $membershipObj->id = $objectId;
         $membershipObj->find();
         $oldType = $contactID = $source = NULL;
         while ($membershipObj->fetch()) {
           $oldType = $membershipObj->membership_type_id;
           $contactID = $membershipObj->contact_id;
           $source = $membershipObj->source;
         }
         if (!empty($oldType) && !in_array($oldType, [1,2])) {
           $params['contact_id'] = $contactID;
           $params['source'] = $source;
           unset($params['id']);
         }
       }
       else {
         $membershipObj = new CRM_Member_DAO_Membership();
         $membershipObj->id = $objectId;
         $membershipObj->find();
         $oldType = $contactID = $source = NULL;
         while ($membershipObj->fetch()) {
           $oldType = $membershipObj->membership_type_id;
           $contactID = $membershipObj->contact_id;
           $source = $membershipObj->source;
         }
         if (!empty($oldType) && in_array($oldType, [1,2])) {
           unset($params['id']);
           $params['contact_id'] = $contactID;
           $params['source'] = $source;
         }
       }
     }
   }
 }

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function membershipreferral_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function membershipreferral_civicrm_navigationMenu(&$menu) {
  _membershipreferral_civix_insert_navigation_menu($menu, 'Mailings', array(
    'label' => E::ts('New subliminal message'),
    'name' => 'mailing_subliminal_message',
    'url' => 'civicrm/mailing/subliminal',
    'permission' => 'access CiviMail',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _membershipreferral_civix_navigationMenu($menu);
} // */
