<?ob_start();
if( !defined( '__DIR__' ) )define( '__DIR__', dirname(__FILE__) );
if (mysqli_connect_errno()) {printf("Connect failed: %s\n", mysqli_connect_error());exit();}
set_time_limit(0);

#####################################################################
# 	INCLUIMOS A SESSÃO
#####################################################################
include_once (ROOT_ADMIN.'/App/Lib/class-session.php');
	
#####################################################################
# 	RETORNA ARQUIVOS CONFORMA O PATERN SETADO
#####################################################################
function rsearch($folder, $pattern) {
	$dir 		= new RecursiveDirectoryIterator($folder);
	$ite 		= new RecursiveIteratorIterator($dir);
	$files 		= new RegexIterator($ite, $pattern, RegexIterator::GET_MATCH);
	$fileList 	= array();
	foreach($files as $file) {$fileList[] = $file[0];}
	return $fileList;
}
#####################################################################
# 	RETORNA ARQUIVOS CONFORMA O PATERN SETADO
#####################################################################
	function get_content_type($file) {
		$info = pathinfo($file);
		$content_types = array(
			"php"		=>"application/x-httpd-php",
			"ez"		=>"application/andrew-inset",
			"aw"		=>"application/applixware",
			"atom"		=>"application/atom+xml",
			"atomcat"	=>"application/atomcat+xml",
			"atomsvc"	=>"application/atomsvc+xml",
			"ccxml"		=>"application/ccxml+xml",
			"cdmia"		=>"application/cdmi-capability",
			"cdmic"		=>"application/cdmi-container",
			"cdmid"		=>"application/cdmi-domain",
			"cdmio"		=>"application/cdmi-object",
			"cdmiq"		=>"application/cdmi-queue",
			"cu"		=>"application/cu-seeme",
			"davmount"	=>"application/davmount+xml",
			"dbk"		=>"application/docbook+xml",
			"dssc"		=>"application/dssc+der",
			"xdssc"		=>"application/dssc+xml",
			"ecma"		=>"application/ecmascript",
			"emma"		=>"application/emma+xml",
			"epub"		=>"application/epub+zip",
			"exi"		=>"application/exi",
			"pfr"		=>"application/font-tdpfr",
			"gml"		=>"application/gml+xml",
			"gpx"		=>"application/gpx+xml",
			"gxf"		=>"application/gxf",
			"stk"		=>"application/hyperstudio",
			"ink"		=>"application/inkml+xml",
			"inkml"		=>"application/inkml+xml",
			"ipfix"		=>"application/ipfix",
			"jar"		=>"application/java-archive",
			"ser"		=>"application/java-serialized-object",
			"class"		=>"application/java-vm",
			"js"		=>"application/javascript",
			"json"		=>"application/json",
			"jsonml"	=>"application/jsonml+json",
			"lostxml"	=>"application/lost+xml",
			"hqx"		=>"application/mac-binhex40",
			"cpt"		=>"application/mac-compactpro",
			"mads"		=>"application/mads+xml",
			"mrc"		=>"application/marc",
			"mrcx"		=>"application/marcxml+xml",
			"ma"		=>"application/mathematica",
			"nb"		=>"application/mathematica", 
			"mb"		=>"application/mathematica",
			"mathml"	=>"application/mathml+xml",
			"mbox"		=>"application/mbox",
			"mscml"		=>"application/mediaservercontrol+xml",
			"metalink"	=>"application/metalink+xml",
			"meta4"		=>"application/metalink4+xml",
			"mets"		=>"application/mets+xml",
			"mods"		=>"application/mods+xml",
			"m21"		=>"application/mp21", 
			"mp21"		=>"application/mp21",
			"mp4s"		=>"application/mp4",
			"doc"		=>"application/msword", 
			"dot"		=>"application/msword",
			"mxf"		=>"application/mxf",
			"bin"		=>"application/octet-stream",
			"dms"		=>"application/octet-stream",
			"lrf"		=>"application/octet-stream",
			"mar"		=>"application/octet-stream",
			"so"		=>"application/octet-stream",
			"dist"		=>"application/octet-stream",
			"distz"		=>"application/octet-stream",
			"pkg"		=>"application/octet-stream",
			"bpk"		=>"application/octet-stream",
			"dump"		=>"application/octet-stream",
			"elc"		=>"application/octet-stream",
			"deploy"	=>"application/octet-stream",
			"oda"		=>"application/oda",
			"opf"		=>"application/oebps-package+xml",
			"ogx"		=>"application/ogg",
			"omdoc"		=>"application/omdoc+xml",
			"onetoc"	=>"application/onenote",
			"onetoc2"	=>"application/onenote",
			"onetmp"	=>"application/onenote",
			"onepkg"	=>"application/onenote",
			"oxps"		=>"application/oxps",
			"xer"		=>"application/patch-ops-error+xml",
			"pdf"		=>"application/pdf",
			"pgp"		=>"application/pgp-encrypted",
			"asc"		=>"application/pgp-signature", 
			"sig"		=>"application/pgp-signature",
			"prf"		=>"application/pics-rules",
			"p10"		=>"application/pkcs10",
			"p7m"		=>"application/pkcs7-mime",
			"p7c"		=>"application/pkcs7-mime",
			"p7s"		=>"application/pkcs7-signature",
			"p8"		=>"application/pkcs8",
			"ac"		=>"application/pkix-attr-cert",
			"cer"		=>"application/pkix-cert",
			"crl"		=>"application/pkix-crl",
			"pkipath"		=>"application/pkix-pkipath",
			"pki"		=>"application/pkixcmp",
			"pls"		=>"application/pls+xml",
			"ai"		=>"application/postscript",
			"eps"		=>"application/postscript", 
			"ps"		=>"application/postscript",
			"cww"		=>"application/prs.cww",
			"pskcxml"		=>"application/pskc+xml",
			"rdf"		=>"application/rdf+xml",
			"rif"		=>"application/reginfo+xml",
			"rnc"		=>"application/relax-ng-compact-syntax",
			"rl"		=>"application/resource-lists+xml",
			"rld"		=>"application/resource-lists-diff+xml",
			"rs"		=>"application/rls-services+xml",
			"gbr"		=>"application/rpki-ghostbusters",
			"mft"		=>"application/rpki-manifest",
			"roa"		=>"application/rpki-roa",
			"rsd"		=>"application/rsd+xml",
			"rss"		=>"application/rss+xml",
			"rtf"		=>"application/rtf",
			"sbml"		=>"application/sbml+xml",
			"scq"		=>"application/scvp-cv-request",
			"scs"		=>"application/scvp-cv-response",
			"spq"		=>"application/scvp-vp-request",
			"spp"		=>"application/scvp-vp-response",
			"sdp"		=>"application/sdp",
			"setpay"		=>"application/set-payment-initiation",
			"setreg"		=>"application/set-registration-initiation",
			"shf"		=>"application/shf+xml",
			"smi"		=>"application/smil+xml",
			"smil"		=>"application/smil+xml",
			"rq"		=>"application/sparql-query",
			"srx"		=>"application/sparql-results+xml",
			"gram"		=>"application/srgs",
			"grxml"		=>"application/srgs+xml",
			"sru"		=>"application/sru+xml",
			"ssdl"		=>"application/ssdl+xml",
			"ssml"		=>"application/ssml+xml",
			"tei"		=>"application/tei+xml",
			"teicorpus"		=>"application/tei+xml",
			"tfi"		=>"application/thraud+xml",
			"tsd"		=>"application/timestamped-data",
			"plb"		=>"application/vnd.3gpp.pic-bw-large",
			"psb"		=>"application/vnd.3gpp.pic-bw-small",
			"pvb"		=>"application/vnd.3gpp.pic-bw-var",
			"tcap"		=>"application/vnd.3gpp2.tcap",
			"pwn"		=>"application/vnd.3m.post-it-notes",
			"aso"		=>"application/vnd.accpac.simply.aso",
			"imp"		=>"application/vnd.accpac.simply.imp",
			"acu"		=>"application/vnd.acucobol",
			"atc"		=>"application/vnd.acucorp",
			"acutc"		=>"application/vnd.acucorp",
			"air"		=>"application/vnd.adobe.air-application-installer-package+zip",
			"fcdt"		=>"application/vnd.adobe.formscentral.fcdt",
			"fxp"		=>"application/vnd.adobe.fxp", 
			"fxpl"		=>"application/vnd.adobe.fxp",
			"xdp"		=>"application/vnd.adobe.xdp+xml",
			"xfdf"		=>"application/vnd.adobe.xfdf",
			"ahead"		=>"application/vnd.ahead.space",
			"azf"		=>"application/vnd.airzip.filesecure.azf",
			"azs"		=>"application/vnd.airzip.filesecure.azs",
			"azw"		=>"application/vnd.amazon.ebook",
			"acc"		=>"application/vnd.americandynamics.acc",
			"ami"		=>"application/vnd.amiga.ami",
			"apk"		=>"application/vnd.android.package-archive",
			"cii"		=>"application/vnd.anser-web-certificate-issue-initiation",
			"fti"		=>"application/vnd.anser-web-funds-transfer-initiation",
			"atx"		=>"application/vnd.antix.game-component",
			"mpkg"		=>"application/vnd.apple.installer+xml",
			"m3u8"		=>"application/vnd.apple.mpegurl",
			"swi"		=>"application/vnd.aristanetworks.swi",
			"iota"		=>"application/vnd.astraea-software.iota",
			"aep"		=>"application/vnd.audiograph",
			"mpm"		=>"application/vnd.blueice.multipass",
			"bmi"		=>"application/vnd.bmi",
			"rep"		=>"application/vnd.businessobjects",
			"cdxml"		=>"application/vnd.chemdraw+xml",
			"mmd"		=>"application/vnd.chipnuts.karaoke-mmd",
			"cdy"		=>"application/vnd.cinderella",
			"cla"		=>"application/vnd.claymore",
			"rp9"		=>"application/vnd.cloanto.rp9",
			"c4g"		=>"application/vnd.clonk.c4group",
			"c4d"		=>"application/vnd.clonk.c4group",
			"c4f"		=>"application/vnd.clonk.c4group",
			"c4p"		=>"application/vnd.clonk.c4group",
			"c4u"		=>"application/vnd.clonk.c4group",
			"c11amc"		=>"application/vnd.cluetrust.cartomobile-config",
			"c11amz"		=>"application/vnd.cluetrust.cartomobile-config-pkg",
			"csp"		=>"application/vnd.commonspace",
			"cdbcmsg"		=>"application/vnd.contact.cmsg",
			"cmc"		=>"application/vnd.cosmocaller",
			"clkx"		=>"application/vnd.crick.clicker",
			"clkk"		=>"application/vnd.crick.clicker.keyboard",
			"clkp"		=>"application/vnd.crick.clicker.palette",
			"clkt"		=>"application/vnd.crick.clicker.template",
			"clkw"		=>"application/vnd.crick.clicker.wordbank",
			"wbs"		=>"application/vnd.criticaltools.wbs+xml",
			"pml"		=>"application/vnd.ctc-posml",
			"ppd"		=>"application/vnd.cups-ppd",
			"car"		=>"application/vnd.curl.car",
			"pcurl"		=>"application/vnd.curl.pcurl",
			"dart"		=>"application/vnd.dart",
			"rdz"		=>"application/vnd.data-vision.rdz",
			"uvf"		=>"application/vnd.dece.data",
			"uvvf"		=>"application/vnd.dece.data",
			"uvd"		=>"application/vnd.dece.data",
			"uvvd"		=>"application/vnd.dece.data",
			"uvt"		=>"application/vnd.dece.ttml+xml",
			"uvvt"		=>"application/vnd.dece.ttml+xml",
			"uvx"		=>"application/vnd.dece.unspecified",
			"uvvx"		=>"application/vnd.dece.unspecified",
			"uvz"		=>"application/vnd.dece.zip",
			"uvvz"		=>"application/vnd.dece.zip",
			"fe_launch"		=>"application/vnd.denovo.fcselayout-link",
			"dna"		=>"application/vnd.dna",
			"mlp"		=>"application/vnd.dolby.mlp",
			"dpg"		=>"application/vnd.dpgraph",
			"dfac"		=>"application/vnd.dreamfactory",
			"kpxx"		=>"application/vnd.ds-keypoint",
			"ait"		=>"application/vnd.dvb.ait",
			"svc"		=>"application/vnd.dvb.service",
			"geo"		=>"application/vnd.dynageo",
			"mag"		=>"application/vnd.ecowin.chart",
			"nml"		=>"application/vnd.enliven",
			"esf"		=>"application/vnd.epson.esf",
			"msf"		=>"application/vnd.epson.msf",
			"qam"		=>"application/vnd.epson.quickanime",
			"slt"		=>"application/vnd.epson.salt",
			"ssf"		=>"application/vnd.epson.ssf",
			"es3"		=>"application/vnd.eszigno3+xml",
			"et3"		=>"application/vnd.eszigno3+xml",
			"ez2"		=>"application/vnd.ezpix-album",
			"ez3"		=>"application/vnd.ezpix-package",
			"fdf"		=>"application/vnd.fdf",
			"mseed"		=>"application/vnd.fdsn.mseed",
			"seed"		=>"application/vnd.fdsn.seed",
			"dataless"		=>"application/vnd.fdsn.seed",
			"gph"		=>"application/vnd.flographit",
			"ftc"		=>"application/vnd.fluxtime.clip",
			"fm"		=>"application/vnd.framemaker",
			"frame"		=>"application/vnd.framemaker",
			"maker"		=>"application/vnd.framemaker",
			"book"		=>"application/vnd.framemaker",
			"fnc"		=>"application/vnd.frogans.fnc",
			"ltf"		=>"application/vnd.frogans.ltf",
			"fsc"		=>"application/vnd.fsc.weblaunch",
			"oas"		=>"application/vnd.fujitsu.oasys",
			"oa2"		=>"application/vnd.fujitsu.oasys2",
			"oa3"		=>"application/vnd.fujitsu.oasys3",
			"fg5"		=>"application/vnd.fujitsu.oasysgp",
			"bh2"		=>"application/vnd.fujitsu.oasysprs",
			"ddd"		=>"application/vnd.fujixerox.ddd",
			"xdw"		=>"application/vnd.fujixerox.docuworks",
			"xbd"		=>"application/vnd.fujixerox.docuworks.binder",
			"fzs"		=>"application/vnd.fuzzysheet",
			"txd"		=>"application/vnd.genomatix.tuxedo",
			"ggb"		=>"application/vnd.geogebra.file",
			"ggt"		=>"application/vnd.geogebra.tool",
			"gex"		=>"application/vnd.geometry-explorer",
			"gre"		=>"application/vnd.geometry-explorer",
			"gxt"		=>"application/vnd.geonext",
			"g2w"		=>"application/vnd.geoplan",
			"g3w"		=>"application/vnd.geospace",
			"gmx"		=>"application/vnd.gmx",
			"kml"		=>"application/vnd.google-earth.kml+xml",
			"kmz"		=>"application/vnd.google-earth.kmz",
			"gqf"		=>"application/vnd.grafeq",
			"gqs"		=>"application/vnd.grafeq",
			"gac"		=>"application/vnd.groove-account",
			"ghf"		=>"application/vnd.groove-help",
			"gim"		=>"application/vnd.groove-identity-message",
			"grv"		=>"application/vnd.groove-injector",
			"gtm"		=>"application/vnd.groove-tool-message",
			"tpl"		=>"application/vnd.groove-tool-template",
			"vcg"		=>"application/vnd.groove-vcard",
			"hal"		=>"application/vnd.hal+xml",
			"zmm"		=>"application/vnd.handheld-entertainment+xml",
			"hbci"		=>"application/vnd.hbci",
			"les"		=>"application/vnd.hhe.lesson-player",
			"hpgl"		=>"application/vnd.hp-hpgl",
			"hpid"		=>"application/vnd.hp-hpid",
			"hps"		=>"application/vnd.hp-hps",
			"jlt"		=>"application/vnd.hp-jlyt",
			"pcl"		=>"application/vnd.hp-pcl",
			"pclxl"		=>"application/vnd.hp-pclxl",
			"sfd-hdstx"		=>"application/vnd.hydrostatix.sof-data",
			"mpy"		=>"application/vnd.ibm.minipay",
			"afp"		=>"application/vnd.ibm.modcap",
			"listafp"		=>"application/vnd.ibm.modcap",
			"list3820"		=>"application/vnd.ibm.modcap",
			"irm"		=>"application/vnd.ibm.rights-management",
			"sc"		=>"application/vnd.ibm.secure-container",
			"icc"		=>"application/vnd.iccprofile",
			"icm"		=>"application/vnd.iccprofile",
			"igl"		=>"application/vnd.igloader",
			"ivp"		=>"application/vnd.immervision-ivp",
			"ivu"		=>"application/vnd.immervision-ivu",
			"igm"		=>"application/vnd.insors.igm",
			"xpw"		=>"application/vnd.intercon.formnet",
			"xpx"		=>"application/vnd.intercon.formnet",
			"i2g"		=>"application/vnd.intergeo",
			"qbo"		=>"application/vnd.intu.qbo",
			"qfx"		=>"application/vnd.intu.qfx",
			"rcprofile"		=>"application/vnd.ipunplugged.rcprofile",
			"irp"		=>"application/vnd.irepository.package+xml",
			"xpr"		=>"application/vnd.is-xpr",
			"fcs"		=>"application/vnd.isac.fcs",
			"jam"		=>"application/vnd.jam",
			"rms"		=>"application/vnd.jcp.javame.midlet-rms",
			"jisp"		=>"application/vnd.jisp",
			"joda"		=>"application/vnd.joost.joda-archive",
			"ktz"		=>"application/vnd.kahootz",
			"ktr"		=>"application/vnd.kahootz",
			"karbon"		=>"application/vnd.kde.karbon",
			"chrt"		=>"application/vnd.kde.kchart",
			"kfo"		=>"application/vnd.kde.kformula",
			"flw"		=>"application/vnd.kde.kivio",
			"kon"		=>"application/vnd.kde.kontour",
			"kpr"		=>"application/vnd.kde.kpresenter",
			"kpt"		=>"application/vnd.kde.kpresenter",
			"ksp"		=>"application/vnd.kde.kspread",
			"kwd"		=>"application/vnd.kde.kword",
			"kwt"		=>"application/vnd.kde.kword",
			"htke"		=>"application/vnd.kenameaapp",
			"kia"		=>"application/vnd.kidspiration",
			"kne"		=>"application/vnd.kinar",
			"knp"		=>"application/vnd.kinar",
			"skp"		=>"application/vnd.koan",
			"skd"		=>"application/vnd.koan",
			"skt"		=>"application/vnd.koan",
			"skm"		=>"application/vnd.koan",
			"sse"		=>"application/vnd.kodak-descriptor",
			"lasxml"		=>"application/vnd.las.las+xml",
			"lbd"		=>"application/vnd.llamagraphics.life-balance.desktop",
			"lbe"		=>"application/vnd.llamagraphics.life-balance.exchange+xml",
			"123"		=>"application/vnd.lotus-1-2-3",
			"apr"		=>"application/vnd.lotus-approach",
			"pre"		=>"application/vnd.lotus-freelance",
			"nsf"		=>"application/vnd.lotus-notes",
			"org"		=>"application/vnd.lotus-organizer",
			"scm"		=>"application/vnd.lotus-screencam",
			"lwp"		=>"application/vnd.lotus-wordpro",
			"portpkg"		=>"application/vnd.macports.portpkg",
			"mcd"		=>"application/vnd.mcd",
			"mc1"		=>"application/vnd.medcalcdata",
			"cdkey"		=>"application/vnd.mediastation.cdkey",
			"mwf"		=>"application/vnd.mfer",
			"mfm"		=>"application/vnd.mfmp",
			"flo"		=>"application/vnd.micrografx.flo",
			"igx"		=>"application/vnd.micrografx.igx",
			"mif"		=>"application/vnd.mif",
			"daf"		=>"application/vnd.mobius.daf",
			"dis"		=>"application/vnd.mobius.dis",
			"mbk"		=>"application/vnd.mobius.mbk",
			"mqy"		=>"application/vnd.mobius.mqy",
			"msl"		=>"application/vnd.mobius.msl",
			"plc"		=>"application/vnd.mobius.plc",
			"txf"		=>"application/vnd.mobius.txf",
			"mpn"		=>"application/vnd.mophun.application",
			"mpc"		=>"application/vnd.mophun.certificate",
			"xul"		=>"application/vnd.mozilla.xul+xml",
			"cil"		=>"application/vnd.ms-artgalry",
			"cab"		=>"application/vnd.ms-cab-compressed",
			"xls"		=>"application/vnd.ms-excel",
			"xlm"		=>"application/vnd.ms-excel",
			"xla"		=>"application/vnd.ms-excel",
			"xlc"		=>"application/vnd.ms-excel",
			"xlt"		=>"application/vnd.ms-excel",
			"xlw"		=>"application/vnd.ms-excel",
			"xlam"		=>"application/vnd.ms-excel.addin.macroenabled.12",
			"xlsb"		=>"application/vnd.ms-excel.sheet.binary.macroenabled.12",
			"xlsm"		=>"application/vnd.ms-excel.sheet.macroenabled.12",
			"xltm"		=>"application/vnd.ms-excel.template.macroenabled.12",
			"eot"		=>"application/vnd.ms-fontobject",
			"chm"		=>"application/vnd.ms-htmlhelp",
			"ims"		=>"application/vnd.ms-ims",
			"lrm"		=>"application/vnd.ms-lrm",
			"thmx"		=>"application/vnd.ms-officetheme",
			"cat"		=>"application/vnd.ms-pki.seccat",
			"stl"		=>"application/vnd.ms-pki.stl",
			"ppt"		=>"application/vnd.ms-powerpoint",
			"pps"		=>"application/vnd.ms-powerpoint",
			"pot"		=>"application/vnd.ms-powerpoint",
			"ppam"		=>"application/vnd.ms-powerpoint.addin.macroenabled.12",
			"pptm"		=>"application/vnd.ms-powerpoint.presentation.macroenabled.12",
			"sldm"		=>"application/vnd.ms-powerpoint.slide.macroenabled.12",
			"ppsm"		=>"application/vnd.ms-powerpoint.slideshow.macroenabled.12",
			"potm"		=>"application/vnd.ms-powerpoint.template.macroenabled.12",
			"mpp"		=>"application/vnd.ms-project",
			"mpt"		=>"application/vnd.ms-project",
			"docm"		=>"application/vnd.ms-word.document.macroenabled.12",
			"dotm"		=>"application/vnd.ms-word.template.macroenabled.12",
			"wps"		=>"application/vnd.ms-works",
			"wks"		=>"application/vnd.ms-works",
			"wcm"		=>"application/vnd.ms-works",
			"wdb"		=>"application/vnd.ms-works",
			"wpl"		=>"application/vnd.ms-wpl",
			"xps"		=>"application/vnd.ms-xpsdocument",
			"mseq"		=>"application/vnd.mseq",
			"mus"		=>"application/vnd.musician",
			"msty"		=>"application/vnd.muvee.style",
			"taglet"		=>"application/vnd.mynfc",
			"nlu"		=>"application/vnd.neurolanguage.nlu",
			"ntf"		=>"application/vnd.nitf",
			"nitf"		=>"application/vnd.nitf",
			"nnd"		=>"application/vnd.noblenet-directory",
			"nns"		=>"application/vnd.noblenet-sealer",
			"nnw"		=>"application/vnd.noblenet-web",
			"ngdat"		=>"application/vnd.nokia.n-gage.data",
			"n-gage"		=>"application/vnd.nokia.n-gage.symbian.install",
			"rpst"		=>"application/vnd.nokia.radio-preset",
			"rpss"		=>"application/vnd.nokia.radio-presets",
			"edm"		=>"application/vnd.novadigm.edm",
			"edx"		=>"application/vnd.novadigm.edx",
			"ext"		=>"application/vnd.novadigm.ext",
			"odc"		=>"application/vnd.oasis.opendocument.chart",
			"otc"		=>"application/vnd.oasis.opendocument.chart-template",
			"odb"		=>"application/vnd.oasis.opendocument.database",
			"odf"		=>"application/vnd.oasis.opendocument.formula",
			"odft"		=>"application/vnd.oasis.opendocument.formula-template",
			"odg"		=>"application/vnd.oasis.opendocument.graphics",
			"otg"		=>"application/vnd.oasis.opendocument.graphics-template",
			"odi"		=>"application/vnd.oasis.opendocument.image",
			"oti"		=>"application/vnd.oasis.opendocument.image-template",
			"odp"		=>"application/vnd.oasis.opendocument.presentation",
			"otp"		=>"application/vnd.oasis.opendocument.presentation-template",
			"ods"		=>"application/vnd.oasis.opendocument.spreadsheet",
			"ots"		=>"application/vnd.oasis.opendocument.spreadsheet-template",
			"odt"		=>"application/vnd.oasis.opendocument.text",
			"odm"		=>"application/vnd.oasis.opendocument.text-master",
			"ott"		=>"application/vnd.oasis.opendocument.text-template",
			"oth"		=>"application/vnd.oasis.opendocument.text-web",
			"xo"		=>"application/vnd.olpc-sugar",
			"dd2"		=>"application/vnd.oma.dd2+xml",
			"oxt"		=>"application/vnd.openofficeorg.extension",
			"pptx"		=>"application/vnd.openxmlformats-officedocument.presentationml.presentation",
			"sldx"		=>"application/vnd.openxmlformats-officedocument.presentationml.slide",
			"ppsx"		=>"application/vnd.openxmlformats-officedocument.presentationml.slideshow",
			"potx"		=>"application/vnd.openxmlformats-officedocument.presentationml.template",
			"xlsx"		=>"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
			"xltx"		=>"application/vnd.openxmlformats-officedocument.spreadsheetml.template",
			"docx"		=>"application/vnd.openxmlformats-officedocument.wordprocessingml.document",
			"dotx"		=>"application/vnd.openxmlformats-officedocument.wordprocessingml.template",
			"mgp"		=>"application/vnd.osgeo.mapguide.package",
			"dp"		=>"application/vnd.osgi.dp",
			"esa"		=>"application/vnd.osgi.subsystem",
			"pdb"		=>"application/vnd.palm",
			"pqa"		=>"application/vnd.palm",
			"oprc"		=>"application/vnd.palm",
			"paw"		=>"application/vnd.pawaafile",
			"str"		=>"application/vnd.pg.format",
			"ei6"		=>"application/vnd.pg.osasli",
			"efif"		=>"application/vnd.picsel",
			"wg"		=>"application/vnd.pmi.widget",
			"plf"		=>"application/vnd.pocketlearn",
			"pbd"		=>"application/vnd.powerbuilder6",
			"box"		=>"application/vnd.previewsystems.box",
			"mgz"		=>"application/vnd.proteus.magazine",
			"qps"		=>"application/vnd.publishare-delta-tree",
			"ptid"		=>"application/vnd.pvi.ptid1",
			"qxd"		=>"application/vnd.quark.quarkxpress",
			"qxt"		=>"application/vnd.quark.quarkxpress",
			"qwd"		=>"application/vnd.quark.quarkxpress",
			"qwt"		=>"application/vnd.quark.quarkxpress",
			"qxl"		=>"application/vnd.quark.quarkxpress",
			"qxb"		=>"application/vnd.quark.quarkxpress",
			"bed"		=>"application/vnd.realvnc.bed",
			"mxl"		=>"application/vnd.recordare.musicxml",
			"musicxml"		=>"application/vnd.recordare.musicxml+xml",
			"cryptonote"		=>"application/vnd.rig.cryptonote",
			"cod"		=>"application/vnd.rim.cod",
			"rm"		=>"application/vnd.rn-realmedia",
			"rmvb"		=>"application/vnd.rn-realmedia-vbr",
			"link66"		=>"application/vnd.route66.link66+xml",
			"st"		=>"application/vnd.sailingtracker.track",
			"see"		=>"application/vnd.seemail",
			"sema"		=>"application/vnd.sema",
			"semd"		=>"application/vnd.semd",
			"semf"		=>"application/vnd.semf",
			"ifm"		=>"application/vnd.shana.informed.formdata",
			"itp"		=>"application/vnd.shana.informed.formtemplate",
			"iif"		=>"application/vnd.shana.informed.interchange",
			"ipk"		=>"application/vnd.shana.informed.package",
			"twd"		=>"application/vnd.simtech-mindmapper",
			"twds"		=>"application/vnd.simtech-mindmapper",
			"mmf"		=>"application/vnd.smaf",
			"teacher"		=>"application/vnd.smart.teacher",
			"sdkm"		=>"application/vnd.solent.sdkm+xml",
			"sdkd"		=>"application/vnd.solent.sdkm+xml",
			"dxp"		=>"application/vnd.spotfire.dxp",
			"sfs"		=>"application/vnd.spotfire.sfs",
			"sdc"		=>"application/vnd.stardivision.calc",
			"sda"		=>"application/vnd.stardivision.draw",
			"sdd"		=>"application/vnd.stardivision.impress",
			"smf"		=>"application/vnd.stardivision.math",
			"sdw"		=>"application/vnd.stardivision.writer",
			"vor"		=>"application/vnd.stardivision.writer",
			"sgl"		=>"application/vnd.stardivision.writer-global",
			"smzip"		=>"application/vnd.stepmania.package",
			"sm"		=>"application/vnd.stepmania.stepchart",
			"sxc"		=>"application/vnd.sun.xml.calc",
			"stc"		=>"application/vnd.sun.xml.calc.template",
			"sxd"		=>"application/vnd.sun.xml.draw",
			"std"		=>"application/vnd.sun.xml.draw.template",
			"sxi"		=>"application/vnd.sun.xml.impress",
			"sti"		=>"application/vnd.sun.xml.impress.template",
			"sxm"		=>"application/vnd.sun.xml.math",
			"sxw"		=>"application/vnd.sun.xml.writer",
			"sxg"		=>"application/vnd.sun.xml.writer.global",
			"stw"		=>"application/vnd.sun.xml.writer.template",
			"sus"		=>"application/vnd.sus-calendar",
			"susp"		=>"application/vnd.sus-calendar",
			"svd"		=>"application/vnd.svd",
			"sis"		=>"application/vnd.symbian.install",
			"sisx"		=>"application/vnd.symbian.install",
			"xsm"		=>"application/vnd.syncml+xml",
			"bdm"		=>"application/vnd.syncml.dm+wbxml",
			"xdm"		=>"application/vnd.syncml.dm+xml",
			"tao"		=>"application/vnd.tao.intent-module-archive",
			"pcap"		=>"application/vnd.tcpdump.pcap",
			"cap"		=>"application/vnd.tcpdump.pcap",
			"dmp"		=>"application/vnd.tcpdump.pcap",
			"tmo"		=>"application/vnd.tmobile-livetv",
			"tpt"		=>"application/vnd.trid.tpt",
			"mxs"		=>"application/vnd.triscape.mxs",
			"tra"		=>"application/vnd.trueapp",
			"ufd"		=>"application/vnd.ufdl",
			"ufdl"		=>"application/vnd.ufdl",
			"utz"		=>"application/vnd.uiq.theme",
			"umj"		=>"application/vnd.umajin",
			"unityweb"		=>"application/vnd.unity",
			"uoml"		=>"application/vnd.uoml+xml",
			"vcx"		=>"application/vnd.vcx",
			"vsd"		=>"application/vnd.visio",
			"vst"		=>"application/vnd.visio",
			"vss"		=>"application/vnd.visio",
			"vsw"		=>"application/vnd.visio",
			"vis"		=>"application/vnd.visionary",
			"vsf"		=>"application/vnd.vsf",
			"wbxml"		=>"application/vnd.wap.wbxml",
			"wmlc"		=>"application/vnd.wap.wmlc",
			"wmlsc"		=>"application/vnd.wap.wmlscriptc",
			"wtb"		=>"application/vnd.webturbo",
			"nbp"		=>"application/vnd.wolfram.player",
			"wpd"		=>"application/vnd.wordperfect",
			"wqd"		=>"application/vnd.wqd",
			"stf"		=>"application/vnd.wt.stf",
			"xar"		=>"application/vnd.xara",
			"xfdl"		=>"application/vnd.xfdl",
			"hvd"		=>"application/vnd.yamaha.hv-dic",
			"hvs"		=>"application/vnd.yamaha.hv-script",
			"hvp"		=>"application/vnd.yamaha.hv-voice",
			"osf"		=>"application/vnd.yamaha.openscoreformat",
			"osfpvg"		=>"application/vnd.yamaha.openscoreformat.osfpvg+xml",
			"saf"		=>"application/vnd.yamaha.smaf-audio",
			"spf"		=>"application/vnd.yamaha.smaf-phrase",
			"cmp"		=>"application/vnd.yellowriver-custom-menu",
			"zir"		=>"application/vnd.zul",
			"zirz"		=>"application/vnd.zul",
			"zaz"		=>"application/vnd.zzazz.deck+xml",
			"vxml"		=>"application/voicexml+xml",
			"wgt"		=>"application/widget",
			"hlp"		=>"application/winhlp",
			"wsdl"		=>"application/wsdl+xml",
			"wspolicy"		=>"application/wspolicy+xml",
			"7z"		=>"application/x-7z-compressed",
			"abw"		=>"application/x-abiword",
			"ace"		=>"application/x-ace-compressed",
			"dmg"		=>"application/x-apple-diskimage",
			"aab"		=>"application/x-authorware-bin",
			"x32"		=>"application/x-authorware-bin",
			"u32"		=>"application/x-authorware-bin",
			"vox"		=>"application/x-authorware-bin",
			"aam"		=>"application/x-authorware-map",
			"aas"		=>"application/x-authorware-seg",
			"bcpio"		=>"application/x-bcpio",
			"torrent"		=>"application/x-bittorrent",
			"blb"		=>"application/x-blorb",
			"blorb"		=>"application/x-blorb",
			"bz"		=>"application/x-bzip",
			"bz2"		=>"application/x-bzip2",
			"boz"		=>"application/x-bzip2",
			"cbr"		=>"application/x-cbr",
			"cba"		=>"application/x-cbr",
			"cbt"		=>"application/x-cbr",
			"cbz"		=>"application/x-cbr",
			"cb7"		=>"application/x-cbr",
			"vcd"		=>"application/x-cdlink",
			"cfs"		=>"application/x-cfs-compressed",
			"chat"		=>"application/x-chat",
			"pgn"		=>"application/x-chess-pgn",
			"nsc"		=>"application/x-conference",
			"cpio"		=>"application/x-cpio",
			"csh"		=>"application/x-csh",
			"deb"		=>"application/x-debian-package",
			"udeb"		=>"application/x-debian-package",
			"dgc"		=>"application/x-dgc-compressed",
			"dir"		=>"application/x-director",
			"dcr"		=>"application/x-director",
			"dxr"		=>"application/x-director",
			"cst"		=>"application/x-director",
			"cct"		=>"application/x-director",
			"cxt"		=>"application/x-director",
			"w3d"		=>"application/x-director",
			"fgd"		=>"application/x-director",
			"swa"		=>"application/x-director",
			"wad"		=>"application/x-doom",
			"ncx"		=>"application/x-dtbncx+xml",
			"dtb"		=>"application/x-dtbook+xml",
			"res"		=>"application/x-dtbresource+xml",
			"dvi"		=>"application/x-dvi",
			"evy"		=>"application/x-envoy",
			"eva"		=>"application/x-eva",
			"bdf"		=>"application/x-font-bdf",
			"gsf"		=>"application/x-font-ghostscript",
			"psf"		=>"application/x-font-linux-psf",
			"otf"		=>"application/x-font-otf",
			"pcf"		=>"application/x-font-pcf",
			"snf"		=>"application/x-font-snf",
			"ttf"		=>"application/x-font-ttf",
			"ttc"		=>"application/x-font-ttf",
			"pfa"		=>"application/x-font-type1",
			"pfb"		=>"application/x-font-type1",
			"pfm"		=>"application/x-font-type1",
			"afm"		=>"application/x-font-type1",
			"woff"		=>"application/x-font-woff",
			"arc"		=>"application/x-freearc",
			"spl"		=>"application/x-futuresplash",
			"gca"		=>"application/x-gca-compressed",
			"ulx"		=>"application/x-glulx",
			"gnumeric"		=>"application/x-gnumeric",
			"gramps"		=>"application/x-gramps-xml",
			"gtar"		=>"application/x-gtar",
			"hdf"		=>"application/x-hdf",
			"install"		=>"application/x-install-instructions",
			"iso"		=>"application/x-iso9660-image",
			"jnlp"		=>"application/x-java-jnlp-file",
			"latex"		=>"application/x-latex",
			"lzh"		=>"application/x-lzh-compressed",
			"lha"		=>"application/x-lzh-compressed",
			"mie"		=>"application/x-mie",
			"prc"		=>"application/x-mobipocket-ebook",
			"mobi"		=>"application/x-mobipocket-ebook",
			"application"		=>"application/x-ms-application",
			"lnk"		=>"application/x-ms-shortcut",
			"wmd"		=>"application/x-ms-wmd",
			"wmz"		=>"application/x-ms-wmz",
			"xbap"		=>"application/x-ms-xbap",
			"mdb"		=>"application/x-msaccess",
			"obd"		=>"application/x-msbinder",
			"crd"		=>"application/x-mscardfile",
			"clp"		=>"application/x-msclip",
			"exe"		=>"application/x-msdownload",
			"dll"		=>"application/x-msdownload",
			"com"		=>"application/x-msdownload",
			"bat"		=>"application/x-msdownload",
			"msi"		=>"application/x-msdownload",
			"mvb"		=>"application/x-msmediaview",
			"m13"		=>"application/x-msmediaview",
			"m14"		=>"application/x-msmediaview",
			"wmf"		=>"application/x-msmetafile",
			"wmz"		=>"application/x-msmetafile",
			"emf"		=>"application/x-msmetafile",
			"emz"		=>"application/x-msmetafile",
			"mny"		=>"application/x-msmoney",
			"pub"		=>"application/x-mspublisher",
			"scd"		=>"application/x-msschedule",
			"trm"		=>"application/x-msterminal",
			"wri"		=>"application/x-mswrite",
			"nc"		=>"application/x-netcdf",
			"cdf"		=>"application/x-netcdf",
			"nzb"		=>"application/x-nzb",
			"p12"		=>"application/x-pkcs12",
			"pfx"		=>"application/x-pkcs12",
			"p7b"		=>"application/x-pkcs7-certificates",
			"spc"		=>"application/x-pkcs7-certificates",
			"p7r"		=>"application/x-pkcs7-certreqresp",
			"rar"		=>"application/x-rar-compressed",
			"ris"		=>"application/x-research-info-systems",
			"sh"		=>"application/x-sh",
			"shar"		=>"application/x-shar",
			"swf"		=>"application/x-shockwave-flash",
			"xap"		=>"application/x-silverlight-app",
			"sql"		=>"application/x-sql",
			"sit"		=>"application/x-stuffit",
			"sitx"		=>"application/x-stuffitx",
			"srt"		=>"application/x-subrip",
			"sv4cpio"		=>"application/x-sv4cpio",
			"sv4crc"		=>"application/x-sv4crc",
			"t3"		=>"application/x-t3vm-image",
			"gam"		=>"application/x-tads",
			"tar"		=>"application/x-tar",
			"tcl"		=>"application/x-tcl",
			"tex"		=>"application/x-tex",
			"tfm"		=>"application/x-tex-tfm",
			"texinfo"		=>"application/x-texinfo",
			"texi"		=>"application/x-texinfo",
			"obj"		=>"application/x-tgif",
			"ustar"		=>"application/x-ustar",
			"src"		=>"application/x-wais-source",
			"der"		=>"application/x-x509-ca-cert",
			"crt"		=>"application/x-x509-ca-cert",
			"fig"		=>"application/x-xfig",
			"xlf"		=>"application/x-xliff+xml",
			"xpi"		=>"application/x-xpinstall",
			"xz"		=>"application/x-xz",
			"z1"		=>"application/x-zmachine",
			"z2"		=>"application/x-zmachine",
			"z3"		=>"application/x-zmachine",
			"z4"		=>"application/x-zmachine",
			"z5"		=>"application/x-zmachine",
			"z6"		=>"application/x-zmachine",
			"z7"		=>"application/x-zmachine",
			"z8"		=>"application/x-zmachine",
			"xaml"		=>"application/xaml+xml",
			"xdf"		=>"application/xcap-diff+xml",
			"xenc"		=>"application/xenc+xml",
			"xhtml"		=>"application/xhtml+xml",
			"xht"		=>"application/xhtml+xml",
			"xml"		=>"application/xml",
			"xsl"		=>"application/xml",
			"dtd"		=>"application/xml-dtd",
			"xop"		=>"application/xop+xml",
			"xpl"		=>"application/xproc+xml",
			"xslt"		=>"application/xslt+xml",
			"xspf"		=>"application/xspf+xml",
			"mxml"		=>"application/xv+xml",
			"xhvml"		=>"application/xv+xml",
			"xvml"		=>"application/xv+xml",
			"xvm"		=>"application/xv+xml",
			"yang"		=>"application/yang",
			"yin"		=>"application/yin+xml",
			"zip"		=>"application/zip",
			"adp"		=>"audio/adpcm",
			"au"		=>"audio/basic",
			"snd"		=>"audio/basic",
			"mid"		=>"audio/midi",
			"midi"		=>"audio/midi",
			"kar"		=>"audio/midi",
			"rmi"		=>"audio/midi",
			"m4a"		=>"audio/mp4",
			"mp4a"		=>"audio/mp4",
			"mpga"		=>"audio/mpeg",
			"mp2"		=>"audio/mpeg",
			"mp2a"		=>"audio/mpeg",
			"mp3"		=>"audio/mpeg",
			"m2a"		=>"audio/mpeg",
			"m3a"		=>"audio/mpeg",
			"oga"		=>"audio/ogg",
			"ogg"		=>"audio/ogg",
			"spx"		=>"audio/ogg",
			"s3m"		=>"audio/s3m",
			"sil"		=>"audio/silk",
			"uva"		=>"audio/vnd.dece.audio",
			"uvva"		=>"audio/vnd.dece.audio",
			"eol"		=>"audio/vnd.digital-winds",
			"dra"		=>"audio/vnd.dra",
			"dts"		=>"audio/vnd.dts",
			"dtshd"		=>"audio/vnd.dts.hd",
			"lvp"		=>"audio/vnd.lucent.voice",
			"pya"		=>"audio/vnd.ms-playready.media.pya",
			"ecelp4800"		=>"audio/vnd.nuera.ecelp4800",
			"ecelp7470"		=>"audio/vnd.nuera.ecelp7470",
			"ecelp9600"		=>"audio/vnd.nuera.ecelp9600",
			"rip"		=>"audio/vnd.rip",
			"weba"		=>"audio/webm",
			"aac"		=>"audio/x-aac",
			"aif"		=>"audio/x-aiff",
			"aiff"		=>"audio/x-aiff",
			"aifc"		=>"audio/x-aiff",
			"caf"		=>"audio/x-caf",
			"flac"		=>"audio/x-flac",
			"mka"		=>"audio/x-matroska",
			"m3u"		=>"audio/x-mpegurl",
			"wax"		=>"audio/x-ms-wax",
			"wma"		=>"audio/x-ms-wma",
			"ram"		=>"audio/x-pn-realaudio",
			"ra"		=>"audio/x-pn-realaudio",
			"rmp"		=>"audio/x-pn-realaudio-plugin",
			"wav"		=>"audio/x-wav",
			"xm"		=>"audio/xm",
			"cdx"		=>"chemical/x-cdx",
			"cif"		=>"chemical/x-cif",
			"cmdf"		=>"chemical/x-cmdf",
			"cml"		=>"chemical/x-cml",
			"csml"		=>"chemical/x-csml",
			"xyz"		=>"chemical/x-xyz",
			"bmp"		=>"image/bmp",
			"cgm"		=>"image/cgm",
			"g3"		=>"image/g3fax",
			"gif"		=>"image/gif",
			"ief"		=>"image/ief",
			"jpeg"		=>"image/jpeg",
			"jpg"		=>"image/jpeg",
			"jpe"		=>"image/jpeg",
			"ktx"		=>"image/ktx",
			"png"		=>"image/png",
			"btif"		=>"image/prs.btif",
			"sgi"		=>"image/sgi",
			"svg"		=>"image/svg+xml",
			"svgz"		=>"image/svg+xml",
			"tiff"		=>"image/tiff",
			"tif"		=>"image/tiff",
			"psd"		=>"image/vnd.adobe.photoshop",
			"uvi"		=>"image/vnd.dece.graphic",
			"uvvi"		=>"image/vnd.dece.graphic",
			"uvg"		=>"image/vnd.dece.graphic",
			"uvvg"		=>"image/vnd.dece.graphic",
			"sub"		=>"image/vnd.dvb.subtitle",
			"djvu"		=>"image/vnd.djvu",
			"djv"		=>"image/vnd.djvu",
			"dwg"		=>"image/vnd.dwg",
			"dxf"		=>"image/vnd.dxf",
			"fbs"		=>"image/vnd.fastbidsheet",
			"fpx"		=>"image/vnd.fpx",
			"fst"		=>"image/vnd.fst",
			"mmr"		=>"image/vnd.fujixerox.edmics-mmr",
			"rlc"		=>"image/vnd.fujixerox.edmics-rlc",
			"mdi"		=>"image/vnd.ms-modi",
			"wdp"		=>"image/vnd.ms-photo",
			"npx"		=>"image/vnd.net-fpx",
			"wbmp"		=>"image/vnd.wap.wbmp",
			"xif"		=>"image/vnd.xiff",
			"webp"		=>"image/webp",
			"3ds"		=>"image/x-3ds",
			"ras"		=>"image/x-cmu-raster",
			"cmx"		=>"image/x-cmx",
			"fh"		=>"image/x-freehand",
			"fhc"		=>"image/x-freehand",
			"fh4"		=>"image/x-freehand",
			"fh5"		=>"image/x-freehand",
			"fh7"		=>"image/x-freehand",
			"ico"		=>"image/x-icon",
			"sid"		=>"image/x-mrsid-image",
			"pcx"		=>"image/x-pcx",
			"pic"		=>"image/x-pict",
			"pct"		=>"image/x-pict",
			"pnm"		=>"image/x-portable-anymap",
			"pbm"		=>"image/x-portable-bitmap",
			"pgm"		=>"image/x-portable-graymap",
			"ppm"		=>"image/x-portable-pixmap",
			"rgb"		=>"image/x-rgb",
			"tga"		=>"image/x-tga",
			"xbm"		=>"image/x-xbitmap",
			"xpm"		=>"image/x-xpixmap",
			"xwd"		=>"image/x-xwindowdump",
			"eml"		=>"message/rfc822",
			"mime"		=>"message/rfc822",
			"igs"		=>"model/iges",
			"iges"		=>"model/iges",
			"msh"		=>"model/mesh",
			"mesh"		=>"model/mesh",
			"silo"		=>"model/mesh",
			"dae"		=>"model/vnd.collada+xml",
			"dwf"		=>"model/vnd.dwf",
			"gdl"		=>"model/vnd.gdl",
			"gtw"		=>"model/vnd.gtw",
			"mts"		=>"model/vnd.mts",
			"vtu"		=>"model/vnd.vtu",
			"wrl"		=>"model/vrml",
			"vrml"		=>"model/vrml",
			"x3db"		=>"model/x3d+binary",
			"x3dbz"		=>"model/x3d+binary",
			"x3dv"		=>"model/x3d+vrml",
			"x3dvz"		=>"model/x3d+vrml",
			"x3d"		=>"model/x3d+xml",
			"x3dz"		=>"model/x3d+xml",
			"appcache"		=>"text/cache-manifest",
			"ics"		=>"text/calendar",
			"ifb"		=>"text/calendar",
			"css"		=>"text/css",
			"csv"		=>"text/csv",
			"html"		=>"text/html",
			"htm"		=>"text/html",
			"n3"		=>"text/n3",
			"txt"		=>"text/plain",
			"text"		=>"text/plain",
			"conf"		=>"text/plain",
			"def"		=>"text/plain",
			"list"		=>"text/plain",
			"log"		=>"text/plain",
			"in"		=>"text/plain",
			"dsc"		=>"text/prs.lines.tag",
			"rtx"		=>"text/richtext",
			"sgml"		=>"text/sgml",
			"sgm"		=>"text/sgml",
			"tsv"		=>"text/tab-separated-values",
			"t"		=>"text/troff",
			"tr"		=>"text/troff",
			"roff"		=>"text/troff",
			"man"		=>"text/troff",
			"me"		=>"text/troff",
			"ms"		=>"text/troff",
			"ttl"		=>"text/turtle",
			"uri"		=>"text/uri-list",
			"uris"		=>"text/uri-list",
			"urls"		=>"text/uri-list",
			"vcard"		=>"text/vcard",
			"curl"		=>"text/vnd.curl",
			"dcurl"		=>"text/vnd.curl.dcurl",
			"scurl"		=>"text/vnd.curl.scurl",
			"mcurl"		=>"text/vnd.curl.mcurl",
			"sub"		=>"text/vnd.dvb.subtitle",
			"fly"		=>"text/vnd.fly",
			"flx"		=>"text/vnd.fmi.flexstor",
			"gv"		=>"text/vnd.graphviz",
			"3dml"		=>"text/vnd.in3d.3dml",
			"spot"		=>"text/vnd.in3d.spot",
			"jad"		=>"text/vnd.sun.j2me.app-descriptor",
			"wml"		=>"text/vnd.wap.wml",
			"wmls"		=>"text/vnd.wap.wmlscript",
			"s"		=>"text/x-asm",
			"asm"		=>"text/x-asm",
			"c"		=>"text/x-c",
			"cc"		=>"text/x-c",
			"cxx"		=>"text/x-c",
			"cpp"		=>"text/x-c",
			"h"		=>"text/x-c",
			"hh"		=>"text/x-c",
			"dic"		=>"text/x-c",
			"f"		=>"text/x-fortran",
			"for"		=>"text/x-fortran",
			"f77"		=>"text/x-fortran",
			"f90"		=>"text/x-fortran",
			"java"		=>"text/x-java-source",
			"opml"		=>"text/x-opml",
			"p"		=>"text/x-pascal",
			"pas"		=>"text/x-pascal",
			"nfo"		=>"text/x-nfo",
			"etx"		=>"text/x-setext",
			"sfv"		=>"text/x-sfv",
			"uu"		=>"text/x-uuencode",
			"vcs"		=>"text/x-vcalendar",
			"vcf"		=>"text/x-vcard",
			"3gp"		=>"video/3gpp",
			"3g2"		=>"video/3gpp2",
			"h261"		=>"video/h261",
			"h263"		=>"video/h263",
			"h264"		=>"video/h264",
			"jpgv"		=>"video/jpeg",
			"jpm"		=>"video/jpm",
			"jpgm"		=>"video/jpm",
			"mj2"		=>"video/mj2",
			"mjp2"		=>"video/mj2",
			"mp4"		=>"video/mp4",
			"mp4v"		=>"video/mp4",
			"mpg4"		=>"video/mp4",
			"mpeg"		=>"video/mpeg",
			"mpg"		=>"video/mpeg",
			"mpe"		=>"video/mpeg",
			"m1v"		=>"video/mpeg",
			"m2v"		=>"video/mpeg",
			"ogv"		=>"video/ogg",
			"qt"		=>"video/quicktime",
			"mov"		=>"video/quicktime",
			"uvh"		=>"video/vnd.dece.hd",
			"uvvh"		=>"video/vnd.dece.hd",
			"uvm"		=>"video/vnd.dece.mobile",
			"uvvm"		=>"video/vnd.dece.mobile",
			"uvp"		=>"video/vnd.dece.pd",
			"uvvp"		=>"video/vnd.dece.pd",
			"uvs"		=>"video/vnd.dece.sd",
			"uvvs"		=>"video/vnd.dece.sd",
			"uvv"		=>"video/vnd.dece.video",
			"uvvv"		=>"video/vnd.dece.video",
			"dvb"		=>"video/vnd.dvb.file",
			"fvt"		=>"video/vnd.fvt",
			"mxu"		=>"video/vnd.mpegurl",
			"m4u"		=>"video/vnd.mpegurl",
			"pyv"		=>"video/vnd.ms-playready.media.pyv",
			"uvu"		=>"video/vnd.uvvu.mp4",
			"uvvu"		=>"video/vnd.uvvu.mp4",
			"viv"		=>"video/vnd.vivo",
			"webm"		=>"video/webm",
			"f4v"		=>"video/x-f4v",
			"fli"		=>"video/x-fli",
			"flv"		=>"video/x-flv",
			"m4v"		=>"video/x-m4v",
			"mkv"		=>"video/x-matroska",
			"mk3d"		=>"video/x-matroska",
			"mks"		=>"video/x-matroska",
			"mng"		=>"video/x-mng",
			"asf"		=>"video/x-ms-asf",
			"asx"		=>"video/x-ms-asf",
			"vob"		=>"video/x-ms-vob",
			"wm"		=>"video/x-ms-wm",
			"wmv"		=>"video/x-ms-wmv",
			"wmx"		=>"video/x-ms-wmx",
			"wvx"		=>"video/x-ms-wvx",
			"avi"		=>"video/x-msvideo",
			"movie"		=>"video/x-sgi-movie",
			"smv"		=>"video/x-smv",
			"ice"		=>"x-conference/x-cooltalk"
		);
		if (empty($content_types[$info['extension']])){return 'text/html; charset=UTF-8';}
		return $content_types[$info['extension']];
	}

