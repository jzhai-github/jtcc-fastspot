<?php

namespace TNS\MailAfterEdit;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Carbon\Carbon;
use TNS\MailAfterEdit\LogService;
use TNS\MailAfterEdit\SendService;

class EntryService {

	public $logger;
	public $send;
	public $mae_fields;
	public $skip_fields;
	public $channel;

	private $settings;

	public function __construct()
	{
		$this->settings = $this->getSettings();

		$fields = ee('Model')->get('ChannelField')
						->all();
		
		$this->mae_fields = [];
		foreach ($fields as $field) {
			$this->mae_fields[$field->field_id] = [
				'field_name'	=> $field->field_name,
				'field_label'	=> $field->field_label,
				'field_type'	=> $field->field_type,
			];
		}

	}

	public function update($entry, $values, $modified)
	{

		LogService::log('Initializing update...');

		$this->checkChannel((int) $values['channel_id']);

		$settings_skip_fields = isset($this->settings['skip_fields']) ? $this->settings['skip_fields'] : [];
		$channel_skip_fields = isset($this->channel['skip_fields']) ? $this->channel['skip_fields'] : [];

		$this->skip_fields = array_merge(
			$settings_skip_fields,
			$channel_skip_fields
		);

		$outputEmails = array();

		// Get user info
		$username = ee()->session->userdata('username');
		$useremail = ee()->session->userdata('email');
		
		// Check if channel ID is in the config. If not, skip it.
		if(
			$this->checkChannel((int) $values['channel_id'], 'edit')
			&& !is_null(
				$this->getEntryChanges(
					$values,
					$modified
				)
			)
		) {

			LogService::log("TITLE: {$values['title']}, ENTRY ID: {$values['entry_id']}");

			// Create Body
			$bodyText = "<h1>" . $this->settings['message_config']['start'] . "</h1>";
			$bodyText .= "<p>TITLE: " . $values['title'] . "</p>";
			$bodyText .= "<p>ENTRY ID: " . $values['entry_id'] . "</p>";
			$bodyText .= "<p>EDITED ON: " . date(DATE_RFC2822, $values['edit_date']) . "</p>";
			$bodyText .= "<p>EDITED BY: " . $username . " (" . $useremail . ")</p>"; 
			$bodyText .= "<p>LINK: " . $this->settings['message_config']['domain'] . '/admin.php?/cp/publish/edit/entry/' . $values['entry_id'] . "<br /></p>";
			$bodyText .= "<p>CHANGES MADE: </p>" . $this->getEntryChanges($values, $modified) . "<br />";
			$bodyText .= $this->settings['message_config']['end'];

			$outputEmails = $this->getEmails($values['channel_id']);
			
			if($this->channel['type'] == 'email') {
				$bodyText .= "<h3>This email has been sent to only you.</h3>";

			} else if($this->channel['type'] == 'member_group') {
				if( version_compare(APP_VER, '5.9.0', '<') ) {
					$bodyText .= "<h3>This email has been sent to your entire member group.</h3>";
				} else {
					$bodyText .= "<h3>This email has been sent to everyone with the selected roles.</h3>";
				}

			}

			// Email it off
			$subject = 'ENTRY: '. $entry->title .' Has Changed';

			LogService::log("Sending email with subject: {$subject}");

			$from = $this->getFrom($values['channel_id']);

			if(isset($this->channel['author']) && $this->channel['author']) {
				$author = $entry->Author;
				$outputEmails[] = $author->email;
			}

			// Should we force BCC?
			$forceBCC = $this->settings['message_config']['force_bcc'];

			$sendIndividually = $this->settings['message_config']['send_individually'];

			if($sendIndividually) {

				foreach ($outputEmails as $$outputEmail) {

					SendService::send($outputEmail, $bodyText, $subject, $from, $forceBCC);	

				}

			} else {

				SendService::send(implode(',', $outputEmails), $bodyText, $subject, $from, $forceBCC);

			}

		}

	}

