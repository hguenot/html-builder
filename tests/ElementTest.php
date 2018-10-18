<?php

namespace TS\Text\HtmlBuilder;

use PHPUnit\Framework\TestCase;

/**
 * @author  Timo Stamm <ts@timostamm.de>
 * @license AGPLv3.0 https://www.gnu.org/licenses/agpl-3.0.txt
 */
class ElementTest extends TestCase {

	public function testText() {
		$el = new Element('p');
		$el->text('abc');

		$this->assertSame(
			'abc',
			$el->text()
		);

		$el->text(new Text('def'));

		$this->assertSame(
			'def',
			$el->text()
		);

		try {
			$el->text(new class(){});
			$this->fail('Must raise an InvalidArgumentException');
		} catch (\InvalidArgumentException $ex) {
		}

	}

	public function testTextReplace() {
		$el = new Element('p');
		$el->text('abc');

		$el->append('br');

		$this->assertSame(
			'abc',
			$el->text()
		);
		$el->text('hallo');

		$this->assertSame(
			"hallo",
			$el->text()
		);

	}

	public function testTextAppend() {
		$el = new Element('p');
		$el->text('abc');

		$el->append('br');

		$el->append('br');

		$el->append(new Text('hallo'));

		$this->assertSame(
			"abchallo",
			$el->text()
		);
	}

	public function testConstructorWithAttributes() {
		$el = new Element('p', ['class' => 'foo']);

		$this->assertSame(
			'<p class="foo"></p>',
			$el->toString()
		);
	}

	public function testPrepend() {
		$el = (new Element('p'))
			->append((new Element('span'))->text('a'))
			->append((new Element('span'))->text('b'))
			->prepend((new Element('span'))->text('c'));

		$this->assertSame('cab', $el->text());

		$el->prepend('span', [
			'class' => 'block'
		]);

		$this->assertSame('<p><span class="block"></span><span>c</span><span>a</span><span>b</span></p>', strval($el));

		$p = new Element('p');
		$el->prependTo($p);

		$this->assertSame('<p><p><span class="block"></span><span>c</span><span>a</span><span>b</span></p></p>', strval($p));

	}

	public function testAppend() {
		$el = (new Element('p'))
			->append((new Element('b'))->text('i am bold'))
			->append((new Element('span'))->text('hello'))
			->append(new Text('world'));

		$this->assertSame(
			'<p><b>i am bold</b><span>hello</span>world</p>',
			$el->toString()
		);
	}

	public function testAppendTo() {
		$ul = (new Element('ul'));

		(new Element('li'))
			->text('a')
			->appendTo($ul);

		(new Element('li'))
			->text('b')
			->appendTo($ul);

		$this->assertSame(
			'<ul><li>a</li><li>b</li></ul>',
			$ul->toString()
		);
	}

	public function testClasses() {
		$el = new Element('p', ['class' => 'foo bar']);

		$this->assertTrue($el->hasClass('foo'));
		$this->assertTrue($el->hasClass('bar'));

		$el->removeClass('foo');
		$this->assertFalse($el->hasClass('foo'));
		$this->assertTrue($el->hasClass('bar'));

		$el->toggleClass('baz', true);
		$el->toggleClass('bar', false);
		$this->assertTrue($el->hasClass('baz'));
		$this->assertFalse($el->hasClass('bar'));
		$this->assertFalse($el->hasClass('foo'));
	}

	public function testAttributes() {
		$el = new Element('p', ['class' => 'foo bar']);

		$this->assertNull($el->attr('style'));
		$this->assertEquals($el->attr('style', 'display: none;'), $el);
		$this->assertEquals($el->attr('style'), 'display: none;');

		$this->assertEquals($el->attr(), ['class' => 'foo bar', 'style' => 'display: none;']);

		$el->removeAttr('style');
		$this->assertNull($el->attr('style'));

		$this->assertEquals($el->attr(), ['class' => 'foo bar']);
	}

	public function testHtml() {
		$el = new Element('p');

		$el->html((new Element('span'))->text('abc'));
		$this->assertSame('<p><span>abc</span></p>',$el->toString());
		$this->assertSame('<span>abc</span>',$el->html());

		$el->html('def');
		$this->assertSame('<p>def</p>',$el->toString());

		$el->html(new Text('ghi'));
		$this->assertSame('<p>ghi</p>',$el->toString());

		$el->html(new RawHtml('<i>jkl</i>'));
		$this->assertSame('<p><i>jkl</i></p>',$el->toString());
	}

}