# REFERENCIA RÁPIDA: Métodos que Cargan Variables

## 🔴 MÉTODO PRINCIPAL

### IdentifierVisitor.php → visitIdentifier($ctx)
**Ubicación:** `Backend/src/Compiler/ARM64/Traits/Helpers/IdentifierVisitor.php` L26

```php
public function visitIdentifier($ctx)  // ← Disparado cuando parser ve: ID
{
    $name = $ctx->ID()->getText();        // Obtiene nombre de variable
    
    if (!$this->func->hasLocal($name)) {  // Valida que variable existe
        $this->addError('Semántico', ...);
        return 'int32';
    }
    
    $offset = $this->func->getOffset($name);  // Obtiene: 16, 24, 32, ...
    $type   = $this->func->getType($name);    // Obtiene: 'int32', 'float32'
    
    if ($type === 'float32') {
        $this->emit("ldr s0, [x29, #-$offset]", "$name (float32)");  // ← LDR FLOAT
    } else {
        $this->emit("ldr x0, [x29, #-$offset]", "$name ($type)");    // ← LDR INT
    }
    return $type;
}
```

**¿Cuándo disparado?** Cuando el analizador ve un `ID` (identificador) en una expresión.

---

## 🟡 MÉTODOS DE SOPORTE: FunctionContext

### 1. FunctionContext.php::getOffset($name)

**Ubicación:** `Backend/src/Compiler/ARM64/FunctionContext.php` L111

```php
public function getOffset(string $name): int
{
    if ($this->hasLocal($name)) {
        return $this->getLocalOffset($name);      // Delega a LocalsManager
    }
    if ($this->hasArray($name)) {
        $info = $this->getArrayInfo($name);
        return $info['base_offset'] ?? 0;         // Delega a ArrayManager
    }
    return 0;
}
```

**Retorna:** Offset en bytes (positivo) → se resta de x29
- E.g.: 16, 24, 32, 40...

---

### 2. FunctionContext.php::getType($name)

**Ubicación:** `Backend/src/Compiler/ARM64/FunctionContext.php` L138

```php
public function getType(string $name): string
{
    if ($this->hasLocal($name)) {
        return $this->getLocalType($name);        // Delega a LocalsManager
    }
    if ($this->hasArray($name)) {
        return 'array';
    }
    return 'int32';
}
```

**Retorna:** 
- `'int32'` → usa registro x0, instrucción `ldr x0`
- `'float32'` → usa registro s0, instrucción `ldr s0`
- `'bool'` → usa registro x0, instrucción `ldr x0`
- `'pointer'` → usa registro x0, instrucción `ldr x0`

---

### 3. FunctionContext/LocalsManager.php::hasLocal($name)

**Ubicación:** `Backend/src/Compiler/ARM64/FunctionContext/LocalsManager.php` L73

```php
public function hasLocal(string $name): bool
{
    return isset($this->locals[$name]);
}
```

**Retorna:** `true` si variable está registrada, `false` sino.

---

### 4. FunctionContext/LocalsManager.php::getLocalOffset($name)

**Ubicación:** `Backend/src/Compiler/ARM64/FunctionContext/LocalsManager.php` L85

```php
public function getLocalOffset(string $name): int
{
    if (!$this->hasLocal($name)) {
        return 0;
    }
    return $this->locals[$name]['offset'];  // Obtiene offset calculado
}
```

**Retorna:** Offset en bytes almacenado durante `allocLocal()`

---

### 5. FunctionContext/LocalsManager.php::getLocalType($name)

**Ubicación:** `Backend/src/Compiler/ARM64/FunctionContext/LocalsManager.php` L100

```php
public function getLocalType(string $name): string
{
    if (!$this->hasLocal($name)) {
        return 'int32';
    }
    return $this->locals[$name]['type'];
}
```

**Retorna:** Tipo de variable ('int32', 'float32', 'bool', 'pointer', etc.)

---

## 🟢 MÉTODOS DE CARGA: SimpleAssignment

### 6. SimpleAssignment.php::loadFromFrame($type, $offset)

**Ubicación:** `Backend/src/Compiler/ARM64/Traits/Assignments/SimpleAssignment.php` L99

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

**Usa:** En asignaciones compuestas (`+=`, `-=`, etc.)  
**Resultado:** Valor cargado en `x0` (int) o `s0` (float)

---

### 7. SimpleAssignment.php::storeToFrame($type, $offset)

**Ubicación:** `Backend/src/Compiler/ARM64/Traits/Assignments/SimpleAssignment.php` L89

