/*! 

	WebSheep Functions v0.3.3
	Development: WebSheep Tecnologia Integrada
	Compressed: {dataMinifiq}

	##################### Insert the code right after the <body>  ##########################

	<div id="ws-root"></div>
	<script>
		(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return;
			js = d.createElement(s); js.id = id;
			js.src = "/admin/App/Templates/js/websheep/functionsws.min.js";
			fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'websheep-functions'));
	</script>
	
	#######################################################################################

*/
var ws = new Object();
ws = {
	info: {
		version: "0.3.4",
		compatible: "5.7+",
		creator: "WebSheep Tecnology"
	},
	init: function() {
		if(ws.verify.jquery() == false && ws.insert.js("/admin/App/Vendor/jquery/2.2.0/jquery.min.js", "jQuery", true) == true) {
			ws.log.info("Jquery 2.2.0 instalado");
		}
		if(document.querySelectorAll("#style_ws").length == 0 && ws.insert.css("/admin/App/Templates/css/websheep/funcionalidades.css", "style_ws", "All") == true) {
			ws.log.info("Style Importado");
		}
	},
	get: {},
	set: {
		obj: function(newVal, valor) {
			ws.get.obj[newVal] = valor;
		}
	},
	verify: {
		jquery: function() {
			if(!window.jQuery && typeof jQuery === 'undefined') {
				console.error("ERRO: Jquery necessário");
				return false;
			} else {
				console.info("Jquery instalado: " + window.$.prototype.jquery.split(" ")[0]);
				return true;
			}
		}
	},
	js: function(id = null) {},
	css: function(id) {},
	audio:{
		speak: function(data="") {
				var w = ($(window).width()/2) - ($( "#dolly" ).width()/2);
				var h = ($(window).height()/2)- ($( "#dolly" ).height()/2);
				$( "#dolly" ).animate({left:w,top: h,},300, function() {
					responsiveVoice.speak(data, "Brazilian Portuguese Female",{rate: 1.2,pitch:1,volume:5,onstart:function(){

						},onend:function(){
							$( "#dolly" ).animate({left:window.dollyposition.left,top: window.dollyposition.top},300)
						}
					});
				})
		}
	},
	form: {
		sendLeads: function(token = null) {
			return {
				form: null,
				token: token,
				thisBefore: null,
				thisajaxSend: null,
				thissuccess: null,
				thiserror: null,
				thiscomplete: null,
				thisUploadProgress: null,
				thisdone: null,
				verifyFn: null,
				thisCaptcha: null,
				errCa: null,
				setForm: function(data) {
					this.form = data;
					return this;
				},
				errorCaptcha: function(data) {
					this.errCa = data;
					return this;
				},
				setCaptcha: function(data) {
					if(document.querySelectorAll(data).length) {
						this.thisCaptcha = data;
					} else {
						ws.log.error("ws->form->sendLeads->setCaptcha : input inválido ou inexistente");
						return false;
					}
					return this;
				},
				verify: function(data) {
					this.verifyFn = data;
					return this;
				},
				setToken: function(data) {
					this.token = data;
					return this;
				},
				beforeSend: function(data) {
					this.thisBefore = data;
					return this;
				},
				ajaxSend: function(data) {
					this.thisajaxSend = data;
					return this;
				},
				success: function(data) {
					this.thissuccess = data;
					return this;
				},
				error: function(data) {
					this.thiserror = data;
					return this;
				},
				complete: function(data) {
					this.thiscomplete = data;
					return this;
				},
				uploadProgress: function(data) {
					this.thisUploadProgress = data;
					return this;
				},
				go: function(data) {
					var escope_this = this;
					if(!ws.exists.dom(this.form)) {
						ws.log.error("ws->form->sendLeads : Formulário inválido ou inexistente");
						return false;
					}
					if(this.token == null) {
						ws.log.error("ws->form->sendLeads->Token : Valor 'null' é invalido");
						return false;
					}
					if(escope_this.thiserror !== null && typeof escope_this.thiserror !== "function") {
						ws.log.error("ws->form->sendLeads->error : Valor incorreto.Por favor inserir uma função 'function(){}'");
						return false;
					}
					if(escope_this.thisBefore !== null && typeof escope_this.thisBefore !== "function") {
						ws.log.error("ws->form->sendLeads->beforeSend : Valor incorreto.Por favor inserir uma função 'function(){}'");
						return false;
					}
					if(escope_this.thisUploadProgress !== null && typeof escope_this.thisUploadProgress !== "function") {
						ws.log.error("ws->form->sendLeads->uploadProgress : Valor incorreto.Por favor inserir uma função 'function(){}'");
						return false;
					}
					if(escope_this.thiscomplete !== null && typeof escope_this.thiscomplete !== "function") {
						ws.log.error("ws->form->sendLeads->complete : Valor incorreto.Por favor inserir uma função 'function(){}'");
						return false;
					}

						function verifyCaptcha(envia) {
							var http = new XMLHttpRequest();
							var url = "/ws-leads/";
							var params = "typeSend=captcha&keyCode=" + document.querySelectorAll(escope_this.thisCaptcha)[0].value;
							http.open("POST", url, true);
							http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
							http.onreadystatechange = function() {
								if(http.readyState == 4) {
									if(http.status == 200) {
										//if (typeof envia == "function") {envia(http);}
									}
								}
							}
							http.send(params);
						}






					function veryFyCaptcha(){
						$(escope_this.form).unbind("submit").bind("submit",function(e){
							e.preventDefault();
							var http = new XMLHttpRequest();
							var url = "/ws-leads/";
							var params = "typeSend=captcha&keyCode=" + document.querySelectorAll(escope_this.thisCaptcha)[0].value;
							http.open("POST", url, true);
							http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
							http.send(params);
							http.onreadystatechange = function() {
								if(http.readyState == 4) {
									if(http.status == 200) {
										if(http.responseText==0 || http.responseText=="0"){
											escope_this.errCa()

											if(escope_this.thisCaptcha != null) {veryFyCaptcha();}else{goForm(false);}

										}else{
											goForm(true);
										}
									}
								}
							}
							return false;
						})
					}

					function goForm(direct=false){
						$(escope_this.form).unbind("submit").ajaxForm({
							type: "POST",
							forceSync:true,
							error: function(error) {
								if(escope_this.thiserror !== null) {
									escope_this.thiserror(error)
									if(escope_this.thisCaptcha != null) {veryFyCaptcha();}else{goForm(false);}
								}
							},
							beforeSerialize: function($form, options) { 
									 if(escope_this.verifyFn != null) {
									 	var response = escope_this.verifyFn();
									 	if(escope_this.thiserror != null && response != true) {
									 		escope_this.thiserror(response);
									 		if(escope_this.thisCaptcha != null) {veryFyCaptcha();}else{goForm(false);}
									 		return false;
									 	}
									 }
							},
							beforeSubmit: function(xhr) {},
							beforeSend: function(xhr) {
								if(escope_this.thisBefore !== null) {
									escope_this.thisBefore(xhr)
									return false;
								}										
							},
							uploadProgress: function(event, position, total, percentComplete, myForm) {
								if(escope_this.thisUploadProgress !== null) {
									escope_this.thisUploadProgress(percentComplete)
								}
							},
							complete: function(e) {
								if(escope_this.thiscomplete !== null) {
									escope_this.thiscomplete(e);
									if(escope_this.thisCaptcha != null) {veryFyCaptcha();}else{goForm(false);}
								}
							}
						})
						if(direct==true){$(escope_this.form).submit();}
					} 
					if(window.ajaxFormInclude != true) {
						ws.load.script({
							file: '/admin/modulos/_leads_/AjaxForm.min.js',
							return: function() {
								if(escope_this.thisCaptcha != null) {veryFyCaptcha();}else{goForm(false);}
							}
						})
					}else{
						if(escope_this.thisCaptcha != null) {veryFyCaptcha();}else{goForm(false);}
					}
				},
			};
		},
		input: function(input) {
			if(typeof input !== "string") {
				ws.log.error("ws->form->input : entrada inválida, utilize strings como selectores '#' ou '.'");
				return false;
			}
			if(!ws.exists.dom(input)) {
				ws.log.error("ws->form->input : input inválido ou inexistente");
				return false;
			}
			return {
				thisinput: input,
				div_result: null,
				div_no_result: null,
				div_combo_result: null,
				is_email: function() {
					var regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
					if(document.querySelectorAll(this.thisinput).length) {
						this.thisinput = document.querySelectorAll(this.thisinput)[0].value;
					}
					return regex.test(this.thisinput);
				},
				is_blank: function() {
					if(document.querySelectorAll(this.thisinput).length) {
						this.thisinput = document.querySelectorAll(this.thisinput)[0].value;
					}
					if(this.thisinput == "") {
						return true;
					} else {
						return false;
					}
				},
				setResult: function(div_result) {
					if(div_result == "" || !ws.exists.dom(div_result)) {
						ws.log.error("ws->form->input->setResult : Div inválida ou inexistente");
						return false;
					} else {
						this.div_result = div_result;
						return this;
					}
				},
				setNoResult: function(div_no_result) {
					if(div_no_result == "" || !ws.exists.dom(div_no_result)) {
						ws.log.error("ws->form->input->setNoResult : Div inválida ou inexistente");
						return false;
					} else {
						this.div_no_result = div_no_result;
						return this;
					}
				},
				setComboResult: function(div_combo_result) {
					if(div_combo_result == "" || !ws.exists.dom(div_combo_result)) {
						ws.log.error("ws->form->input->setComboResult: Div inválida ou inexistente");
						return false;
					} else {
						this.div_combo_result = div_combo_result;
						return this;
					}
				}
			}
		}
	},
	cookie: {
		set: function(name, value, days) {
			if(days) {
				var date = new Date();
				date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
				var expires = "; expires=" + date.toGMTString();
			} else var expires = "";
			document.cookie = name + "=" + value + expires + "; path=/";
		},
		get: function(name) {
			var nameEQ = name + "=";
			var ca = document.cookie.split(';');
			for(var i = 0; i < ca.length; i++) {
				var c = ca[i];
				while(c.charAt(0) == ' ') c = c.substring(1, c.length);
				if(c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
			}
			return null;
		},
		erase: function(name) {
			ws.cookie.set(name, "", -1);
		}
	},
	insert: {
		css: function(documento, id, Media) {
			if(document.querySelectorAll('#' + id).length) {
				document.querySelectorAll('#' + id).remove()
			}
			var script = document.createElement('link');
			script.id = id;
			script.rel = 'stylesheet';
			script.type = 'text/css';
			script.media = Media;
			script.href = documento;
			var s = document.getElementsByTagName('link')[0];
			s.parentNode.insertBefore(script, s);
			return true;
		},
		js: function(documento, id = null, reload = false) {
			if(id == null) {
				ws.log.error("ws.insert.js: faltou id");
				return false;
			}
			if(!reload) {
				reload = false;
			}
			if(!document.querySelectorAll('script#' + id).length) {
				ws.log.info("Adicionando arquivo .JS: \n  #" + id + " : " + documento)
				var script = document.createElement('script');
				script.id = id;
				script.type = 'text/javascript';
				script.src = documento;
				var s = document.getElementsByTagName('script')[0];
				s.parentNode.insertBefore(script, s);
			} else if(document.querySelectorAll('script#' + id).length && reload == true) {
				ws.log.info("Substituindo arquivo .JS: \n  #" + id + " : " + documento)
				$('script#' + id).remove();
				var script = document.createElement('script');
				script.id = id;
				script.type = 'text/javascript';
				script.src = documento;
				var s = document.getElementsByTagName('script')[0];
				s.parentNode.insertBefore(script, s);
			}
			return true;
		}
	},
	mouse: function(input) {
		return {
			cursor: "default",
			target: "*",
			setTarget: function(target = null) {
				this.target = target;
				return this;
			},
			disableContextMenu: function(action) {
				ws.verify.jquery();
				$(this.target).bind("contextmenu", function(event) {
					event.preventDefault();
					action();
				});
				return this;
			},
			setCursor: function(cursor = "*") {
				ws.verify.jquery();
				if(cursor == "*") {
					ws.log.warn("Não foi setado o target. Por default está como '*' ")
				}
				$(this.target).css({
					'cursor': cursor
				});
				return this;
			},
			disableDrop: function(action) {
				ws.verify.jquery();
				$(this.target).on({
					dragover: function(e) {
						e.preventDefault();
						action();
						return false;
					},
					drop: function(e) {
						e.preventDefault();
						action();
						return false;
					}
				});
			}
		}
	},
	encode: {
		utf8: function(e) {
			e = e.replace(/\r\n/g, "\n");
			var t = "";
			for(var n = 0; n < e.length; n++) {
				var r = e.charCodeAt(n);
				if(r < 128) {
					t += String.fromCharCode(r)
				} else if(r > 127 && r < 2048) {
					t += String.fromCharCode(r >> 6 | 192);
					t += String.fromCharCode(r & 63 | 128)
				} else {
					t += String.fromCharCode(r >> 12 | 224);
					t += String.fromCharCode(r >> 6 & 63 | 128);
					t += String.fromCharCode(r & 63 | 128)
				}
			}
			return t;
		},
		base64: function(e) {
			var _keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
			var t = "";
			var n, r, i, s, o, u, a;
			var f = 0;
			e = ws.encode.utf8(e);
			while(f < e.length) {
				n = e.charCodeAt(f++);
				r = e.charCodeAt(f++);
				i = e.charCodeAt(f++);
				s = n >> 2;
				o = (n & 3) << 4 | r >> 4;
				u = (r & 15) << 2 | i >> 6;
				a = i & 63;
				if(isNaN(r)) {
					u = a = 64
				} else if(isNaN(i)) {
					a = 64
				}
				t = t + _keyStr.charAt(s) + _keyStr.charAt(o) + _keyStr.charAt(u) + _keyStr.charAt(a)
			}
			return t;
		}
	},
	decode: {
		utf8: function(e) {
			var t = "";
			var n = 0;
			var r = c1 = c2 = 0;
			while(n < e.length) {
				r = e.charCodeAt(n);
				if(r < 128) {
					t += String.fromCharCode(r);
					n++
				} else if(r > 191 && r < 224) {
					c2 = e.charCodeAt(n + 1);
					t += String.fromCharCode((r & 31) << 6 | c2 & 63);
					n += 2
				} else {
					c2 = e.charCodeAt(n + 1);
					c3 = e.charCodeAt(n + 2);
					t += String.fromCharCode((r & 15) << 12 | (c2 & 63) << 6 | c3 & 63);
					n += 3
				}
			}
			return t;
		},
		base64: function(e) {
			var _keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
			var t = "";
			var n, r, i;
			var s, o, u, a;
			var f = 0;
			e = e.replace(/[^A-Za-z0-9\+\/\=]/g, "");
			while(f < e.length) {
				s = _keyStr.indexOf(e.charAt(f++));
				o = _keyStr.indexOf(e.charAt(f++));
				u = _keyStr.indexOf(e.charAt(f++));
				a = _keyStr.indexOf(e.charAt(f++));
				n = s << 2 | o >> 4;
				r = (o & 15) << 4 | u >> 2;
				i = (u & 3) << 6 | a;
				t = t + String.fromCharCode(n);
				if(u != 64) {
					t = t + String.fromCharCode(r)
				}
				if(a != 64) {
					t = t + String.fromCharCode(i)
				}
			}
			t = ws.decode.utf8(t);
			return t;
		}
	},
	alert: {
		top: function(opcoes) {
			ws.verify.jquery();
			var options = $.extend({
				mensagem	: "Mensagem padrão",
				clickclose	: true,
				height		: 20,
				position: "fixed",
				botClose: null,
				onClose: function() {},
				posFn: function() {},
				timeoutFn: function() {},
				timer: 3000,
				type: null,
				styleText: null,
				classText: null,
				color: "#E04E1F",
				background: "#F3DB7A",
				bottomColor: "#F5C814",
			}, opcoes);

			if(!ws.exists.dom("#avisoTopo")){$("body").prepend('<div id="avisoTopo"></div>');}

			if(options.clickclose == true) {
				$('#avisoTopo').unbind("click").click(function() {
					$(this).animate({
						height: 0,
						"padding": 0
					}, 200, 'linear');
					options.onClose();
				})
			}
			clearTimeout(window.recolheTopAlert);
			$('#avisoTopo').animate({
				height: 0,
				'padding': 0
			}, 200, 'linear', function() {
				if(options.type == 1) {
					options.color = "#E04E1F";
					options.background = "#F3DB7A";
					options.bottomColor = "#F5C814";
				}
				if(options.type == 2) {
					options.color = "#FFF";
					options.background = "#D4250D";
					options.bottomColor = "#990600";
				}
				if(options.type == 3) {
					options.color = "#FFF";
					options.background = "#85BE47";
					options.bottomColor = "#439900";
				}
				if(options.type == 4) {
					options.color = "#FFF";
					options.background = "#61A8D8";
					options.bottomColor = "#003D99";
				}
				$('#avisoTopo').css({
					"top": 0,
					"lef": 0,
					"width": "100%",
					"zIndex": 1000,
					"position": options.position,
					"background": options.background,
					"color": options.color,
					"border-bottom-color": options.bottomColor,
					"overflow": "hidden"
				});
				if(options.styleText == null && options.classText == null) {
					$('#avisoTopo').html(options.mensagem);
				} else {
					if(options.styleText == null) {
						options.styleText = "";
					} else {
						options.styleText = options.styleText.split('"').join('\"');
					}
					if(options.classText == null) {
						options.classText = "";
					} else {
						options.classText = options.classText.split('"').join('\"');
					}
					$('#avisoTopo').html("<div class=\"" + options.classText + "\" style=\"" + options.styleText + "\">" + options.mensagem + "</div>");
				}
				if(options.botClose != null) {
					$(options.botClose).unbind("click").click(function() {
						$("#avisoTopo").animate({
							height: 0,
							"padding": 0
						}, 200, 'linear');
						options.onClose()
					})
				}
				options.posFn();
				$('#avisoTopo').animate({
					height: options.height,
					"padding": 10
				}, 200, 'linear');
				window.recolheTopAlert = setTimeout(function() {
					$('#avisoTopo').animate({
						height: 0,
						"padding": 0
					}, 200, 'linear');
					options.timeoutFn();
				}, options.timer);
			});
		}
	},
	plugin: {
		contents: {}
	},
	exists: {
		file: function(opcoes) {
			ws.verify.jQuery();
			var options = $.extend({
				file: null,
				success: function(e) {},
				error: function(e) {}
			}, opcoes);
			$.ajax({
				url: options.file,
				type: 'HEAD',
				success: function(e) {
					options.success(e)
				},
				error: function(e) {
					options.error(e)
				}
			});
		},
		dom: function(selector) {
			if(typeof selector !== "string") {
				ws.log.error("Entrada inválida, utilize strings como selectores '#' ou '.'");
				return false;
			}
			if(document.querySelectorAll(selector).length) {
				return true;
			} else {
				return false;
			}
		}
	},
	load: {
		json: function(opcoes) {
			ws.verify.jquery();
			var options = $.extend({
				file: null,
				return: function(d) {}
			}, opcoes);
			$.getJSON(options.file, function(data) {
				options.return(data)
			});
		},
		script: function(opcoes) {
			ws.verify.jquery();
			var options = $.extend({
				file: null,
				return: function(d, t, j) {}
			}, opcoes);
			$.getScript(options.file, function(data, textStatus, jqxhr) {
				options.return(data, textStatus, jqxhr)
			});
		}
	},
	searchList: function() {
		/*					ws.verify.jquery();
							var options = $.extend({
								input 		: $(""),
								container 	: $(""),
								element 	: $(""),
								noResult 	: $(""),
							}, opcoes)


					  $(options.input).keyup(function(){
						var searchFTP = options.container;
						var texto 		= $(this).val().toUpperCase();
						var textSearch  = $(searchFTP).text().split("	").join("").split("\n").join("").split(" ").join("\n").toUpperCase()
						var resultGeral = textSearch.indexOf(texto);
						
						if(resultGeral > 0 && texto!="") {
							$(options.element).each(function(){
								var thistext 	= $(this).text().split("	").join("").split("\n").join("").split(" ").join("\n").toUpperCase();
								var resultado 	= thistext.indexOf(texto);
								if(resultado <= 0) {
								$(options.noResult).hide();
								$(this).hide();
								}else {
								$(this).show();
								}else{

								}
							}); 
						}else{
							$(options.noResult).show();
							$(searchFTP+" li").show();
						}              
					  });
		*/
	},
	is_on_screen: function(element) {
		var win = $(window);
		var viewport = {
			top: win.scrollTop(),
			left: win.scrollLeft()
		};
		viewport.right = viewport.left + win.width();
		viewport.bottom = viewport.top + win.height();
		var bounds = element.offset();
		bounds.right = bounds.left + element.outerWidth();
		bounds.bottom = bounds.top + element.outerHeight();
		return(!(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom));
	},
	popup: function(url, w, h, s = "yes") {
		var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
		var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;
		var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
		var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;
		var left = ((width / 2) - (w / 2)) + dualScreenLeft;
		var top = ((height / 2) - (h / 2)) + dualScreenTop;
		var newWindow = window.open(url, null, 'scrollbars=' + s + ', width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
	},
	getUrlVars: function(url) {
		url = url.replace(/&amp;/g, "ws_amp");
		var vars = {};
		var parts = url.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
			vars[key] = value.replace(/ws_amp/g, "&amp;");
		});
		return vars;
	},
	downloadFile: function(opcoes) {
		var options = $.extend({
			typeSend: "GET",
			file: null,
			newfile: null,
			abort: function(e) {},
			error: function(e) {},
			load: function(e) {},
			finish: function(e) {},
			progress: function(e) {}
		}, opcoes);
		if(options.file == null) {
			alert("Por favor, dê um nome ao arquivo...");
			return false
		}
		$.ajax({
			xhr: function() {
				var xhr = new window.XMLHttpRequest();
				xhr.responseType = "arraybuffer";
				xhr.addEventListener("abort", function() {
					options.abort()
				})
				xhr.addEventListener("error", function() {
					options.error()
				})
				xhr.addEventListener("loadend", function() {
					options.finish()
				})
				xhr.addEventListener("load", function() {
					console.log('load')
						//############################################################################################
						//##################################################################### capta data-type e nome
						//############################################################################################
					var file_type = xhr.getResponseHeader('Content-Type');
					var disposition = xhr.getResponseHeader('Content-Disposition');
					if(disposition && disposition.indexOf('attachment') !== -1) {
						var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
						var matches = filenameRegex.exec(disposition);
						if(matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
					} else {
						filename = options.file.replace(/^.*[\\\/]/, '')
					}
					window.BlobBuilder = window.BlobBuilder || window.WebKitBlobBuilder || window.MozBlobBuilder || window.MSBlobBuilder;
					window.URL = window.URL || window.webkitURL;
					var arrayBufferView = new Uint8Array(this.response);
					var blob = new Blob([arrayBufferView], {
						type: file_type
					});
					var urlCreator = window.URL || window.BlobBuilder;
					var imageUrl = urlCreator.createObjectURL(blob);
					var a = document.createElement("a");
					document.body.appendChild(a);
					a.href = imageUrl;
					if(options.newfile != null) {
						a.download = options.newfile;
					} else {
						a.download = filename;
					}
					a.click();
					options.load()
				}, false);
				xhr.addEventListener("progress", function(evt) {
					if(evt.lengthComputable) {
						var percentComplete = Math.floor((evt.loaded / evt.total) * 100);
						console.log(percentComplete)
						options.progress(percentComplete)
					}
				}, false);
				return xhr;
			},
			type: options.typeSend,
			url: options.file
		});
	},
	accordion: function(opcoes) {
		if(typeof opcoes === 'string') {
			var options = $.extend({
				cabecalho: opcoes,
				initOpen: function(e) {},
				initClose: function(e) {},
				finishOpen: function(e) {},
				finishClose: function(e) {},
			}, null);
		} else {
			var options = $.extend({
				cabecalho: "",
				initOpen: function(e) {},
				initClose: function(e) {},
				finishOpen: function(e) {},
				finishClose: function(e) {},
			}, opcoes);
		}
		$(options.cabecalho).next().slideUp("slow");
		$(options.cabecalho).click(function() {
			if($(this).next().hasClass('SanfonaOpen')) {
				$(this).removeClass('FolderOpen');
				options.initClose();
				$(this).next().slideUp("slow", function() {
					options.finishClose()
				}).removeClass('SanfonaOpen');
			} else {
				options.initOpen()
				$(this).addClass('FolderOpen');
				$(this).next().slideDown("slow", function() {
					options.finishOpen()
				}).addClass('SanfonaOpen');
			};
		});
	},
	string: {
		formatHTML: function(html) {
			function getIndent(level) {
				var result = '',
					i = level * 4;
				if(level < 0) {
					throw "Level is below 0";
				}
				while(i--) {
					result += ' ';
				}
				return result;
			}
			html = html.trim();
			var result = '',
				indentLevel = 0,
				tokens = html.split(/</);
			for(var i = 0, l = tokens.length; i < l; i++) {
				var parts = tokens[i].split(/>/);
				if(parts.length === 2) {
					if(tokens[i][0] === '/') {
						indentLevel--;
					}
					result += getIndent(indentLevel);
					if(tokens[i][0] !== '/') {
						indentLevel++;
					}
					if(i > 0) {
						result += '<';
					}
					result += parts[0].trim() + ">\n";
					if(parts[1].trim() !== '') {
						result += getIndent(indentLevel) + parts[1].trim().replace(/\s+/g, ' ') + "\n";
					}
					if(parts[0].match(/^(img|hr|br)/)) {
						indentLevel--;
					}
				} else {
					result += getIndent(indentLevel) + parts[0] + "\n";
				}
			}
			return result;
		},
		slashes: {
			add: function(str) {
				str = str.replace(/\\/g, '\\\\');
				str = str.replace(/\'/g, '\\\'');
				str = str.replace(/\"/g, '\\"');
				str = str.replace(/\0/g, '\\0');
				return str;
			},
			remove: function(str) {
				str = str.replace(/\\'/g, '\'');
				str = str.replace(/\\"/g, '"');
				str = str.replace(/\\0/g, '\0');
				str = str.replace(/\\\\/g, '\\');
				return str;
			}
		}
	},
	confirm: function(opcoes) {
		ws.verify.jquery();
		var options = $.extend({
			conteudo: "Mensagem padrão",
			width: 500,
			height: 'auto',
			mleft: 0,
			mtop: 0,
			posFn: function() {},
			Init: function() {},
			posClose: function() {},
			bots: [
				// {
				// 		label		: "Aceitar",
				// 		class		: "",
				// 		style 		: "",
				// 		css 		: {"color":"#000"},
				//		ErrorCheck	: function() {},
				//		Check 		: function() {return true},
				// 		action		: function(){console.log("1111111")},
				// }
			],
			bot1: false,
			cancel: false,
			idModal: "ws_confirm",
			divScroll: "body",
			divBlur: "#menu_tools,#container,#header",
			drag: true,
			botclose: false,
			newFun: function() {},
			onCancel: function() {},
			onClose: function() {},
			Callback: function() {},
			ErrorCheck: function() {},
			Check: function() {
				return true
			}
		}, opcoes)
		options.Init();
		var BotClose = ""
		var ArryBotoes = "";
		var largBot = (100 / options.bots.length);
		var marBots = (options.bots.length * 5);
		var index_highest = 1000;
		$(".ws_popup_confirm").each(function() {
			var index_current = parseInt($(this).css("zIndex"), 10);
			if(index_current >= index_highest) {
				index_highest = index_current + 1;
			}
		});
		// MONTA OS BOTÕES DO ALERTA
		$.each(options.bots, function(index, value) {
			var id = "botConfirma_" + index + "_" + index_highest;
			if(!value.class || value.class=="undefined"){value.class="";}
			if(!value.style || value.style=="undefined"){value.style="";}
			ArryBotoes += "<div id='" + id + "' class='botao " + value.class + "' style='width:calc(" + largBot + "% - 6px);margin: 0 2px;float: left;position: relative;padding: 10px 0;" + value.style + "'>" + value.label + "</div>\n";
		});
		// SE TIVER BOTÕES:
		if(options.bots.length > 0) {
			options.bot1 = false;
			options.cancel = false;
			var Botoes = "<div id='bottons' class='bottons'>" + ArryBotoes + "</div>";
		} else {
			if(options.bot1 == false) {
				var botao1 = ""
			} else {
				var botao1 = "<div id='aceitar' class='botao aceitar'>" + options.bot1 + "</div>"
			}
			if(options.cancel == false) {
				var botao2 = ""
			} else {
				var botao2 = "<div id='recusar' class='recusar'>" + options.cancel + "</div>"
			}
			if(options.botclose == false) {
				var BotClose = ""
			} else {
				var BotClose = "<div id='close' class='botao close' >x</div>"
			}
			if(options.bot1 == false && options.cancel == false) {
				var Botoes = "";
			} else {
				var Botoes = "<div id='bottons' class='bottons'>" + botao1 + botao2 + "</div>";
			}
			if(options.bot1 == false && options.cancel == false && options.botclose == false) {
				var BotClose = "<div id='close' class='botao close' >x</div>";
			}
		}


		if($.type(options.idModal) === "string") {
			if(options.idModal.indexOf("#") == 0) {
				options.idModal = options.idModal.slice(1);
			} else {
				options.idModal = options.idModal;
			}
		} else {
			return false;
		}
		$("#" + options.idModal).remove();
		$('body').prepend("<div id='" + options.idModal + "' class='ws_popup_confirm' style='opacity:1;width:100%;height:100%;z-index:" + index_highest + "!important'><div class='body'>" + BotClose + "<div class='ws_confirm_conteudo w1'>" + options.conteudo + "</div>" + Botoes + "</div></div>");
		$("#" + options.idModal + " .body").css({
			"width": options.width,
			"height": options.height
		});
		if(options.cancel == false) {
			$("#" + options.idModal + " .aceitar").css({
				"left": '50%',
				"transform": "translateX(-50%)"
			});
		}
		$(options.divScroll).addClass("scrollhidden");
		$("#" + options.idModal).fadeIn('fast', function() {});
		var closed = false
		options.posFn();
		$(options.divBlur).addClass("blur");
		$("#" + options.idModal + " .body").css({
			"cursor": 'default'
		})

		function closeAlert() {
			closed = true;
			$(options.divScroll).removeClass("scrollhidden");
			$(options.divBlur).removeClass("blur");
			$("#" + options.idModal).animate({ opacity: 0 }, 200, 'linear', function() { $("#" + options.idModal).remove() });
			options.posClose();
		}
		if(options.bots.length > 0) {
			$.each(options.bots, function(index, value) {
				if(value.css) {
					$("#botConfirma_" + index + "_" + index_highest).css(value.css)
				}
				if(value.style) {
					var atualStyle = $("#botConfirma_" + index + "_" + index_highest).attr("style");
					$("#botConfirma_" + index + "_" + index_highest).attr("style", atualStyle+value.style);
				}
				$("#botConfirma_" + index + "_" + index_highest).unbind("click").bind("click", function() {
					if(typeof(value.Check) == 'function') {
						if(value.Check() === true) {
							value.action();
							closeAlert();
						} else {
							value.ErrorCheck();
							return false;
						}
					} else {
						value.action();
						closeAlert();
					}
				});
			});
		} else {
			$("#" + options.idModal + " .recusar").click(function() {
				options.onCancel();
				closeAlert();
			});
			$("#" + options.idModal + " .close").click(function() {
				options.onClose();
				closeAlert();
			});
			$("#" + options.idModal + " .aceitar").click(function() {
				if(options.Check() == true) {
					options.newFun();
					options.Callback();
					closeAlert();
				} else {
					options.ErrorCheck();
				}
			});
		}
	},
	getObjects: function(obj, key, val) {
		var objects = [];
		for(var i in obj) {
			if(!obj.hasOwnProperty(i)) continue;
			if(typeof obj[i] == 'object') {
				objects = objects.concat(getObjects(obj[i], key, val));
			} else if(i == key && obj[key] == val) {
				objects.push(obj);
			}
		}
		return objects;
	},
	json_convert: function(e) {
		var arrayData, objectData;
		arrayData = e.serializeArray();
		objectData = {};
		$.each(arrayData, function() {
			var value;
			if(e.value != null) {
				value = e.value;
			} else {
				value = '';
			}
			if(objectData[e.name] != null) {
				if(!objectData[e.name].push) {
					objectData[e.name] = [objectData[e.name]];
				}
				objectData[e.name].push(value);
			} else {
				objectData[e.name] = value;
			}
		});
		return objectData;
	},
	log: {
		error: function(message) {
			console.error(message);
		},
		info: function(message) {
			console.info(message);
		},
		warn: function(message) {
			console.warn(message);
		}
	}
}