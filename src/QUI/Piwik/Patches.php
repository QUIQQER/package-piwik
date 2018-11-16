<?php

namespace QUI\Piwik;

use QUI\Exception;
use QUI\Projects\Project;
use QUI\System\Log;
use QUI\Translator;

/**
 * Class Patches
 *
 * @package QUI\Piwik
 *
 * @author PCSG (Jan Wennrich)
 */
class Patches
{
    const SITE_IDS_TO_LOCALE_VARIABLES = 'movedSiteIdsToLocaleVariables';

    /**
     * Moves the previously used settings values to locale variables.
     * We need to do this in order to use the InputMultiLang control.
     */
    public static function moveSiteIdsToLocaleVariables()
    {
        try {
            $Package = \QUI::getPackage('quiqqer/piwik');
            $Config  = $Package->getConfig();

            $isPatchExecutedAlready = $Config->get('patches', self::SITE_IDS_TO_LOCALE_VARIABLES) == 1;

            if (!$isPatchExecutedAlready) {
                $projectList = \QUI\Projects\Manager::getProjects(true);
                foreach ($projectList as $Project) {
                    /** @var Project $Project */
                    $langdataJSON = $Project->getConfig('piwik.settings.langdata');

                    if (empty($langdataJSON)) {
                        continue;
                    }

                    $languageData = json_decode($langdataJSON, true);

                    if (is_null($languageData) || empty($languageData)) {
                        continue;
                    }

                    $localeVariableData     = [
                        'package'  => 'project/' . $Project->getName(),
                        'datatype' => 'php,js',
                        'html'     => 0
                    ];
                    $wasLocaleVariableFound = false;

                    foreach ($languageData as $key => $data) {
                        if (isset($data['id']) && !empty($data['id'])) {
                            $localeVariableData[$key] = $data['id'];
                            $wasLocaleVariableFound   = true;
                        }
                    }

                    if ($wasLocaleVariableFound) {
                        $group = 'project/' . $Project->getName();

                        Translator::addUserVar(
                            $group,
                            Piwik::LOCALE_KEY_SITE_IDS,
                            $localeVariableData
                        );
                        Translator::publish($group);
                    }
                }

                $Config->set('patches', self::SITE_IDS_TO_LOCALE_VARIABLES, 1);
                $Config->save();
            }
        } catch (Exception $Exception) {
            Log::addError($Exception->getMessage(), $Exception->getContext());
        }
    }
}
