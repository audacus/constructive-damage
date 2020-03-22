
<div id="container">
	<div id="leftMenu">
		<div id="titleContainer">
			<div class="title" id="objects">
				<button type="button" class="titleButton" id="addButton" onclick="addNewObject()">+</button>
				<h1>Objects</h1>
				<button type="button" class="titleButton" id="refreshButton" onclick="requestUpdate()">↻</button>
			</div>
		</div>
		<div id="contentContainer">
		</div>
	</div>
	<div id="editorContainer">
		<pre id="editor" onresize="editor.resize()">
		</pre>
		<div id="additionalElementsContainer">
			<div id="controlElements">
				<button type="button" class="buttonRight" onclick="saveScript()">Save</button>
				<!-- <button type="button" class="buttonRight" onclick="validateScript()">Validate</button> -->
				<button type="button" onclick="showProperty()">Properties</button>
			</div>
			<div id="output">
				<pre id="outputPre">
				</pre>
			</div>
		</div>
	</div>
</div>
<script src="scripts/js/ace-min-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="scripts/editor/js/_cde.js" type="text/javascript" charset="utf-8"></script>
<div id="properties">
	<h1>Properties</h1>
	<table>
		<tr>
			<td>
				Name:
			</td>
			<td>
				<input id="nameProperty" type="text">
			</td>
		</tr>
		<tr>
			<td>
				Description:
			</td>
			<td>
				<textarea id="decriptionProperty" name="Text1" rows="5" ... ></textarea>
			</td>
		</tr>
		<tr>
			<td>
				<button type="button" onclick="resetProperty()">Reset</button>
			</td>
			<td>
				<button type="button" class="buttonRight" onclick="hideProperty()">Hide</button>
			</td>
		</tr>
	</table>
</div>
