<?php

namespace TS\Text\HtmlBuilder;

/**
 * @author  Timo Stamm <ts@timostamm.de>
 * @license AGPLv3.0 https://www.gnu.org/licenses/agpl-3.0.txt
 */
class Html {

	public static $voidTags = [
		'area',
		'base',
		'basefont',
		'bgsound',
		'br',
		'col',
		'command',
		'embed',
		'frame',
		'hr',
		'image',
		'img',
		'input',
		'isindex',
		'keygen',
		'link',
		'menuitem',
		'meta',
		'nextid',
		'param',
		'source',
		'track',
		'wbr'
	];

	/**
	 * Creates a comment node
	 *
	 * @param string $text Comment content
	 *
	 * @return Comment
	 */
	public static function comment(string $text) {
		return new Comment($text);
	}

	/**
	 * Creates a text node
	 *
	 * @param string $text Text node content
	 *
	 * @return Text
	 */
	public static function text(string $text) {
		return new Text($text);
	}

	/**
	 * Creates a new HTML element
	 *
	 * @param string $tagName
	 * @param null|string[] $attrs
	 *
	 * @return Element
	 */
	public static function element(string $tagName, ?array $attrs = null) {
		$attrs = is_null($attrs) ? [] : $attrs;

		$el = new Element($tagName, $attrs);

		return $el;
	}

}


