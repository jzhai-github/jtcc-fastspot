<?php

class StructureIntegration extends BaseIntegration
{
    protected $options = null;

    public function __construct($entry, $objects, $settings)
    {
        parent::__construct($entry, $objects, $settings);

        $this->options = $this->config[__CLASS__];
    }

    public function run()
    {
        foreach ($this->objects as &$object) {
            $structure = array_reverse($this->getStructure($object['entry_id']));
            $object['structure'] = $structure ?: null;
        }

        return $this->objects;
    }

    /**
     * @param $entryId
     * @param array $structure
     * @return array
     */
    protected function getStructure($entryId, $structure = [])
    {
        $result = ee()->db
            ->select('parent_id')
            ->from('structure')
            ->where('entry_id', $entryId)
            ->limit(1)
            ->get();

        if ($result->num_rows() == 1 && $parentId = $result->row('parent_id')) {
            $parentTitle = ee()->db
                ->select('title')
                ->from('channel_titles')
                ->where('entry_id', $parentId)
                ->limit(1)
                ->get()
                ->row('title');

            if (!in_array($parentTitle, $this->options['ignore'])) {
                if (!$this->options['labels']) {
                    $structure[] = $parentTitle;
                } elseif ($label = $this->getLabel($parentTitle)) {
                    $structure[$label] = $parentTitle;
                }
            }

            $structure = $this->getStructure($parentId, $structure);
        }

        return $structure;
    }

    /**
     * @param $title
     * @return int|string|null
     */
    protected function getLabel($title)
    {
        $title = strtolower($title);

        foreach ($this->options['labels'] as $label => $values) {
            if (in_array($title, $values)) {
                return $label;
            }
        }

        return null;
    }
}
