# 🔧 Golampi Compiler — Proyecto 2 · Plan de Implementación por Fases

> **Skill file** — Referencia de estado y contexto para Claude. Actualizar al completar cada subfase.
> OLC2 — 1er Semestre 2026 · Universidad de San Carlos de Guatemala (USAC)

---

## 📐 Arquitectura del sistema (lectura obligada)

```
Código Golampi (.go)
       │
       ▼
  ANTLR4 Lexer/Parser  ← GolampiLexer.g4 / GolampiParser.g4  (REUTILIZADA de P1)
       │
       ▼ CST (árbol de parseo)
  SemanticAnalyzer.php  ← Nuevo: análisis semántico REAL (tabla de tipos, scopes)
       │
       ▼ AST anotado con tipos
  ARM64Generator.php    ← Extiende GolampiBaseVisitor (ya existe en P1)
       │
       ▼ Código ARM64 (.s)
  CompilationResult     ← assembly + errores + tabla de símbolos
       │
       ├── CompilationHandler.php   (orquesta todo)
       └── ApiRouter.php            (POST /api/compile)
```

### Separación P1 vs P2

| Componente | P1 (ya existe) | P2 (a implementar/ampliar) |
|---|---|---|
| Gramática ANTLR4 | ✅ Completa y funcional | Reutilizar tal cual |
| ARM64Generator (traits) | ✅ Fase 1 básica | Ampliar en fases 2-4 |
| Análisis semántico real | ⚠️ Solo errores básicos | Refactorizar → SemanticAnalyzer |
| float32 (SIMD) | 🔜 | Fase 2 |
| Funciones multi-param/return | 🔜 | Fase 2 |
| Arrays + punteros | 🔜 | Fase 3 |
| Ejecución QEMU integrada | 🔜 | Fase 4 |

---

## 🗺️ Mapa de fases (del SVG de arquitectura)

```
[Fase 1 ✅]          [Fase 2]             [Fase 3]             [Fase 4]
Gramática reusada    Tipos completos      Arreglos+punteros    QEMU integrado
main() → ARM64       Funciones usuario    switch/case           Ejecución real
int32, aritmética    float32, bool        Arrays 1D/multidim   Frontend output
if/for/switch        string, rune         Paso por referencia

Pipeline completo:
Código → ANTLR4 → Semántico → ARM64 Gen → .s → GCC+LD → QEMU → stdout
```

---

## ✅ Estado actual — Fase 1 COMPLETADA

### Lo que ya funciona (según README y test_phase1.php)

- [x] `main()` sin parámetros — prólogo/epílogo ARM64
- [x] Variables `int32` (`var`, `:=`, `const`)
- [x] Variables `bool`
- [x] Variables `string` (solo literales, no concatenación)
- [x] Aritmética `+ - * / %`
- [x] Operadores lógicos `&& ||  !` (con cortocircuito)
- [x] Comparaciones `== != < <= > >=`
- [x] Asignación compuesta `+= -= *= /=`
- [x] `++` y `--`
- [x] `if / else if / else`
- [x] `for` (clásico, while, infinito)
- [x] `switch / case / default`
- [x] `break` y `continue`
- [x] `return`
- [x] `fmt.Println` (int32, bool, string)
- [x] Funciones de usuario (sin params / 1 param int32)
- [x] Pool de strings internados (sección `.data`)
- [x] Tabla de símbolos
- [x] Reporte de errores (léxico, sintáctico, semántico básico)
- [x] Tests automatizados: `php tests/test_phase1.php`

### Arquitectura de traits en ARM64Generator

