<?php
/**
 * Calendar for ExpressionEngine
 *
 * @package       Solspace:Calendar
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2010-2020, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/calendar/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\Calendar\Repositories;

use Solspace\Addons\Calendar\Model\Preference;

class PreferenceRepository extends AbstractRepository
{
    /**
     * @return PreferenceRepository
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    /**
     * @return Preference
     */
    public function getOrCreate()
    {
        $siteId = ee()->config->item('site_id');

        $preference = ee('Model')
            ->get('calendar:Preference')
            ->filter('site_id', $siteId)
            ->first();

        if (!$preference instanceof Preference) {
            $preference = Preference::create($siteId);
            $preference->save();
        }

        return $preference;
    }

    /**
     * Gets a list of all member group ID's for a preference
     *
     * @param Preference $preference
     *
     * @return array
     */
    public function getMemberGroupIds(Preference $preference)
    {
        /** @var array $memberGroupIds */
        $memberGroupIds = ee()->db
            ->select('group_id')
            ->from('calendar_calendar_member_groups')
            ->where(array('id' => -($preference->id)))
            ->get()
            ->result_array();

        $result = array_column($memberGroupIds, 'group_id');

        return $result;
    }
}
