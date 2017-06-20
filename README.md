![WebSheep](https://raw.githubusercontent.com/websheep/cms/master/admin/App/Templates/img/websheep/logoEmail.png)

O WebSheep é um aplicativo usado para criar, editar, gerenciar e publicar conteúdo de forma consistentemente organizada permitindo que o mesmo seja modificado, removido e adicionado com facilidade. Usados para armazenar, controlar, prover documentação, empresarial tais como notícias, artigos, manuais de operação, manuais técnicos, guias de vendas e brochuras de marketing. O conteúdo pode incluir imagens, áudios, vídeos, documentos eletrônicos e conteúdo Web.

## Licença

Este software é distribuido sob a licença [LGPL 2.1](http://www.gnu.org/licenses/lgpl-2.1.html). Por favor, leia a LICENÇA para obter informações sobre disponibilidade e distribuição do websheep.

## Instalação simples e rápida

Faça download do diretório **/ws-update/**  e jogue em seu FTP. 
Execute o script e prossiga com a instalação normalmente. 


### Minimal installation

While installing the entire package manually or with composer is simple, convenient and reliable, you may want to include only vital files in your project. At the very least you will need [class.phpmailer.php](https://github.com/PHPMailer/PHPMailer/tree/master/class.phpmailer.php). If you're using SMTP, you'll need [class.smtp.php](https://github.com/PHPMailer/PHPMailer/tree/master/class.smtp.php), and if you're using POP-before SMTP, you'll need [class.pop3.php](https://github.com/PHPMailer/PHPMailer/tree/master/class.pop3.php). For all of these, we recommend you use [the autoloader](https://github.com/PHPMailer/PHPMailer/tree/master/PHPMailerAutoload.php) too as otherwise you will either have to `require` all classes manually or use some other autoloader. You can skip the [language](https://github.com/PHPMailer/PHPMailer/tree/master/language/) folder if you're not showing errors to users and can make do with English-only errors. You may need the additional classes in the [extras](extras/) folder if you are using those features, including NTLM authentication and ics generation. If you're using Google XOAUTH2 you will need `class.phpmaileroauth.php` and `class.oauth.php` classes too, as well as the composer dependencies.

## Documentation

Examples of how to use PHPMailer for common scenarios can be found in the [examples](https://github.com/PHPMailer/PHPMailer/tree/master/examples) folder. If you're looking for a good starting point, we recommend you start with [the Gmail example](https://github.com/PHPMailer/PHPMailer/tree/master/examples/gmail.phps).

There are tips and a troubleshooting guide in the [GitHub wiki](https://github.com/PHPMailer/PHPMailer/wiki). If you're having trouble, this should be the first place you look as it's the most frequently updated.

Complete generated API documentation is [available online](http://phpmailer.github.io/PHPMailer/).

You'll find some basic user-level docs in the [docs](docs/) folder, and you can generate complete API-level documentation using the [generatedocs.sh](https://github.com/PHPMailer/PHPMailer/tree/master/docs/generatedocs.sh) shell script in the docs folder, though you'll need to install [PHPDocumentor](http://www.phpdoc.org) first. You may find [the unit tests](https://github.com/PHPMailer/PHPMailer/tree/master/test/phpmailerTest.php) a good source of how to do various operations such as encryption.

If the documentation doesn't cover what you need, search the [many questions on Stack Overflow](http://stackoverflow.com/questions/tagged/phpmailer), and before you ask a question about "SMTP Error: Could not connect to SMTP host.", [read the troubleshooting guide](https://github.com/PHPMailer/PHPMailer/wiki/Troubleshooting).

## Security

Please disclose any vulnerabilities found responsibly - report any security problems found to the maintainers privately.

PHPMailer versions prior to 5.2.22 (released January 9th 2017) have a local file disclosure vulnerability, [CVE-2017-5223](https://web.nvd.nist.gov/view/vuln/detail?vulnId=CVE-2017-5223). If content passed into `msgHTML()` is sourced from unfiltered user input, relative paths can map to absolute local file paths and added as attachments. Also note that `addAttachment` (just like `file_get_contents`, `passthru`, `unlink`, etc) should not be passed user-sourced params either! Reported by Yongxiang Li of Asiasecurity.

PHPMailer versions prior to 5.2.20 (released December 28th 2016) are vulnerable to [CVE-2016-10045](https://web.nvd.nist.gov/view/vuln/detail?vulnId=CVE-2016-10045) a remote code execution vulnerability, responsibly reported by [Dawid Golunski](https://legalhackers.com/advisories/PHPMailer-Exploit-Remote-Code-Exec-CVE-2016-10045-Vuln-Patch-Bypass.html), and patched by Paul Buonopane (@Zenexer).

PHPMailer versions prior to 5.2.18 (released December 2016) are vulnerable to [CVE-2016-10033](https://web.nvd.nist.gov/view/vuln/detail?vulnId=CVE-2016-10033) a critical remote code execution vulnerability, responsibly reported by [Dawid Golunski](http://legalhackers.com/advisories/PHPMailer-Exploit-Remote-Code-Exec-CVE-2016-10033-Vuln.html).

See [SECURITY](https://github.com/PHPMailer/PHPMailer/tree/master/SECURITY.md) for more detail on security issues.

## Contributing

Please submit bug reports, suggestions and pull requests to the [GitHub issue tracker](https://github.com/PHPMailer/PHPMailer/issues).

We're particularly interested in fixing edge-cases, expanding test coverage and updating translations.

With the move to the PHPMailer GitHub organisation, you'll need to update any remote URLs referencing the old GitHub location with a command like this from within your clone:

```sh
git remote set-url upstream https://github.com/PHPMailer/PHPMailer.git
```

Please *don't* use the SourceForge or Google Code projects any more.

## Sponsorship

Development time and resources for PHPMailer are provided by [Smartmessages.net](https://info.smartmessages.net/), a powerful email marketing system.

<a href="https://info.smartmessages.net/"><img src="https://www.smartmessages.net/img/smartmessages-logo.svg" width="250" height="28" alt="Smartmessages email marketing"></a>

Other contributions are gladly received, whether in beer 🍺, T-shirts 👕, Amazon wishlist raids, or cold, hard cash 💰.

## Changelog

See [changelog](changelog.md).

## History
- PHPMailer was originally written in 2001 by Brent R. Matzelle as a [SourceForge project](http://sourceforge.net/projects/phpmailer/).
- Marcus Bointon (coolbru on SF) and Andy Prevost (codeworxtech) took over the project in 2004.
- Became an Apache incubator project on Google Code in 2010, managed by Jim Jagielski.
- Marcus created his fork on [GitHub](https://github.com/Synchro/PHPMailer).
- Jim and Marcus decide to join forces and use GitHub as the canonical and official repo for PHPMailer.
- PHPMailer moves to the [PHPMailer organisation](https://github.com/PHPMailer) on GitHub.

### What's changed since moving from SourceForge?
- Official successor to the SourceForge and Google Code projects.
- Test suite.
- Continuous integration with Travis-CI.
- Composer support.
- Public development.
- Additional languages and language strings.
- CRAM-MD5 authentication support.
- Preserves full repo history of authors, commits and branches from the original SourceForge project.
