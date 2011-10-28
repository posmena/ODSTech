<?php

class core_default
{
	private $isHome = true;

	public function direct($db, $qs) {
		if (true === isset($qs['action'])) {
			if (method_exists($this, $qs['loc'] . '_' . $qs['action'])) {
				$this->assignments['action'] = $qs['loc'] . '_' . $qs['action'];
				call_user_func(array(get_class($this), $qs['loc'] . '_' . $qs['action']));
				return true;
			}
		}
		return false;
	}
	
	public function isHome() {
		return $this->isHome;
	}
}