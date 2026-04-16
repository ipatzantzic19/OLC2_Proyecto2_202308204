# ANÁLISIS: Carga de Variables en Registros ARM64

## 📋 Resumen Ejecutivo

El compilador Golampi carga variables en registros ARM64 mediante un flujo de 3 capas:

| Capa | Archivo | Responsabilidad |
|------|---------|-----------------|
| **Parser** | `Golampi.g4` | Reconoce `ID` → dispara `visitIdentifier` |
| **Visitador** | `IdentifierVisitor.php` | Resuelve tipo y emite instrucción `ldr` |
| **Contexto** | `FunctionContext.php` | Calcula offset de variable en stack frame |

---

## 🎯 Punto de Entrada: IdentifierVisitor.php

**Archivo:** `/Backend/src/Compiler/ARM64/Traits/Helpers/IdentifierVisitor.php`

### Método Core: `visitIdentifier($ctx)`

```php
public function visitIdentifier($ctx)
{
    $name = $ctx->ID()->getText();
    
    // ✓ Validar que variable existe
    if (!$this->func->hasLocal($name)) {
        $this->addError('Semántico', "Variable '$name' no declarada");
        $this->emit('mov x0, xzr', "error");
        return 'int32';
    }
    
    // ✓ Resolver ubicación en stack
    $offset = $this->func->getOffset($name);
    $type   = $this->func->getType($name);
    
    // ✓ Emitir instrucción de carga según tipo
    if ($type === 'float32') {
        $this->emit("ldr s0, [x29, #-$offset]", "$name (float32)");
    } else {
        $this->emit("ldr x0, [x29, #-$offset]", "$name ($type)");
    }
    return $type;
}
```

### Proceso Paso a Paso:

1. **Extrae nombre:** `$name = $ctx->ID()->getText()` → `"x"`
2. **Verifica declaración:** `$this->func->hasLocal($name)` → `true/false`
3. **Calcula offset:** `$this->func->getOffset($name)` → `16` (bytes desde x29)
4. **Obtiene tipo:** `$this->func->getType($name)` → `"int32"` o `"float32"`
5. **Elige registro:**
   - `x0, x1, x2...` para enteros
   - `s0, s1, s2...` para floats
6. **Emite instrucción:**
   ```assembly
   ldr x0, [x29, #-16]   // Si int32
   ldr s0, [x29, #-16]   // Si float32
   ```

---

## 🔍 Resolución de Variables: FunctionContext.php

**Archivo:** `/Backend/src/Compiler/ARM64/FunctionContext.php`

### Stack Frame Layout (AAPCS64):

```
Dirección alta
    ┌──────────────────────┐
    │ x29 (saved FP)       │ ← [x29 + 0]
    │ x30 (saved LR)       │ ← [x29 + 8]
    ├──────────────────────┤ ← x29 (frame pointer)
    │ callee-saved regs    │ ← [x29 - 8...N*8]
    ├──────────────────────┤
    │ var1 (int32)         │ ← [x29 - 16]    offset = 16
    │ var2 (int32)         │ ← [x29 - 24]    offset = 24
    │ var3 (float32)       │ ← [x29 - 32]    offset = 32
    ├──────────────────────┤
    │ temporales expr.     │
    └──────────────────────┘ ← sp
Dirección baja
```

### Métodos Críticos:

#### 1. `hasLocal(string $name): bool`
```php
// Ubicación: FunctionContext/LocalsManager.php
public function hasLocal(string $name): bool
{
    return isset($this->locals[$name]);
}
```
**E.g.:** `hasLocal("x")` → `true/false`

#### 2. `getOffset(string $name): int`
```php
// Ubicación: FunctionContext.php
public function getOffset(string $name): int
{
    if ($this->hasLocal($name)) {
        return $this->getLocalOffset($name);  // LocalsManager
    }
    if ($this->hasArray($name)) {
        $info = $this->getArrayInfo($name);
        return $info['base_offset'] ?? 0;      // ArrayManager
    }
    return 0;
}
```
**E.g.:** `getOffset("x")` → `16` bytes desde x29

#### 3. `getType(string $name): string`
```php
// Ubicación: FunctionContext.php
public function getType(string $name): string
{
    if ($this->hasLocal($name)) {
        return $this->getLocalType($name);     // 'int32', 'float32', etc.
    }
    if ($this->hasArray($name)) {
        return 'array';
    }
    return 'int32';
}
```
**E.g.:** `getType("x")` → `"int32"` o `"float32"`

---

## 🔧 Casos de Uso Principales

### 1️⃣ Expresión Simple: `print(x)`

