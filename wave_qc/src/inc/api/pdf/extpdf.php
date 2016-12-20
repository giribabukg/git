<?php
require_once('Zend/Pdf.php');

class Ext_Pdf_Table {
	public $lX;
	public $lY;
	public $lPage;
	public $lBorder = 0.5;

	protected $lPages = array();
	protected $lRows = array();

	function __construct($aPage, $aX, $aY) {
		$this->lPage = $aPage;
		$this->lX = $aX;
		$this->lY = $aY;
	}

	public function addRow(Ext_Pdf_Table_Row $aRow) {
		$this->lRows[] = $aRow;
	}

	public function render() {
		$lY = $this->lPage->getHeight() - $this->lY;

		foreach ($this->lRows as $lRow) {
			if ($lY - $lRow->testRender($this->lPage, $this->lX, $lY) < 0) {
				$lFnt = $this->lPage->getFont();
				$lFntSiz = $this->lPage->getFontSize();
				$this->lPage = new Ext_Pdf_Page($this->lPage);

				$this->lPage->setFont($lFnt, $lFntSiz);
				$this->lPage->setLineWidth($this->lBorder);
				$this->lPages[] = $this->lPage;
				$lY = $this->lPage->getHeight();
			}

			$lRow->render($this->lPage, $this->lX, $lY);
			$lY -= $lRow->getHeight();
		}

		return $this->lPages;

	}
}

class Ext_Pdf_Table_Row {
	protected $lWidth;
	protected $lHeight;
	protected $lCols = array();

	public function getHeight() {
		return $this->lHeight;
	}

	public function addColumn(Ext_Pdf_Table_Column $aCol) {
		$this->lCols[] = $aCol;
	}

	public function renderBorder($aPage, $aX, $aY) {
		foreach ($this->lCols as $lCol) {
			$lCol->renderBorder($aPage, $aX, $aY, $this->lHeight);
			$aX += $lCol->getWidth();
		}
	}

	public function testRender($aPage, $aX, $aY) {
		$lMaxHeight = 0;

		foreach ($this->lCols as $lCol)
		{

			$lCol->testRender($aPage, $aX, $aY);
			$lDumHeight = $lCol->getHeight();

			if ($lDumHeight > $lMaxHeight)
			{
				$lMaxHeight = $lDumHeight;
			}

			$aX += $lCol->getWidth();
		}

		$this->lHeight = $lMaxHeight;

		return $this->lHeight;

	}

	public function render($aPage, $aX, $aY) {
		$lTmpX = $aX;
		$lMaxHeight = 0;

		foreach($this->lCols as $lCol) {
			$lCol->render($aPage, $aX, $aY);
			$lDumHeight = $lCol->getHeight();

			if ($lDumHeight > $lMaxHeight) {
				$lMaxHeight = $lDumHeight;
			}

			$aX += $lCol->getWidth();
		}

		$this->lHeight = $lMaxHeight;
		$this->renderBorder($aPage, $lTmpX, $aY);
	}
}

class Ext_Pdf_Table_Column {
	protected $lWidth;
	protected $lHeight;
	protected $lText;
	protected $lPad = 3;
	protected $lAlign = 'left';

	public function setText($aText) {
		$this->lText = $aText;

		return $this;
	}
	
	public function setWidth($aWidth) {
		$this->lWidth = $aWidth;

		return $this;
	}

	public function getWidth() {
		return $this->lWidth;
	}

	public function getHeight() {
		return $this->lHeight;
	}

	public function setAlignment($aAlign) {
		$this->lAlign = $aAlign;
	}

	public function renderBorder($aPage, $aX, $aY, $aHeight) {
		$lFntSiz = $aPage->getFontSize();
		$aPage->drawRectangle($aX, $aY, $aX + $this->lWidth, $aY - $aHeight, $fillType = Zend_Pdf_Page::SHAPE_DRAW_STROKE);

	}

	public function testRender($aPage, $aX, $aY) {

		$lFntSiz = $aPage->getFontSize();
		$lTxtSiz = $aPage->getVariableText($this->lText, $aX + $this->lPad, $aPage->getHeight() - $aY + $lFntSiz, $this->lWidth - $this->lPad);

		$this->lHeight = $lTxtSiz['height'] + $this->lPad;

		$this->lWidth = $this->lWidth + $this->lPad;

	}

	public function render($aPage, $aX, $aY) {
		$lFntSiz = $aPage->getFontSize();
		$lTxtSiz = $aPage->drawVariableText($this->lText, $aX + $this->lPad, $aPage->getHeight() - $aY + $lFntSiz, $this->lWidth - $this->lPad, $this->lAlign);
		$this->lHeight = $lTxtSiz['height'] + $this->lPad;
		$this->lWidth = $this->lWidth + $this->lPad;
	}
}