```
ARM64Generator
  ├── EmitterTrait        → emit(), label(), comment(), buildAssembly()
  ├── StringPoolTrait     → internString(), asmEscape()
  ├── DeclarationsTrait   → var, :=, const, prescanBlock
  ├── AssignmentsTrait    → =, +=, -=, *=, /=, ++, --
  ├── ControlFlowTrait    → if, for×3, switch, break, continue, return
  ├── ExpressionsTrait    → operadores binarios y unarios
  ├── LiteralsTrait       → int32, float32, rune, string, bool, nil
  ├── FunctionCallTrait   → fmt.Println, funciones usuario
  └── HelpersTrait        → tipos, allocVar, storeDefault, addSymbol
```

---

## 🚀 Fase 2 — Tipos completos + Funciones multi-param/return

**Estado: ⏳ PENDIENTE**

### Objetivo
Ampliar el compilador para soportar todos los tipos primitivos con ARM64 correcto,
funciones con múltiples parámetros (x0–x7) y múltiples valores de retorno.

### 2A · float32 con registros SIMD

**Archivos a modificar:** `LiteralsTrait.php`, `ExpressionsTrait.php`, `FunctionCallTrait.php`, nuevo `FloatOpsTrait.php`

**Qué hacer:**
1. `visitFloatLiteral` → usar `fmov s0, #val` real en lugar de movk de bits
2. Aritmética float: `fadd`, `fsub`, `fmul`, `fdiv` sobre registros `s0–s7`
3. Conversión int32↔float32: `scvtf` / `fcvtzs`
4. `fmt.Println(float)` → `printf("%f\n", ...)` con `d0` (double para variadic)
5. Comparaciones float: `fcmp`, `fcsel`

**Convención de registros para float32:**
```
s0–s7   → argumentos y retorno float (paso a funciones)
s8–s15  → caller-saved
s19–s31 → callee-saved
```

**Conversión float→double para printf variadic:**
```asm
fcvt d0, s0     // s0 (float32) → d0 (float64) para printf
```

### 2B · Tipos rune y string completos

**Archivos a modificar:** `LiteralsTrait.php`, `FunctionCallTrait.php`, nuevo `StringOpsTrait.php`

**Qué hacer:**
1. `rune` → ya funciona como int32 (alias). Verificar `fmt.Println(rune)` imprime el carácter, no el número
2. `string` concatenación con `+` → runtime helper `golampi_strcat` en `.text`
3. `len(string)` → syscall `strlen` o helper
4. `substr(s, start, len)` → helper en assembly
5. `typeOf()` → retorna puntero a string constante en `.data`

### 2C · Funciones multi-parámetro y multi-retorno

**Archivos a modificar:** `ARM64Generator.php` (`extractParams`, `generateFunction`, `generateUserCall`)

**Qué hacer:**
1. Pasar hasta 8 argumentos enteros por x0–x7
2. Pasar argumentos float por s0–s7
3. Guardar/restaurar registros callee-saved que se usen (x19–x28)
4. Multi-retorno: empaquetar en x0+x1 (hasta 128 bits), o en stack para más
5. Hoisting ya funciona — extender para firmas complejas

**Convención AArch64 para multi-return:**
```
2 valores int32  → x0, x1
1 float + 1 int  → s0, x0
> 2 valores      → puntero a struct en stack (x8 = sret pointer)
```

### 2D · Funciones built-in completas

**Archivos a modificar:** `FunctionCallTrait.php`

| Función | Implementación ARM64 |
|---|---|
| `len(string)` | `bl strlen` + libc |
| `len(array)` | constante en compile time |
| `now()` | `bl time` + `bl strftime` |
| `substr(s,i,n)` | helper .text |
| `typeOf(x)` | string literal en .data |

### Tests de Fase 2

```php
// test_phase2.php
test('float32 suma', 'func main() { a := 1.5; b := 2.5; fmt.Println(a+b) }', ...);
test('funciones multi-param', 'func suma(a,b,c int32) int32 { return a+b+c }', ...);
test('multi-return', 'func div(a,b int32)(int32,bool) { return a/b, b!=0 }', ...);
test('string concatenación', 'func main() { s := "hola" + " mundo"; fmt.Println(s) }', ...);
```

