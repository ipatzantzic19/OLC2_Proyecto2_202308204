# Compilador Golampi — Fase 1 🔧

> OLC2 — Organización de Lenguajes y Compiladores 2  
> Universidad de San Carlos de Guatemala (USAC) — 1er Semestre 2026

---

## 📐 Arquitectura de la Fase 1

```
Código Golampi (.go)
       │
       ▼
  ANTLR4 Lexer/Parser  ──── generado desde GolampiLexer.g4 / GolampiParser.g4
       │
       ▼ CST (árbol de parseo)
  ARM64Generator.php   ──── extiende GolampiBaseVisitor
       │
       ▼ Código ARM64 (.s)
  CompilationResult    ──── assembly + errores + tabla de símbolos
       │
       ├── CompilationHandler.php  (API layer)
       └── ApiRouter.php           (POST /api/compile)
```

---

## 🗂 Archivos de la Fase 1

| Archivo | Propósito |
|---------|-----------|
| `src/Compiler/CompilationResult.php` | DTO con assembly, errores, tabla de símbolos |
| `src/Compiler/ARM64/FunctionContext.php` | Gestión del stack frame por función |
| `src/Compiler/ARM64/ARM64Generator.php` | Visitor principal → genera código ARM64 |
| `src/Compiler/CompilationHandler.php` | Orquesta ANTLR4 + generator + errores |
| `src/Api/ApiRouter.php` | Rutas REST: `/api/compile`, `/api/download-asm`, etc. |
| `src/Traits/ErrorHandler.php` | Normalización de errores ANTLR4 |
| `tests/test_phase1.php` | Suite de tests sin necesidad de QEMU |

### Frontend (Svelte)
| Archivo | Propósito |
|---------|-----------|
| `Frontend/src/components/Editor.svelte` | IDE principal con botón **Compilar → ARM64** |
| `Frontend/src/components/AssemblyView.svelte` | Visor ARM64 con syntax highlighting |
| `Frontend/src/components/Console.svelte` | Consola de salida del intérprete/compilador |
| `Frontend/src/components/Modal.svelte` | Modal reutilizable para reportes |
| `Frontend/src/components/ErrorsTable.svelte` | Tabla de errores (intérprete + compilador) |
| `Frontend/src/components/SymbolsTable.svelte` | Tabla de símbolos (intérprete + compilador) |
| `Frontend/src/lib/store.js` | Estado global Svelte (Svelte writable stores) |
| `Frontend/src/lib/api.js` | Funciones de llamada a la API |

---

## ⚙️ Setup

### 1. Instalar dependencias PHP
```bash
cd Backend
composer install
```

### 2. Regenerar parser ANTLR4 (solo si cambia la gramática)
```bash
java -jar tools/antlr-4.13.1-complete.jar \
  -Dlanguage=PHP -visitor \
  -o generated/ \
  grammar/GolampiLexer.g4 grammar/GolampiParser.g4
```

### 3. Levantar servidor de desarrollo PHP
```bash
cd Backend
php -S localhost:8000 public/index.php
```

### 4. Frontend Svelte
```bash
cd Frontend
npm install
npm run dev
```

---

## 🧪 Tests de la Fase 1

```bash
cd Backend
php tests/test_phase1.php
```

Cubre:
- Prólogo/epílogo ARM64 (stp/ldp/ret)
- `fmt.Println` con int32, bool, string literal
- Variables: `var`, `:=`, `const`
- Aritmética: `+`, `-`, `*`, `/`, `%`
- Asignación compuesta: `+=`, `-=`, `*=`, `/=`
- Incremento/decremento: `++`, `--`
- Lógica: `&&` (cortocircuito), `||` (cortocircuito), `!`
- Comparación: `==`, `!=`, `<`, `<=`, `>`, `>=`
- Control de flujo: `if/else if/else`, `for` (3 variantes), `switch`, `break`, `continue`
- Funciones de usuario básicas
- Errores semánticos (variable no declarada, sin `main`)
- Pool de strings / sección `.data`
- Tabla de símbolos

---

## 📦 Endpoints de la API

### Compilador (Fase 2)

| Método | Ruta | Descripción |
|--------|------|-------------|
| `POST` | `/api/compile` | Compila código Golampi → ARM64 |
| `GET`  | `/api/last-assembly` | Assembly de la última compilación |
| `GET`  | `/api/compile-errors` | Errores de la última compilación |
| `GET`  | `/api/compile-symbols` | Tabla de símbolos de la última compilación |
| `GET`  | `/api/download-asm` | Descarga el archivo `.s` generado |

### Intérprete (Proyecto 1 — se mantiene)

| Método | Ruta | Descripción |
|--------|------|-------------|
| `POST` | `/api/execute` | Interpreta código Golampi |
| `GET`  | `/api/last-errors` | Errores de la última ejecución |
| `GET`  | `/api/last-symbols` | Tabla de símbolos de la última ejecución |

---

## 🏗️ Stack Frame ARM64 (AArch64)

```
[dirección alta]
┌──────────────┐ ← sp_original
│  x29 (saved) │ ← [fp + 0]      ← stp x29, x30, [sp, #-16]!
│  x30 (saved) │ ← [fp + 8]
├──────────────┤ ← x29 = fp
│  local var 1 │ ← [fp - 8]
│  local var 2 │ ← [fp - 16]
│     ...      │
└──────────────┘ ← sp (fp - FRAME_SIZE)
[dirección baja]
```

Cada variable local ocupa **8 bytes** (alineado). El `FRAME_SIZE` se calcula en el prescan del bloque y se redondea al múltiplo de 16 más cercano.

---

## 🧮 Ejecutar el código generado (Fase 4)

```bash
# Compilar con aarch64-linux-gnu-gcc
aarch64-linux-gnu-gcc -o programa program.s -lc

# Ejecutar con QEMU
qemu-aarch64 -L /usr/aarch64-linux-gnu ./programa
```

---

## 📋 Alcance de la Fase 1

| Categoría | Estado |
|-----------|--------|
| `main()` sin parámetros | ✅ |
| Variables `int32` (`var`, `:=`, `const`) | ✅ |
| Variables `bool` | ✅ |
| Variables `string` (solo literales) | ✅ |
| Aritmética `+ - * / %` | ✅ |
| Operadores lógicos `&& \|\| !` (cortocircuito) | ✅ |
| Comparaciones `== != < <= > >=` | ✅ |
| Asignación compuesta `+= -= *= /=` | ✅ |
| `++` y `--` | ✅ |
| `if / else if / else` | ✅ |
| `for` (clásico, while, infinito) | ✅ |
| `switch / case / default` | ✅ |
| `break` y `continue` | ✅ |
| `return` | ✅ |
| `fmt.Println` (int32, bool, string) | ✅ |
| Funciones de usuario (sin params / 1 param int32) | ✅ |
| Pool de strings internados | ✅ |
| Tabla de símbolos | ✅ |
| Reporte de errores | ✅ |
| `float32` completo (SIMD) | 🔜 Fase 2 |
| Funciones con múltiples parámetros/returns | 🔜 Fase 2 |
| `string` concatenación | 🔜 Fase 2 |
| Arrays | 🔜 Fase 3 |
| Ejecución QEMU integrada | 🔜 Fase 4 |