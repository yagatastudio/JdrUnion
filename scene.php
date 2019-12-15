<style type="text/css">
	#playingGround{
		width: 100%;
		height: 100%;
		position: fixed;
		left: 0;
		top: 0;
		background-color: red;
		z-index: 1;
	}
	.ui-draggable-dragging{
		z-index:40 !important;
	}


	#leftMenu{
		width: 15%;
		height: 100%;
		float: left;
		left: 0;
		background-color: white;
		z-index: 2;
		position: absolute;
	}
	#folderName{
		width: 100%;
		height: 5%;
		margin: 0;
		background-color: grey;
	}
	#list{
		width: 100%;
		height: 85%;
		overflow-y: auto !important; 
		padding: 0;
	}
	#list > .container{
		width: 100%;
		height: 50px;
		padding: 2%;
		vertical-align: middle;
		margin: 0;
		background-color: green;
		cursor: move;
		float: left;
		display: inline-block;
		position: relative;
		z-index: 2;
		border-bottom: black 2px solid;
	}
	.container > div{
		width: 80%;
		margin-right: 5%;
		display: inline;
		vertical-align: middle;
	}
	.container > img{
		display: inline;
		width: 10%;
		margin: 0;
		padding: 0;
		vertical-align: middle;
	}
	.help{
	    border : 1px solid #1877D5;
	    background : #84BEFD;
	    opacity : 0.3;
	}


	#rightMenu{
		height: 100%;
		width: 15%;
		height: 100%;
		position: absolute;
		right: 0;
		background-color: white;
		z-index: 2;
	}
	.selectedItem{
		background-color: yellow;
	}
	.selectedImage{
		z-index: 1000 !important;
	}
	#list2 > .container:hover{
		cursor: pointer;
	}
</style>
<div>
	<div id="leftMenu">
		<div id="folderName">IMAGE</div>
		<div id="list"></div>
	</div>
	<div id="rightMenu">
		<div id="list2"></div>
	</div>
	<div id="playingGround"></div>
