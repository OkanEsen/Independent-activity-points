<?php
// wcf imports
require_once(WBB_DIR.'lib/data/thread/ThreadEditor.class.php');
require_once(WBB_DIR.'lib/data/post/PostEditor.class.php');
require_once(WBB_DIR.'lib/data/board/BoardEditor.class.php');
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

/**
 * Increment activity point if count user post option ist deactivated
 * 
 * @author		Okan Esen
 * @copyright	2011 Okan Esen
 * @package		com.okanesen.wbb.independentActivitypoint
 * @license		Creative Commons <by-nd> <http://creativecommons.org/licenses/by-nd/3.0/deed.de>
 */
class PostAddFormActivityListener implements EventListener {
	public $threadID = 0;
	public $postID = 0;
	public $thread;
	
	/**
	 * @see	EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		// ignore guests
		if (WCF::getUser()->userID == 0) {
			return;
		}
		
		if (isset($_REQUEST['threadID'])) $this->threadID = intval($_REQUEST['threadID']);
		if (isset($_REQUEST['postID'])) $this->postID = intval($_REQUEST['postID']);
		
		// get thread
		$this->thread = new ThreadEditor($this->threadID, null, $this->postID);
		$this->threadID = $this->thread->threadID;
		
		// get board
		$this->board = new BoardEditor($this->thread->boardID);
		
		// update activity point
		if (WCF::getUser()->userID && !$this->board->countUserPosts) {
			if (ACTIVITY_POINTS_PER_POST) {
				require_once(WCF_DIR.'lib/data/user/rank/UserRank.class.php');
				UserRank::updateActivityPoints(ACTIVITY_POINTS_PER_POST);
			}
		}
	}
}
?>