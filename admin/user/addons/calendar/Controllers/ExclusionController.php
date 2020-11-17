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

namespace Solspace\Addons\Calendar\Controllers;

use EllisLab\ExpressionEngine\Service\Model\Collection;
use Solspace\Addons\Calendar\Helpers\DateTimeHelper;
use Solspace\Addons\Calendar\Library\Carbon\Carbon as Carbon;
use Solspace\Addons\Calendar\Model\Event;
use Solspace\Addons\Calendar\Model\Exclusion as Exclusion;
use Solspace\Addons\Calendar\Repositories\PreferenceRepository;

/**
 * The exclusion controller handles all the fetching and mingling of
 * ExpressionEngine data with our model objects
 */
class ExclusionController
{
    /**
     * Returns an exclusion
     *
     * @param  mixed $id An array of ids or an id
     *
     * @return mixed An Exclusion or a collection of exclusions
     */
    public function get($id)
    {
        if (is_array($id)) {
            return Exclusion::whereIn('id', $id)->get();
        } else {
            return Exclusion::find($id);
        }
    }

    /**
     * Saves an exclusion
     *
     * @param array $formData The submitted form data
     * @param Event $event
     *
     * @return Exclusion[]
     */
    public function save($formData, Event $event)
    {
        if (!isset($formData['exclude']) || empty($formData['exclude'])) {
            $this->deleteByEvents(array($event->id));

            return array();
        }

        $preference = PreferenceRepository::getInstance()->getOrCreate();

        $dateList = $formData['exclude'];

        array_walk(
            $dateList,
            function (&$value) use ($preference) {
                if (preg_match('/\d{4}-\d{2}-\d{2}/', $value)) {
                    $value = new Carbon($value, DateTimeHelper::TIMEZONE_UTC);
                } else {
                    $value = Carbon::createFromFormat($preference->date_format, $value, DateTimeHelper::TIMEZONE_UTC);
                }

                $value = $value->toDateString();
            }
        );

        $existingDates      = array();
        $existingExclusions = $this->getExclusionsForEvent($event);
        foreach ($existingExclusions as $exclusion) {
            $date = new Carbon($exclusion->date, DateTimeHelper::TIMEZONE_UTC);
            $date = $date->toDateString();
            if (!in_array($date, $dateList)) {
                $exclusion->delete();
            } else {
                $existingDates[] = $exclusion->date;
            }
        }

        $datesToInsert = array_diff($dateList, $existingDates);


        $exclusionModelList = array();
        foreach ($datesToInsert as $date) {
            /** @var Exclusion $exclusionModel */
            $exclusionModel              = ee('Model')->make('calendar:Exclusion');
            $exclusionModel->date        = $date;
            $exclusionModel->event_id    = $event->id;
            $exclusionModel->save();

            $exclusionModelList[] = $exclusionModel;
        }

        return $exclusionModelList;
    }

    /**
     * Creates models of all exclusions based on form data
     *
     * @param array $formData
     *
     * @return Collection|Exclusion[] $exclusionModelList
     */
    public function getExclusionsFromFormData(array $formData)
    {
        $dateList = isset($formData['exclude']) ? $formData['exclude'] : array();

        $exclusionModelList = array();
        foreach ($dateList as $date) {
            /** @var Exclusion $exclusionModel */
            $exclusionModel              = ee('Model')->make('calendar:Exclusion');
            $exclusionModel->date        = $date;
            $exclusionModel->event_id    = null;

            $exclusionModelList[] = $exclusionModel;
        }

        return $exclusionModelList;
    }

    /**
     * Deletes all exclusions for a given event
     *
     * @param array $eventIds
     *
     * @return int
     */
    public function deleteByEvents(array $eventIds)
    {
        if (empty($eventIds)) {
            return;
        }

        ee()->db
            ->from('calendar_exclusions')
            ->where_in('event_id', $eventIds)
            ->delete();

        return ee()->db->affected_rows();
    }

    /**
     * @param Event $event
     *
     * @return Exclusion[]
     */
    private function getExclusionsForEvent(Event $event)
    {
        return ee('Model')
            ->get('calendar:Exclusion')
            ->filter('event_id', $event->id)
            ->all();
    }
}
