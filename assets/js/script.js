var j = jQuery.noConflict();
j(document).ready(function(){
	// var rowIndex = 0;
	j('#addWS').on('click', function(e){
		e.preventDefault();
		var addrow = '<tr><td>test</td><td>test</td></tr>';
		j('#inputWS tbody').append(addrow);
	});

	j('#testjquery').on('click', function(e){
		e.preventDefault();
		alert('jquery loaded');
	});
});

