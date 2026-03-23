<?php
// Router simple - solo sirve archivos estáticos reales
$requested = __DIR__ . $_SERVER['REQUEST_URI'];
if (is_file($requested)) {
    return false;
}
// Todo lo demás, return false para que PHP intente lo siguiente
return false;





