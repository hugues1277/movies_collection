<?php
script('people', ['Tag','Form','Movie','Imdb','script']);
style('movies_collection', 'style');
?>

<div id="app">
	<div id="app-navigation">
		<?php print_unescaped($this->inc('navigation/index')); ?>
	</div>

	<div id="app-content">
		<?php print_unescaped($this->inc('content/index')); ?>
	</div>
</div>

