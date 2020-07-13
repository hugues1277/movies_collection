<?php
script('movies_collection', 'script');
script('movies_collection', 'Imdb');
script('movies_collection', 'Movie');
script('movies_collection', 'Form');
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