	public function insert($entry, $entryData)
	{
		LogService::log('Initializing insert...');

		$outputEmails = array();

		// Get user info
		$username = ee()->session->userdata('username');
		$useremail = ee()->session->userdata('email');

		$this->checkChannel((int) $entryData['channel_id']);

		$this->skip_fields = array_merge(
			$this->settings['skip_fields'],
			(array) $this->channel['skip_fields']
		);

		// Check if channel ID is in the config. If not, skip it.
		if($this->checkChannel((int) $entryData['channel_id'], 'create')) {

			LogService::log("TITLE: {$entryData['title']}, ENTRY ID: {$entryData['entry_id']}");

			// Create Body
			$bodyText = "<h1>" . $this->settings['message_config']['start'] . "</h1>";
			$bodyText .= "<p>TITLE: " . $entryData['title'] . "</p>";
			$bodyText .= "<p>ENTRY ID: " . $entryData['entry_id'] . "</p>";
			$bodyText .= "<p>CREATED ON: " . date(DATE_RFC2822, $entryData['edit_date']) . "</p>";
			$bodyText .= "<p>CREATED BY: " . $username . " (" . $useremail . ")</p>"; 
			$bodyText .= "<p>LINK: " . $this->settings['message_config']['domain'] . '/admin.php?/cp/publish/edit/entry/' . $entryData['entry_id'] . "<br /></p>";
			$bodyText .= "<p>CHANGES MADE: </p>" . $this->getEntryInit($entryData) . "<br />";
			$bodyText .= $this->settings['message_config']['end'];

			$outputEmails = $this->getEmails($entryData['channel_id']);

			if($this->channel['type'] == 'email') {
				$bodyText .= "\n\nThis email has been sent to only you.";
			}

			if($this->channel['type'] == 'member_group') {
				if( version_compare(APP_VER, '5.9.0', '<') ) {
					$bodyText .= "\n\nThis email has been sent to your entire member group.";
				} else {
					$bodyText .= "\n\nThis email has been sent to everyone with the selected roles.";
				}

			}

			if(isset($this->channel['author']) && $this->channel['author']) {
				$author = $entry->Author;
				$outputEmails[] = $author->email;
			}

			// Email it off
			$subject = 'ENTRY: '. $entry->title .' Has Been Created';

			LogService::log("Sending email with subject: {$subject}");

			$from = $this->getFrom($entry->channel_id);

			// Should we force BCC?
			$forceBCC = $this->settings['message_config']['force_bcc'];

			$sendIndividually = $this->settings['message_config']['send_individually'];

			if($sendIndividually) {
				foreach ($outputEmails as $$outputEmail) {
					SendService::send($outputEmail, $bodyText, $subject, $from, $forceBCC);	
				}
			} else {
				SendService::send(implode(',', $outputEmails), $bodyText, $subject, $from, $forceBCC);
			}
		}
	}

	// Private functions
	private function getEmails($channel)
	{
		$outputEmails = [];

		if($this->channel['type'] == 'email') {
			$outputEmails = explode('|', $this->channel['data']);

		} else if($this->channel['type'] == 'member_group') {

			$groups = $this->channel['data'];

			if( version_compare(APP_VER, '5.9.0', '<') ) {
				$query = ee()->db->select('*')
							->from('members m')
							->where_in('m.group_id', $groups)
							->get();
				foreach ($query->result() as $row) {
					$outputEmails[] = $row->email;
				}
			} else {
				$query = ee('Model')->get('Role')
								->with('Members')
								->filter('role_id', $groups)
								->get();
				$members = $query->Members;

				foreach ($members as $member) {
					$outputEmails[] = $member->email;
				}
			}
		}

		return $outputEmails;
	}