</div>
<script>
	const selectedFolder = "image";
	const zoomRatio = 0.15;
	var maxIndex = 0;
	var fileIntoSelectedFolder = new Array();
	var fileInPlayingGround = new Array();
	var selectedItem = "";
	var mouseX = 0;
	var mouseY = 0;

	$(document).on('contextmenu', function(event) {
		event.preventDefault();
	});

	//Set by interval the array fileIntoSelectedFolder and populate the object with id #list
	setInterval(function(){
		$.ajax({
			url: selectedFolder+"/",
			success: function(_data){
				$(_data).find("td > a").each(function(){
					if(openFile($(this).attr("href"))){
						if(fileIntoSelectedFolder.indexOf($(this).attr("href")) == -1){
							fileIntoSelectedFolder.push($(this).attr("href"));
							$('#leftMenu #list').append('<div class="container draggableItem"><div>'+$(this).attr("href")+'</div><img src="'+selectedFolder+'/'+$(this).attr("href")+'"></div>')
						}
					}
				})
			}
		});
		
	}, 20);

	//Test if the file passed is a image and return true
	function openFile(file) {
		var extension = file.substr( (file.lastIndexOf('.') +1) );
	    switch(extension) {
	    	case 'jpg':
	        case 'png':
		    case 'gif':   // the alert ended with pdf instead of gif.
	    	 	return true;
	        	break;
	    	default:
	      		return false;
	    }
	};
	
	
	function addToList(objName, objId){
		fileInPlayingGround.push(objName);
		$('#rightMenu #list2').append('<div class="container"><div id="list_'+objId+'">'+objName+'<img class="unlock" src="image/openLock.png"><img class="delete" id="del_'+objId+'" src="delete.png"</div></div>');
	}


	//DRAG N DROP
	setInterval(function(){
			$('.draggableItem').draggable({
				constrainment : '#playingGround',
				cursor : 'move',
				stack : '.draggableItem',
				helper : 'clone',
				revert: true,
				revertDuration: 0
			});

			$('#playingGround').droppable({
				drop : function(event, ui){
					var current = ui.draggable.clone();
					var path = current.text();
					var num = Math.floor(Math.random() * 101)+Math.floor(Math.random() * 11);
					if($('#'+path+num).length){
						num = Math.floor(Math.random() * 101)+Math.floor(Math.random() * 11);
					}
					current.fadeOut(function(){
						var top = ui.offset.top-ui.draggable.height()*2;
						var left = ui.offset.left-ui.draggable.width()*2;
						maxIndex += Math.floor(Math.random() * 11);
						$('#playingGround').html(['<img style="top:'+top+'px;left:'+left+'px;position:absolute;z-index:'+maxIndex+'" id="'+path+num+'" src="image/'+path+'">',$('#playingGround').html()].join());
						addToList(path, path+num);
							setSelectedItem(path+num);
					});
				}
			});

			$('.draggableItem').sortable();

			$('.inPlayingGround').resizable({
				aspectRatio : true,
				helper : 'help',
				animate : true
			});

			$('#list2 .container').on('click', function(){
				setSelectedItem($(this).find('div').attr('id').replace('list_', ''));
			});

			$('#playingGround img').on('click', function(event){
				setSelectedItem($(this).attr('id'));
				mouseX = event.clientX;
				mouseY = event.clientY;
			});

			$('#list2 .delete').on('click', function(){
				deleteImage($(this));
			});
			
			$(document).on("mousedown", '#playingGround', function(event){
				if(event.which == 2){
					event.preventDefault();
					console.log("middle mouse");
					//Make ping action
				}
			});
			
			//NEED TO TEST!!
			$(document).on("mousemove", function(event){
				//Detect if left click is pressed
				var mouseDiffPosX = 0;
				var mouseDiffPosY = 0;
				if(event.which == 1){
					//prevent default event
					event.preventDefault();
					//get actual mouse position
					//Define the diff between first mouse position and current position
					mouseDiffPosX = event.clientX - mouseX;
					mouseDiffPosY = event.clientY - mouseY;
					//Actualize mouse position NEED TO TEST!!
					$('#'+ selectedItem).css({top: $('#'+ selectedItem).offset.top + mouseDiffPosY, left: $('#'+ selectedItem).offset.left + mouseDiffPosX});
				}
			});
			
			//NEED TO TEST!!!
			$('#playingGround').on('mousewheel DOMMouseScroll', function(event){
				if(selectedItem != null && selectedItem != ""){
					if(typeof e.originalEvent.detail == 'number' && e.originalEvent.detail !== 0) {
						if(e.originalEvent.detail > 0) {
							//reduce size of the image
							var posX = ($('#'+ selectedItem).width()/zoomRatio - $('#'+ selectedItem).width())/2;
							var posY = ($('#'+ selectedItem).height()/zoomRatio - $('#'+ selectedItem).height())/2;
							$('#'+ selectedItem).css({width: ($('#'+ selectedItem).width()/zoomRatio), height: ($('#'+ selectedItem).height()/zoomRatio), top: $('#'+ selectedItem).offset.top + posY, left: $('#'+ selectedItem).offset.left + posX});
							console.log('Down');
						} else if(e.originalEvent.detail < 0){
							//increase size of the image
							var posX = ($('#'+ selectedItem).width()*zoomRatio - $('#'+ selectedItem).width())/2;
							var posY = ($('#'+ selectedItem).height()*zoomRatio - $('#'+ selectedItem).height())/2;
							$('#'+ selectedItem).css({width: ($('#'+ selectedItem).width()*zoomRatio), height: ($('#'+ selectedItem).height()*zoomRatio), top: $('#'+ selectedItem).offset.top + posY, left: $('#'+ selectedItem).offset.left + posX});
							console.log('Up');
						}
					  } else if (typeof e.originalEvent.wheelDelta == 'number') {
						if(e.originalEvent.wheelDelta < 0) {
							//reduce size of the image
							var posX = ($('#'+ selectedItem).width()/zoomRatio - $('#'+ selectedItem).width())/2;
							var posY = ($('#'+ selectedItem).height()/zoomRatio - $('#'+ selectedItem).height())/2;
							$('#'+ selectedItem).css({width: ($('#'+ selectedItem).width()/zoomRatio), height: ($('#'+ selectedItem).height()/zoomRatio), top: $('#'+ selectedItem).offset.top + posY, left: $('#'+ selectedItem).offset.left + posX});
							console.log('Down');
						} else if(e.originalEvent.wheelDelta > 0) {
							//increase size of the image
							var posX = ($('#'+ selectedItem).width()*zoomRatio - $('#'+ selectedItem).width())/2;
							var posY = ($('#'+ selectedItem).height()*zoomRatio - $('#'+ selectedItem).height())/2;
							$('#'+ selectedItem).css({width: ($('#'+ selectedItem).width()*zoomRatio), height: ($('#'+ selectedItem).height()*zoomRatio), top: $('#'+ selectedItem).offset.top + posY, left: $('#'+ selectedItem).offset.left + posX});
							console.log('Up');
						}
					  }
					  event.preventDefault();
				}
			});
	}, 20);

	//Set the selected item on the right list and put the image associate first
	function setSelectedItem(objId){
		console.log("init: selected Image");
		if(objId && typeof obj != 'undefined'){
			console.log("if passed");
			if(selectedItem != document.getElementById('list_'+objId).parentElement){
				var allListObj = document.getElementsByClassName('container');
				for (var i = 0; i < allListObj.length; i++) {
					allListObj[i].classList.remove('selectedItem')
				}
				selectedItem = document.getElementById('list_'+objId).parentElement;
				selectedItem.classList.add('selectedItem');
				var allListImg = document.getElementById('playingGround').children;
				for (var i = 0; i < allListImg.length; i++) {
					allListImg[i].classList.remove('selectedImage');
				}
				document.getElementById(objId).classList.add('selectedImage');
			}
		}else{
			console.log(objId);
			selectedItem = "";
		}
	}

	//Delete the image and the list record associate
	//Need to resolve error with after deleting element like not able to read src but not important
	function deleteImage(obj){
		setSelectedItem("");
		var image = document.getElementById(obj.attr('id').replace('del_', ''));
		if(image.src){
			image.remove();
		}
		var listEntry = document.getElementById(obj.attr('id'));
		if(listEntry.src){
			listEntry.parentNode.parentNode.remove();
		}
	}
</script>
