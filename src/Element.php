<?php

namespace TS\Text\HtmlBuilder;

use TS\Data\Tree\ProtectedAccess\AttributesTrait;

/**
 * @author  Timo Stamm <ts@timostamm.de>
 * @license AGPLv3.0 https://www.gnu.org/licenses/agpl-3.0.txt
 */
class Element extends Node {

	use AttributesTrait;

	private $tagName = '';

	/**
	 * Element constructor.
	 *
	 * @param string     $tagName    HTML Tag
	 * @param array|null $attributes Initial tag attributes
	 */
	public function __construct(string $tagName, ?array $attributes = null) {
		$this->tagName = $tagName;
		if (is_array($attributes)) {
			foreach ($attributes as $name => $value) {
				$this->setAttribute($name, $value);
			}
		}
	}

	// since we do not parse html, it does not make much sense to expose a query api
	/*
	public function children() {
		$this->children(function(Node $node){
			return $node instanceof Element;
		});
	}
	
	public function find(callable $where) {
		if ( $where($this) ) {
			yield $this;
		}
		$eles = $this->descendants(function(Node $node){
			return $node instanceof Element && $where($node);
		});
		foreach ($eles as $el) {
			yield $el;
		}
	}
	
	public function closest(callable $where) {
		if ( $where($this) ) {
			return $this;
		}
		return $this->ancestor(function(Node $node){
			return $node instanceof Element && $where($node);
		});
	}
	*/

	/**
	 * @param string $name Attribute name to remove
	 *
	 * @return $this
	 */
	public function removeAttr(string $name): self {
		$this->removeAttribute($name);

		return $this;
	}

	/**
	 * Depending on the number of argument when calling this method. It should :
	 * - set an attribute value (called with 2 arguments) and returns current element for fluent call
	 * - get the current value of an attribute (called with 1 argument)
	 * - get an array of all attributes and their value (called with no argument)
	 *
	 * @param string $name
	 * @param null   $value
	 *
	 * @return $this|mixed|mixed[]|null Current object if method was called with 2 args, attribute value or null if only argname is used
	 */
	public function attr(?string $name = null, $value = null) {
		if (1 == func_num_args()) {
			if (false === $this->hasAttribute($name)) {
				return null;
			}

			return $this->getAttribute($name, true);
		} else if (2 == func_num_args()) {
			$this->setAttribute($name, $value);

			return $this;
		} else if (0 === func_num_args()) {
			return $this->getAttributes();
		} else {
			throw new \InvalidArgumentException();
		}
	}

	/**
	 * Append an element to the current (current element becomes parent of `$tagNameOrNode`)
	 *
	 * @param Node|string $tagNameOrNode HTML tag name or node to append.
	 * @param array|null  $attrs         Attributes (must be null if Node is used as 1st argument)
	 *
	 * @return $this
	 */
	public function append($tagNameOrNode, array $attrs = null): self {
		if ($tagNameOrNode instanceof Node) {
			$this->addChild($tagNameOrNode);
			if (is_null($attrs) == false) {
				throw new \InvalidArgumentException();
			}
		} else {
			$el = Html::element($tagNameOrNode, $attrs);
			$this->addChild($el);
		}

		return $this;
	}

	/**
	 * Appends current element to specified target (current element becomes child of `$target`)
	 *
	 * @param Element $target
	 *
	 * @return $this
	 */
	public function appendTo(Element $target): self {
		$target->append($this);

		return $this;
	}

	/**
	 * Prepend an element to the current (current element becomes parent of `$tagNameOrNode` and `$tagNameOrNode` becomes the first child)
	 *
	 * @param Node|string $tagNameOrNode HTML tag name or node to prepend.
	 * @param array|null  $attrs         Attributes (must be null if Node is used as 1st argument)
	 *
	 * @return $this
	 */
	public function prepend($tagNameOrNode, array $attrs = null): self {
		if ($tagNameOrNode instanceof Node) {
			$this->insertChildAt(0, $tagNameOrNode);
			if (is_null($attrs) == false) {
				throw new \InvalidArgumentException();
			}
		} else {
			$el = Html::element($tagNameOrNode, $attrs);
			$this->insertChildAt(0, $el);
		}

		return $this;
	}

	/**
	 * Prepends current element to specified target (current element becomes first child of `$target`)
	 *
	 * @param Element $target
	 *
	 * @return Element
	 */
	public function prependTo(Element $target): self {
		$target->prepend($this);

		return $this;
	}