---

## 🚀 Fase 3 — Arrays + Punteros en ARM64

**Estado: ⏳ PENDIENTE**

### Objetivo
Soporte completo de arreglos 1D y multidimensionales con gestión de heap,
y punteros con semántica de paso por referencia.

### 3A · Arrays 1D en stack (tamaño estático pequeño ≤ 8 elementos)

**Qué hacer:**
1. Ampliar `FunctionContext` para reservar espacio contiguo: `N * 8 bytes`
2. `var a [5]int32` → prescan calcula `5 * 8 = 40 bytes` adicionales en frame
3. Acceso `a[i]` → `ldr x0, [x29, #-(base + i*8)]` (i constante) o con desplazamiento dinámico
4. Inicialización `[3]int32{1,2,3}` → 3 instrucciones `str`

**Estructura en FunctionContext:**
```php
protected array $arrays = []; // name => ['base_offset' => int, 'size' => int, 'elem_type' => string]
```

### 3B · Arrays multidimensionales

**Qué hacer:**
1. `[2][3]int32` → reservar `2*3*8 = 48 bytes`
2. Acceso `m[i][j]` → offset = `base + (i * cols + j) * 8`
3. Inicialización anidada `{{1,2},{3,4}}`

### 3C · Arrays en heap (malloc/free) para arrays grandes o dinámicos

**Qué hacer:**
1. Si el array supera el límite del frame (256 bytes de la Fase 1), usar heap
2. `bl malloc` con tamaño en x0
3. Puntero retornado en x0, almacenar en variable local
4. Acceso indirecto: `ldr x1, [x29, #-offset_ptr]` luego `ldr x0, [x1, x2, lsl #3]`

### 3D · Punteros

**Qué hacer:**
1. `&x` → ya existe `visitAddressOf` en ExpressionsTrait → generar `sub x0, x29, #offset`
2. `*ptr` → `visitDereference` → `ldr x0, [x0]`
3. `*ptr = val` → `visitPointerAssignment` → evaluar expr, `str x0, [x1]`
4. Paso por referencia: parámetro `*[N]int32` → pasar dirección del array

### Tests de Fase 3

```php
test('array 1D básico', 'var a [3]int32 = [3]int32{1,2,3}; fmt.Println(a[1])', ...);
test('array 2D acceso', 'var m [2][2]int32; m[0][1]=5; fmt.Println(m[0][1])', ...);
test('puntero modifica', 'func inc(p *int32) { *p = *p + 1 }; x:=5; inc(&x); fmt.Println(x)', ...);
```

---

## 🚀 Fase 4 — Ejecución QEMU integrada

**Estado: ⏳ PENDIENTE**

### Objetivo
Integrar el pipeline completo en el servidor: compilar el `.s` generado,
ensamblarlo con GCC cross-compiler y ejecutarlo con QEMU, devolviendo stdout al frontend.

### Pipeline en servidor

```php
// CompilationHandler.php
1. Generar .s → guardar en /tmp/golampi_XXXX.s
2. aarch64-linux-gnu-gcc -static -o /tmp/golampi_XXXX /tmp/golampi_XXXX.s -lc
3. timeout 5 qemu-aarch64 /tmp/golampi_XXXX > /tmp/golampi_XXXX.out 2>&1
4. Leer stdout y devolverlo en programOutput
5. Limpiar archivos temporales
```

### Requisitos del servidor

```bash
# Debian/Ubuntu
sudo apt install gcc-aarch64-linux-gnu qemu-user

# Verificar
which aarch64-linux-gnu-gcc   # debe existir
which qemu-aarch64             # debe existir
```

### Cambios en API

```
POST /api/compile
  request:  { code: string, execute: bool }
  response: { ..., programOutput: string, qemuExitCode: int }
```

### Seguridad (importante en Fase 4)

