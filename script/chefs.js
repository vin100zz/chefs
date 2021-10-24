var btn = $.fn.button.noConflict() // reverts $.fn.button to jqueryui btn
$.fn.btn = btn // assigns bootstrap button functionality to $.fn.btn

// http://www.meadow.se/wordpress/?p=536
function RefreshTable(tableId, urlData)
{
  $.getJSON(urlData, null, function( json )
  {
    table = $(tableId).dataTable();
    oSettings = table.fnSettings();
    
    table.fnClearTable(this);

    for (var i=0; i<json.aaData.length; i++)
    {
      table.oApi._fnAddData(oSettings, json.aaData[i]);
    }

    oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
    table.fnDraw();
    table.fnAdjustColumnSizing();
	
	initRowButtons();
  });
}

// get id of row on which the user clicked
function getRowId(el) {
  return $($(el).closest("tr")[0]).attr('data-id');
}

// init edit/delete buttons
var idToDelete = null;
function initRowButtons()
{
	$(".action.edit")
	  .click(function() {
      var id = getRowId(this);
      $("#newedit_id").val(id);
      var evt = bom[id];
      $("#newedit_date").val(evt["Date"]);
      $("#newedit_pays").val(evt["Pays"]);
      $("#newedit_evt").val(evt["Evenement"]);
      //$("#newedit_ancien").val(evt["Ancien"]);
      $("#newedit_nouveau").val(evt["Nouveau"]);
      $("#newedit_titre").val(evt["Titre"]);
      $("#newedit_depart").val(evt["Depart"]);
      $(".chzn-done").trigger("liszt:updated");
      $("#dialog_newedit").dialog("open");
    });
	  
	$(".action.delete")
	  .click(function() {
      idToDelete = getRowId(this);
      $("#dialog_delete").dialog("open");
    });
}

// init page
var bom = [];