```php
protected function storeToFrame(string $type, int $offset): void
{
    if ($type === 'float32') {
        $this->emit("str s0, [x29, #-$offset]", 'guardar float32');
    } else {
        $this->emit("str x0, [x29, #-$offset]", "guardar $type");
    }
}
```

**Usa:** Después de evaluar una asignación  
**Precondición:** Valor en `x0` (int) o `s0` (float)

---

## 🟠 MÉTODOS DE DIRECCIONES: UnaryOps

### 8. UnaryOps.php::visitAddressOf($ctx)

**Ubicación:** `Backend/src/Compiler/ARM64/Traits/Expressions/UnaryOps.php` L71

```php
public function visitAddressOf($ctx)   // Maneja: &ID
{
    $name = $ctx->ID()->getText();
    
    if (!$this->func->hasLocal($name)) {
        $this->addError('Semántico', "Variable '$name' no declarada");
        return 'pointer';
    }
    
    $offset = $this->func->getOffset($name);
    $this->emit("sub x0, x29, #$offset", "&$name → dirección");
    return 'pointer';
}
```

**Instrucción:** `sub x0, x29, #offset`  
**Resultado:** x0 contiene **dirección de variable** en stack

---

### 9. UnaryOps.php::visitDereference($ctx)

**Ubicación:** `Backend/src/Compiler/ARM64/Traits/Expressions/UnaryOps.php` L102

```php
public function visitDereference($ctx)  // Maneja: *expr
{
    $this->visit($ctx->unary());        // Evalúa expr → x0 (contiene dirección)
    $this->emit('ldr x0, [x0]', '*ptr → valor en x0');
    return 'int32';
}
```

**Precondición:** x0 ya contiene una dirección  
**Instrucción:** `ldr x0, [x0]`  
**Resultado:** x0 contiene **valor desde la dirección**

---

### 10. UnaryOps.php::visitArrayAccess($ctx)

**Ubicación:** `Backend/src/Compiler/ARM64/Traits/Expressions/UnaryOps.php` L110

```php
public function visitArrayAccess($ctx)  // Maneja: ID[i], ID[i][j], etc.
{
    $name = $ctx->ID()->getText();
    
    if (!$this->func->hasArray($name)) {
        $this->addError('Semántico', "Array '$name' no declarado");
        return 'int32';
    }
    
    // Evalúa cada índice → stack
    // Calcula offset dinámico (row-major)
    // Carga dirección base del array
    // Calcula dirección del elemento
    // ldr x0, [dirección] → resultado
}
```

**Usa:** `getOffset()` para obtener dirección base  
**Resultado:** x0 contiene **elemento del array**

---

## 🔵 MÉTODOS DE OPERACIONES: ArithmeticOps

### 11. ArithmeticOps.php - Operaciones binarias

**Ubicación:** `Backend/src/Compiler/ARM64/Traits/Expressions/ArithmeticOps.php`

Patrón general:
```php
public function visitAdditiveExpression($ctx)  // x + y
{
    // 1. Evalúa LHS
    $this->visit($ctx->additive() ?? $ctx->unary());  // x → x0
    
    // 2. Push a stack (temporal)
    $this->pushStack();                               // str x0, [sp,...]
    
    // 3. Evalúa RHS
    $this->visit($ctx->unary());                      // y → x0
    
    // 4. Pop LHS desde stack
    $this->emit('ldr x1, [sp]', 'lhs ← stack');      // ← CARGA DESDE STACK
    
    // 5. Operación binaria
    $this->emit('add x0, x1, x0', 'x0 = lhs + rhs');
}
```

**Carga clave:** `ldr x1, [sp]` → pop valor anterior del stack

---

## 🟣 MÉTODOS DE MODIFICACIÓN: IncrementDecrement

### 12. IncrementDecrement.php::visitPostIncrementExpr($ctx)

**Ubicación:** `Backend/src/Compiler/ARM64/Traits/Assignments/IncrementDecrement.php` L22

```php
public function visitPostIncrementExpr($ctx)  // x++
{
    $name = $ctx->ID()->getText();
    
    if (!$this->func || !$this->func->hasLocal($name)) 
        return null;
    
    $offset = $this->func->getOffset($name);
    $type   = $this->func->getType($name);
    
    // Carga variable
    if ($type === 'float32') {
        $this->emit("ldr s0, [x29, #-$offset]", "cargar $name");     // ← CARGA
    } else {
        $this->emit("ldr x0, [x29, #-$offset]", "cargar $name");     // ← CARGA
    }
    
    // Incrementa
    if ($type === 'float32') {
        $this->emit("fadd s0, s0, s1", "incrementar (s1 contiene 1.0)");
    } else {
        $this->emit("add x0, x0, #1", "incrementar");
    }
    
    // Guarda
    if ($type === 'float32') {
        $this->emit("str s0, [x29, #-$offset]", "guardar $name");    // ← GUARDA
    } else {
        $this->emit("str x0, [x29, #-$offset]", "guardar $name");    // ← GUARDA
    }
    
    return $type;
}
```

