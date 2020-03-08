//lance l'initialisation d'un datables

    $.fn.dataTableExt.oApi.fnGetHiddenTrNodes = function(oSettings) {
      /* Note the use of a DataTables 'private' function thought the 'oApi' object */
    	var api = new jQuery.fn.dataTable.Api( oSettings );
    	var anNodes = api.rows().nodes().toArray();
    	var anDisplay = $('tbody tr', oSettings.nTable);
    
      /* Remove nodes which are being displayed */
      for ( var i = 0; i < anDisplay.length; i++) {
        var iIndex = jQuery.inArray(anDisplay[i], anNodes);
        if (iIndex != -1) {
          anNodes.splice(iIndex, 1);
        }
      }
    
      /* Fire back the array to the caller */
      return anNodes;
    }
    
    $.fn.dataTableExt.oApi.fnGetFilteredNodes = function ( oSettings )
    {
      var anRows = [];
      for ( var i=0, iLen=oSettings.aiDisplay.length ; i<iLen ; i++ )
      {
        var nRow = oSettings.aoData[ oSettings.aiDisplay[i] ].nTr;
        anRows.push( nRow );
      }
      return anRows;
    };
    
    $.fn.dataTableExt.afnSortData['dom-checkbox'] = function(oSettings, iColumn) {
      var aData = [];
      $('td:eq(' + iColumn + ') input', oSettings.oApi._fnGetTrNodes(oSettings))
          .each(function() {
            aData.push(this.checked == true ? "1" : "0");
          });
      return aData;
    }
    
    /* Create an array with the values of all the input boxes in a column */
    $.fn.dataTableExt.afnSortData['dom-inputtext'] = function  ( oSettings, iColumn )
    {
      var aData = [];
      $( 'td:eq('+iColumn+') input', oSettings.oApi._fnGetTrNodes(oSettings) ).each( function () {
        aData.push( this.value );
      } );
      return aData;
    }

    /* Create an array with the values of all the select options in a column */
    $.fn.dataTableExt.afnSortData['dom-select'] = function  ( oSettings, iColumn )
    {
      var aData = [];
      $( 'td:eq('+iColumn+') select', oSettings.oApi._fnGetTrNodes(oSettings) ).each( function () {
        aData.push( $(this).val() );
      } );
      return aData;
    }
    
    function trim(str) {
    	str = str.replace(/^\s+/, '');
    	for (var i = str.length - 1; i >= 0; i--) {
    		if (/\S/.test(str.charAt(i))) {
    			str = str.substring(0, i + 1);
    			break;                                
    			}                        
    		}                        
    	return str;                
    	} 
    
    function dateHeight(dateStr){        
    	if (trim(dateStr) != '') {               
    		var frDate = trim(dateStr).split(' ');   
    		if( frDate[1] == null ) {
    			var frTime = "00:00:00";
    	      	}
    		else
    			{
    			var frTime = frDate[1].split(':');
    			}
    		var frDateParts = frDate[0].split('/');               
    		var day = frDateParts[0] * 60 * 24;                
    		var month = frDateParts[1] * 60 * 24 * 31;               
    		var year = frDateParts[2] * 60 * 24 * 366;                
    		var hour = frTime[0] * 60;               
    		var minutes = frTime[1];               
    		var x = day+month+year+hour+minutes;        
    		} else {                
    			var x = 99999999999999999; //GoHorse!        
    			}        
    	return x;
    	}                 
    
    $.fn.dataTableExt.oSort['date-euro-asc'] = function(a, b) {
    	var x = dateHeight(a);                        
    	var y = dateHeight(b);                        
    	return ((x < y) ? -1 : ((x > y) ? 1 : 0));                        
    	};                 
    	
    $.fn.dataTableExt.oSort['date-euro-desc'] = function(a, b) {                       
    	var x = dateHeight(a);                       
    	var y = dateHeight(b);                        
    	return ((x < y) ? 1 : ((x > y) ? -1 : 0));                        
    	};
    	
    	

function initDT(id, objet)
{
	// fix pour IE9
	if(navigator.appName == "Microsoft Internet Explorer") {
	    var re = new RegExp("MSIE ([0-9]{1,}[.0-9]{0,})");
	    if (re.exec(navigator.userAgent) != null) {
	    	if (parseInt(RegExp.$1) == 9) {
	    		var expr = new RegExp('>[ \t\r\n\v\f]*<', 'g');
	    		var tbhtml = $('#'+id).html();
	    		$('#'+id).html(tbhtml.replace(expr, '><'));
	    		caenHelpIconInit();
	    		}
	    	}
		}
	var cnf = initialiserDataTable();
	MergeRecursive(cnf, objet);
	//pour chaque élement surlequele on doit initialiser le datatable
	$('#'+id).each(function(){
		oTable = $(this).dataTable(cnf);
			oTable.closest('form').submit( function() {
  				 var nNodes = oTable.fnGetHiddenTrNodes();
  	  	         for (var i = 0; i < nNodes.length; i++)
  	  	         {     
  	  	        	 
  	  	          if ($('input:checked',nNodes[i]).val())
  	  	           {
  	  	             // CHECKBOX
  	  	             var chk = $('input:checked',nNodes[i]);       
  	  	             console.log(chk);
  	  	             chk.attr('checked','checked');
  	  	             //chk.val(1);
  	  	             chk.attr('style','display:none');       
  	  	             $(this).append(chk);
  	  	           }
  	  	             // SELECT
  	  	             var select = $('select',nNodes[i]);       
  	  	             $(this).append(select);
  	  	             
  	  	             // INPUT
  	  	             var input = $('input[type=text]',nNodes[i]);        
  	  	             $(this).append(input);
  	  	             
  	  	             
  	  	             
  	  	             var textarea = $('textarea',nNodes[i]);
  	  	             textarea.hide();
  	  	             $(this).append(textarea);
  	  	             
  	  	         }
  	  	         return true;
  			} );
	});
	
	$('#' + id).width("0px");
}


window.initDT = initDT;

//fonction d'initialisation d'un datatable
function initialiserDataTable()
{
	
	return {
		"autoWidth": false,
		"sPaginationType": "full_numbers",
		"bStateSave": true,
		"aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Toutes"]],
		"oLanguage": {
			"sLengthMenu": "Afficher _MENU_ lignes par page",
			"sZeroRecords": "Aucun résultat",
			"sInfo": "Eléments _START_ à _END_ des _TOTAL_ trouvés",
			"sInfoEmpty": "Aucun résultat",
			"sInfoFiltered": "(filtrés des _MAX_ disponibles)",
			"sSearch": " <i class=\" fas fa-search \"></i> ",
			"oPaginate": {
              "sFirst": "<<",
              "sPrevious": "<",
              "sNext": ">",
              "sLast": ">>"
				} 
			}

		}
}

