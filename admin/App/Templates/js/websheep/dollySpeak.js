var wsAssistent = new Object();
wsAssistent = {
	listen:false,
	functions:{
		logout:function(){
			$.ajax({
				type: "POST",
				async: true,
				url: "/admin/App/Modulos/login/functions.php",
				data:{'function':'logout'},
				beforeSend: function() {
					$("#iniciarsessao").hide('fast')
					$("#iniciarsessao_disabled").show('fast')
					setTimeout(function(){confirma({width: "auto", conteudo: "...<div class=\'preloaderupdate\' style=\'left: 50%;margin-left: -15px; position: absolute;width: 30px;height: 18px;top: 53px;background-image:url(\"/admin/App/Templates/img/websheep/loader_thumb.gif\");background-repeat:no-repeat;background-position: top center;\'></div>", drag: false, bot1: 0, bot2: 0 })},1000)
				}
			}).done(function(e){
				document.cookie.split(";").forEach(function(c) { 
					document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/");
				}); 
				window.location.reload();
			})
		}

	},
	exec:function(text){
		var breakParent = false;
		var valIndex;
		var index;
		$.ajax({
			url			: "/admin/App/Core/ws-speak.php",
			type 		: 'POST',
			data 		: {search:text},
			success: function(data, status, jqXHR) {
				console.log(data)
				eval(data)
			}
		})
	},
	dragDolly:function(){
			window.dollyposition = $( "#dolly" ).position();
			$( "#dolly" ).draggable({stop: function() {window.dollyposition = $( "#dolly" ).position()}});

	},
	init:function(){
     	window.SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition || null;

		if (window.SpeechRecognition === null) {
	        	ws.alert.top({mensagem:"Falha ao iniciar módulo de voz,<a id='TopAlertAtivarVoz'><b>Tentar novamente</b></a>", clickclose:true, height:20, timer:7000, posFn:function(){ $("#TopAlertAtivarVoz").bind("click tap press",function(){wsAssistent.init();}) },styleText:"color:#FFF",background:"#d60000",bottomColor:"#000"});
	    }else{
	    	var recognizer						= new window.SpeechRecognition();
				recognizer.continuous			= true;
				recognizer.interimResults		= true;

				recognizer.onstart = function() {
	        		console.log("Dolly started");
				};
				recognizer.onresult = function(event){
					for (var i = event.resultIndex; i < event.results.length; i++) {
						var frase = event.results[i][0].transcript.trim().toLowerCase();
						console.log(frase);
						responsiveVoice.cancel();
						 if(event.results[i].isFinal){
						 	wsAssistent.exec(frase);
						 }
					}
				};
				recognizer.onerror = function(e) {
				  console.log("Error"+e);
				};

				recognizer.onend = function() {
					console.log("Speech recognition ended");
					recognizer.start();
				};
	        	recognizer.start();
	        	wsAssistent.dragDolly();
				wsAssistent.speak("Diga, OK DOLLY.",true,true);
        }
	},
	speak:function(data="",centralize,back) {
		if(centralize==true){
			wsAssistent.dollyCentralize();
		}
		responsiveVoice.speak(data, "Brazilian Portuguese Female",{rate: 1.2,pitch:1,volume:5,onstart:function(){
		},onend:function(){
			if(back==true){
				wsAssistent.dollyBack();
			}
		}});
	},
	getWiki:function(keyWork){
		var url = "https://pt.wikipedia.org/w/api.php?action=opensearch&search="+encodeURI(keyWork)+"&format=json&callback=?"; 
		$.ajax({
		url: url,
		type: 'GET',
		contentType: "application/json; charset=utf-8",
		async: false,
		dataType: "json",
		success: function(data, status, jqXHR) {
			 var title = data[1][0];
			 var text = data[2][0]
			 wsAssistent.speak(title+". "+text);
			}
		})
	},

	dollyCentralize:function(fn=null){
			var w = ($(window).width()/2) - ($( "#dolly" ).width()/2);
			var h = ($(window).height()/2)- ($( "#dolly" ).height()/2);
			$( "#dolly" ).animate({left:w,top: h,},300, function() {if(fn!=null){eval(fn)}})
	},
	dollyBack:function(fn=null){
		$( "#dolly" ).animate({left:window.dollyposition.left,top: window.dollyposition.top},300, function() {if(fn!=null){eval(fn)}})
	},
	random_array: function (array){
		return  array[Math.floor(Math.random()*array.length)];
	},
	in_array: function (array,value){
		 var i;
		 if(typeof array=="array"){
			 for (i=0; i < array.length; i++){
				 if (array[i] == value){
					 return true;
				 }
			 }
		 }else{
		 	for(var a in array) { 
		 		if (array[a] == value){
					 return true;
				 }
		 	}
		 }
		 return false;
	}


}

wsAssistent.init();