	private function getEntryChanges($entry, $modified)
	{
		$entryId = $entry['entry_id'];

		$channelId = $entry['channel_id'];

		// Initalize vars
		$outputData = $inData = $customFields = $fieldName = $entryChannelData = array();

		$inData = $entryChannelData = array();

		$newEntry = ee('Model')
						->get('ChannelEntry')
						->filter('entry_id', $entry['entry_id'])
						->first();

		$oldEntry = ee('Model')
						->get('ChannelEntryVersion')
						->filter('entry_id', $entry['entry_id'])
						->order('version_id', 'desc')
						->offset(1)
						->first();

		if(!empty($oldEntry->version_data)) {
			foreach ($oldEntry->version_data as $key => $value) {
				if(!in_array($key, $this->skip_fields)) $inData[$key] = $value;
			}
		} else {
			foreach ($modified as $modkey => $modvalue) {
				if(!in_array($modkey, $this->skip_fields)) {
					$inData[$modkey] = $modvalue;
				}
			}
			LogService::log('No versioning found');
		}

		foreach ($inData as $startFieldKey => $startFieldValue) {
			$fieldId = (int) str_replace('field_id_', '', $startFieldKey);
			if(!$fieldId) continue;
			$outputDataFieldNameKey = $this->mae_fields[$fieldId]['field_label'];

			// Check for relationships first
			if (strpos($startFieldKey, 'field_id_') !== false && $this->mae_fields[$fieldId]['field_type'] == 'relationship') {

				$query = ee()->db->select('*')
								->from('relationships r')
								->where(
									array(
										'r.parent_id' => $entry['entry_id'],
										'r.grid_field_id' => ''
									)
								)
								->get();

				$oldRelationships = $startFieldValue['data'];

				// If there is nothing in the relationship field, then we'll skip it
				if(!empty($oldRelationships) && !is_null($oldRelationships)) {

					foreach ($oldRelationships as $key => $value) {
						$oldRelationships[$key] = (int) $value;
					}

					$newRelationships = $oldIDs = $newIDs = $oldTitles = $newTitles = [];

					foreach ($query->result() as $row) {
						$newRelationships[] = $row->child_id;
					}

					// If its in old and not new, it's removed. If its in new and not old, it's been added.
					$fullDiff = array_merge(array_diff($oldRelationships, $newRelationships), array_diff($newRelationships, $oldRelationships));

					foreach ($fullDiff as $diff) {
						if(in_array($diff, $oldRelationships)) {
							$oldIDs[] = $diff;
						} else {
							$newIDs[] = $diff;
						}
					}

					// Get relationship titles
					$relationshipTitles = ee('Model')->get('ChannelEntry')->filter('entry_id', 'IN', array_merge($oldIDs, $newIDs))->fields('entry_id', 'title')->all();

					foreach ($relationshipTitles as $r) {
						if(in_array($r->entry_id, $oldIDs)) {
							$oldTitles[] = $r->title;
						} else {
							$newTitles[] = $r->title;
						}
					}

					$outputData[$outputDataFieldNameKey] = array(
						'old' => (!empty($oldTitles) ? 'REMOVED: ' . implode(', ', $oldTitles) . "\n": 'Nothing removed' . "\n"),
						'new' => (!empty($newTitles) ? 'ADDED: ' . implode(', ', $newTitles) . "\n" : 'Nothing added' . "\n")
					);

				} else {


					$newRelationships = $oldIDs = $newIDs = $oldTitles = $newTitles = [];

					foreach ($query->result() as $row) {

						$newRelationships[] = $row->child_id;

					}

					$relationshipTitles = ee('Model')->get('ChannelEntry')->filter('entry_id', 'IN', $newRelationships)->fields('entry_id', 'title')->all();

					$outputData[$outputDataFieldNameKey] = array(
						'old' => 'Nothing removed' . "\n",
						'new' => 'ADDED: ' . implode(', ', $relationshipTitles) . "\n"
					);

				}

			} else {

				if(isset($entry[$startFieldKey])) {

					// There are special cases we need to change
					switch ($startFieldKey) {

						case 'edit_date':
							$startFieldValue = $startFieldValue->format(DATE_RFC2822);
							break;

						case 'sticky':
							$startFieldValue = ($startFieldValue == 'y' ? TRUE : FALSE);
							break;
						
						default:
							// Do nothing.
							break;
					}

					// Now we need to get Grid and Relationship data
					if (strpos($startFieldKey, 'field_id_') !== false) { 

						$fieldId = str_replace('field_id_', '', $startFieldKey);

						$field = $this->mae_fields[$fieldId];

						$fieldName[$startFieldKey] = $field['field_label'];

						if ($field['field_type'] == 'grid') {

							$entryQuery = ee()->db->select('*')
												->from('channel_grid_field_' . $field['field_id'] . ' c')
												->where(
													array(
														'entry_id' => $entryId
													)
												)
												->order_by('c.row_id')
												->order_by('c.row_order')
												->get();

							$entryChannelData[$startFieldKey]['rows'] = new stdClass();

							$counter = 1;

							foreach ($entryQuery->result() as $row) {
								unset($row->row_id, $row->entry_id, $row->row_order, $row->fluid_field_data_id);
								$objname = 'new_row_' . $counter;
								$entryChannelData[$startFieldKey]['rows']->$objname = $row;
								$counter++;
							}

						}

						if ($field['field_type'] == 'date' && $startFieldValue != (int) 0) {

							// OLD VALUE
							try {
								$startFieldValue = Carbon::parse($startFieldValue)->format(DATE_RFC2822);
							} catch (\Carbon\Exceptions\InvalidDateException $exp) {
								try {
									$startFieldValue = Carbon::createFromFormat('U', $startFieldValue)->format(DATE_RFC2822);
								} catch (\Carbon\Exceptions\InvalidDateException $exp) {
									$startFieldValue = $startFieldValue;
								}
							} catch (\Exception $exp) {
								try {
									$startFieldValue = Carbon::createFromFormat('U', $startFieldValue)->format(DATE_RFC2822);
								} catch (\Carbon\Exceptions\InvalidDateException $exp) {
									$startFieldValue = $startFieldValue;
								}
							}

							// NEW VALUE
							try {
								$entryChannelData[$startFieldKey] = Carbon::parse($entry[$startFieldKey])->format(DATE_RFC2822);
							} catch (\Carbon\Exceptions\InvalidDateException $exp) {
								try {
									$entryChannelData[$startFieldKey] = Carbon::createFromFormat('U', $entry[$startFieldKey])->format(DATE_RFC2822);
								} catch (\Carbon\Exceptions\InvalidDateException $exp) {
									$entryChannelData[$startFieldKey] = $entry[$startFieldKey];
								}
							} catch (\Exception $exp) {
								try {
									$entryChannelData[$startFieldKey] = Carbon::createFromFormat('U', $entry[$startFieldKey])->format(DATE_RFC2822);
								} catch (\Carbon\Exceptions\InvalidDateException $exp) {
									$entryChannelData[$startFieldKey] = $entry[$startFieldKey];
								}
							}

						}

						// Multiselect
						if($field['field_type'] == 'multi_select' || $field['field_type'] == 'checkboxes') {
							$startFieldValue = implode('|', $startFieldValue);
						}

					}

					if((!(array_key_exists($startFieldKey, $entryChannelData)) && $entry[$startFieldKey] != $startFieldValue) || (array_key_exists($startFieldKey, $entryChannelData) && !(json_encode($startFieldValue) == json_encode($entryChannelData[$startFieldKey])))) {

						$outputData[$outputDataFieldNameKey] = array(
							'old' => $startFieldValue,
							'new' => array_key_exists($startFieldKey, $entryChannelData)
									? $entryChannelData[$startFieldKey]
									: $entry[$startFieldKey]
						);
					}
				}

			}

		}
		
		$outputBody = '';
		if(empty($outputData)) {
			$outputBody .= "No changes were made, but the entry was saved.";
		} else {
			foreach ($outputData as $key => $value) {
				$outputBody .= "<p><strong>{$key}</strong>";
				$outputBody .= "<p><strong>old: </strong> " . ((is_array($value['old']) || is_object($value['old']) ? json_encode($value['old']) : $value['old'])) . "</p>";
				$outputBody .= "<p><strong>new: </strong> " . (is_array($value['new']) || is_object($value['new']) ? json_encode($value['new']) : $value['new']) . "</p>";
			}
		}

		return $outputBody;
	}

