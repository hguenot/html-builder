<?php

namespace TS\Text\HtmlBuilder;

/**
 * @author  Timo Stamm <ts@timostamm.de>
 * @license AGPLv3.0 https://www.gnu.org/licenses/agpl-3.0.txt
 */
class RawHtml extends Node {

	public $html;

	public function __construct(string $html = '') {
		$this->html = $html;
	}

	public function toString() {
		return $this->html;
	}

}

