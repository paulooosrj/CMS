$(document).ready(function () {
    $("#menu-websheep").unbind("click tap press").bind("click tap press", function () {
        var root = window.top;
        $("#opt-websheep").css({
            top: 30,
            left: $("#menu-websheep").offset().left + 15
        });
        if ($("#opt-websheep").is(":visible")) {
            $("#opt-websheep").hide("fast");
        } else {
            $("#opt-websheep").show("fast");
        }
    })
    $("#opt-websheep").mouseleave(function () {
        $('#opt-websheep').hide("fast");
    });
    $("#opt-websheep li").unbind("click tap press").bind("click tap press", function () {
        var root = window.top;

        var type = $(this).data("action");
        if (type == "addTool") {
            root.functions({
                funcao: "InsertCode",
                vars: "",
                patch: "App/Modulos/webmaster",
                Sucess: function (e) {
                    root.confirma({
                        conteudo: e,
                        width: 620,
                        bot1: 'Inserir Código',
                        bot2: 'Cancelar',
                        drag: 0,
                        botclose: 0,
                        Check: function () {
                            if (!root.$("#formTags input[type='radio']:checked") || root.$("#shortcodes").val() == "") {
                                return false;
                            } else {
                                return true;
                            }
                        },
                        ErrorCheck: function () {
                            alert("Preencha tipo e ferramenta desejados");
                        },
                        posFn: function () {},
                        newFun: function () {
                            root.functions({
                                funcao: "InsertCodeCampos",
                                vars: root.$("#formTags").serialize(),
                                patch: "App/Modulos/webmaster",
                                Sucess: function (e) {
                                    root.$("#codiad")[0].contentWindow.codiad.editor.insertContent(e)
                                }
                            })
                        }
                    })
                }
            })
        }
        if (type == "addPlugin") {

            root.functions({
                funcao: "loadShortCodes",
                vars: "",
                patch: "App/Modulos/webmaster",
                Sucess: function (e) {
                    root.confirma({
                        conteudo: e,
                        width: 600,
                        bot1: 'Inserir shortcode',
                        bot2: 'Cancelar',
                        drag: 0,
                        botclose: 0,
                        posFn: function () {

                        },
                        newFun: function () {
                            var pathPlug = root.$("#shortcodes").val();
                            $.ajax({
                                type: "POST",
                                url: "/admin/App/Modulos/webmaster/functions.php",
                                data: {
                                    'function': 'getShortCodesPlugin',
                                    'path': pathPlug
                                }
                            }).done(function (data) {
                                root.$("#codiad")[0].contentWindow.codiad.editor.insertContent(data)
                            });
                        }
                    })
                }
            })
        }
        if (type == "addForm") {
            root.functions({
                funcao: "InsertCodeForm",
                vars: "",
                patch: "App/Modulos/webmaster",
                Sucess: function (e) {
                    root.confirma({
                        conteudo: e,
                        width: 600,
                        bot1: 'Inserir formulário',
                        bot2: 'Cancelar',
                        drag: 0,
                        botclose: 0,
                        Check: function () {
                            if (!root.$("#formTags input[type='radio']:checked").val() || root.$("#shortcodes").val() == "") {
                                return false;
                            } else {
                                return true;
                            }
                        },
                        ErrorCheck: function () {
                            root.TopAlert({
                                mensagem: "Preencha tipo de envio desejado",
                                type: 2
                            });
                        },
                        posFn: function () {},
                        newFun: function () {
                            root.functions({
                                funcao: "InsertCodeFormCampos",
                                vars: root.$("#formTags").serialize(),
                                patch: "App/Modulos/webmaster",
                                Sucess: function (data) {
                                    root.$("#codiad")[0].contentWindow.codiad.editor.insertContent(data)
                                }
                            })
                        }
                    })
                }
            })

        }
        if (type == "addBootstrap") {
            root.confirma({
                conteudo: '<iframe id="bootstrap" src="/admin/App/Modulos/webmaster/templateBootstrap/" width="100%" height="100%"></iframe>',
                width: 'calc(100% - 100px)',
                height: 'calc(100% - 100px)',
                bot1: 'Inserir',
                bot2: 'Cancelar',
                drag: 0,
                botclose: 0,
                posFn: function () {},
                newFun: function () {
                    var $less = root.$("#bootstrap").contents().find(".html .output_container pre.mixins").text();
                    var $code = root.$("#bootstrap").contents().find(".html .output_container pre.markup").text();
                    var $insert = "<!--  LESS MIXINS --> \n " + $less + " \n <!--  END LESS MIXINS --> \n " + $code
                    if ($less != "") {
                        root.$("#codiad")[0].contentWindow.codiad.editor.insertContent($insert)
                    } else {
                        root.$("#codiad")[0].contentWindow.codiad.editor.insertContent($code)
                    }
                }
            })

        }
        if (type == "addPaginate") {
            root.functions({
                funcao: "InsertPagination",
                vars: "",
                patch: "App/Modulos/webmaster",
                Sucess: function (e) {
                    root.confirma({
                        conteudo: e,
                        width: 700,
                        height: 500,
                        bot1: 'Inserir Código',
                        bot2: 'Cancelar',
                        drag: 0,
                        botclose: 0,
                        Check: function () {
                            if (!root.$("#formTags input[type='radio']:checked") || root.$("#shortcodes").val() == "") {
                                return false;
                            } else {
                                return true;
                            }
                        },
                        ErrorCheck: function () {
                            alert("Preencha tipo e ferramenta desejados");
                        },
                        posFn: function () {
                            var root = window.top;
                            root.$.getScript('/admin/App/Vendor/Codiad/components/editor/ace-editor/ace.js', function () {
                                root.$.getScript('/admin/App/Vendor/Codiad/components/editor/ace-editor/ext-language_tools.js', function () {
                                    root.ace.config.set('basePath', '/admin/App/Vendor/Codiad/components/editor/ace-editor');
                                    root.htmEditorPagination = root.ace.edit("editorHTML");
                                    root.htmEditorPagination.setTheme("ace/theme/ambiance");
                                    root.htmEditorPagination.getSession().setMode("ace/mode/html");
                                    root.htmEditorPagination.setHighlightActiveLine(true);
                                    root.htmEditorPagination.setShowInvisibles(0);
                                    root.htmEditorPagination.getSession().setUseSoftTabs(false);
                                    root.htmEditorPagination.getSession().setUseWrapMode(true);
                                    root.$(".chosen-results,.nave_folders").perfectScrollbar({
                                        suppressScrollX: true
                                    });
                                    root.htmEditorPagination.getSession().on('change', function (e) {
                                        root.$("textarea[name='editorHTML']").val(root.htmEditorPagination.getSession().getValue())
                                    })
                                    root.htmEditorPagination.getSession().setValue(decodeURIComponent(root.$("textarea[name='editorHTML']").val()))
                                    root.htmEditorCountPage = root.ace.edit("editorCOUNT");
                                    root.htmEditorCountPage.setTheme("ace/theme/ambiance");
                                    root.htmEditorCountPage.getSession().setMode("ace/mode/html");
                                    root.htmEditorCountPage.setHighlightActiveLine(true);
                                    root.htmEditorCountPage.setShowInvisibles(0);
                                    root.htmEditorCountPage.getSession().setUseWrapMode(true);
                                    root.htmEditorCountPage.getSession().setUseSoftTabs(false);
                                    root.$(".chosen-results,.nave_folders").perfectScrollbar({
                                        suppressScrollX: true
                                    });
                                    root.htmEditorCountPage.getSession().on('change', function (e) {
                                        root.$("textarea[name='editorCOUNT']").val(root.htmEditorCountPage.getSession().getValue())
                                    })
                                    root.htmEditorCountPage.getSession().setValue(decodeURIComponent(root.$("textarea[name='editorCOUNT']").val()))
                                    root.htmEditorCountPage.setOptions({
                                        minLines: 1
                                    });
                                    root.htmEditorCountPageActive = root.ace.edit("editorCOUNTactive");
                                    root.htmEditorCountPageActive.setTheme("ace/theme/ambiance");
                                    root.htmEditorCountPageActive.getSession().setMode("ace/mode/html");
                                    root.htmEditorCountPageActive.setHighlightActiveLine(true);
                                    root.htmEditorCountPageActive.setShowInvisibles(0);
                                    root.htmEditorCountPageActive.getSession().setUseWrapMode(true);
                                    root.htmEditorCountPageActive.getSession().setUseSoftTabs(false);
                                    root.$(".chosen-results,.nave_folders").perfectScrollbar({
                                        suppressScrollX: true
                                    });
                                    root.htmEditorCountPageActive.getSession().on('change', function (e) {
                                        root.$("textarea[name='editorCOUNTactive']").val(root.htmEditorCountPageActive.getSession().getValue())
                                    })
                                    root.htmEditorCountPageActive.getSession().setValue(decodeURIComponent(root.$("textarea[name='editorCOUNTactive']").val()))
                                })
                            })

                        },
                        newFun: function () {
                            root.functions({
                                funcao: "InsertPaginationCampos",
                                vars: root.$("#formTags").serialize(),
                                patch: "App/Modulos/webmaster",
                                Sucess: function ($code) {
                                    root.$("#codiad")[0].contentWindow.codiad.editor.insertContent($code)
                                }
                            })
                        }
                    })
                }
            })


        }


    })
})