**Archivo:** `Traits/Helpers/IdentifierVisitor.php`

```
Golampi:  print(x)
   ↓ (parser)
Grammar:  primary → ID                # Identifier
   ↓ (visitor)
Code:     visitIdentifier($ctx)
   ↓ (emit)
ARM64:    ldr x0, [x29, #-16]    // Carga x al registro x0
          // Continúa con print(x0)
```

**Instrucción emitida:**
```assembly
ldr x0, [x29, #-16]         ; cargar variable x de stack
```

---

### 2️⃣ Address-Of: `&x` (Obtener dirección)

**Archivo:** `Traits/Expressions/UnaryOps.php`

```php
public function visitAddressOf($ctx)  // Regla grammar: '&' ID
{
    $name = $ctx->ID()->getText();    // "x"
    
    if (!$this->func->hasLocal($name)) {
        $this->addError('Semántico', "Variable '$name' no declarada");
        return 'pointer';
    }
    
    $offset = $this->func->getOffset($name);  // 16
    
    // ✓ Calcula dirección relativa a frame pointer
    // Dirección de x = x29 - 16 = &x
    $this->emit("sub x0, x29, #$offset", "&$name → dirección");
    
    return 'pointer';
}
```

**Instrucción emitida:**
```assembly
sub x0, x29, #16             ; x0 = x29 - 16 = &x (dirección)
```

---

### 3️⃣ Dereference: `*ptr` (Leer desde puntero)

**Archivo:** `Traits/Expressions/UnaryOps.php`

```php
public function visitDereference($ctx)  // Regla grammar: '*' unary
{
    $this->visit($ctx->unary());      // Evalúa expr → x0 (contiene dirección)
    
    // ✓ x0 contiene dirección, carga el valor desde esa dirección
    $this->emit('ldr x0, [x0]', '*ptr → valor en x0');
    
    return 'int32';
}
```

**Instrucción emitida:**
```assembly
ldr x0, [x0]                 ; Lee valor desde dirección en x0
```

---

### 4️⃣ Increment: `x++` o `x--`

**Archivo:** `Traits/Assignments/IncrementDecrement.php`

```php
// x++:
$offset = $this->func->getOffset($name);
$this->emit("ldr x0, [x29, #-$offset]", "cargar $name");
$this->emit("add x0, x0, #1", "incrementar");
$this->emit("str x0, [x29, #-$offset]", "guardar");

// Es equivalente a: x = x + 1
```

**Instrucciones emitidas:**
```assembly
ldr x0, [x29, #-16]          ; Cargar x
add x0, x0, #1               ; Incrementar
str x0, [x29, #-16]          ; Guardar resultado
```

---

### 5️⃣ Asignación Compuesta: `x += 5`

**Archivo:** `Traits/Assignments/SimpleAssignment.php`

```php
// 1. Cargar valor anterior de x
$this->loadFromFrame($varType, $offset);  // ldr x0, [x29, #-offset]

// 2. Push a stack (guardar lhs temporalmente)
$this->pushStack();                      // str x0, [sp,...]

// 3. Evaluar rhs (5)
$this->visit($ctx->expression());        // Resultado en x0

// 4. Cargar lhs desde stack
$this->emit('ldr x1, [sp]', 'lhs ← stack');

// 5. Sumar
$this->emit('add x0, x1, x0', 'x0 = lhs + rhs');

// 6. Guardar resultado
$this->storeToFrame($varType, $offset);  // str x0, [x29, #-offset]
```

Método helper `loadFromFrame`:
```php
protected function loadFromFrame(string $type, int $offset): void
{
    if ($type === 'float32') {
        $this->emit("ldr s0, [x29, #-$offset]", 'cargar float32');
    } else {
        $this->emit("ldr x0, [x29, #-$offset]", "cargar $type");
    }
}
```

**Instrucciones emitidas:**
```assembly
ldr x0, [x29, #-16]          ; Cargar x (lhs)
str x0, [sp, #...]           ; Push a stack
mov x0, #5                   ; Literal 5
ldr x1, [sp]                 ; Pop lhs
add x0, x1, x0               ; Suma
add sp, sp, #16              ; Ajusta stack pointer
str x0, [x29, #-16]          ; Guardar resultado
```

---

### 6️⃣ Acceso a Array: `a[i]`

**Archivo:** `Traits/Expressions/UnaryOps.php`

