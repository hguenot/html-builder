<?php

namespace TS\Text\HtmlBuilder;


use TS\Data\Tree\Interfaces\Lookup;
use TS\Data\Tree\Interfaces\Node as NodeInterface;
use TS\Data\Tree\ProtectedAccess\ChildrenTrait;
use TS\Data\Tree\ProtectedAccess\LookupTrait;

/** @noinspection PhpSuperClassIncompatibleWithInterfaceInspection */

/**
 *
 * @author Timo Stamm <ts@timostamm.de>
 * @license AGPLv3.0 https://www.gnu.org/licenses/agpl-3.0.txt
 */
abstract class Node implements NodeInterface, Lookup {
	
	use ChildrenTrait;
	use LookupTrait;

	protected function childNodesToString()
	{
		$html = '';
		foreach ($this->getChildren() as $child) {
			/** @var Node $child */
			$html .= $child->toString();
		}
		return $html;
	}

	abstract function toString();

	public function __toString()
	{
		return $this->toString();
	}

	protected function removeAllChildren() {
		while ($this->getChildren()) {
			$this->removeChildAt(0);
		}
	}

}