	/**
	 * Replaces HTML or get the current one
	 *
	 * @param string|Node|null $rawHtmlOrNode New HTML content
	 *
	 * @return $this|string
	 */
	public function html($rawHtmlOrNode = null) {
		if (1 === func_num_args()) {
			if (is_string($rawHtmlOrNode)) {
				$this->removeAllChildren();
				$this->addChild(new RawHtml($rawHtmlOrNode));
			} else if ($rawHtmlOrNode instanceof Node) {
				$this->removeAllChildren();
				$this->addChild($rawHtmlOrNode);
			} else {
				throw new \InvalidArgumentException();
			}

			return $this;
		} else if (0 === func_num_args()) {
			$html = '';
			foreach ($this->getChildren() as $node) {
				/** @var Node $node */
				$html .= $node->toString();
			}

			return $html;
		} else {
			throw new \InvalidArgumentException();
		}
	}

	/**
	 * Replaces text content or get the current one
	 *
	 * @param string|Node|null $rawTextOrNode New HTML content
	 *
	 * @return $this|string
	 */
	public function text($rawTextOrNode = null) {
		if (1 === func_num_args()) {
			if (is_string($rawTextOrNode)) {
				$this->removeAllChildren();
				$this->addChild(new Text($rawTextOrNode));
			} else if ($rawTextOrNode instanceof Node) {
				$this->removeAllChildren();
				$this->addChild($rawTextOrNode);
			} else {
				throw new \InvalidArgumentException();
			}

			return $this;
		} else if (0 === func_num_args()) {
			$txt = '';

			if ($this instanceof Text) {
				$txt .= $this->text;
			}
			foreach ($this->descendants() as $node) {
				if ($node instanceof Text) {
					$txt .= $node->text;
				}
			}

			return $txt;
		} else {
			throw new \InvalidArgumentException();
		}
	}

	/**
	 * Add CSS class
	 *
	 * @param string $class CSS class name
	 *
	 * @return $this
	 */
	public function addClass(string $class): self {
		$o = $this->getClasses();
		$o[] = $class;
		$this->setAttribute('class', join(' ', $o));

		return $this;
	}

	/**
	 * Check if css class is applied
	 *
	 * @param string $class CSS class name
	 *
	 * @return bool
	 */
	public function hasClass(string $class): bool {
		return in_array($class, $this->getClasses());
	}

	/**
	 * Removes CSS class
	 *
	 * @param string $class CSS class name
	 *
	 * @return Element
	 */
	public function removeClass(string $class): self {
		$o = $this->getClasses();
		$i = array_search($class, $o);
		if ($i !== false) {
			unset($o[$i]);
		}
		$classes = trim(join(' ', $o));
		if ($classes === '') {
			$this->removeAttribute('class');
		} else {
			$this->setAttribute('class', $classes);
		}

		return $this;
	}

	/**
	 * Toggle class : if `$state` is null, switch class state ; else depending on the `$state` boolean value
	 *
	 * @param string    $class CSS class name
	 * @param bool|null $state if `true` adds the CSS class, if `false` removes it and if null switch the state
	 *
	 * @return $this
	 */
	public function toggleClass(string $class, ?bool $state = null): self {
		$hasClass = $this->hasClass($class);
		if ($state === null) {
			$state = !$hasClass;
		}

		if ($hasClass && !$state) {
			$this->removeClass($class);
		} else if (!$hasClass && $state) {
			$this->addClass($class);
		}

		return $this;
	}

	/**
	 * Returns list of CSS classes
	 *
	 * @return string[] CSS classes
	 */
	private function getClasses() {
		$o = $this->getAttribute('class');
		$o = is_string($o) ? explode(' ', $o) : [];
		$o = array_map(
			function ($i) {
				return trim($i);
			},
			$o
		);

		return $o;
	}

	/**
	 * @return string Tag name
	 */
	public function getTagname() {
		return $this->tagName;
	}

	/**
	 * Change the tag name
	 *
	 * @param $value New HTML tag name
	 *
	 * @return $this
	 */
	public function switchTagname($value) {
		$this->tagName = $value;

		return $this;
	}

	public function toString() {
		if (count($this->getChildren()) === 0 && in_array($this->tagName, Html::$voidTags)) {
			$attrs = $this->attributesToString();

			return sprintf('<%s%s>', $this->tagName, $attrs === '' ? '' : ' ' . $attrs);
		} else {
			$content = $this->childNodesToString();
			$attrs = $this->attributesToString();

			return sprintf('<%s%s>%s</%s>', $this->tagName, $attrs === '' ? '' : ' ' . $attrs, $content, $this->tagName);
		}
	}

	protected function attributesToString() {
		$parts = [];
		foreach ($this->getAttributes() as $key => $value) {
			$k = strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $key));
			if ($value === true || $value === null) {
				$parts[] = $k;
			} else {
				$v = strval($value);
				$v = htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE | ENT_DISALLOWED | ENT_HTML5, 'UTF-8');
				$parts[] = sprintf('%s="%s"', $k, $v);
			}
		}

		return implode(' ', $parts);
	}

	public function __toString() {
		return $this->toString();
	}

}

