<?php // $Id: getfile.php,v 1.3 2011/03/01 11:14:14 davmon Exp $
 
/**
 * Gets a file from the videos repository
 * 
 * @package      blocks/myvideos
 * @copyright    2010 David Monllao <david.monllao@urv.cat>
 * @license      http://www.gnu.org/licenses/gpl-2.0.txt
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->dirroot.'/lib/filelib.php');


$videoid = optional_param('videoid', 0, PARAM_INT);
$thumb = optional_param('thumb', 0, PARAM_INT);
$cmid = optional_param('cmid', 0, PARAM_INT);

$video = get_record('myvideos_video', 'id', $videoid);

if (!$video || $video->link == 1) {
    die();
}


// Private video 
if ($video->publiclevel == 0 && $video->userid != $USER->id && !has_capability('moodle/site:doanything', get_context_instance(CONTEXT_SYSTEM))) {
    
    // The petition should come from the module
    if (!$cmid) {
        die();
    }
    
    // If it's not the propietary of a private video check the cmid access
    if (!has_capability('mod/myvideos:view', get_context_instance(CONTEXT_MODULE, $cmid))) {
        die();
    }
    
// Moodle video (only accessible to authenticated Moodle users
} else if ($video->publiclevel == 1 && $USER->id == 0 && !has_capability('moodle/site:doanything', get_context_instance(CONTEXT_SYSTEM))) {
    die();
}


if ($thumb) {
    $dir = 'thumbs';
    $resource = str_replace('.flv', '.jpg', $video->video);
} else {
    $dir = 'videos';
    $resource = $video->video;
}

session_write_close();

$filepath = rtrim(get_config('blocks/myvideos', 'moodlepath'), '/').'/'.$video->userid.'/'.$dir.'/'.$resource;
send_file($filepath, $resource);

?>