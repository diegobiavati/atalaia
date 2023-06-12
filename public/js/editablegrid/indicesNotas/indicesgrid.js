/**
 *  highlightRow and highlight are used to show a visual feedback. If the row has been successfully modified, it will be highlighted in green. Otherwise, in red
 */
function highlightRow(rowId, bgColor, after)
{
	var rowSelector = $("#" + rowId);
	rowSelector.css("background-color", bgColor);
	rowSelector.fadeTo("normal", 0.5, function() { 
		rowSelector.fadeTo("fast", 1, function() { 
			rowSelector.css("background-color", '');
		});
	});
}

function highlight(div_id, style) {
	highlightRow(div_id, style == "error" ? "#e5afaf" : style == "warning" ? "#ffcc00" : "#8dc70a");
}



//create our editable grid
var editableGrid = new EditableGrid("GridIndice", {
	enableSort: true, // true is the default, set it to false if you don't want sorting to be enabled
	editmode: "absolute", // change this to "fixed" to test out editorzone, and to "static" to get the old-school mode
	editorzoneid: "edition", // will be used only if editmode is set to "fixed"
	//pageSize: 10,
	//maxBars: 10
});

//helper function to display a message
function displayMessage(text, style) { 
	_$("message").innerHTML = "<p class='" + (style || "ok") + "'>" + text + "</p>"; 
} 

//helper function to get path of a demo image
function image(relativePath) {
	return "/images/editablegrid/" + relativePath;
}

//this will be used to render our table headers
function InfoHeaderRenderer(message) { 
	this.message = message; 
	this.infoImage = new Image();
	this.infoImage.src = image("information.png");
};

InfoHeaderRenderer.prototype = new CellRenderer();
InfoHeaderRenderer.prototype.render = function(cell, value) 
{
	if (value) {
		// here we don't use cell.innerHTML = "..." in order not to break the sorting header that has been created for us (cf. option enableSort: true)
		var link = document.createElement("a");
		link.href = "javascript:alert('" + this.message + "');";
		link.appendChild(this.infoImage);
		cell.appendChild(document.createTextNode("\u00a0\u00a0"));
		cell.appendChild(link);
	}
};

//this function will initialize our editable grid
EditableGrid.prototype.initializeGrid = function() 
{
	with (this) {

		// use a special header renderer to show an info icon for some columns
		if(this.hasColumn('nr_item')){
			setHeaderRenderer("nr_item", new InfoHeaderRenderer("O número do item não pode ser repetido."));
			setHeaderRenderer("score_total", new InfoHeaderRenderer("O Score Total deve ser preenchido."));

			if (hasColumn('assunto_basico')) {
				setHeaderRenderer("assunto_basico", new InfoHeaderRenderer("Informe o nome da avaliação do período básico1."));
				// use autocomplete on assunto_basico
				//setCellEditor("assunto_basico", new AutocompleteCellEditor({
				//	suggestions: ['Arm Mun Tiro', 'Tec Mil I', 'Tec Mil II', 'Tec Mil III', 'Lid Mil', 'Et Prof MilDir', 'Hist Mil Brasil'
				//,'L Inglesa']
				//}));
			}
	
			// add a cell validator to check that the score_total is in [0, 10[
			addCellValidator("score_total", new CellValidator({
				isValid: function(value) { return value == "" || (parseInt(value) >= 0 && parseInt(value) < 30); }
			}));
	
			// register the function that will handle model changes
			modelChanged = function(rowIndex, columnIndex, oldValue, newValue, row) {
				updateCellValue(this, rowIndex, columnIndex, oldValue, newValue, row);
			};
	
			// update paginator whenever the table is rendered (after a sort, filter, page change, etc.)
			//tableRendered = function() { this.updatePaginator(); };
			tableRendered = function() { getGBM() };

			rowSelected = function(oldRowIndex, newRowIndex) {
				if (oldRowIndex < 0) displayMessage("Linha Selecionada '" + this.getRowId(newRowIndex) + "'", 'message_feedback');
				else displayMessage("A linha selecionada mudou de '" + this.getRowId(oldRowIndex) + "' to '" + this.getRowId(newRowIndex) + "'", 'message_feedback');
			};
	
			rowRemoved = function(oldRowIndex, rowId) {
				displayMessage("Índice removido '" + oldRowIndex + "' - ID = " + rowId, 'message_feedback');
			};
	
			// render for the action column
			setCellRenderer("action", new CellRenderer({render: function(cell, value) {
				// this action will remove the row, so first find the ID of the row containing this cell 
				var rowId = editableGrid.getRowId(cell.rowIndex);
	
				//cell.innerHTML = "<a onclick=\"if (confirm('Você tem certeza que vai excluir o Índice ? ')) { editableGrid.remove(" + cell.rowIndex + "); } \" style=\"cursor:pointer\">" +
				cell.innerHTML = "<a onclick=\"editableGrid.deleteRow(" + cell.rowIndex + "); \" style=\"cursor:pointer\">" +
				'<i onclick="" style="color:#ff0000a3;padding: 0px 5px 0px 5px;font-size:20px;" class="ion-trash-a" alt=\"delete\" title=\"Excluir Linha\"></i></a>';
	
				/*cell.innerHTML+= "&nbsp;<a onclick=\"editableGrid.duplicate(" + cell.rowIndex + ");\" style=\"cursor:pointer\">" +
				"<img src=\"" + image("duplicate.png") + "\" border=\"0\" alt=\"duplicate\" title=\"Duplicate row\"/></a>";*/
				
			}})); 

			
			
			// render the grid (parameters will be ignored if we have attached to an existing HTML table)
			renderGrid("tablecontent", "testgrid", "tableid");
		}
		
	}
};

EditableGrid.prototype.onloadJSON = function(url) 
{
	// register the function that will be called when the XML has been fully loaded
	this.tableLoaded = function() { 
		displayMessage("Carregamento de " + this.getRowCount() + " índice(s)", 'message_feedback'); 
		this.initializeGrid();
	};

	// load JSON URL
	this.loadJSON(url);
};

EditableGrid.prototype.fetchGrid = function(url)  {
	// call a PHP script to get the data
	this.loadJSON(url);
};