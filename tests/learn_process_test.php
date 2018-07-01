<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY, without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Learn processor unit tests.
 *
 * @package    tool_trigger
 * @copyright  Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

/**
 * Learn processor unit tests.
 *
 * @package     tool_trigger
 * @copyright   Matt Porritt <mattp@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class tool_trigger_learn_process_testcase extends advanced_testcase {

    public function setup() {
        $this->resetAfterTest(true);
    }


    /**
     * Test is event ignored.
     * Test event with no associated workflow is ignored.
     */
    public function test_get_learnt_events() {
        global $DB;

        // Add event records to database.
        $learntevent = new \stdClass();
        $learntevent->eventname = '\core\event\user_loggedin';
        $learntevent->component = 'core';
        $learntevent->action = 'loggedin';
        $learntevent->target = 'user';
        $learntevent->objecttable = 'user';
        $learntevent->objectid = 121000;
        $learntevent->crud = 'r';
        $learntevent->edulevel = 0;
        $learntevent->contextid = 1;
        $learntevent->contextlevel = 10;
        $learntevent->contextinstanceid = 0;
        $learntevent->userid = 121000;
        $learntevent->courseid = 0;
        $learntevent->relateduserid = '';
        $learntevent->anonymous = 0;
        $learntevent->other = 'a:1:{s:8:"username";s:9:"username1";}';
        $learntevent->timecreated = 1530406950;
        $learntevent->origin = 'cli';
        $learntevent->ip = '';
        $learntevent->realuserid ='';

        $learntevent2 = $learntevent;
        $learntevent2->eventname = '\core\event\user_loggedout';
        $learntevent2->action = 'loggedout';

        $DB->insert_records('tool_trigger_learn_events', array($learntevent, $learntevent2));

        $expected = array('\core\event\user_loggedin', '\core\event\user_loggedout');  // Expected result.

        // We're testing a private method, so we need to setup reflector magic.
        $method = new ReflectionMethod('tool_trigger\learn_process', 'get_learnt_events');
        $method->setAccessible(true); // Allow accessing of private method.
        $proxy = $method->invoke(new \tool_trigger\learn_process); // Get result of invoked method.

        $this->assertEquals(sort($expected), sort($proxy));  // Order of returned array is not important, just values.
    }

}