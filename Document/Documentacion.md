# Documentación Técnica — Golampi Compiler

> **Organización de Lenguajes y Compiladores 2**  
> Universidad San Carlos de Guatemala — Facultad de Ingeniería  
> 1er Semestre 2026

---

## Índice

1. [Descripción General del Proyecto](#1-descripción-general-del-proyecto)
2. [Gramática Formal del Lenguaje Golampi](#2-gramática-formal-del-lenguaje-golampi)
3. [Arquitectura del Sistema](#3-arquitectura-del-sistema)
4. [Diagrama de Clases](#4-diagrama-de-clases)
5. [Flujo de Procesamiento y Tabla de Símbolos](#5-flujo-de-procesamiento-y-tabla-de-símbolos)
6. [Fases del Compilador](#6-fases-del-compilador)
7. [Modelo de Memoria ARM64](#7-modelo-de-memoria-arm64)
8. [Endpoints de la API REST](#8-endpoints-de-la-api-rest)
9. [Estructura del Proyecto](#9-estructura-del-proyecto)
10. [Guía de Instalación y Ejecución](#10-guía-de-instalación-y-ejecución)

---

## 1. Descripción General del Proyecto

**Golampi Compiler** es un compilador académico completo para el lenguaje Golampi, un lenguaje de programación inspirado en la sintaxis y semántica de Go (Golang), desarrollado como proyecto del curso de Organización de Lenguajes y Compiladores 2.

El compilador implementa las fases clásicas de compilación definidas por Aho, Lam, Sethi y Ullman en _"Compiladores: Principios, Técnicas y Herramientas"_:

| Fase | Herramienta/Tecnología |
|---|---|
| Análisis Léxico | ANTLR4 (GolampiLexer) |
| Análisis Sintáctico | ANTLR4 (GolampiParser + CST) |
| Análisis Semántico | Visitor Pattern (ARM64Generator) |
| Generación de Código | Ensamblador ARM64 (AArch64) |
| Ensamblado y Ejecución | GNU as + QEMU (aarch64) |

**Stack Tecnológico:**

- **Backend:** PHP 8, ANTLR4 Runtime para PHP
- **Frontend:** Svelte + Vite, Monaco Editor
- **Generación de código:** ARM64 (AArch64) ensamblador GNU
- **Ejecución:** QEMU (modo usuario, `qemu-aarch64`)

---

## 3. Arquitectura del Sistema

El sistema sigue una arquitectura web monolítica con separación lógica entre frontend y backend.

![Arquitectura del Sistema](img/Arquitectura%20del%20Sistema.png)

### 3.1 Flujo de Compilación Completo

![Flujo de Compilación Completo](img/Flujo%20de%20Compilaci%C3%B3n%20Completo)

---

## 4. Diagrama de Clases

### 4.1 Clases Principales del Compilador

![Clases Principales del Compilador](img/Clases%20Principales%20del%20Compilador)

### 4.2 Traits del ARM64Generator (Organización Modular)

![Traits del ARM64Generator (Organización Modular)](img/Traits%20del%20ARM64Generator%20%28Organizaci%C3%B3n%20Modular%29)

### 4.3 Clases del FunctionContext (Gestión del Stack Frame)

![Clases del FunctionContext (Gestión del Stack Frame)](img/Clases%20del%20FunctionContext%20%28Gesti%C3%B3n%20del%20Stack%20Frame%29)

---

## 5. Flujo de Procesamiento y Tabla de Símbolos

### 5.1 Flujo de Procesamiento del Compilador (dos pasadas)

![Flujo de Procesamiento y Tabla de Símbolos](img/Flujo%20de%20Procesamiento%20y%20Tabla%20de%20S%C3%ADmbolos)

### 5.2 Construcción de la Tabla de Símbolos

La tabla de símbolos se construye de forma incremental durante el recorrido del AST. Cada vez que se visita una declaración, el `SymbolManager` registra el identificador.

![Construcción de la Tabla de Símbolos](img/Construcci%C3%B3n%20de%20la%20Tabla%20de%20S%C3%ADmbolos)

### 5.3 Formato de la Tabla de Símbolos

| Campo | Tipo | Descripción |
|---|---|---|
| `identifier` | string | Nombre del identificador |
| `type` | string | Tipo del símbolo (`int32`, `float32`, `bool`, `string`, `rune`, `function`, `array`) |
| `scope` | string | Ámbito donde fue declarado (`global`, nombre de función, `bloque`) |
| `value` | mixed | Valor inicial (si aplica). `null` para funciones |
| `line` | int | Línea en el código fuente |
| `column` | int | Columna en el código fuente |
| `isConstant` | bool | `true` si fue declarado con `const` |

**Ejemplo de tabla generada para el programa de referencia:**

```
Identificador | Tipo     | Ámbito | Valor | Línea | Columna
-------------|----------|--------|-------|-------|--------
a            | int32    | main   | 8     | 2     | 5
b            | int32    | main   | 7     | 3     | 5
resultado    | int32    | main   | 0     | 4     | 9
```

---

## 6. Fases del Compilador

### 6.1 Análisis Léxico (GolampiLexer)

El lexer generado por ANTLR4 reconoce los siguientes tokens:

![Análisis Léxico (GolampiLexer)](img/An%C3%A1lisis%20L%C3%A9xico%20%28GolampiLexer%29)

### 6.2 Análisis Sintáctico (GolampiParser)

El parser construye un Árbol Sintáctico Concreto (CST). El patrón Visitor es usado para recorrerlo:

![Análisis Sintáctico (GolampiParser)](img/An%C3%A1lisis%20Sint%C3%A1ctico%20%28GolampiParser%29)

### 6.3 Análisis Semántico

Realizado durante el recorrido del CST por el `ARM64Generator`. Detecta y reporta:

| Tipo de error semántico | Ejemplo |
|---|---|
| Variable no declarada | Usar `z` sin haberla declarado |
| Reutilización de identificador | Declarar `x` dos veces en el mismo scope |
| Incompatibilidad de tipos | Sumar `int32` con `bool` |
| Función no definida | Llamar a `foo()` que no existe |
| `break`/`continue` fuera de bucle | Usar `break` fuera de `for` o `switch` |
| Ausencia de `main` | Programa sin función `main` |

### 6.4 Generación de Código ARM64

#### Ejemplo de traducción

**Entrada (Golampi):**
```go
func main() {
    a := 8
    b := 7
    var resultado int32
    if a > b {
        resultado = a
    } else {
        resultado = b
    }
    fmt.Println(resultado)
}
```

**Salida (ARM64):**
```asm
_start:
    stp x29, x30, [sp, #-16]!   # guardar fp y lr
    mov x29, sp                   # frame pointer
    sub sp, sp, #32               # reservar frame (3 vars × 8 + alineación)

    # a := 8
    mov x0, #8
    str x0, [x29, #-8]

    # b := 7
    mov x0, #7
    str x0, [x29, #-16]

    # var resultado int32 = 0
    mov x0, xzr
    str x0, [x29, #-24]

    # if a > b
    ldr x0, [x29, #-8]           # cargar a
    mov x1, x0
    ldr x0, [x29, #-16]          # cargar b
    cmp x1, x0
    b.le .else_branch_1           # si a <= b → else

    # resultado = a
    ldr x0, [x29, #-8]
    str x0, [x29, #-24]
    b .if_end_0

.else_branch_1:
    # resultado = b
    ldr x0, [x29, #-16]
    str x0, [x29, #-24]

.if_end_0:
    # fmt.Println(resultado) → syscall write
    ldr x0, [x29, #-24]
    add x3, x0, #48              # convertir a ASCII
    adrp x4, buffer
    add x4, x4, :lo12:buffer
    strb w3, [x4]
    mov x0, #1                   # fd stdout
    mov x1, x4
    mov x2, #2                   # 2 bytes (dígito + \n)
    mov x8, #64                  # syscall write
    svc #0

    # epílogo
    add sp, sp, #32
    ldp x29, x30, [sp], #16
    mov x0, #0
    mov x8, #93                  # syscall exit
    svc #0
```

---

## 7. Modelo de Memoria ARM64

### 7.1 Stack Frame (Registro de Activación)

![Stack Frame (Registro de Activación)](img/Stack%20Frame%20%28Registro%20de%20Activaci%C3%B3n%29)

### 7.2 Convención de Registros AArch64 (AAPCS64)

| Registro(s) | Rol | Quién lo preserva |
|---|---|---|
| `x0 – x7` | Argumentos enteros / valor de retorno | Caller-saved |
| `s0 – s7` | Argumentos float32 / retorno float | Caller-saved |
| `x0 – x1` | Retorno múltiple (hasta 128 bits) | — |
| `x19 – x28` | Variables temporales preservadas | Callee-saved |
| `x29` | Frame Pointer (FP) | Callee-saved |
| `x30` | Link Register (LR / dirección de retorno) | Callee-saved |
| `sp` | Stack Pointer | Callee-saved |
| `xzr` | Registro cero (siempre 0) | — |

### 7.3 Instrucciones ARM64 Utilizadas

| Categoría | Instrucciones |
|---|---|
| Aritmética entera | `add`, `sub`, `mul`, `sdiv`, `msub`, `neg` |
| Aritmética flotante | `fadd`, `fsub`, `fmul`, `fdiv`, `fneg` |
| Conversión de tipos | `scvtf`, `fcvtzs`, `fcvt`, `fmov` |
| Carga/almacenamiento | `ldr`, `str`, `ldrb`, `strb`, `ldrsw`, `stp`, `ldp` |
| Comparación | `cmp`, `fcmp`, `cset`, `cbz`, `cbnz` |
| Saltos | `b`, `b.eq`, `b.ne`, `b.lt`, `b.le`, `b.gt`, `b.ge`, `bl`, `ret` |
| Inmediatos grandes | `mov`, `movz`, `movk` |
| Stack | `sub sp, sp, #N`, `add sp, sp, #N` |
| Syscalls | `svc #0` (con número en `x8`) |

### 7.4 Syscalls Linux AArch64 Utilizados

| Syscall | Número (`x8`) | Descripción |
|---|---|---|
| `write` | 64 | Escribir en stdout |
| `exit` | 93 | Terminar el proceso |

### 7.5 Secciones del Archivo .s Generado

```
.section .data          ← strings literales, floats, buffers estáticos
    .str_0: .string "Hola"
    .flt_0: .single 3.14
    msg:    .ascii "\n"
    buffer: .ascii "0\n"

.section .text          ← código ejecutable
.align 2
.global _start

_start:                 ← función main (punto de entrada)
    ...instrucciones...

suma:                   ← funciones de usuario
    ...instrucciones...

# Runtime helpers       ← generados solo si se usan
golampi_concat:
    ...
golampi_substr:
    ...
golampi_now:
    ...
```

---

## 8. Endpoints de la API REST

### 8.1 Compilador

| Método | Ruta | Descripción | Cuerpo / Respuesta |
|---|---|---|---|
| `POST` | `/api/compile` | Compila código Golampi → ARM64 | Body: `{"code": "..."}` / Response: `{success, assembly, errors, symbolTable, executionTime}` |
| `GET` | `/api/last-assembly` | Assembly de la última compilación | Response: `{success, assembly}` |
| `GET` | `/api/compile-errors` | Errores de la última compilación | Response: `{success, errors[], errorCount}` |
| `GET` | `/api/compile-symbols` | Tabla de símbolos | Response: `{success, symbolTable[], symbolCount}` |
| `GET` | `/api/download-asm` | Descarga el archivo `.s` | Content-Disposition: attachment |

### 8.2 Intérprete (Proyecto 1 — se mantiene)

| Método | Ruta | Descripción |
|---|---|---|
| `POST` | `/api/execute` | Interpreta código Golampi |
| `GET` | `/api/last-errors` | Errores de la última ejecución |
| `GET` | `/api/last-symbols` | Tabla de símbolos de la última ejecución |

### 8.3 Formato de Respuesta del Compilador

```json
{
  "success": true,
  "assembly": ".section .data\n...",
  "errors": [
    {
      "id": 1,
      "type": "Semántico",
      "description": "Variable 'z' no declarada",
      "line": 5,
      "column": 12
    }
  ],
  "symbolTable": [
    {
      "identifier": "resultado",
      "type": "int32",
      "scope": "main",
      "value": 0,
      "line": 4,
      "column": 9,
      "isConstant": false
    }
  ],
  "programOutput": "",
  "executionTime": "12.5ms",
  "timestamp": "2026-04-18 10:30:00",
  "errorCount": 0,
  "symbolCount": 3
}
```

---

## 9. Estructura del Proyecto

```
Golampi/
├── Backend/
│   ├── generated/                    # Código generado por ANTLR4
│   │   ├── GolampiLexer.php
│   │   ├── GolampiParser.php
│   │   ├── GolampiVisitor.php
│   │   └── GolampiBaseVisitor.php
│   ├── src/
│   │   ├── Api/
│   │   │   ├── ApiRouter.php          # Enrutador REST
│   │   │   └── ExecutionHandler.php   # Handler del intérprete (P1)
│   │   ├── Compiler/
│   │   │   ├── CompilationHandler.php # Orquestador del compilador
│   │   │   ├── CompilationResult.php  # DTO de resultado
│   │   │   └── ARM64/
│   │   │       ├── ARM64Generator.php # Visitor principal
│   │   │       ├── FunctionContext.php
│   │   │       ├── FunctionContext/   # Managers del stack frame
│   │   │       │   ├── LocalsManager.php
│   │   │       │   ├── ArrayManager.php
│   │   │       │   ├── RegisterManager.php
│   │   │       │   ├── FrameCalculator.php
│   │   │       │   └── FunctionContextHandler.php
│   │   │       └── Traits/
│   │   │           ├── Emitter/       # emit(), label(), buildAssembly()
│   │   │           ├── Declarations/  # var, const, :=, prescan
│   │   │           ├── Expressions/   # aritmética, lógica, comparación
│   │   │           ├── ControlFlow/   # if, for, switch, break, continue, return
│   │   │           ├── Assignments/   # =, +=, ++, arrays, punteros
│   │   │           ├── FunctionCall/  # fmt.Println, builtins, funciones usuario
│   │   │           ├── Helpers/       # tipos, frame, tabla de símbolos
│   │   │           ├── FloatOps/      # SIMD float32
│   │   │           ├── Literals/      # int, float, rune, string, bool, nil
│   │   │           ├── StringPool/    # pool de strings .data
│   │   │           ├── StringOps/     # concat, substr, now, typeOf
│   │   │           ├── RegisterAllocation/  # Chaitin-Briggs coloring
│   │   │           └── Phases/        # PrescanPhase, GenerationPhase, ProgramPhase
│   │   └── Traits/
│   │       └── ErrorHandler.php       # Manejo de errores ANTLR
│   ├── Golampi.g4                     # Gramática ANTLR4
│   ├── index.php                      # Entry point
│   ├── router.php
│   ├── test/                          # Tests por fase
│   │   ├── test_phase1.php
│   │   ├── test_phase2.php
│   │   ├── test_phase3.php
│   │   ├── test_phase4.php
│   │   └── example.go
│   └── vendor/                        # Dependencias PHP (Composer)
├── Frontend/
│   ├── src/
│   │   ├── App.svelte
│   │   ├── components/
│   │   │   ├── Editor.svelte          # IDE principal
│   │   │   ├── AssemblyView.svelte    # Visor ARM64 con highlight
│   │   │   ├── Console.svelte         # Consola de salida
│   │   │   ├── Modal.svelte           # Modal reutilizable
│   │   │   ├── ErrorsTable.svelte     # Tabla de errores
│   │   │   └── SymbolsTable.svelte    # Tabla de símbolos
│   │   └── lib/
│   │       ├── store.js               # Estado global (Svelte writable stores)
│   │       └── api.js                 # Funciones de llamada a la API
│   ├── index.html
│   └── vite.config.js
├── Document/
│   └── README.md
└── start.sh                           # Script de inicio rápido
```

---

## 10. Guía de Instalación y Ejecución

### 10.1 Requisitos Previos

| Herramienta | Versión mínima |
|---|---|
| PHP | 8.1+ |
| Composer | 2.x |
| Java (para ANTLR4) | 11+ |
| Node.js | 18+ |
| npm | 9+ |
| ANTLR4 | 4.13.x |
| aarch64-linux-gnu-gcc | (opcional, para ensamblar) |
| qemu-aarch64 | (opcional, para ejecutar) |

### 10.2 Instalación del Backend

```bash
# 1. Instalar dependencias PHP
cd Backend
composer install

# 2. (Solo si se modifica la gramática) Regenerar parser ANTLR4
java -jar tools/antlr-4.13.1-complete.jar \
     -Dlanguage=PHP -visitor \
     -o generated/ \
     Golampi.g4

# 3. Iniciar servidor de desarrollo PHP
php -S localhost:8000 index.php
```

### 10.3 Instalación del Frontend

```bash
cd Frontend
npm install
npm run dev
# Disponible en http://localhost:5173
```

### 10.4 Inicio Rápido (Script)

```bash
chmod +x start.sh
./start.sh
```

El script inicia ambos servicios simultáneamente y muestra las URLs disponibles.

### 10.5 Ejecución del Código Generado

Después de compilar desde la interfaz y descargar el archivo `program.s`:

```bash
# Ensamblar el código ARM64
aarch64-linux-gnu-as -o program.o program.s

# Enlazar el objeto (bare-metal / standalone)
aarch64-linux-gnu-ld -static -o programa program.o

# Ejecutar con QEMU en modo usuario
qemu-aarch64 ./programa
```

**O bien, usando GCC (para programas que usan libc):**
```bash
aarch64-linux-gnu-gcc -o programa program.s -static
qemu-aarch64 -L /usr/aarch64-linux-gnu ./programa
```

### 10.6 Ejecución de Tests

```bash
cd Backend

# Test Fase 1: Variables, aritmética, control de flujo básico
php test/test_phase1.php

# Test Fase 2: float32, funciones multi-parámetro, strings, builtins
php test/test_phase2.php

# Test Fase 3: Arrays multidimensionales
php test/test_phase3.php

# Test Fase 4: Ciclo completo compilar → ensamblar → ejecutar
php test/test_phase4.php test/example.go
```

---

## Apéndice: Funciones Embebidas (Built-ins)

| Función | Firma | Descripción |
|---|---|---|
| `fmt.Println` | `fmt.Println(args...)` | Imprime uno o más valores en stdout con salto de línea |
| `len` | `len(s string\|array) int32` | Retorna la longitud de una cadena o array |
| `substr` | `substr(s string, inicio int32, longitud int32) string` | Extrae una subcadena |
| `now` | `now() string` | Retorna la fecha/hora actual en formato `YYYY-MM-DD HH:MM:SS` |
| `typeOf` | `typeOf(expr) string` | Retorna el nombre del tipo de una expresión |

---

## Apéndice: Tabla de Promoción de Tipos

### Suma y Resta (`+`, `-`)

| | `int32` | `float32` | `bool` | `rune` | `string` |
|---|---|---|---|---|---|
| **int32** | int32 | float32 | — | int32 | — |
| **float32** | float32 | float32 | — | float32 | — |
| **bool** | — | — | — | — | — |
| **rune** | int32 | float32 | — | int32 | — |
| **string** | — | — | — | — | string |

### Módulo (`%`)

| | `int32` | `rune` |
|---|---|---|
| **int32** | int32 | int32 |
| **rune** | int32 | int32 |

### Comparaciones (`==`, `!=`, `>`, `>=`, `<`, `<=`)

Resultado siempre es `bool`. Operandos compatibles: `int32` ↔ `float32` ↔ `rune`, `bool` ↔ `bool`, `string` ↔ `string`.

---