- Timeout de 5 segundos en QEMU (`proc_open` con timeout)
- Sandbox: archivos en `/tmp` con nombre aleatorio
- Límite de tamaño de stdout (max 64KB)
- Limpiar archivos después de cada ejecución

### Tests de Fase 4

```bash
# Compilar y ejecutar hello world completo
php test_phase4.php
# Debe imprimir: "8" (resultado del programa de ejemplo del enunciado)
```

---

## 📋 Checklist completo del enunciado vs implementación

### Tipos estáticos

| Tipo | Fase 1 | Fase 2 | Estado |
|---|---|---|---|
| `int32` | ✅ | — | Completo |
| `float32` | ⚠️ bits only | Refactorizar | Pendiente |
| `bool` | ✅ | — | Completo |
| `rune` | ✅ básico | Printf char | Parcial |
| `string` literales | ✅ | — | Completo |
| `string` concatenación | ❌ | `+` operator | Pendiente |

### Control de flujo

| Construcción | Fase 1 | Estado |
|---|---|---|
| `if/else if/else` | ✅ | Completo |
| `for` clásico | ✅ | Completo |
| `for` while | ✅ | Completo |
| `for` infinito | ✅ | Completo |
| `switch/case/default` | ✅ | Completo |
| `break/continue` | ✅ | Completo |
| `return` | ✅ | Completo |

### Funciones

| Característica | Fase 1 | Fase 2 | Estado |
|---|---|---|---|
| Sin parámetros | ✅ | — | Completo |
| 1 param int32 | ✅ | — | Completo |
| N params (x0–x7) | ❌ | ✅ | Pendiente |
| Multi-return | ❌ | ✅ | Pendiente |
| Hoisting | ✅ | — | Completo |
| Recursión | ✅ (implícita) | — | A verificar |

### Funciones built-in

| Función | Fase 1 | Fase 2 | Estado |
|---|---|---|---|
| `fmt.Println` int32/bool/string | ✅ | — | Completo |
| `fmt.Println` float32 | ⚠️ | Refactorizar | Parcial |
| `len(string)` | ❌ | ✅ | Pendiente |
| `len(array)` | ❌ | ✅ | Pendiente |
| `now()` | ❌ | ✅ | Pendiente |
| `substr()` | ❌ | ✅ | Pendiente |
| `typeOf()` | ❌ | ✅ | Pendiente |

### Arrays y punteros

| Característica | Fase 3 | Estado |
|---|---|---|
| `var a [N]T` | ✅ | Pendiente |
| Inicialización `{v1,v2}` | ✅ | Pendiente |
| Acceso `a[i]` | ✅ | Pendiente |
| Arrays multidim `[2][3]T` | ✅ | Pendiente |
| Punteros `*T`, `&x`, `*p` | ✅ | Pendiente |
| Paso por referencia | ✅ | Pendiente |

### Ejecución

| Característica | Fase 4 | Estado |
|---|---|---|
| Generar `.s` descargable | ✅ (ya) | Completo |
| Ensamblar con GCC cross | ✅ | Pendiente |
| Ejecutar con QEMU | ✅ | Pendiente |
| stdout en frontend | ✅ | Pendiente |

---

## 🔑 Decisiones de diseño importantes

### 1. Gramática: REUTILIZAR sin cambios
La gramática `Golampi.g4` ya cubre todo el lenguaje incluyendo arrays,
punteros, multi-retorno, etc. **No modificar** la gramática ni regenerar
los parsers ANTLR4. Solo ampliar los visitors.

### 2. Análisis semántico: refactorizar SemanticAnalyzer (Fase 2)
En P1 el análisis semántico está mezclado en el generator. Para P2
necesitamos un pasaje semántico separado que:
- Construya tabla de tipos con alcances
- Valide tipos en expresiones (int32 + float32 → float32, etc.)
- Resuelva nombres de funciones y sus firmas
- El generator solo consulta la tabla de tipos pre-construida

