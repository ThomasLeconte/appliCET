function caenHelpIconInit(){
	
	$('.helpIcon').each(function()
	{
		$(this).qtip(
		{   
			content: { 
			    text: $(this).next().find('div.message').html()
			},
			position: {
				corner: { 
					target: $(this).next().find('div.target').text(), 
					tooltip: $(this).next().find('div.tooltip').text()
				}
			},
			show: { 
				/*when: 'mouseover', 
				delay: 0
				*/
				when: 'click'
			},
			hide: 'unfocus',
			style: {
				classes: 'qtip-light qtip-shadow',
				width: ($(this).next().find('div.width').text() != '') ? parseInt($(this).next().find('div.width').text()) : '350px',
				tip: true,
			    background: '#DBEBFF',
				border: { 
					width: 1, 
					radius: 0, 
					color: '#106fb1' 
				}
			}
		});

	});
	
}

$(document).ready(caenHelpIconInit());	