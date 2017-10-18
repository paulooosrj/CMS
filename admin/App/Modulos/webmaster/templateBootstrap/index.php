<!DOCTYPE html>
<html lang="en">
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="The only visual Bootstrap 3 grid builder featuring full responsive media query views and fully functional preview.">
    <script src="//code.jquery.com/jquery-latest.min.js"></script>
    <!--[if lt IE 9]>
      <script src="//cdnjs.cloudflare.com/ajax/libs/respond.js/1.2.0/respond.js?b45bc0"></script>
      <script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.6.2/html5shiv.js?b45bc0"></script>
    <![endif]-->
    <link rel="stylesheet" href="./../../../Templates/css/fontes/fonts.css">
    <link rel="stylesheet" href="./../../../Templates/css/perfect-scrollbar/perfect-scrollbar.min.css">
    <link rel="stylesheet" href="css/bigsky.aui.css">
    <link rel="stylesheet" href="css/styles.css">
    <link href="favicon.png" rel="shortcut icon" type="image/png">
  </head>
  <body>
    <div class="application-frame easing">
      <div class="panels-wrapper">
        <div class="navigator easing">
          <div class="preview xs easing">
            <div class="title-bar">
              <div class="size-icon"><i class="icon-mobile-phone"> </i></div>
              <div class="title">Celular</div>
            </div>
            <div class="preview-container">
              <div class="preview-rows"></div>
            </div>
          </div>
          <div class="preview sm easing">
            <div class="title-bar">
              <div class="size-icon"><i class="icon-tablet"> </i></div>
              <div class="title">Tablet</div>
            </div>
            <div class="preview-container">
              <div class="preview-rows"></div>
            </div>
          </div>
          <div class="preview md easing">
            <div class="title-bar">
              <div class="size-icon"><i class="icon-laptop"> </i></div>
              <div class="title">Desktop</div>
            </div>
            <div class="preview-container">
              <div class="preview-rows"></div>
            </div>
          </div>
          <div class="preview lg easing">
            <div class="title-bar">
              <div class="size-icon"><i class="icon-desktop">  </i></div>
              <div class="title">Large Desktop</div>
            </div>
            <div class="preview-container">
              <div class="preview-rows"></div>
            </div>
          </div>
        </div>
        <div class="workspace easing"></div>
        <div class="html easing">
          <div class="collapse-panel right"> <span class="open"><i class="icon-caret-right"> </i></span><span class="closed"><i class="icon-caret-left"> </i></span></div>
          <div class="html-wrapper">
            <div class="options easing">
              <ul>
                <li class="output-html easing active">HTML</li>
                <li class="output-jade easing">Jade</li>
                <li class="output-edn easing">EDN</li>
              </ul>
              <div class="container-check">
                <label>
                  <input type="checkbox" class="use-less-mixin">Usar LESS
                </label>
                <label>
                  <input type="checkbox" class="include-container">Incluir Container
                </label>
              </div>
              <div class="clear"></div>
            </div>
            <div class="output_container">
              <pre class="output prettyprint lang-html markup"></pre>
              <div class="clear"></div>
              <pre class="output prettyprint lang-text mixins"></pre>
            </div>
            <textarea class="copy-output"></textarea>
          </div>
        </div>
      </div>
    </div>
    <script type="text/javascript" src="./../../../Vendor/prettify/r298/prettify.min.js"></script>
    <script type="text/javascript" src="./../../../Vendor/beautify-html/beautify-html.js"></script>
    <!-- <script type="text/javascript" src="./../../../Templates/js/websheep/websheep_full.js"></script> -->
    <script type="text/javascript" src="./js/cljs.js"></script>
    <script type="text/javascript">
      $(document).ready(function(){
       // $("body").perfectScrollbar()
      // $(".preview .preview-container").perfectScrollbar()
      // $(".workspace.easing").perfectScrollbar()
      })
    </script>
  </body>
</html>
