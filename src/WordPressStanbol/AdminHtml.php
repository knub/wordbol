<?php

namespace WordPressStanbol;

class AdminHtml {

	public static function runStanbolButtonHtml() {
		return <<<TEXT
		<div class="misc-pub-section my-options">
			<input name="enhancement" type="submit" class="button button-primary button-large" id="enhancement" value="Run enhancement">
		</div>
TEXT;
	}
	public static function stanbolSelectionHtml() {
		return <<<TEXT
		<!--<input name="stanbol" type="text" id="stanbol" value="text" />-->
		<input name="text" type="submit" id="text" value="text" />'
TEXT;
	}
}
