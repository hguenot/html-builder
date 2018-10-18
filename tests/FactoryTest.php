<?php

namespace TS\Text\HtmlBuilder;

use PHPUnit\Framework\TestCase;

/**
 *
 * @author  Timo Stamm <ts@timostamm.de>
 * @license AGPLv3.0 https://www.gnu.org/licenses/agpl-3.0.txt
 */
class FactoryTest extends TestCase {

	public function testComment() {
		$n = Html::comment('hallo welt');
		$this->assertInstanceOf(Comment::class, $n);
		$this->assertSame('<!--hallo welt-->', $n->toString());
	}

	public function testComment2() {
		$n = new Comment(Html::element('p')->text('Hello world'));
		$this->assertInstanceOf(Comment::class, $n);
		$this->assertSame('<!--<p>Hello world</p>-->', $n->toString());
	}

	public function testComment3() {
		$n = new Comment(new class {
			public function __toString() {
				return 'Hello World for an anonymous class';
			}
		});
		$this->assertInstanceOf(Comment::class, $n);
		$this->assertSame('<!--Hello World for an anonymous class-->', $n->toString());
	}

	public function testComment4() {
		$n = new Comment(new class {
			public function __toString() {
				return 'Hello World for an anonymous class';
			}
		});
		$this->assertInstanceOf(Comment::class, $n);
		$this->assertSame('<!--Hello World for an anonymous class-->', strval($n));
	}

	public function testText() {
		$n = Html::text('hallo welt');
		$this->assertInstanceOf(Text::class, $n);
		$this->assertSame('hallo welt', $n->toString());
	}

	public function testElement() {
		$n = Html::element('p');
		$this->assertInstanceOf(Element::class, $n);
		$this->assertSame('<p></p>', $n->toString());
	}

	public function testElementWithAttributes() {
		$n = Html::element('a', ['href' => '/foo']);
		$this->assertInstanceOf(Element::class, $n);
		$this->assertSame('<a href="/foo"></a>', $n->toString());
	}

}