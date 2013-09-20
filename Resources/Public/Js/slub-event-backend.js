
function checkBoxes(objThis){
  // Checkbox selected? (true/false)
  var blnChecked = objThis.checked;
  // parent node
  var objHelp = objThis.parentNode;

  while(objHelp.nodeName.toUpperCase() != "LI"){
    // next parent node
    objHelp = objHelp.parentNode;
  }

  var arrInput = objHelp.getElementsByTagName("input");
  var intLen = arrInput.length;

  for(var i=0; i<intLen; i++){
    // select/unselect Checkbox
    if(arrInput[i].type == "checkbox"){
      arrInput[i].checked = blnChecked;
    }
  }
}

// --------------------
// fold tree js part
// --------------------

// add new event initTreefolder
addEvent(window, "load", initTreeFolder);
function initTreeFolder() {
	TreeFolder.init();
}

// add class name
function addClassName(oNode, sClass, bAdd) {
	if (bAdd==null) bAdd = true;
	var aClass = oNode.className.split(" ");
	var iPos = -1;
	for (var i=0; i<aClass.length; i++) {
		if (aClass[i]==sClass) {
			iPos = i;
			break;
		}
	}
	if (bAdd&&iPos==-1) aClass.push(sClass);
	else if (!bAdd&&iPos>=0) aClass.splice(iPos,1);
	oNode.className = aClass.join(" ");
}

//////////////////
// isClassName //
function isClassName(oNode,sClass) {
	return (" "+oNode.className+" ").indexOf(sClass)!=-1;
}

///////////////
// addEvent //
function addEvent( obj, type, fn) {
	if ( obj.attachEvent ) {
		obj['e'+type+fn] = fn;
		obj[type+fn] = function() { obj['e'+type+fn]( window.event ); }
		obj.attachEvent( 'on'+type, obj[type+fn] );
	} else obj.addEventListener( type, fn, false );
}
function removeEvent( obj, type, fn ) {
	if ( obj.detachEvent ) {
		obj.detachEvent( 'on'+type, obj[type+fn] );
		obj[type+fn] = null;
	} else obj.removeEventListener( type, fn, false );
}

////////////////
// construct //
var oSelectedPage;
var TreeFolder = new function() {
}

// init
TreeFolder.init = function() {
    // Find td with classname 'foldtree' which contains the folable tree
    if (!document.getElementsByTagName) return;
    var aUls = document.getElementsByTagName("td");
    for (var i=0; i<aUls.length; i++) {
		if (isClassName(aUls[i], "foldtree")) {
			TreeFolder.prepare(aUls[i]);
		}
	}
}

// prepare //
TreeFolder.prepare = function(oElement, iDepth) {
	// we currently don't need the iDepth but... nobody knows.
	if (iDepth==null) var iDepth = -1;
	iDepth++;
	var bFold = true;
	var iUl = -1;
	var iLi = -1;
	var iA = -1;
	for (var i=0; i<oElement.childNodes.length; i++) {
		var oChild = oElement.childNodes[i];
		switch (oChild.nodeName.toLowerCase()) {
			case "ul":
				iUl = i;
				TreeFolder.prepare(oChild, iDepth);

				// check if some checkboxes are checked and fold everything else by default
				var av = oChild.getElementsByTagName("input");
				for (e = 0; e < av.length; e++)
				{
					if (av[e].checked == true)
					{
						bFold = false;
					}
				}
				//~ alert(bFold + "checked?" + addon);
				if (bFold) oChild.style.display = "none";
			break;
			case "li":
				iLi = i;
				TreeFolder.prepare(oChild,iDepth);
			break;
			case "input":
				iA = i;
			break;
		}
	}
	 // insert extra anchor
	if (iA>=0 && iUl>=0) {
		var oA = oElement.childNodes[iA];
		iOnclick = -1;
		var oUl = oElement.childNodes[iUl];
		for(var j=0;j<oA.attributes.length;j++) {
			if (oA.attributes[j].nodeName.toLowerCase()=="onclick") {
				iOnclick = j;
			}
		}
		// name class of parent list element
		//~ if (bFold)
			//~ oElement.className = "open";
		//~ else
			oElement.className = (bFold ? "closed" : "open");
		// create extra fold anchor
		var oAfold = document.createElement("a");
		oAfold.className = "foldicon";

		oAfold.innerHTML = "<span>" + (bFold ? "+" : "-") + "</span> ";

		oElement.insertBefore(oAfold,oA);
		addEvent(oAfold,"click",function(){
			TreeFolder.liAClicked(oAfold)
		});
	} else if (iA>=0 && iDepth>2) {

		var oAfold = document.createElement("div");
		oAfold.className = "foldicon";

		oAfold.innerHTML = "<span>&nbsp;</span>";

		oElement.insertBefore(oAfold,oElement.childNodes[iA]);
	}

}

// activateALi //
TreeFolder.liAClicked = function(oAnchor) {
	var oLi = oAnchor.parentNode;
	var aSibling = oLi.childNodes;
	var bDisplay = false;
	for(var i=0;i<aSibling.length;i++) {
		var oSibling = aSibling[i];
		if (oSibling.nodeName.toLowerCase()=="ul") {
			bDisplay = oSibling.style.display=="none";
			oSibling.style.display = bDisplay?"block":"none";
		}
		var oSpan = oAnchor.getElementsByTagName("span")[0];
		oSpan.innerHTML = bDisplay ? "-" : "+";
	}
	addClassName(oAnchor.parentNode,"open",bDisplay);
	addClassName(oAnchor.parentNode,"closed",!bDisplay);
}