class Ext_Pdf_Page extends Zend_Pdf_Page {
	public function getVariableText($aStr, $aX, $aY, $aMaxWidth) {
		$aY = $this->getHeight() - $aY;
		$lFnt = $this->getFont();
		$lFntSiz = $this->getFontSize();
		$lWrds = explode(' ', $aStr);
		$lWrdsLen = array();
		$lEm = $lFnt->getUnitsPerEm();
		$lSpaceSiz = array_sum($lFnt->widthsForGlyphs(array(ord(' ')))) / $lEm * $lFntSiz;

		foreach ($lWrds as $i => $lWrd) {
			$lWrd .= ' ';
			$lGlyphs = array();

			foreach (range(0, strlen($lWrd) - 1) as $i2) {
				if (isset($aStr[$i2]))
					$lGlyphs[] = ord($aStr[$i2]);
			}

                        $lWrdsLen[] = array_sum($lFnt->widthsForGlyphs($lGlyphs)) / $lEm * $lFntSiz;
		}

		$lIncY = $aY;
		$lIncX = 0;
		$lLines = array();
		$lLine = array();
		$lDum = 0;
		$max_length = count($lWrds);

		while ($lDum < $max_length)
		{
			if (($lIncX + $lWrdsLen[$lDum]) < $aMaxWidth)
			{
				$lIncX += $lWrdsLen[$lDum] + $lSpaceSiz;
				$lLine[] = $lWrds[$lDum];
			}
			else
			{
				$lLines[] = array($lLine, $aX, $lIncY);
				$lIncY -= $lFntSiz;
				$lIncX = 0;
				$lLine = array();
				$lLine[] = $lWrds[$lDum + 1];
				$lDum++;
			}

			$lDum++;
		}

		unset($lWrdsLen);
		$lLines[] = array($lLine, $aX, $lIncY);

		return array('width' => $aMaxWidth, 'height' => ($lFntSiz * count($lLines)), 'lines' => $lLines);
	}

	public function calculateTextWidth($aStr) {
		$lFnt = $this->getFont();
		$lFntSiz = $this->getFontSize();
		$lStr = iconv('UTF-8', 'UTF-16BE//IGNORE', $aStr);
		$lChars = array();

		for ($lDum = 0; $lDum < strlen($lStr); $lDum++)
		{
			$lChars[] = (ord($lStr[$lDum++]) << 8) | ord($lStr[$lDum]);
		}

		$lGlyphs = $lFnt->glyphNumbersForCharacters($lChars);
		$lWidths = $lFnt->widthsForGlyphs($lGlyphs);
		$lRes = (array_sum($lWidths) / $lFnt->getUnitsPerEm()) * $lFntSiz;

		return $lRes;
	}

	public function drawVariableText($aStr, $aX, $aY, $aMaxWidth, $aAlign = 'left') {
		$lText = $this->getVariableText($aStr, $aX, $aY, $aMaxWidth);

		foreach ($lText['lines'] as $lLine) {
			list($aStr, $aX, $aY) = $lLine;
			$lPosX = $aX;

			if ($aAlign == 'right') {
				$lLen = $this->calculateTextWidth(implode(' ', $aStr));
				$lPosX += $aMaxWidth - $lLen;
			}
			else
			if ($aAlign == 'center') {
				$lLen = $this->calculateTextWidth(implode(' ', $aStr));
				$lPosX += ($aMaxWidth - $lLen) / 2;
			}

			$this->drawText(implode(' ', $aStr), $lPosX, $aY);
		}

		return array('width' => $aMaxWidth, 'height' => $lText['height']);
	}

	public function drawMultilineText($aLines, $aX, $aY) {
		$aY = $this->getHeight() - $aY;
		$lFntSiz = $this->getFontSize();

		foreach ($aLines as $lDum => $lLine)
		{
			$this->drawText($lLine, $aX + 2, $aY - ($lFntSiz * 1.2 * $lDum));
		}
	}

	public function drawInfoBox($aHead, $aLines, $aX, $aY, $aWidth, $aHeight) {
		$lFntSiz = $this->getFontSize();
		$this->drawRectangle($aX, $this->getHeight() - $aY, $aX + $aWidth, $this->getHeight() - $aY - $aHeight, $fillType = Zend_Pdf_Page::SHAPE_DRAW_STROKE);
		$this->drawLine($aX, $this->getHeight() - $aY - ($lFntSiz * 2), $aX + $aWidth, $this->getHeight() - $aY - ($lFntSiz * 2));
		$this->drawText($aHead, $aX + ($lFntSiz / 2), $this->getHeight() - $aY - $lFntSiz - ($lFntSiz / 4));
		$this->drawMultilineText($aLines, $aX + 3, $aY + ($lFntSiz * 3));
	}
}