**Patrón:** Load → Modify → Store

---

### 13. IncrementDecrement.php::visitPostDecrementExpr($ctx)

**Ubicación:** `Backend/src/Compiler/ARM64/Traits/Assignments/IncrementDecrement.php` L41

Similar a `visitPostIncrementExpr` pero con `sub` en lugar de `add`.

---

## 🎯 FLUJO DE DECISIÓN: Tamaño de Registro

```
¿Qué registro usar?

getType() == 'float32'?
    ├─ SÍ  → s0, s1, s2, ... (64-bit SIMD registers)
    │       ldr s0, [x29, #-offset]   (64-bit load)
    │       str s0, [x29, #-offset]   (64-bit store)
    │       fadd s0, s0, s1            (operación float)
    │
    └─ NO  → x0, x1, x2, ... (64-bit integer registers)
            ldr x0, [x29, #-offset]   (64-bit load)
            str x0, [x29, #-offset]   (64-bit store)
            add x0, x0, #1             (operación entera)
```

---

## 📊 Tabla de Métodos Rápida

| # | Método | Archivo | Línea | Propósito |
|---|--------|---------|-------|----------|
| 1 | `visitIdentifier()` | IdentifierVisitor.php | 26 | 🔴 PRINCIPAL: carga variable |
| 2 | `getOffset()` | FunctionContext.php | 111 | Obtiene offset de variable |
| 3 | `getType()` | FunctionContext.php | 138 | Obtiene tipo (int32/float32) |
| 4 | `hasLocal()` | LocalsManager.php | 73 | Valida existencia |
| 5 | `getLocalOffset()` | LocalsManager.php | 85 | Regresa offset almacenado |
| 6 | `getLocalType()` | LocalsManager.php | 100 | Regresa tipo almacenado |
| 7 | `loadFromFrame()` | SimpleAssignment.php | 99 | Carga variable (helper) |
| 8 | `storeToFrame()` | SimpleAssignment.php | 89 | Guarda variable (helper) |
| 9 | `visitAddressOf()` | UnaryOps.php | 71 | Obtiene dirección (&x) |
| 10 | `visitDereference()` | UnaryOps.php | 102 | Desreferencia puntero (*ptr) |
| 11 | `visitArrayAccess()` | UnaryOps.php | 110 | Accede a elemento array |
| 12 | `visitPostIncrementExpr()` | IncrementDecrement.php | 22 | Incrementa (x++) |
| 13 | `visitPostDecrementExpr()` | IncrementDecrement.php | 41 | Decrementa (x--) |

---

## 💨 Llamadas Rápidas

### Para cargar una variable entera:
```php
$offset = $this->func->getOffset('x');
$this->emit("ldr x0, [x29, #-$offset]", "cargar x");
```

### Para cargar una variable float:
```php
$offset = $this->func->getOffset('x');
$this->emit("ldr s0, [x29, #-$offset]", "cargar x (float32)");
```

### Para obtener dirección de variable:
```php
$offset = $this->func->getOffset('x');
$this->emit("sub x0, x29, #$offset", "dirección de x");
```

### Para dereferencia:
```php
$this->emit('ldr x0, [x0]', "desreferencia");
```

---

## 🚀 Punto de entrada de debugging

Si necesitas entender cómo se carga una variable:

1. **Punto de entrada:** `visitIdentifier()` en IdentifierVisitor.php L26
2. **Valida:** `hasLocal()` en LocalsManager.php L73
3. **Obtiene offset:** `getOffset()` en FunctionContext.php L111
4. **Obtiene tipo:** `getType()` en FunctionContext.php L138
5. **Emite:** `$this->emit("ldr ...")` en IdentifierVisitor.php L46-50

Sigue los métodos en ese orden para rastrear una carga de variable.

---

## 🔗 Mapa Mental

```
Parser ve: ID
    ↓
visitIdentifier() [IdentifierVisitor.php:26]
    ↓
if (!hasLocal()) → error
    ↓ (Si OK)
getOffset() [FunctionContext.php:111]
    ↓
getType() [FunctionContext.php:138]
    ↓
if (type == 'float32')
    emit("ldr s0, ...")
else
    emit("ldr x0, ...")
    ↓
Retorna tipo para próxima expresión
```

