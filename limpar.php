<?php

echo "<pre>";

try {
    echo "🔄 Limpando cache Laravel...\n";

    // Limpa cache de configuração, rotas, views
    passthru('php artisan config:clear');
    passthru('php artisan cache:clear');
    passthru('php artisan view:clear');
    passthru('php artisan route:clear');

    // Remove arquivos da pasta storage/framework
    function limparPasta($pasta) {
        $arquivos = glob($pasta . '/*');
        foreach ($arquivos as $arquivo) {
            if (is_file($arquivo)) {
                unlink($arquivo);
            } elseif (is_dir($arquivo)) {
                limparPasta($arquivo);
                rmdir($arquivo);
            }
        }
    }

    $base = __DIR__;
    limparPasta($base . '/storage/framework/cache/data');
    limparPasta($base . '/storage/framework/views');
    limparPasta($base . '/storage/framework/sessions');

    // Remove config cache compilado
    if (file_exists($base . '/bootstrap/cache/config.php')) {
        unlink($base . '/bootstrap/cache/config.php');
        echo "🧹 Removido bootstrap/cache/config.php\n";
    }

    echo "\n✅ Finalizado com sucesso!\n";
} catch (Exception $e) {
    echo "❌ Erro ao limpar: " . $e->getMessage();
}

echo "</pre>";
