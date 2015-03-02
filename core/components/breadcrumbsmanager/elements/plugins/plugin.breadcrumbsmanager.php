<?php
/**
 * BreadcrumbsManager
 *
 * Copyright 2014 by Roman Tanaev <tanaevr@gmail.com>
 *
 * @package breadcrumbsmanager
 *
 * @var modX $modx
 * @var int $id
 * @var string $mode
 */

/**
 * @var modx $modx
 */
$path = $modx->getOption('breadcrumbsmanager.core_path',null,$modx->getOption('core_path').'components/breadcrumbsmanager/').'model/breadcrumbsmanager/';

if (!$BreadCrumbsManager = $modx->getService('breadcrumbsmanager', 'BreadCrumbsManager', $modx->getOption('breadcrumbsmanager.core_path', null, $modx->getOption('core_path') . 'components/breadcrumbsmanager/') . 'model/breadcrumbsmanager/', $scriptProperties)) {
	return 'Could not load BreadCrumbsManager class!';
}

$eventName = $modx->event->name;

switch($eventName) {
    case 'OnDocFormSave':

    break;
    case 'OnDocFormPrerender':
        if ($modx->event->name == 'OnDocFormPrerender') {
            $BreadCrumbsManager->get($resource, $mode);
            return;
        }
    break;
}

if (isset($result) && $result === true)
    return;
elseif (isset($result)) {
    $modx->log(modX::LOG_LEVEL_ERROR,'[BreadCrumbsManager] An error occured. Event: '.$eventName.' - Error: '.($result === false) ? 'undefined error' : $result);
    return;
}