#####################################################################
# 	TRANSFORMA UM NUMERO EM PORCENTAGEM
#####################################################################
function pxToPct($elemento=100, $total=100){
	return (100 / $total) * $elemento;
}


#####################################################################
# 	COMPRIME OS ARQUIVOS DO SITE PARA GZIP
#####################################################################
	function gzipWebsite(){
		 $ftp_files = rsearch(ROOT_ADMIN."/","/^.*\.(js|css)$/");
		foreach ($ftp_files as $key => $value) {
				$gzfile 		= $value . '.gz';
				$contentNormal 	= file_get_contents($value);
				$contentGzip 	= gzencode($contentNormal,9);
				file_put_contents($gzfile,$contentGzip);
				unlink($value);
		}
	}
	############################################################################################################################
	#	CASO ESTEJA LOGADO DIRETAMENTE COM SERIALKEY
	############################################################################################################################
		function verifyUserLogin($return=false){
				if(ws::urlPath(3,false)){
					$keyAccess = ws::getTokenRest(ws::urlPath(3,false),false);
				}elseif(ws::urlPath(2,false)){
					$keyAccess = ws::getTokenRest(ws::urlPath(2,false),false);
				}
				$log_session = new session();
				if(	
					(SECURE==TRUE && $keyAccess==false) &&
					($log_session->verifyLogin()!=true)
				){
					$log_session->finish();
					if($return==true){ 
						return false;
					}else{
						echo '<script>
									document.cookie.split(";").forEach(function(c) {document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/");}); 
									if(window.location.pathname=="/admin/"){
										window.top.location.reload();
									}else{
										window.top.location = "/admin/";
									}
							</script>';
						exit;
					}
				}else{
					if($return==true){ 
						return true;
					}
				}
		}
		
	function aplicaRascunho($ws_id_ferramenta,$id_item,$apenasAplica=false){
			global $_conectMySQLi_;
			if($apenasAplica==true){goto apenasAplica;}
			##########################################################################################################
			# SEPARA OS CAMPOS UTILIZADOS NA FERRAMENTA
			##########################################################################################################
				$campos							= new MySQL();
				$campos->set_table(PREFIX_TABLES.'_model_campos');
				$campos->set_order(	"posicao","ASC");
				$campos->set_where(	'ws_id_ferramenta="'.$ws_id_ferramenta.'"');
				$campos->select();

			##########################################################################################################
			# SELECIONA O RASCUNHO A SER APLICADO
			##########################################################################################################
				$get_draft				= new MySQL();
				$get_draft->set_table(PREFIX_TABLES."_model_item");
				$get_draft->set_where('ws_draft="1"');
				$get_draft->set_where('AND ws_id_draft="'.$id_item.'"');
				$get_draft->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
				$get_draft->select();
				if($get_draft->_num_rows==0){
					die('Não existe rascunho cadastrado deste ítem');
				}
				$rascunho = $get_draft->fetch_array[0];
			##########################################################################################################
			# ABRE OS DADOS DO ÍTEM A SER ALTERADO
			##########################################################################################################
				$Set_Item				= new MySQL();
				$Set_Item->set_table(PREFIX_TABLES.'_model_item');
				$Set_Item->set_where(PREFIX_TABLES.'_model_item.id="'.$id_item.'"');

			##########################################################################################################
			# ADICIONA OS REGISTROS NOS CAMPOS ADICIONADOS DA FERRAMENTA
			##########################################################################################################
				foreach ($campos->fetch_array as $value) {
					if($value['coluna_mysql']!=""){
						$rascunhoSave = mysqli_real_escape_string($_conectMySQLi_,urldecode($rascunho[$value['coluna_mysql']]));
						$Set_Item->set_update($value['coluna_mysql'], $rascunhoSave);
					}
				}
				if($Set_Item->salvar()){
					apenasAplica:
					##########################################################################################################
					# EXCLUI O RASCUNHO DO ÍTEM
					##########################################################################################################
						$get_draft				= new MySQL();
						$get_draft->set_table(PREFIX_TABLES."_model_item");
						$get_draft->set_where('ws_draft="1"');
						$get_draft->set_where('AND ws_id_draft="'.$id_item.'"');
						$get_draft->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
						$get_draft->exclui();

					##########################################################################################################
					# EXCLUI OS REGISTROS DAS IMAGENS DO ÍTEM ORIGINAL
					##########################################################################################################
						$ExclIMGs				= new MySQL();
						$ExclIMGs->set_table(PREFIX_TABLES."_model_img");
						$ExclIMGs->set_where('ws_draft="0"');
						$ExclIMGs->set_where('AND ws_id_draft="0"');
						$ExclIMGs->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
						$ExclIMGs->set_where('AND id_item="'.$id_item.'"');
						if($apenasAplica==true){
							$ApenasAplicaQuery = new MySQL();
							$ApenasAplicaQuery->select("SELECT COUNT(*) as count FROM ".PREFIX_TABLES."_model_img where(ws_draft='1' AND ws_id_ferramenta='".$ws_id_ferramenta."' AND id_item='".$id_item."')");
							$ExclIMGs->set_where('AND '.$ApenasAplicaQuery->obj[0]->count.'>0');
						}
						$ExclIMGs->exclui();
					##########################################################################################################
					# AGORA HABILITA COMO ORIGINAL OS REGISTROS DO RASCUNHO
					##########################################################################################################
						$Set_img				= new MySQL();
						$Set_img->set_table(PREFIX_TABLES.'_model_img');
						$Set_img->set_where('ws_draft="1" AND ws_id_draft="'.$id_item.'"');
						$Set_img->set_update("ws_draft","0");
						$Set_img->set_update("ws_id_draft","0");
						$Set_img->salvar();

					##########################################################################################################
					# EXCLUI AS GALERIAS ORIGINAIS
					##########################################################################################################
						$ExclGal = new MySQL();
						$ExclGal->set_table(PREFIX_TABLES.'_model_gal');
						$ExclGal->set_where('ws_draft="0"');
						$ExclGal->set_where('AND ws_id_draft="0"');
						$ExclGal->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
						$ExclGal->set_where('AND id_item="'.$id_item.'"');
						if($apenasAplica==true){
							$ApenasAplicaQuery = new MySQL();
							$ApenasAplicaQuery->select("SELECT COUNT(*) as contador FROM ".PREFIX_TABLES."_model_gal where(ws_draft='1' AND ws_id_ferramenta='".$ws_id_ferramenta."' AND id_item='".$id_item."' AND ws_id_draft='".$id_item."')");
							$ExclGal->set_where('AND '.$ApenasAplicaQuery->obj[0]->contador.'>0');
						}				
						$ExclGal->exclui();

					##########################################################################################################
					# APLICANDO AS GALERIAS DE FOTOS
					##########################################################################################################
						$Set_img = new MySQL();
						$Set_img->set_table(PREFIX_TABLES.'_model_gal');
						$Set_img->set_where('ws_draft="1" AND ws_id_draft="'.$id_item.'"');
						$Set_img->set_update("ws_draft","0");
						$Set_img->set_update("ws_id_draft","0");
						$Set_img->salvar();

					##########################################################################################################
					# EXCLUI AS IMAGENS DAS GALERIAS ORIGINAIS
					##########################################################################################################
						$ExclGal = new MySQL();
						$ExclGal->set_table(PREFIX_TABLES.'_model_img_gal');
						$ExclGal->set_where('ws_draft="0" AND ws_id_draft="0" AND ws_id_ferramenta="'.$ws_id_ferramenta.'" AND id_item="'.$id_item.'"');
						if($apenasAplica==true){
							$ApenasAplicaQuery = new MySQL();
							$ApenasAplicaQuery->select("SELECT COUNT(*) as contador FROM ".PREFIX_TABLES."_model_img_gal where(ws_draft='1' AND ws_id_ferramenta='".$ws_id_ferramenta."' AND id_item='".$id_item."' AND ws_id_draft='".$id_item."')");
							$ExclGal->set_where('AND '.$ApenasAplicaQuery->obj[0]->contador.'>0');
						}
						$ExclGal->exclui();

					##########################################################################################################
					# APLICANDO AS IMAGENS NAS GALERIAS DE FOTOS
					##########################################################################################################
						$Set_img = new MySQL();
						$Set_img->set_table(PREFIX_TABLES.'_model_img_gal');
						$Set_img->set_where('ws_draft="1" AND ws_id_draft="'.$id_item.'"');
						$Set_img->set_update("ws_draft","0");
						$Set_img->set_update("ws_id_draft","0");
						$Set_img->salvar();

					##########################################################################################################
					# EXCLUI OS REGISTROS DOS ARQUIVOS DO ÍTEM ORIGINAL
					##########################################################################################################
						$ExclFiles				= new MySQL();
						$ExclFiles->set_table(PREFIX_TABLES."_model_files");
						$ExclFiles->set_where('ws_draft="0"');
						$ExclFiles->set_where('AND ws_id_draft="0"');
						$ExclFiles->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
						$ExclFiles->set_where('AND id_item="'.$id_item.'"');
						if($apenasAplica==true){						
							$ApenasAplicaQuery = new MySQL();
							$ApenasAplicaQuery->select("SELECT COUNT(*) as contador FROM ".PREFIX_TABLES."_model_files where(ws_draft='1' AND ws_id_ferramenta='".$ws_id_ferramenta."' AND id_item='".$id_item."' AND ws_id_draft='".$id_item."')");
							$ExclFiles->set_where('AND '.$ApenasAplicaQuery->obj[0]->contador.'>0');
						}
						$ExclFiles->exclui();

					##########################################################################################################
					# AGORA HABILITA COMO ORIGINAL OS REGISTROS DO RASCUNHO
					##########################################################################################################
						$Set_files				= new MySQL();
						$Set_files->set_table(PREFIX_TABLES.'_model_files');
						$Set_files->set_where('ws_draft="1" AND ws_id_draft="'.$id_item.'"');
						$Set_files->set_update("ws_draft","0");
						$Set_files->set_update("ws_id_draft","0");
						$Set_files->salvar();

					##########################################################################################################
					# EXCLUI OS REGISTROS DOS RELACIONAMENTOS ORIGINAIS
					##########################################################################################################
						$ExclLink				= new MySQL();
						$ExclLink->set_table(PREFIX_TABLES."_model_link_prod_cat");
						$ExclLink->set_where(' ws_draft="0" ');
						$ExclLink->set_where('AND ws_id_draft="0" ');
						$ExclLink->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
						$ExclLink->set_where('AND id_item="'.$id_item.'"');
						$ExclLink->exclui();

					##########################################################################################################
					# AGORA HABILITA COMO ORIGINAL OS RASCUNHOS
					##########################################################################################################
						$Set_Link				= new MySQL();
						$Set_Link->set_table(PREFIX_TABLES.'_model_link_prod_cat');
						$Set_Link->set_where('ws_draft="1" AND ws_id_draft="'.$id_item.'"');
						$Set_Link->set_where('AND id_item="'.$id_item.'"');
						$Set_Link->set_update("ws_draft","0");
						$Set_Link->set_update("ws_id_draft","0");
						$Set_Link->salvar();
						
					##########################################################################################################
					# EXCLUI OS REGISTROS DOS RELACIONAMENTOS ORIGINAIS
					##########################################################################################################
						$ExclLink				= new MySQL();
						$ExclLink->set_table(PREFIX_TABLES."ws_link_itens");
						$ExclLink->set_where(' ws_draft="0"  AND ws_id_draft="0"  AND id_item="'.$id_item.'"');
						$ExclLink->exclui();

					##########################################################################################################
					# AGORA HABILITA COMO ORIGINAL OS RASCUNHOS
					##########################################################################################################
						$Set_Link				= new MySQL();
						$Set_Link->set_table(PREFIX_TABLES.'ws_link_itens');
						$Set_Link->set_where('ws_draft="1" AND ws_id_draft="'.$id_item.'" AND id_item="'.$id_item.'"');
						$Set_Link->set_update("ws_draft","0");
						$Set_Link->set_update("ws_id_draft","0");
						$Set_Link->salvar();

					##########################################################################################################
					# END
					##########################################################################################################
				};
			return true;
	}
	
function criaRascunho($ws_id_ferramenta=0,$id_item=null, $imagens=false){
		global $_conectMySQLi_;
		##########################################################################################################
		# VERIFICA SE JÁ TEM RASCUNHO
		##########################################################################################################
			$draft				= new MySQL();
			$draft->set_table(PREFIX_TABLES."_model_item");
			$draft->set_where('ws_draft="1"');
			$draft->set_where('AND ws_id_draft="'.$id_item.'"');
			$draft->select();
		##########################################################################################################
		# VERIFICA SE É´PARA GERAR APENAS RASCUNHOS DAS IMAGENS E ARQUIVOS INTERNOS
		##########################################################################################################
		if($imagens==true){goto imagens;}
		##########################################################################################################
		# CASO NÃO TENHA CRIA UM RASCUNHO, CLONAMOS O ORIGINAL PARA O RASCUNHO 
		##########################################################################################################
			if($draft->_num_rows==0){
				##########################################################################################################
				# SEPARA O ÍTEM ORIGINAL
				##########################################################################################################
					$get_produto	= new MySQL();
					$get_produto->set_table(PREFIX_TABLES.'_model_item');
					$get_produto->set_where(PREFIX_TABLES.'_model_item.id="'.$id_item.'"');
					$get_produto->select();
				##########################################################################################################
				# INICIA A CÓPIA
				##########################################################################################################
					$Set_Draft	= new MySQL();
					$Set_Draft->set_table(PREFIX_TABLES.'_model_item');
					$Set_Draft->set_insert('ws_draft','1');
					$Set_Draft->set_insert('ws_id_draft',$id_item);
					$Set_Draft->set_insert('ws_id_ferramenta',$ws_id_ferramenta);
					$Set_Draft->set_insert('token', $get_produto->fetch_array[0]['token']);
				##########################################################################################################
				# SEPARAMOS OS CAMPOS DESTE ÍTEM
				##########################################################################################################
					$campos							= new MySQL();
					$campos->set_table(PREFIX_TABLES.'_model_campos');
					$campos->set_order(	"posicao","ASC");
					$campos->set_where(	'ws_id_ferramenta="'.$ws_id_ferramenta.'"');
					$campos->select();
					foreach ($campos->fetch_array as $value) {
						if($value['coluna_mysql']!=""){
							$Set_Draft->set_insert($value['coluna_mysql'], mysqli_real_escape_string($_conectMySQLi_,urldecode($get_produto->fetch_array[0][$value['coluna_mysql']])));
						}
					}
					$Set_Draft->insert();

				imagens:
				##########################################################################################################
				# GERA RASCUNHO DAS IMAGENS DIRETAS
				##########################################################################################################
					$getIMGs				= new MySQL();
					$getIMGs->set_table(PREFIX_TABLES."_model_img");
					$getIMGs->set_where('ws_draft="0"');
					$getIMGs->set_where('AND ws_id_draft="0"');
					$getIMGs->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
					$getIMGs->set_where('AND id_item="'.$id_item.'"');
					$getIMGs->select();
					$draftIMG				= new MySQL();
					$draftIMG->set_table(PREFIX_TABLES."_model_img");
					$draftIMG->set_where('ws_draft="1"');
					$draftIMG->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
					$draftIMG->set_where('AND ws_id_draft="'.$id_item.'"');
					$draftIMG->select();
					//CASO NÃO TENHA RASCUNHO AINDA
					if($draftIMG->_num_rows<1 && $getIMGs->_num_rows>0){
						foreach ($getIMGs->fetch_array as $valueImg) {
							$Set_DraftIMG	= new MySQL();
							$Set_DraftIMG->set_table(PREFIX_TABLES.'_model_img');
							$Set_DraftIMG->set_insert('ws_draft',			'1');
							$Set_DraftIMG->set_insert('ws_id_draft',		$id_item);
							$Set_DraftIMG->set_insert('ws_type',			$valueImg['ws_type']);
							$Set_DraftIMG->set_insert('avatar',				$valueImg['avatar']);
							$Set_DraftIMG->set_insert('ws_id_ferramenta',	$ws_id_ferramenta);
							$Set_DraftIMG->set_insert('ws_tool_item',		$valueImg['ws_tool_item']);
							$Set_DraftIMG->set_insert('id_item',			$id_item);
							$Set_DraftIMG->set_insert('id_cat',				$valueImg['id_cat']);
							$Set_DraftIMG->set_insert('ws_nivel',			$valueImg['ws_nivel']);
							$Set_DraftIMG->set_insert('posicao',			$valueImg['posicao']);
							$Set_DraftIMG->set_insert('painel',				$valueImg['painel']);
							$Set_DraftIMG->set_insert('titulo',				$valueImg['titulo']);
							$Set_DraftIMG->set_insert('url',				$valueImg['url']);
							$Set_DraftIMG->set_insert('texto',				$valueImg['texto']);
							$Set_DraftIMG->set_insert('imagem',				$valueImg['imagem']);
							$Set_DraftIMG->set_insert('filename',			$valueImg['filename']);
							$Set_DraftIMG->set_insert('token',				$valueImg['token']);
							$Set_DraftIMG->insert();
						}
					}
				##########################################################################################################
				# GERA RASCUNHO DAS GALERIAS E SUAS IMAGENS
				##########################################################################################################
					##########################################################################################################
					# GERA RASCUNHO DAS GALERIAS E SUAS IMAGENS
					##########################################################################################################
						$draftGals				= new MySQL();
						$draftGals->set_table(PREFIX_TABLES."_model_gal");
						$draftGals->set_where('ws_draft="1"');
						$draftGals->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
						$draftGals->set_where('AND ws_id_draft="'.$id_item.'"');
						$draftGals->select();
						$getGALS				= new MySQL();
						$getGALS->set_table(PREFIX_TABLES."_model_gal");
						$getGALS->set_where('ws_draft="0"');
						$getGALS->set_where('AND ws_id_draft="0"');
						$getGALS->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
						$getGALS->set_where('AND id_item="'.$id_item.'"');
						$getGALS->select();

						if($draftGals->_num_rows<1 && $getGALS->_num_rows>0){
							foreach ($getGALS->fetch_array as $valueGal){
								##########################################################################################################
								# CLONA A GALERIA DO LOOP
								##########################################################################################################
									$Set_DraftGal	= new MySQL();
									$Set_DraftGal->set_table(PREFIX_TABLES.'_model_gal');
									$Set_DraftGal->set_insert('ws_id_draft',		$id_item);
									$Set_DraftGal->set_insert('ws_draft',			'1');
									$Set_DraftGal->set_insert('ws_type',			$valueGal['ws_type']);
									$Set_DraftGal->set_insert('ws_id_ferramenta',	$ws_id_ferramenta);
									$Set_DraftGal->set_insert('ws_tool_id',			$id_item);
									$Set_DraftGal->set_insert('ws_tool_item',		$id_item);
									$Set_DraftGal->set_insert('ws_nivel',			$valueGal['ws_nivel']);
									$Set_DraftGal->set_insert('id_cat',				$valueGal['id_cat']);
									$Set_DraftGal->set_insert('id_item',			$id_item);
									$Set_DraftGal->set_insert('posicao',			$valueGal['posicao']);
									$Set_DraftGal->set_insert('avatar',				$valueGal['avatar']);
									$Set_DraftGal->set_insert('filename',			$valueGal['filename']);
									$Set_DraftGal->set_insert('titulo',				$valueGal['titulo']);
									$Set_DraftGal->set_insert('token',				$valueGal['token']);
									$Set_DraftGal->set_insert('texto',				$valueGal['texto']);
									$Set_DraftGal->set_insert('url	',				$valueGal['url']);
									$Set_DraftGal->insert();
								##########################################################################################################
								# PEGA O ID DA GALERIA ADICIONADA
								##########################################################################################################
									$CloneGal	= new MySQL();
									$CloneGal->set_table(PREFIX_TABLES.'_model_gal');
									$CloneGal->set_order('id','DESC');
									$CloneGal->set_colum('id');
									$CloneGal->set_limit(1);
									$CloneGal->select();
									$idCloneGal = $CloneGal->fetch_array[0]['id'];
								##########################################################################################################
								# SELECIONA AS IMAGENS DESSA GALERIA
								##########################################################################################################
									$imgGaleria				= new MySQL();
									$imgGaleria->set_table(PREFIX_TABLES."_model_img_gal");
									$imgGaleria->set_where('id_galeria="'.$value['id'].'"');
									$imgGaleria->select();							
								##########################################################################################################
								# AGORA CLONA OS REGISTROS DAS IMAGENS DA GALERIA ORIGINAL COM A REFERENCIA DESSA GALERIA CLONADA
								##########################################################################################################
									foreach ($imgGaleria->fetch_array as $imgVal){
										$Set_Draft_img_Gal	= new MySQL();
										$Set_Draft_img_Gal->set_table(PREFIX_TABLES.'_model_img_gal');
										$Set_Draft_img_Gal->set_insert('ws_draft',			'1');
										$Set_Draft_img_Gal->set_insert('ws_id_draft',		$id_item);
										$Set_Draft_img_Gal->set_insert('id_galeria',		$idCloneGal);//ID DA GALERIA CLONADA
										$Set_Draft_img_Gal->set_insert('ws_type',			$imgVal['ws_type']);
										$Set_Draft_img_Gal->set_insert('ws_id_ferramenta',	$ws_id_ferramenta);
										$Set_Draft_img_Gal->set_insert('ws_tool_id',		$imgVal['ws_tool_id']);
										$Set_Draft_img_Gal->set_insert('ws_tool_item',		$imgVal['ws_tool_item']);
										$Set_Draft_img_Gal->set_insert('id_item',			$id_item);
										$Set_Draft_img_Gal->set_insert('id_cat',			$imgVal['id_cat']);
										$Set_Draft_img_Gal->set_insert('posicao',			$imgVal['posicao']);
										$Set_Draft_img_Gal->set_insert('ws_nivel',			$imgVal['ws_nivel']);
										$Set_Draft_img_Gal->set_insert('titulo',			$imgVal['titulo']);
										$Set_Draft_img_Gal->set_insert('url',				$imgVal['url']);
										$Set_Draft_img_Gal->set_insert('texto',				$imgVal['texto']);
										$Set_Draft_img_Gal->set_insert('imagem',			$imgVal['imagem']);
										$Set_Draft_img_Gal->set_insert('filename',			$imgVal['filename']);
										$Set_Draft_img_Gal->set_insert('file',				$imgVal['file']);
										$Set_Draft_img_Gal->set_insert('avatar',			$imgVal['avatar']);
										$Set_Draft_img_Gal->set_insert('token',				$imgVal['token']);
										$Set_Draft_img_Gal->insert();
									}
							}
						}
					##########################################################################################################
					# GERA RASCUNHO DOS ARQUIVOS
					##########################################################################################################
						$getFiles				= new MySQL();
						$getFiles->set_table(PREFIX_TABLES."_model_files");
						$getFiles->set_where('ws_draft="0"');
						$getFiles->set_where('AND ws_id_draft="0"');
						$getFiles->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
						$getFiles->set_where('AND id_item="'.$id_item.'"');
						$getFiles->select();

						$draftFiles				= new MySQL();
						$draftFiles->set_table(PREFIX_TABLES."_model_files");
						$draftFiles->set_where('ws_draft="1"');
						$draftFiles->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
						$draftFiles->set_where('AND ws_id_draft="'.$id_item.'"');
						$draftFiles->set_where('AND id_item="'.$id_item.'"');
						$draftFiles->select();
						//CASO NÃO TENHA RASCUNHO AINDA E TENHA ARQUIVOS NO ORIGINAL
						if($draftFiles->_num_rows<1 && $getFiles->_num_rows>0){
							foreach ($getFiles->fetch_array as $valueFile) {
								$Set_DraftFiles	= new MySQL();
								$Set_DraftFiles->set_table(PREFIX_TABLES.'_model_files');
								$Set_DraftFiles->set_insert('ws_id_draft',		$id_item);
								$Set_DraftFiles->set_insert('ws_draft',			'1');
								$Set_DraftFiles->set_insert('ws_type',			$valueFile['ws_type']);
								$Set_DraftFiles->set_insert('ws_id_ferramenta',	$ws_id_ferramenta);
								$Set_DraftFiles->set_insert('ws_tool_id',		$valueFile['ws_tool_id']);
								$Set_DraftFiles->set_insert('ws_tool_item',		$valueFile['ws_tool_item']);
								$Set_DraftFiles->set_insert('id_item',			$id_item);
								$Set_DraftFiles->set_insert('id_cat',			$valueFile['id_cat']);
								$Set_DraftFiles->set_insert('ws_nivel',			$valueFile['ws_nivel']);
								$Set_DraftFiles->set_insert('posicao',			$valueFile['posicao']);
								$Set_DraftFiles->set_insert('uploaded',			$valueFile['uploaded']);
								$Set_DraftFiles->set_insert('titulo',			$valueFile['titulo']);
								$Set_DraftFiles->set_insert('painel',			$valueFile['painel']);
								$Set_DraftFiles->set_insert('url',				$valueFile['url']);
								$Set_DraftFiles->set_insert('texto',			$valueFile['texto']);
								$Set_DraftFiles->set_insert('file',				$valueFile['file']);
								$Set_DraftFiles->set_insert('filename',			$valueFile['filename']);
								$Set_DraftFiles->set_insert('token',			$valueFile['token']);
								$Set_DraftFiles->set_insert('size_file',		$valueFile['size_file']);
								$Set_DraftFiles->set_insert('download',			$valueFile['download']);
								$Set_DraftFiles->insert();
							}
						}
				##########################################################################################################
				# GERA RASCUNHO DO RELACIONAMENTO DE CATEGORIAS
				##########################################################################################################
					$getCat				= new MySQL();
					$getCat->set_table(PREFIX_TABLES."_model_link_prod_cat");
					$getCat->set_where('ws_draft="0"');
					$getCat->set_where('AND ws_id_draft="0"');
					$getCat->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
					$getCat->set_where('AND id_item="'.$id_item.'"');
					$getCat->select();

					$draftLink				= new MySQL();
					$draftLink->set_table(PREFIX_TABLES."_model_link_prod_cat");
					$draftLink->set_where('ws_draft="1"');
					$draftLink->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
					$draftLink->set_where('AND ws_id_draft="'.$id_item.'"');
					$draftLink->set_where('AND id_item="'.$id_item.'"');
					$draftLink->select();
					//CASO NÃO TENHA RASCUNHO E TENHA CATEGORIAS NO ORIGINAL
					if($draftLink->_num_rows<1 && $getCat->_num_rows>0){
						foreach ($getCat->fetch_array as $valueCat) {
							$Set_Cat	= new MySQL();
							$Set_Cat->set_table(PREFIX_TABLES.'_model_link_prod_cat');
							$Set_Cat->set_insert('ws_id_draft',		$id_item);
							$Set_Cat->set_insert('ws_draft',		'1');
							$Set_Cat->set_insert('id_cat',			$valueCat['id_cat']);
							$Set_Cat->set_insert('ws_id_ferramenta',$ws_id_ferramenta);
							$Set_Cat->set_insert('id_item',		$valueCat['id_item']);
							$Set_Cat->set_insert('ws_tool_id',		$valueCat['ws_tool_id']);
							$Set_Cat->set_insert('ws_tool_item',	$id_item);
							$Set_Cat->set_insert('ws_nivel',		$valueCat['ws_nivel']);
							$Set_Cat->insert();
						}
					}
				##########################################################################################################
				# GERA RASCUNHO DO RELACIONAMENTO ENTRE ITENS
				##########################################################################################################
					$getLinkProd				= new MySQL();
					$getLinkProd->set_table(PREFIX_TABLES."ws_link_itens");
					$getLinkProd->set_where('ws_draft="0"');
					$getLinkProd->set_where('AND ws_id_draft="0"');
					$getLinkProd->set_where('AND id_item="'.$id_item.'"');
					$getLinkProd->select();
					$draftLinkProd				= new MySQL();
					$draftLinkProd->set_table(PREFIX_TABLES."ws_link_itens");
					$draftLinkProd->set_where('ws_draft="1"');
					$draftLinkProd->set_where('AND ws_id_draft="'.$id_item.'"');
					$draftLinkProd->set_where('AND id_item="'.$id_item.'"');
					$draftLinkProd->select();
					//CASO NÃO TENHA RASCUNHO E TENHA CATEGORIAS NO ORIGINAL
					if($draftLinkProd->_num_rows<1 && $getLinkProd->_num_rows>0){
						foreach ($getLinkProd->fetch_array as $valueCat) {
							$Set_Cat	= new MySQL();
							$Set_Cat->set_table(PREFIX_TABLES.'ws_link_itens');
							$Set_Cat->set_insert('ws_id_draft',		$id_item);
							$Set_Cat->set_insert('ws_draft',		'1');
							$Set_Cat->set_insert('id_item',			$valueCat['id_item']);
							$Set_Cat->set_insert('id_item_link',	$valueCat['id_item_link']);
							$Set_Cat->set_insert('id_cat_link',		$valueCat['id_cat_link']);
							$Set_Cat->insert();
						}
					}

				##########################################################################################################
				# GERA RASCUNHO DOS ARQUIVOS DIRETOS
				##########################################################################################################

					$getFILES 				= new MySQL();
					$getFILES->set_table(PREFIX_TABLES."_model_files");
					$getFILES->set_where('ws_draft="0"');
					$getFILES->set_where('AND ws_id_draft="0"');
					$getFILES->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
					$getFILES->set_where('AND id_item="'.$id_item.'"');
					$getFILES->select();

					$draftFILES				= new MySQL();
					$draftFILES->set_table(PREFIX_TABLES."_model_files");
					$draftFILES->set_where('ws_draft="1"');
					$draftFILES->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
					$draftFILES->set_where('AND ws_id_draft="'.$id_item.'"');
					$draftFILES->select();

					//CASO NÃO TENHA RASCUNHO AINDA
					if($draftFILES->_num_rows<1 && $getFILES->_num_rows>0){
						foreach ($getFILES->fetch_array as $valueFile) {
							$Set_DraftIMG	= new MySQL();
							$Set_DraftIMG->set_table(PREFIX_TABLES.'_model_files');
							$Set_DraftIMG->set_insert('ws_draft',			'1');
							$Set_DraftIMG->set_insert('ws_id_draft',		$id_item);
							$Set_DraftIMG->set_insert('id_item',			$id_item);
							$Set_DraftIMG->set_insert('ws_id_ferramenta',	$ws_id_ferramenta);
							$Set_DraftIMG->set_insert('ws_type',			$valueFile['ws_type']);
							$Set_DraftIMG->set_insert('ws_tool_id',			$valueFile['ws_tool_id']);
							$Set_DraftIMG->set_insert('ws_tool_item',		$valueFile['ws_tool_item']);
							$Set_DraftIMG->set_insert('id_cat',				$valueFile['id_cat']);
							$Set_DraftIMG->set_insert('ws_nivel',			$valueFile['ws_nivel']);
							$Set_DraftIMG->set_insert('posicao',			$valueFile['posicao']);
							$Set_DraftIMG->set_insert('titulo',				$valueFile['titulo']);
							$Set_DraftIMG->set_insert('painel',				$valueFile['painel']);
							$Set_DraftIMG->set_insert('url',				$valueFile['url']);
							$Set_DraftIMG->set_insert('texto',				$valueFile['texto']);
							$Set_DraftIMG->set_insert('file',				$valueFile['file']);
							$Set_DraftIMG->set_insert('filename',			$valueFile['filename']);
							$Set_DraftIMG->set_insert('token',				$valueFile['token']);
							$Set_DraftIMG->set_insert('size_file',			$valueFile['size_file']);
							$Set_DraftIMG->set_insert('download',			$valueFile['download']);
							$Set_DraftIMG->set_insert('uploaded',			$valueFile['uploaded']);
							$Set_DraftIMG->insert();
						}
					}

				############################################### END ######################################################
				return true;
			}

		##########################################################################################################
		# FIM (apenas se ñ tiover rascunho do ítem)
		##########################################################################################################
}

function createPass($tamanho = 8, $maiusculas = true, $numeros = true, $simbolos = false){
	$lmin = 'abcdefghijklmnopqrstuvwxyz';
	$lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$num  = '1234567890';
	$simb = '!@#$%*-';
	$retorno = '';
	$caracteres = '';
	$caracteres .= $lmin;
	if ($maiusculas) $caracteres .= $lmai;
	if ($numeros) $caracteres .= $num;
	if ($simbolos) $caracteres .= $simb;
	$len = strlen($caracteres);
	for ($n = 1; $n <= $tamanho; $n++) {
		$rand = mt_rand(1, $len);
		$retorno .= $caracteres[$rand-1];
	}
	return $retorno;
}

##########################################################################################################
# FUNÇÃO QUE CRIA O JSON COM A LISTA DOS PLUGINS INSTALADOS
##########################################################################################################

function refreshJsonPluginsList(){
	$setupdata 	= new MySQL();
	$setupdata->set_table(PREFIX_TABLES.'setupdata');
	$setupdata->set_order('id','DESC');
	$setupdata->set_limit(1);
	$setupdata->debug(0);
	$setupdata->select();
	$setupdata = $setupdata->fetch_array[0];
	//################################################################################################
	$_path_plugin_ = ROOT_WEBSITE.'/'.$setupdata['url_plugin']; 
	$json_plugins = array();
	if(is_dir($_path_plugin_)){
		$dh = opendir($_path_plugin_);
		while($diretorio = readdir($dh)){
			if($diretorio != '..' && $diretorio != '.' && $diretorio != '.htaccess' ){
				$phpConfig 	= $_path_plugin_.'/'.$diretorio.'/plugin.config.php';
				if(file_exists($phpConfig)){
					ob_start();
					@include($phpConfig);
					$jsonRanderizado=ob_get_clean();
					$contents=$plugin;
				}
				$itemArray = Array();
				if(file_exists($_path_plugin_.'/'.$diretorio.'/active')){
					@$contents->{'active'}="yes";
				}else{
					@$contents->{'active'}="no";
				}
				$contents->{'realPath'}=str_replace(ROOT_WEBSITE,'',$_path_plugin_).'/'.$diretorio;
				//################################################################################################
				$json_plugins[] = $contents;
			}
		}
	}
	file_put_contents(ROOT_ADMIN.'/App/Templates/json/ws-plugin-list.json', json_encode($json_plugins,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
}
function print_pre($str){
	echo "<pre>";
	print_r($str);
	echo "</pre>";
}
function json_decode_nice($json, $assoc = TRUE){
    $json = str_replace("\n","\\n",$json);
    $json = str_replace("\r","",$json);
    $json = preg_replace('/([{,]+)(\s*)([^"]+?)\s*:/','$1"$3":',$json);
    $json = preg_replace('/(,)\s*}$/','}',$json);
    return json_decode($json,$assoc);
}
function CopiaDir($DirFont, $DirDest){
	if(!file_exists($DirDest)){mkdir($DirDest); }
    if ($dd = opendir($DirFont)) {
        while (false !== ($Arq = readdir($dd))) {
            if($Arq != "." && $Arq != ".."){
                $PathIn = "$DirFont/$Arq";
                $PathOut = "$DirDest/$Arq";
                if(is_dir($PathIn)){
                    CopiaDir($PathIn, $PathOut);
                }elseif(is_file($PathIn)){
                    copy($PathIn, $PathOut);
                }
            }
        }
        closedir($dd);
    }
}
function installExternalTool($webtool=null,$grupoPai=null){

	if(isset($_REQUEST['base64'])){ 
		####################################################################
		# TRANSFORMAMOS O bse64 NO REQUEST MASTER
		####################################################################
		$_REQUEST = $_REQUEST['base64'];
		####################################################################
		# para ferramentas antigas que nao tinham prefixo ainda nas tabelas
		####################################################################
		$content =	str_replace('{PREFIX_TABLES}',PREFIX_TABLES, base64_decode($_REQUEST['base64']));
		goto processa;
	}

	if($webtool==null){		echo "Insira um arquivo na função";	exit;}
	if($grupoPai==null){	echo "Insira um grupo pai";			exit;}
	$pathinfo 	= pathinfo($webtool);
	$ext 		= $pathinfo['extension'];
	if($ext=="ws"){
		include(ROOT_ADMIN.'/App/Lib/class-base2n.php');
		$binary 	= new Base2n(6);
		$content	= $binary->decode(file_get_contents($webtool));
	}elseif($ext=="json"){
		$content		=	file_get_contents($webtool);
	}

	processa:
	if(isset($_REQUEST['prefix'])){$prefix   = $_REQUEST['prefix'];}else{$prefix = "";}
	if(isset($_REQUEST['base64'])){
		$getAll				=	array(json_decode($content,true));
	}else{
		$getAll				=	json_decode($content,true);
	}


	foreach ($getAll as $newTool){
		$token 		= _token(PREFIX_TABLES.'ws_ferramentas','token');
		$colunasListItens 	=	explode(',',$newTool['det_listagem_item']);
		$colunasListPrefix 	= 	Array();
		foreach ($colunasListItens as $val){$colunasListPrefix[] = $prefix.$val;};
		$colunasListItens 	=	implode(array_map("duplicateColumName",$colunasListPrefix),',');
		$ferramenta 		=	str_replace(
											array(
												'{{prefix}}',
												'{{token}}',
												'{{grupo_pai}}',
												'{{det_listagem_item}}',
												'{{slugTool}}',
												'{{nameTool}}'
											),
											array(
												$prefix,
												$token,
												$grupoPai,
												$colunasListItens,
												$_REQUEST['slugTool'],
												$_REQUEST['nameTool']
											),$newTool['tool']);

		$campos 			=   $newTool['colunas'];
		$insert = new MySQL();
		if($insert->select($ferramenta)){
			$Ferramenta_atual 					= new MySQL();
			$Ferramenta_atual->set_table(PREFIX_TABLES.'ws_ferramentas');
			$Ferramenta_atual->set_where('token="'.$token.'"');
			$Ferramenta_atual->select();
			$ws_id_ferramenta = $Ferramenta_atual->fetch_array[0]['id'];

			if(count($campos)>0){
				$AddColunaItem= new MySQL();
				$AddColunaItem->set_table(PREFIX_TABLES.'_model_item');
				foreach ($campos as $value) {
					if(isset($value['query'])){
						$token 			= _token(PREFIX_TABLES.'_model_campos','token');
						$coluna 		= duplicateColumName($prefix.$value['colum']);
						$value['query'] = str_replace(
						 							array('{{ws_id_ferramenta}}','{{name}}','{{id_campo}}','{{coluna_mysql}}','{{token}}'), 
						 							array($ws_id_ferramenta,$coluna,$coluna,$coluna,$token),
						 							$value['query']);
						 $InsertCampo 	= new MySQL();
						 $InsertCampo   ->select($value['query']);
						 $AddColunaItem->set_colum(array($coluna,$value['insert']));
					}
				}
				$AddColunaItem->add_column();
			}

		}
		return true;
	};
exit;
}
function duplicateColumName($colunaVerificar){
	$i=2;
	$colunasAtuais = array();
	$D = new MySQL();
	$D->set_table(PREFIX_TABLES.'_model_item');
	$D->show_columns();
	foreach ($D->fetch_array as $coluna){$colunasAtuais[] =$coluna['Field'];};
	verificaNovamente:
	if(!in_array($colunaVerificar, $colunasAtuais)){
		//	CASO NAO EXISTA NENHUMA COLUNA COM ESSE NOME ADD NA TABELA
		return $colunaVerificar;exit;
	}else{ //CASO JÁ EXISTA
		//final com o i
		$str = '_'.$i;
		//final atual da coluna
		$finalAtual = substr($colunaVerificar,-strlen($str));
		//Nome da coluna sem o i
		$colunName  = substr($colunaVerificar,0,-strlen($str));
		//verifica se é uma coluna já duplicada, com final _(int)  se for aumenta um valor e verifica
		if($finalAtual==$str){
			$i = $i+1;
			$colunaVerificar = $colunName.'_'.$i;
		}else{
			//se nao for duplicado ou com valor numerico, adiciona _2
			$colunaVerificar = $colunaVerificar.'_'.$i;
		}
	}
	goto verificaNovamente;
}
function _likeString($str) {
	$str = trim(strtolower($str));
	while (strpos($str,"  "))
		$str 					= str_replace("  "," ",$str);
		$caracteresPerigosos 	= array ("Ã","ã","Õ","õ","á","Á","é","É","í","Í","ó","Ó","ú","Ú","ç","Ç","à","À","è","È","ì","Ì","ò","Ò","ù","Ù","ä","Ä","ë","Ë","ï","Ï","ö","Ö","ü","Ü","Â","Ê","Î","Ô","Û","â","ê","î","ô","û","!","?",",","“","”","-","\"","\\","/");
		$caracteresLimpos    	= array ("a","a","o","o","a","a","e","e","i","i","o","o","u","u","c","c","a","a","e","e","i","i","o","o","u","u","a","a","e","e","i","i","o","o","u","u","A","E","I","O","U","a","e","i","o","u",".",".",".",".",".",".","." ,"." ,".");
		$str 					= str_replace($caracteresPerigosos,$caracteresLimpos,$str);
		$caractresSimples 		= array("a","e","i","o","u","c");
		$caractresEnvelopados 	= array("[a]","[e]","[i]","[o]","[u]","[c]");
		$str 					= str_replace($caractresSimples,$caractresEnvelopados,$str);
		$caracteresParaRegExp 	= array(
			"(a|ã|á|à|ä|â|&atilde;|&aacute;|&agrave;|&auml;|&acirc;|Ã|Á|À|Ä|Â|&Atilde;|&Aacute;|&Agrave;|&Auml;|&Acirc;)",
			"(e|é|è|ë|ê|&eacute;|&egrave;|&euml;|&ecirc;|É|È|Ë|Ê|&Eacute;|&Egrave;|&Euml;|&Ecirc;)",
			"(i|í|ì|ï|î|&iacute;|&igrave;|&iuml;|&icirc;|Í|Ì|Ï|Î|&Iacute;|&Igrave;|&Iuml;|&Icirc;)",
			"(o|õ|ó|ò|ö|ô|&otilde;|&oacute;|&ograve;|&ouml;|&ocirc;|Õ|Ó|Ò|Ö|Ô|&Otilde;|&Oacute;|&Ograve;|&Ouml;|&Ocirc;)",
			"(u|ú|ù|ü|û|&uacute;|&ugrave;|&uuml;|&ucirc;|Ú|Ù|Ü|Û|&Uacute;|&Ugrave;|&Uuml;|&Ucirc;)",
			"(c|ç|Ç|&ccedil;|&Ccedil;)" );
		$str = str_replace($caractresEnvelopados,$caracteresParaRegExp,$str);
		$str = utf8_decode(str_replace(" ",".*",$str));
		return $str;
}
function _excluiDir($Dir){
	if(file_exists($Dir) && is_dir($Dir)){
	   if ($dd = opendir($Dir)) {
	        while (false !== ($Arq = readdir($dd))) {
	            if($Arq != "." && $Arq != ".."){
	                $Path = "$Dir/$Arq";
	                if(is_dir($Path)){
	                    _excluiDir($Path);
	                }elseif(is_file($Path)){
	                    unlink($Path);
	                }
	            }
	        }
	        closedir($dd);
	    }
	    rmdir($Dir);
	}else{
		echo "O diretório '".$Dir."' não existe!";
		exit;
	}
}
function _extract()														{	if(extract($_REQUEST)){return true;}else{return false;}};
function _exec($fn)														{	if(_extract() && !empty($_REQUEST['function']) && !empty($fn) && function_exists($fn) ) call_user_func($fn);}
function _crypt()														{	$CodeCru = @crypt(md5(rand(0,50)));$vowels = array("$","/", ".",'=');$onlyconsonants = str_replace($vowels, "", $CodeCru);return substr($onlyconsonants,1);}
function _codePass($senha,$ash="aquiPODEserQUALQUERcoisaPOISéUMhash") 	{	
	$salt 		= md5($ash);
	$codifica 	= crypt($senha,$salt);
	$codifica 	= hash('sha512',$codifica);
	return $codifica;
}
function _token($tabela,$coluna,$type="all"){
	$tk 					=	_crypt($type);
	$setToken				= 	new MySQL();
	$setToken->set_table($tabela);
	$setToken->set_where($coluna.'="'.$tk.'"');
	$setToken->select();
	if($setToken->_num_rows!=0){
		$tk = _crypt();
		_token($tabela,$coluna);
	}else{
		return $tk;
	}
}
function _erro($error)							{echo '<pre style="position: relative;color: #F00;background-color: #FFCB00;font-weight: bold;padding: 10px;">! -- Internal WS Error -- ! '.PHP_EOL.$error.PHP_EOL."</pre>";}
function decodeURIcomponent($smth="")			{
	$smth = preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($smth)); 
	$smth = str_replace(array("<",">"),array("&lt;","&gt;"),html_entity_decode($smth,null,'UTF-8'));
	return $smth ;
}
function quote2entities($string,$entities_type='number'){
    $search                     = array("\"","'");
    $replace_by_entities_name   = array("&quot;","&apos;");
    $replace_by_entities_number = array("&#34;","&#39;");
    $do = null;
    if ($entities_type == 'number'){$do = str_replace($search,$replace_by_entities_number,$string);}else if ($entities_type == 'name'){$do = str_replace($search,$replace_by_entities_name,$string);}else{$do = addslashes($string);}
    return $do;
}
function encodeURIComponent($string) 				{$result = "";for ($i = 0; $i < strlen($string); $i++) {$result .= encodeURIComponentbycharacter(urlencode($string[$i]));}return $result;}
function encodeURIComponentbycharacter($char) 		{if ($char == "+") { return "%20"; }   if ($char == "%21") { return "!"; }   if ($char == "%27") { return '"'; }   if ($char == "%28") { return "("; }   if ($char == "%29") { return ")"; }   if ($char == "%2A") { return "*"; }   if ($char == "%7E") { return "~"; }   if ($char == "%80") { return "%E2%82%AC"; }   if ($char == "%81") { return "%C2%81"; }   if ($char == "%82") { return "%E2%80%9A"; }   if ($char == "%83") { return "%C6%92"; }   if ($char == "%84") { return "%E2%80%9E"; }   if ($char == "%85") { return "%E2%80%A6"; }   if ($char == "%86") { return "%E2%80%A0"; }   if ($char == "%87") { return "%E2%80%A1"; }   if ($char == "%88") { return "%CB%86"; }   if ($char == "%89") { return "%E2%80%B0"; }   if ($char == "%8A") { return "%C5%A0"; }   if ($char == "%8B") { return "%E2%80%B9"; }   if ($char == "%8C") { return "%C5%92"; }   if ($char == "%8D") { return "%C2%8D"; }   if ($char == "%8E") { return "%C5%BD"; }   if ($char == "%8F") { return "%C2%8F"; }   if ($char == "%90") { return "%C2%90"; }   if ($char == "%91") { return "%E2%80%98"; }   if ($char == "%92") { return "%E2%80%99"; }   if ($char == "%93") { return "%E2%80%9C"; }   if ($char == "%94") { return "%E2%80%9D"; }   if ($char == "%95") { return "%E2%80%A2"; }   if ($char == "%96") { return "%E2%80%93"; }   if ($char == "%97") { return "%E2%80%94"; }   if ($char == "%98") { return "%CB%9C"; }   if ($char == "%99") { return "%E2%84%A2"; }   if ($char == "%9A") { return "%C5%A1"; }   if ($char == "%9B") { return "%E2%80%BA"; }   if ($char == "%9C") { return "%C5%93"; }   if ($char == "%9D") { return "%C2%9D"; }   if ($char == "%9E") { return "%C5%BE"; }   if ($char == "%9F") { return "%C5%B8"; }   if ($char == "%A0") { return "%C2%A0"; }   if ($char == "%A1") { return "%C2%A1"; }   if ($char == "%A2") { return "%C2%A2"; }   if ($char == "%A3") { return "%C2%A3"; }   if ($char == "%A4") { return "%C2%A4"; }   if ($char == "%A5") { return "%C2%A5"; }   if ($char == "%A6") { return "%C2%A6"; }   if ($char == "%A7") { return "%C2%A7"; }   if ($char == "%A8") { return "%C2%A8"; }   if ($char == "%A9") { return "%C2%A9"; }   if ($char == "%AA") { return "%C2%AA"; }   if ($char == "%AB") { return "%C2%AB"; }   if ($char == "%AC") { return "%C2%AC"; }   if ($char == "%AD") { return "%C2%AD"; }   if ($char == "%AE") { return "%C2%AE"; }   if ($char == "%AF") { return "%C2%AF"; }   if ($char == "%B0") { return "%C2%B0"; }   if ($char == "%B1") { return "%C2%B1"; }   if ($char == "%B2") { return "%C2%B2"; }   if ($char == "%B3") { return "%C2%B3"; }   if ($char == "%B4") { return "%C2%B4"; }   if ($char == "%B5") { return "%C2%B5"; }   if ($char == "%B6") { return "%C2%B6"; }   if ($char == "%B7") { return "%C2%B7"; }   if ($char == "%B8") { return "%C2%B8"; }   if ($char == "%B9") { return "%C2%B9"; }   if ($char == "%BA") { return "%C2%BA"; }   if ($char == "%BB") { return "%C2%BB"; }   if ($char == "%BC") { return "%C2%BC"; }   if ($char == "%BD") { return "%C2%BD"; }   if ($char == "%BE") { return "%C2%BE"; }   if ($char == "%BF") { return "%C2%BF"; }   if ($char == "%C0") { return "%C3%80"; }   if ($char == "%C1") { return "%C3%81"; }   if ($char == "%C2") { return "%C3%82"; }   if ($char == "%C3") { return "%C3%83"; }   if ($char == "%C4") { return "%C3%84"; }   if ($char == "%C5") { return "%C3%85"; }   if ($char == "%C6") { return "%C3%86"; }   if ($char == "%C7") { return "%C3%87"; }   if ($char == "%C8") { return "%C3%88"; }   if ($char == "%C9") { return "%C3%89"; }   if ($char == "%CA") { return "%C3%8A"; }   if ($char == "%CB") { return "%C3%8B"; }   if ($char == "%CC") { return "%C3%8C"; }   if ($char == "%CD") { return "%C3%8D"; }   if ($char == "%CE") { return "%C3%8E"; }   if ($char == "%CF") { return "%C3%8F"; }   if ($char == "%D0") { return "%C3%90"; }   if ($char == "%D1") { return "%C3%91"; }   if ($char == "%D2") { return "%C3%92"; }   if ($char == "%D3") { return "%C3%93"; }   if ($char == "%D4") { return "%C3%94"; }   if ($char == "%D5") { return "%C3%95"; }   if ($char == "%D6") { return "%C3%96"; }   if ($char == "%D7") { return "%C3%97"; }   if ($char == "%D8") { return "%C3%98"; }   if ($char == "%D9") { return "%C3%99"; }   if ($char == "%DA") { return "%C3%9A"; }   if ($char == "%DB") { return "%C3%9B"; }   if ($char == "%DC") { return "%C3%9C"; }   if ($char == "%DD") { return "%C3%9D"; }   if ($char == "%DE") { return "%C3%9E"; }   if ($char == "%DF") { return "%C3%9F"; }   if ($char == "%E0") { return "%C3%A0"; }   if ($char == "%E1") { return "%C3%A1"; }   if ($char == "%E2") { return "%C3%A2"; }   if ($char == "%E3") { return "%C3%A3"; }   if ($char == "%E4") { return "%C3%A4"; }   if ($char == "%E5") { return "%C3%A5"; }   if ($char == "%E6") { return "%C3%A6"; }   if ($char == "%E7") { return "%C3%A7"; }   if ($char == "%E8") { return "%C3%A8"; }   if ($char == "%E9") { return "%C3%A9"; }   if ($char == "%EA") { return "%C3%AA"; }   if ($char == "%EB") { return "%C3%AB"; }   if ($char == "%EC") { return "%C3%AC"; }   if ($char == "%ED") { return "%C3%AD"; }   if ($char == "%EE") { return "%C3%AE"; }   if ($char == "%EF") { return "%C3%AF"; }   if ($char == "%F0") { return "%C3%B0"; }   if ($char == "%F1") { return "%C3%B1"; }   if ($char == "%F2") { return "%C3%B2"; }   if ($char == "%F3") { return "%C3%B3"; }   if ($char == "%F4") { return "%C3%B4"; }   if ($char == "%F5") { return "%C3%B5"; }   if ($char == "%F6") { return "%C3%B6"; }   if ($char == "%F7") { return "%C3%B7"; }   if ($char == "%F8") { return "%C3%B8"; }   if ($char == "%F9") { return "%C3%B9"; }   if ($char == "%FA") { return "%C3%BA"; }   if ($char == "%FB") { return "%C3%BB"; }   if ($char == "%FC") { return "%C3%BC"; }   if ($char == "%FD") { return "%C3%BD"; }   if ($char == "%FE") { return "%C3%BE"; }   if ($char == "%FF") { return "%C3%BF"; }   return $char;}
function _verifica_tabela($tabela)					{global $_conectMySQLi_;while ($row = mysqli_fetch_row(mysqli_query($_conectMySQLi_,"SHOW TABLES"))) { if($tabela==$row[0]){return false ;exit;};}return true ;exit;}
function _define_page_($var, $page)					{ob_start();require_once $page; define($var, ob_get_clean());ob_end_flush();}
function _encripta( $plaintext, $key){	
	$ivlen 			= openssl_cipher_iv_length($cipher="AES-128-CBC");
	$iv 			= openssl_random_pseudo_bytes($ivlen);
	$ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
	$hmac 			= hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
	return base64_encode($iv.$hmac.$ciphertext_raw);
}
function _decripta( $ciphertext, $key) {	
	$c 					= base64_decode($ciphertext);
	$ivlen 				= openssl_cipher_iv_length($cipher="AES-128-CBC");
	$iv 				= substr($c, 0, $ivlen);
	$hmac 				= substr($c, $ivlen, $sha2len=32);
	$ciphertext_raw		= substr($c, $ivlen+$sha2len);
	$original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
	$calcmac 			= hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
	if(!function_exists('hash_equals')){
	    function hash_equals($str1, $str2){
	        if(strlen($str1) != strlen($str2)){
	            return false;
	        }else{
	            $res = $str1 ^ $str2;
	            $ret = 0;
	            for($i = strlen($res) - 1; $i >= 0; $i--){
	                $ret |= ord($res[$i]);
	            }
	            return !$ret;
	        }
	    }
	}
	if (hash_equals($hmac, $calcmac)){
	    return $original_plaintext;
	}else{
	    return false;
	}
}
function _return_code($codigo)					{return '<div id="editor" class="prettyprint linenums" style="width: 710px; margin-bottom: -40px;margin-left: 15px; text-align: left; padding: 10px 20px 10px 40px; background-color: rgb(0, 0, 0);text-shadow: none;">&lt;?'.str_replace(array('<','>'), array('&lt;','&gt;'),$codigo).'?&gt;</div><script type="text/javascript">prettyPrint();</script>';}
function url_amigavel_filename($texto){
	$array1 = array("{","}","[","]","´","&",",","/"," ","á","à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç");
	$array2 = array("","","","","","e","","-","_","a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c" );
	return strtolower(str_replace( $array1, $array2, strtolower($texto)));
}
function url_amigavel($texto,$isso="",$porisso="") 					{
	$array1 = array("´","&",",",".","/"," ", "á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç");
	$array2 = array("","e","", "","-","+","a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c");
	$tratamento=strtolower(str_replace( $isso, $porisso, strtolower($texto)));
	return strtolower(str_replace( $array1, $array2, $tratamento));
}
function retira_acentos($str,$espaco="")		{return strtr(utf8_decode($str),utf8_decode('ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ '),'SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy'.$espaco);}
function exec_SQL($filename=null)					{
	global $_conectMySQLi_;
	if(file_exists($filename)){
		$templine = '';
		$filename 	= file_get_contents($filename);
		$filename 	= str_replace('{_prefix_}',PREFIX_TABLES,$filename);
 		$filename   = str_replace(array("\n","\r" ,PHP_EOL),PHP_EOL, $filename); 
 		$lines 		= explode(PHP_EOL,$filename);


		foreach($lines as $line_num => $line) {
			if (substr($line, 0, 2) != '--' && $line != '') {
				$templine .= $line;
				if (substr(trim($line), -1, 1) == ';') {
					mysqli_query($_conectMySQLi_,$templine) or die("Erro em gravar banco de dados: \n :".PHP_EOL.mysqli_error() );
					$templine = '';
				}
			}
		}
		return true;
	}elseif(is_string($filename)){
		$templine 	= '';
		$filename 	= str_replace('{_prefix_}',PREFIX_TABLES,$filename);
 		$filename   = str_replace(array("\n","\r" ,PHP_EOL),PHP_EOL, $filename); 
 		$lines 		= explode(PHP_EOL,$filename);

		foreach($lines as $line_num => $line) {
			if (substr($line, 0, 2) != '--' && $line != '') {
				$templine .= $line;
				if (substr(trim($line), -1, 1) == ';') {
					mysqli_query($_conectMySQLi_,$templine) or die("Erro em gravar banco de dados: \n :".mysqli_error().PHP_EOL.'Comando: '.$templine );
					$templine = '';
				}
			}
		}
		return true;

	}
}
function _filesize($file, $size="M", $decimals=2, $dec_sep='.', $thousands_sep=','){
	if(file_exists($file)){
		$bytes = filesize($file);
	}elseif(is_numeric($file) || is_int($file)){
		$bytes = $file;
	}else{
		$bytes = 0;
	}
	if($bytes<1024){$size="B";}
	elseif($bytes<(1024*1024)){$size="K";}
	elseif($bytes<(1024*1024*1024)){$size="M";}
	elseif($bytes<(1024*1024*1024*1024)){$size="G";}
	elseif($bytes<(1024*1024*1024*1024*1024)){$size="T";}
	elseif($bytes<(1024*1024*1024*1024*1024*1024)){$size="P";}
	$sizes = 'BKMGTP';
	if (isset($size)){
		$factor = strpos($sizes, $size[0]);
	} else {
		$factor = floor((strlen($bytes) - 1) / 3);
		$size = $sizes[$factor];
	}
	return number_format($bytes/pow(1024, $factor), $decimals, $dec_sep, $thousands_sep).' '.$size;
}
function _str_to_bin($text)						{$tm = strlen($text);$x = 0;for($i = 1;$i<=$tm;$i++){$letra[$i] = substr($texto,$x,1);$cod[$i] = ord($letra[$i]);$bin[$i] = str_pad(decbin($cod[$i]), 8, "0", STR_PAD_LEFT);$x++;}$a= 1;$binario = array();for($i = 1;$i <= $tm;$i++){if($a == 16) {$binario[]=$bin[$i]." ".PHP_EOL;$a=0;}else{$binario[]=$bin[$i]." ";}$a++;}return implode($binario,"");}
function color_inverse($color){					$color = str_replace('#', '', $color);if (strlen($color) != 6){ return '000000'; }$rgb = '';for ($x=0;$x<3;$x++){$c = 255 - hexdec(substr($color,(2*$x),2));$c = ($c < 0) ? 0 : dechex($c);$rgb .= (strlen($c) < 2) ? '0'.$c : $c;}return '#'.$rgb;}
function _set_session($id){
		ob_start();
		if(empty($_SESSION) && session_id()!=$id){
			ini_set('session.cookie_secure',	1);
			ini_set('session.cookie_httponly',	1);
			ini_set('session.cookie_lifetime', "432000");
			ini_set("session.gc_maxlifetime",	"432000");
			ini_set("session.use_trans_sid", 	0);
			ini_set('session.use_cookies', 	1);
			ini_set('session.use_only_cookies', 1);
			ini_set('session.name', 			'_WS_');
			session_cache_expire("432000");
			session_cache_limiter('private');
			session_id($id);
			session_name($id);
			session_start();
		}
};
function _session(){
	if(isset($_COOKIE['ws_session'])){
		session_name('_WS_');
		@session_id($_COOKIE['ws_session']);
		@session_start(); 
		@session_regenerate_id();
	};
};
function remoteFileExists($url) 				{
	$curl = @curl_init($url);
	@curl_setopt($curl, CURLOPT_NOBODY, true);
	$result = @curl_exec($curl);$ret = false;
	if ($result !== false) {
		$statusCode = @curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if ($statusCode == 200) {
			$ret = true;
		}
	}
	@curl_close($curl);
	return $ret;
}
if(!defined('__VENC__')) define('__VENC__',date('Y/m/d', strtotime("+90 days",strtotime(date ("d-m-Y", filectime(__DIR__.'/ws-connect-mysql.php'))))));
?>