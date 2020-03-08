$(document).ready(function() {

	$('.date').datepicker({
		inline: true,
		closeText:"Fermer",
		prevText:"",
		nextText:"",
		currentText:"Aujourd'hui",
		monthNames:["Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Décembre"],
		monthNamesShort:["Janv.","Févr.","Mars","Avril","Mai","Juin","Juil.","Août","Sept.","Oct.","Nov.","Déc."],
		dayNames:["Dimanche","Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi"],
		dayNamesShort:["Dim.","Lun.","Mar.","Mer.","Jeu.","Ven.","Sam."],
		dayNamesMin:["D","L","M","M","J","V","S"],
		weekHeader:"Sem.",
		dateFormat:"dd/mm/yy",
		firstDay:1,
		isRTL:!1,
		showMonthAfterYear:!1,
		yearSuffix:"",
		changeYear: true,
		changeMonth: true
	}).after(' <i class="fas fa-calendar couleur_icone_defaut" style="font-size:1.5em;"></i>');
	
	$("#ui-datepicker-div").hide();
	
});