```php
public function visitArrayAccess($ctx)  // Regla grammar: ID ('[' expression ']')+
{
    $name = $ctx->ID()->getText();       // "a"
    
    // ✓ Valida que es un array
    if (!$this->func->hasArray($name)) {
        $this->addError('Semántico', "Array '$name' no declarado");
        return 'int32';
    }
    
    // ✓ Evaluaría índices, calcularía offsets, y cargaría elemento
    // Fase 3: implementa cálculo dinámico de índices
}
```

**Instrucciones emitidas (conceptualmente):**
```assembly
; Evaluar índice i → x0
; ...código evaluación...

; Cargar dirección base del array
ldr x1, [x29, #-32]          ; x1 = dirección base de array a

; Calcular offset: base + (i * sizeof(elemento))
add x0, x1, x0
add x0, x0, x0               ; Multiplica por 8 (tamaño int32)

; Cargar elemento
ldr x0, [x0]                 ; x0 = a[i]
```

---

### 7️⃣ Operaciones Binarias: `x + y`

**Archivo:** `Traits/Expressions/ArithmeticOps.php`

```
Golampi:  x + y
   ↓ (parser)
Grammar:  additive → primary x ADD primary y
   ↓ (visitAdditiveExpression)
Code:     visit(lhs) → x0
          push x0
          visit(rhs) → x0
          pop x1
          add x0, x1, x0
```

**Instrucciones emitidas:**
```assembly
ldr x0, [x29, #-16]          ; x → x0
str x0, [sp, #-16]!          ; Push x0
ldr x0, [x29, #-24]          ; y → x0
ldr x1, [sp], #16            ; Pop x al x1
add x0, x1, x0               ; x0 = x + y
```

---

## 📊 Tabla de Instrucciones LDR

| Patrón | Instrucción | Tipo | Propósito |
|--------|------------|------|----------|
| Cargar variable int32 | `ldr x0, [x29, #-16]` | int32 | Traer variable del frame |
| Cargar variable float32 | `ldr s0, [x29, #-16]` | float32 | Traer float al registro SIMD |
| Cargar desde stack | `ldr x1, [sp]` | int32 | Pop temporal |
| Cargar desde stack (float) | `ldr s0, [sp]` | float32 | Pop float temporal |
| Dereferencia puntero | `ldr x0, [x0]` | int32* | Leer valor desde dirección |
| Lectura genérica | `ldr x1, [x2]` | int32 | Leer desde dirección en x2 |

---

## 📁 Estructura de Archivos Clave

```
Backend/src/Compiler/ARM64/
│
├─ FunctionContext.php                      [CORE: Métodos getOffset, getType, hasLocal]
│  ├─ FunctionContext/LocalsManager.php     [Gestión de variables locales]
│  ├─ FunctionContext/ArrayManager.php      [Gestión de arrays multidimensionales]
│  └─ FunctionContext/FrameCalculator.php   [Cálculo de tamaño de frame]
│
├─ Traits/
│  │
│  ├─ Helpers/
│  │  ├─ IdentifierVisitor.php              [🔴 VISITADOR PRINCIPAL: visitIdentifier]
│  │  ├─ HelpersHandler.php                 [Orquestador de helpers]
│  │  └─ FrameAllocator.php                 [Asignación de memoria del frame]
│  │
│  ├─ Expressions/
│  │  ├─ ExpressionEntry.php                [Punto entrada: visitExpression]
│  │  ├─ UnaryOps.php                       [visitAddressOf, visitDereference, visitArrayAccess]
│  │  ├─ ArithmeticOps.php                  [Operaciones binarias con ldr stack]
│  │  ├─ Comparisons.php                    [Comparaciones]
│  │  └─ LogicalOps.php                     [Operaciones lógicas]
│  │
│  ├─ Assignments/
│  │  ├─ SimpleAssignment.php               [loadFromFrame, storeToFrame]
│  │  ├─ IncrementDecrement.php             [++, -- con ldr]
│  │  ├─ PointerAssignment.php              [*ptr = expr]
│  │  └─ ArrayAssignment.php                [array[i] = expr]
│  │
│  ├─ Literals/
│  │  ├─ LiteralsHandler.php                [Orquestador de literales]
│  │  ├─ IntLiteral.php                     [Literales enteros: mov x0, #N]
│  │  ├─ FloatLiteral.php                   [Literales float: adrp+ldr s0]
│  │  ├─ StringLiteral.php                  [Literales string: adrp+add x0]
│  │  └─ ScalarLiteral.php                  [true, false, nil]
│  │
│  └─ Emitter/
│     └─ AssemblyBuilder.php                 [Construcción final del archivo .s]
│
└─ ARM64Generator.php                        [Generador principal, orquesta todo]
```

---

## 🔗 Grafo de Flujo de Variable

