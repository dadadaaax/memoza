<?php
// Image Fit Text Class 0.1 by ming0070913
CLASS ImageFitText{
	public $font, $fontsize, $width, $height;
	public $step_wrap, $step_fontsize;
	
	public function __construct($font, $step_wrap=1, $step_fontsize=1){
		$this->font = $font;
		$this->step_wrap = $step_wrap>1?$step_wrap:1;
		$this->step_fontsize = $step_fontsize>1?$step_fontsize:1;
	}
	
	function fit($width, $height, $text, $fontsize, $min_fontsize=5, $min_wraplength=0){
		$this->fontsize = & $fontsize;
		$text_ = $text;
		
		while($this->TextHeight($text_)>$height && $fontsize>$min_fontsize)
			$fontsize -= $this->step_fontsize;
		
		while(($this->TextWidth($text_)>$width || $this->TextHeight($text_)>$height) && $fontsize>$min_fontsize){
			$fontsize -= $this->step_fontsize;
			$wraplength = $this->maxLen($text);
			$text_ = $text;
			
			while($this->TextWidth($text_)>$width && $wraplength>=$min_wraplength+$this->step_wrap){
				$wraplength -= $this->step_wrap;
				$text_ = wordwrap($text, $wraplength, "\n", true);
				
				//To speed up:
				if($this->TextHeight($text_)>$height) break;
				if($wraplength<=$min_wraplength) break;
				$wraplength_ = $wraplength;
				$wraplength = ceil($wraplength/($this->TextWidth($text_)/$width));
				$wraplength = $wraplength<($min_wraplength+$this->step_wrap)?($min_wraplength+$this->step_wrap):$wraplength;
			}
		}
		
		$this->width = $this->TextWidth($text_);
		$this->height = $this->TextHeight($text_);
		
		return array("fontsize"=>$fontsize, "text"=>$text_, "width"=>$this->width, "height"=>$this->height);
	}

	function maxLen($text){
		$lines = explode("\n", str_replace("\r", "", $text));
		foreach($lines as $line)
			$t[] = strlen($line);
		return max($t);
	}

	function TextWidth($text){
		$t = imagettfbbox($this->fontsize, 0, $this->font, $text);
		return $t[2]-$t[0];
	}

	function TextHeight($text){
		$t = imagettfbbox($this->fontsize, 0, $this->font, $text);
		return $t[1]-$t[7];
	}
}
?>