Archivo nuevo: `Backend/src/Compiler/SemanticAnalyzer.php`

### 3. Stack frame: ampliar FunctionContext para arrays
La Fase 1 tiene un límite de 256 bytes (32 variables). En Fase 3 esto
debe ser dinámico. Ampliar `FunctionContext::MAX_FRAME` a 2048 bytes
y calcular el tamaño real según las variables + arrays declarados.

### 4. Registros float: separar de int
En Fase 1 los floats se almacenan como bits en registros enteros.
En Fase 2 usar siempre registros SIMD:
- `s0–s7` para paso de parámetros float
- `str s0, [x29, #-offset]` para guardar en stack
- `ldr s0, [x29, #-offset]` para cargar

### 5. Tests: un archivo por fase
```
test/test_phase1.php  ← ya existe y pasa
test/test_phase2.php  ← crear al iniciar Fase 2
test/test_phase3.php  ← crear al iniciar Fase 3
test/test_phase4.php  ← crear al iniciar Fase 4
```

---

## 📁 Mapa de archivos

### Nuevos archivos a crear

```
Backend/src/Compiler/
  SemanticAnalyzer.php              ← Fase 2A: análisis semántico separado
  ARM64/Traits/
    FloatOpsTrait.php               ← Fase 2A: operaciones float32 SIMD
    StringOpsTrait.php              ← Fase 2B: concatenación, len, substr
    ArrayCodegenTrait.php           ← Fase 3A/3B: arrays en stack/heap
    PointerTrait.php                ← Fase 3D: punteros ARM64
  ExecutionManager.php             ← Fase 4: GCC + QEMU pipeline

Backend/test/
  test_phase2.php
  test_phase3.php
  test_phase4.php
```

### Archivos a modificar

```
Backend/src/Compiler/
  CompilationHandler.php            ← Integrar SemanticAnalyzer + Fase 4
  ARM64/ARM64Generator.php          ← use nuevos traits
  ARM64/FunctionContext.php         ← ampliar frame para arrays Fase 3
  ARM64/Traits/ExpressionsTrait.php ← float ops Fase 2
  ARM64/Traits/LiteralsTrait.php    ← float literal real Fase 2
  ARM64/Traits/FunctionCallTrait.php← multi-param Fase 2, built-ins Fase 2
  ARM64/Traits/AssignmentsTrait.php ← array/ptr assignment Fase 3
Backend/src/Api/ApiRouter.php       ← execute flag Fase 4
```

---

## 🧪 Orden de implementación sugerido

```
Semana 1: Fase 2A (float32 real) + Fase 2B (string concat)
Semana 2: Fase 2C (multi-param/return) + Fase 2D (builtins)
Semana 3: Fase 3A (arrays 1D) + Fase 3B (multidim)
Semana 4: Fase 3C (heap arrays) + Fase 3D (punteros)
Semana 5: Fase 4 (QEMU integrado) + integración frontend
Semana 6: Testing + bugfixes + documentación
```

---

## 📌 Contexto rápido para Claude (al retomar sesión)

**Si estás leyendo esto al inicio de una nueva sesión, lo más importante:**

1. **Gramática**: NO tocar. Todo está generado en `Backend/generated/`
2. **Estado actual**: Fase 1 completa. Los tests pasan con `php test/test_phase1.php`
3. **Próximo paso**: Ver qué fase dice "⏳ PENDIENTE" más arriba y revisar la sección correspondiente
4. **Estructura**: Todo el código del compilador está en `Backend/src/Compiler/ARM64/Traits/`
5. **Tests**: Siempre crear `test_phaseN.php` antes de implementar la fase N
6. **Convención**: Toda función visit* debe retornar el tipo PHP string (ej: 'int32', 'float32')
7. **El intérprete P1** (en `Backend/src/Visitor/`) NO se toca — sigue funcionando en paralelo

---

*Última actualización: Fase 1 completada. Iniciando planificación Fase 2.*