```
┌─────────────────────────────────────────────────────────────┐
│ 1. PARSER (ANTLR - Golampi.g4)                              │
│    Rule: primary → ID    # Identifier                        │
└──────────────────────┬──────────────────────────────────────┘
                       ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. VISITOR (IdentifierVisitor.php)                          │
│    visitIdentifier($ctx) es disparado                        │
│    ✓ Extrae nombre variable: "x"                            │
│    ✓ Valida con FunctionContext.hasLocal("x")               │
│    ✓ Obtiene offset, tipo → ldr x0/s0, [x29, #-offset]     │
└──────────────────────┬──────────────────────────────────────┘
                       ↓
┌─────────────────────────────────────────────────────────────┐
│ 3. FUNCTION CONTEXT (FunctionContext.php)                   │
│    ✓ LocalsManager: hasLocal(), getLocalOffset()            │
│    ✓ Resolve offset en stack frame (16, 24, 32, ...)        │
│    ✓ Resolve tipo (int32, float32, bool, ...)               │
└──────────────────────┬──────────────────────────────────────┘
                       ↓
┌─────────────────────────────────────────────────────────────┐
│ 4. EMITTER (AssemblyBuilder.php)                            │
│    Genera: ldr x0, [x29, #-16]                              │
│    Genera: ldr s0, [x29, #-24]                              │
└──────────────────────┬──────────────────────────────────────┘
                       ↓
┌─────────────────────────────────────────────────────────────┐
│ 5. ASSEMBLY OUTPUT                                          │
│    .text                                                    │
│    main:                                                    │
│      ldr x0, [x29, #-16]    ; Carga variable x              │
│      ...                                                    │
└─────────────────────────────────────────────────────────────┘
```

---

## 💡 Regla Universal

```
TODA referencia a variable en expresión Golampi sigue el patrón:

    IDENTIFIER en Grammar
        ↓
    visitIdentifier($ctx) disparado
        ↓
    hasLocal() verifica existencia
        ↓
    getOffset() obtiene ubicación en stack
    getType()   obtiene tipo (qué registro)
        ↓
    Emite instrucción ldr corrrecta:
    
    if (type === 'float32')
        ldr s0, [x29, #-offset]
    else
        ldr x0, [x29, #-offset]
```

---

## 🐛 Validaciones y Manejo de Errores

| Validación | Ubicación | Acción |
|------------|-----------|--------|
| Variable no existe | `IdentifierVisitor.php` L32 | `addError` + emite `mov x0, xzr` |
| Tipo incorrecto | `SimpleAssignment.php` | Aplica coerción (scvtf, fcvtzs) |
| Offset inválido | `FunctionContext.php` | Retorna 0 (fallback seguro) |
| Array no declarado | `UnaryOps.php` L134 | `addError` + emite `mov x0, xzr` |

---

## 📝 Ejemplo Completo: `var x int32 = 5; x++`

```golampi
var x int32 = 5
x++
```

**Compilación:**

1. **Declaración:** `var x int32`
   - LocalsManager registra: `x → offset=16, type='int32'`

2. **Inicialización:** `= 5`
   - IntLiteral: `mov x0, #5`
   - Store: `str x0, [x29, #-16]`

3. **Increment:** `x++`
   - IdentifierVisitor: `ldr x0, [x29, #-16]` carga x
   - Arithmetic: `add x0, x0, #1` incrementa
   - Store: `str x0, [x29, #-16]` guarda

**ARM64 generado:**
```assembly
main:
    stp x29, x30, [sp, #-16]!
    mov x29, sp
    sub sp, sp, #16
    
    ; Inicialización: x = 5
    mov x0, #5
    str x0, [x29, #-16]        ; x guardado
    
    ; Increment: x++
    ldr x0, [x29, #-16]        ; Carga x actual
    add x0, x0, #1             ; Incrementa
    str x0, [x29, #-16]        ; Guarda resultado
    
    mov x0, xzr
    ldp x29, x30, [sp], #16
    ret
```

---

## 🎓 Conclusión

El compilador Golampi carga variables en registros ARM64 mediante:

1. **Reconocimiento:** Parser ANTLR detalla `ID` → dispara visitador
2. **Resolución:** FunctionContext resuelve ubicación (offset) y tipo
3. **Emisión:** IdentifierVisitor emite instrucción `ldr` correcta
4. **Registro:** `x0/x1/x2...` para enteros, `s0/s1/s2...` para floats

**Instrucción clave:**
```assembly
ldr <registro>, [x29, #-<offset>]
```

Donde `offset` se calcula como distancia desde frame pointer, y el tipo determina el registro.