	private function getEntryFieldName($entry, $field)
	{
		$fieldName = $field['field_name'];
		$fieldType = $field['field_type'];
		$legacyFieldName = 'field_id_' . ((string) $field['field_id']);

		// Set the field data
		if(isset($entry->{$fieldName})) {
			return $entry->{$fieldName};
		}

		if(isset($entry->{$legacyFieldName})) {
			return $entry->{$legacyFieldName};
		}

		return null;

	}

	private function getEntryInit($entryData)
	{
		$outputData = [];

		foreach ($entryData as $startFieldKey => $startFieldValue) {
			if(strpos($startFieldKey, 'field_id_') === false) continue;

			$fieldId = (int) str_replace('field_id_', '', $startFieldKey);
			$outputDataFieldNameKey = $this->mae_fields[$fieldId]['field_label'];

			// Check for relationships first
			if (strpos($startFieldKey, 'field_id_') !== false && $this->mae_fields[$fieldId]['field_type'] == 'relationship') {

				$query = ee()->db->select('*')
								->from('relationships r')
								->where(
									array(
										'r.parent_id' => $entryData['entry_id'],
										'r.grid_field_id' => ''
									)
								)
								->get();

				$oldRelationships = $startFieldValue['data'];
				
				$newRelationships = $newIDs = $newTitles = [];

				foreach ($query->result() as $row) {
					$newRelationships[] = $row->child_id;
				}

				$relationshipTitles = ee('Model')->get('ChannelEntry')->filter('entry_id', 'IN', $newRelationships)->fields('entry_id', 'title')->all();

				$outputData[$outputDataFieldNameKey] = 'ADDED: ' . implode(', ', $relationshipTitles) . "\n";

			} else {

				$field = $this->mae_fields[$fieldId];

				if ($field->field_type == 'grid') {
					$outputData[$outputDataFieldNameKey] = 'Grid data added';

				// DATE
				} elseif ($field->field_type == 'date' && $startFieldValue != (int) 0) {
					// NEW VALUE
					$outputData[$outputDataFieldNameKey] = Carbon::parse($entry[$startFieldKey])->format(DATE_RFC2822);
				// Multiselect
				} elseif($field->field_type == 'multi_select' || $field->field_type == 'checkboxes') {
					$outputData[$outputDataFieldNameKey] = implode('|', $startFieldValue);
				} else {
					$outputData[$outputDataFieldNameKey] = $startFieldValue;
				}			
			}
		}

		// Parse the text
		$outputText = '';

		foreach ($outputData as $outputDataKey => $outputDataValue) {
			// We'll try and output anything we can here:
			$outputResult = json_encode($outputDataValue);
			$outputText .= "<p><strong>{$outputDataKey}:</strong> {$outputResult}";
		}

		return $outputText;
	}

	// Private functions
	private function getSettings()
	{
		$query = ee()->db
					->select('settings')
					->from('extensions')
					->where(
						[
							'class'	=> 'Mail_after_edit_ext',
							'method'=> 'after_channel_entry_insert'

						]
					)
					->get()
					->result_array();

		$configData = empty($query) ? '' : $query[0];
		$settings = unserialize($configData['settings']);
		return $settings;
	}

	private function getFrom($channel)
	{
		// First, check if the from address is set on the channel
		if(isset($this->channel['from'])) {
			return $this->channel['from'];
		}

		// Default to main from address
		if(isset($this->settings['message_config']['from'])) {
			return $this->settings['message_config']['from'];
		}

		// Well, we need something.
		return 'from@example.com';

	}

	private function checkChannel($channelId, $type = null)
	{
		$result = false;
		foreach ($this->settings['channel_config'] as $ch) {
			if(
				((int) $ch['channel'] == (int) $channelId)
				&& (
					!$type
					|| (
						$type
						&& isset($ch['mail_on'])
						&& in_array($type, $ch['mail_on'])
					)
				)
			) {
				$result = true;
				$this->channel = $ch;
			}
		}
		return $result;
	}

}