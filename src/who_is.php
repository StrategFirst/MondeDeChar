<ul>
<?php


$T6_char = json_decode(file_get_contents('./who_is.json'));

foreach($T6_char as $char) {
	if($char->is_premium) {
		$cl = 't6-prem';
	} else {
		$cl = 't6-norm';
	}
	echo '<li class="'. $cl . '">';
		echo '<img src="' . $char->images->small_icon . '" />';

		echo $char->short_name;
	echo '</li>';
}

?>
</ul>

<style>
ul {
  list-style-type: none;
}

.t6-norm {
  border-left: 8px solid green;
}

.t6-prem {
  border-left: 8px solid gold;
}

ul li {
  margin: 10px 0px;
}
</style>
