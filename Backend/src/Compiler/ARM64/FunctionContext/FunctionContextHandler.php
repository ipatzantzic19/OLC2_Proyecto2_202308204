<?php

namespace Golampi\Compiler\ARM64\FunctionContext;

use Golampi\Compiler\ARM64\FunctionContext\LocalsManager;
use Golampi\Compiler\ARM64\FunctionContext\ArrayManager;
use Golampi\Compiler\ARM64\FunctionContext\RegisterManager;
use Golampi\Compiler\ARM64\FunctionContext\FrameCalculator;

require_once __DIR__ . '/LocalsManager.php';
require_once __DIR__ . '/ArrayManager.php';
require_once __DIR__ . '/RegisterManager.php';
require_once __DIR__ . '/FrameCalculator.php';

/**
 * FunctionContextHandler — Orquestador de contexto de función
 *
 * Coordina los cuatro sub-managers que forman el contexto de ejecución
 * de una función Golampi en arquitectura AArch64.
 *
 * Responsabilidad de la clase contenedora (FunctionContext):
 *   - Punto de entrada único para crear y gestionar contextos
 *   - Integra el modelo de memoria completo (variables + arrays + registros)
 *   - Expone API pública coherente para el generador ARM64
 *   - Gestiona el ciclo de vida del contexto (creación → prescan → generación)
 *
 * Sub-managers coordinados:
 *
 *   1. LocalsManager
 *      → allocLocal(), hasLocal(), getLocalType(), getLocals()
 *      → Responsable de variables escalares en stack
 *
 *   2. ArrayManager
 *      → allocArray(), getArrayElementOffset(), getArrayInfo()
 *      → Responsable de arrays multidimensionales con indexación row-major
 *
 *   3. RegisterManager
 *      → useCalleeSaved(), getCalleeSaved(), generateCalleeSavedProlog/Epilog()
 *      → Responsable de registros preservados en prólogo/epílogo
 *
 *   4. FrameCalculator
 *      → calculateFrameSize(), getFrameSize(), isFrameValid()
 *      → Responsable de cálculo total del stack frame
 *
 * Separación de responsabilidades (Principios de compiladores):
 *   ✓ Cada manager gestiona UN aspecto del frame
 *   ✓ No hay dependencias circulares (todos consultan nextOffset al ArrayManager)
 *   ✓ Fácil de extender (agregar nuevo tipo de persistencia = nuevo manager)
 *   ✓ Código testeable en unidades (cada manager por separado)
 *
 * Concepto académico (Aho, Lam, Sethi):
 *   El registro de activación es la estructura ćentral del modelo de tiempo de ejecución.
 *   Aquí separamos sus componentes lógicos: locales, arrays, registros, frame.
 *   Cada componente sigue la jerarquía de información de un compilador típico.
 */
trait FunctionContextHandler
{
    use LocalsManager;       // Gestión de variables scalares
    use ArrayManager;        // Gestión de arrays multidimensionales
    use RegisterManager;     // Gestión de registros callee-saved
    use FrameCalculator;     // Cálculo de tamaño total del frame
}
