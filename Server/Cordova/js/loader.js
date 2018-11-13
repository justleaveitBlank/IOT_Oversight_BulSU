$(function(){
	var $loader = $('#loader'),
	max = 10, speed = 1000,
	char = '<i>&#8226;</i>', count = 0,
	dots = function(){
		if ( count <= max ) {
			count++;
			for ( var i = 0; i < 1; i++ ) {
				$loader.append(char);
				$loader.find('i').fadeIn(speed);
				console.log(i);
			}
		} 
		else {
			clearInterval(dots);
		}
		// COLOR FUN * OPTIONAL *
		/*
		$('#loader i').each(function(i){
		var hue = 10 * i;
		$(this).css({ color: 'hsl('+hue+',75%,50%)' });
		});
		*/
	};
	setInterval(dots,speed/2);
	

});