$(document).ready( function ()
{
	// IE killer
	if(!$.browser.chrome) { $("body").html("<h1>Veuillez utiliser Google Chrome :)</h1>"); return; }
  
	// dialogs
	$(".dialog").dialog({
	  autoOpen: false,
	  modal: true,
	  show: {
        effect: "blind",
        duration: 300
      },
	  hide: {
        effect: "blind",
        duration: 300
      }
    });
	
	$("#dialog_filtres").dialog({
      width: 450,
      Cancel: function() {
          $(this).dialog("close");
        }
    });
	
	$("#dialog_newedit").dialog({
      width: 450,
      Cancel: function() {
          $(this).dialog("close");
        }
    });
	
	$("#dialog_delete").dialog({
      width: 300,
      Cancel: function() {
          $(this).dialog("close");
        }
    });
	
	// table
	$('#table_chronologie').dataTable( {
		"bAutoWidth" : true,
    "bProcessing": true,
		"bFilter": false,
    "sAjaxSource": "data.php",
		"iDisplayLength": 50,
		"bLengthChange": false,
		"fnDrawCallback": function(oSettings, json) {initRowButtons();},
		"aoColumnDefs": [{'bSortable': false, 'aTargets': [9]}],
		"aaSorting": [[1, 'desc']],
		"aoColumns": [{"bVisible": false}, null, null, null, null, null, null, null, {"asSorting": ["desc", "asc"]}, null],
		"oLanguage": {
		  "sInfo": "_START_-_END_ sur _TOTAL_",
		  "sInfoThousands": "",
		  "oPaginate": {"sPrevious": "Précédent", "sNext": "Suivant"}
		},
    "fnRowCallback": function(nRow, aData, iDisplayIndex) {
      var id = aData[0];
      nRow.setAttribute("data-id", id);
      bom[id] = aData[aData.length-1];
      return nRow;
    }
	});

	// autocompletes
	$("#filtres select").chosen();
  $("#dialog_newedit select").chosen();
  
  // deploy
  $("#quick_filtre .title")
  .click(function() {
    if (!$("#quick_filtre .title").hasClass("deployed")) {
      $("#quick_filtre ul").css('max-height', '400px');
    } else {
      $("#quick_filtre ul").css('max-height', '0');
    }
    $("#quick_filtre .title").toggleClass("deployed");
    $("#quick_filtre .title").toggleClass("undeployed");
  });
	
	// buttons
	$("#button-filter")
  .click(function() {
    $("#dialog_filtres").dialog("open");
  });
  
  $("#button-quick-filter")
  .click(function() {
    $("#quick_filtre").show();
  });    
      
  $("#button_filtres_reset")
  .button({
    icons: {
      primary: "ui-icon-trash"
    }
  })
  .click(function(evt) {
    evt.preventDefault();
    clearFilterForm();
  });
	  
	$("#button_filtres_ok")
  .button({
    icons: {
    primary: "ui-icon-check"
    }
  })
  .click(function() {
    var params = ["ts=" + new Date().getTime()];
    
    $("#timeline").html('');
  
    // table filtering
    var date = $("#filtre_date").val();
    params.push("date=" + date);
    
    var pays = $("#filtre_pays").val();
    if(pays != null) params.push("pays=" + pays);
        
    var combiPays = $("#filtre_combipays").val();
    if(combiPays != null) params.push("combiPays=" + combiPays);
    
    var evt = $("#filtre_evt").val();
    if(evt != null) params.push("evt=" + evt);
    
    var perso = $("#filtre_perso").val();
    if(perso != null) params.push("perso=" + perso);
    
    var titre = $("#filtre_titre").val();
    if(titre != null) params.push("titre=" + titre);
    
    var enCours = $("#filtre_encours").is(':checked');
    params.push("enCours=" + enCours);

    params = params.join("&");
    if(params != "") params = "?" + params;
    
    RefreshTable("#table_chronologie", "data.php" + params);
    
    $("#dialog_filtres").dialog("close");
    
    return false;
  });	  
	  
	$("#button-new")
  .click(function() {
    clearNewEditForm();
    $("#dialog_newedit").dialog("open");
  });
	  
	$("#button_newedit_ok")
  .button({
    icons: {
      primary: "ui-icon-check"
    }
  }).click(function(evt) {
    evt.preventDefault();
    $.post('new_edit.php', $("#dialog_newedit").serialize(), newEditCallback);
  });
	  
	$("#button_newedit_cancel")
  .button({
    icons: {
      primary: "ui-icon-cancel"
    }
  })
  .click(function(evt) {
    evt.preventDefault();
    $("#dialog_newedit").dialog("close");
    clearNewEditForm();
  });

	$("#button_delete_yes")
  .button({
    icons: {
      primary: "ui-icon-check"
    }
  })
  .click(function() {
    $.post('delete.php', {id: idToDelete}, deleteCallback);
  });
	  
	$("#button_delete_no")
  .button({
    icons: {
    primary: "ui-icon-cancel"
    }
  }).click(function() {
      $("#dialog_delete").dialog("close");
  });
	
	// slider
  var year = new Date().getFullYear() + 1;
	$("#filtre_date_slider").slider({
      range: true,
      min: 1900,
      max: year,
      values: [1900, year],
      vcSetField: function(ui) {  },
      slide: function(event, ui) { $("#filtre_date").val(ui.values[0] + "-" + ui.values[1]); }
    });
	$("#filtre_date").val("1900-" + year);	
	
	$("#filtre_encours").button();
  
  // tooltips
  $('#action-filter').tooltip({title: 'Filtrer', container: $('#action-filter'), placement: 'left'});
  $('#action-quick-filter').tooltip({title: 'Filtrer par pays', container: $('#action-quick-filter'), placement: 'left'});
  $('#action-new').tooltip({title: 'Ajouter un événement', container: $('#action-new'), placement: 'left'});
  
  $('#quick_filtre .flag').tooltip({placement: 'bottom'});
  
  $('#quick_filtre .flag').click(function() {
    clearFilterForm();
    var pays = $(this).data('original-title');
    $("#filtre_pays").val(pays);
    $("#filtres .chzn-done").trigger("liszt:updated");
    $.get('timeline.php?pays=' + pays, timeline);
    RefreshTable("#table_chronologie", "data.php?pays=" + pays);
  });
});

function clearNewEditForm() {
  $("#dialog_newedit")[0].reset();
  $("#newedit_id").val('');
  $("#dialog_newedit .chzn-done").trigger("liszt:updated");
}

function clearFilterForm() {
  $("#filtres")[0].reset();
  $("#filtres .chzn-done").trigger("liszt:updated");
}

function newEditCallback(data, textStatus, jqXHR) {
  /*var newAnciens = JSON.parse(data);
  $('#newedit_ancien').html('');
  var str = '';
  for (var i=0; i<newAnciens.length; ++i) {
    str += '<option value="' + newAnciens[i] + '">' + newAnciens[i] + '</option>';
  }
  $('#newedit_ancien').html(str);
  $('#newedit_ancien').chosen();*/
  
  $("#dialog_newedit").dialog("close");
  clearNewEditForm();
  $("#button_filtres_ok").click();
}

function deleteCallback(data, textStatus, jqXHR) {
  $("#dialog_delete").dialog("close");
  idToDelete = null;
  $("#button_filtres_ok").click();
}

// timeline
function timeline(data) {
  var events = JSON.parse(data);
  var periods = [];
  
  for (var i=0; i<events.length; ++i) {
    var event = events[i];
    if (event.Nouveau) {
      periods.push({
        begin: new Date(event.Date),
        end: new Date(event.Depart),
        id: event.Id,
        name: event.Nouveau
      });
    }
  }

  drawTimeline({
    container: document.querySelector('#timeline'),
    periods: periods
  });

}








