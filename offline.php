<?php
ob_start();
require __DIR__ . '/src/Config.inc.php';

use Blengs\Models\Session;
use Blengs\Models\Link;

$Session = new Session();
$logoff = filter_input(INPUT_GET, 'logoff', FILTER_VALIDATE_BOOLEAN);
if ($logoff):
    unset($_SESSION['userlogin']);
    header('Location: ' . URL_BASE);
else:
    $userId = ( isset($_SESSION['userlogin']['usuario_id']) ? $_SESSION['userlogin']['usuario_id'] : 0 );
    $verifyUser = new \Blengs\Conexao\Read;
    $verifyUser->ExeRead("mod_usuario", "Where usuario_id = {$userId}");
    if (!$verifyUser->getResult()):
        unset($_SESSION['userlogin']);
    endif;
endif;
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta name="theme-color" content="#2E39BF" />
        <!--[if lt IE 9]>
            <script src="../../src/Plugins/html5.js"></script>
         <![endif]-->
        <meta name="keywords" content="Anuncios, gratis, anunciar, anuciar grátis, anuncio grátis, classificados, hospedagem ,site, criação, website, host, cpanel, barata, econômica, rapida"/>

        <?php
        $Link = new Link;
        $Link->getTags();
        ?>
        <link rel="manifest" href="<?= URL_BASE; ?>/manifest.json">
        <link rel="shorcut icon" type="image/png" href="<?= INCLUDE_PATH; ?>/img/favcon.png">
        <link rel="stylesheet" href="<?= URL_BASE; ?>/src/Css/estilo.css">
        <link rel="stylesheet" href="<?= URL_BASE; ?>/src/Css/fontawesome.css">
    </head>
    <body class="fundo_cinza">
        <h1 class="none"></h1>
        <div id="installContainer" class="hidden">
            <button id="butInstall" type="button">
                Install
            </button>
        </div>
        <?php
        require(REQUIRE_TEMA . 'header.php');

        if (!empty($Link->getLocal()[0]) && in_array($Link->getLocal()[0], $cat_page)):
            require(REQUIRE_TEMA . 'home.php');
        elseif (!require($Link->getPatch())):
            MsgErro('Erro ao incluir arquivo de navegação!', NOT_ERROR, true);
        endif;
        ?>
    </body>
    <script src="<?= URL_BASE; ?>/src/Plugins/jquery.js"></script>
    <script src="<?= URL_BASE; ?>/src/Plugins/jquery-ui.js"></script>
    <script src="<?= URL_BASE; ?>/src/Plugins/jquery.maskMoney.min.js"></script>
    <script src="<?= URL_BASE; ?>/src/Plugins/jquery.mask.min.js"></script>
    <script src="<?= URL_BASE; ?>/src/Plugins/plugins.js"></script>
    <script src="<?= URL_BASE; ?>/src/Plugins/cookies.js"></script>
    <script src="<?= URL_BASE; ?>/src/Plugins/cookies.js"></script>
    <script>
        const divInstall = document.getElementById('installContainer');
        const butInstall = document.getElementById('butInstall');

        window.addEventListener('beforeinstallprompt', (event) => {
            // Impedir que o mini-infobar apareça no celular.
            event.preventDefault();
            console.log('👍', 'beforeinstallprompt', event);
            // Esconder o evento para que possa ser acionado mais tarde.
            window.deferredPrompt = event;
            // Remover a classe 'oculta' do contêiner do botão de instalação.
            divInstall.classList.toggle('hidden', false);
        });

        butInstall.addEventListener('click', async () => {
            console.log('👍', 'butInstall-clicked');
            const promptEvent = window.deferredPrompt;
            if (!promptEvent) {
                // The deferred prompt isn't available.
                return;
            }
            // Show the install prompt.
            promptEvent.prompt();
            // Log the result
            const result = await promptEvent.userChoice;
            console.log('👍', 'userChoice', result);
            // Reset the deferred prompt variable, since
            // prompt() can only be called once.
            window.deferredPrompt = null;
            // Hide the install button.
            divInstall.classList.toggle('hidden', true);
        });

        window.addEventListener('appinstalled', (event) => {
            console.log('👍', 'appinstalled', event);
            // Limpa o deferredPrompt para que possa ser coletado como lixo
            window.deferredPrompt = null;
        });

        /* Only register a service worker if it's supported */
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/service-worker.js');
        }

        /* Check protocol and redirect to https */
        if (window.location.protocol === 'http:') {
            const requireHTTPS = document.getElementById('requireHTTPS');
            const link = requireHTTPS.querySelector('a');
            link.href = window.location.href.replace('http://', 'https://');
            requireHTTPS.classList.remove('hidden');
        }
    </script>


    <?php
    if (in_array($Link->getLocal()[0], $pages_conta)):
        ?>
        <script src="<?= URL_BASE; ?>/src/Plugins/tinymce/tinymce.min.js"></script>
        <script src="<?= URL_BASE; ?>/src/Plugins/tinymce.js"></script>
        <?php
    endif;
    ?>
</html>
<?php
ob_end_flush();
