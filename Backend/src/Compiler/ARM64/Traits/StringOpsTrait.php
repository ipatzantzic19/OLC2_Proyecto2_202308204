<?php

namespace Golampi\Compiler\ARM64\Traits;

/**
 * StringOpsTrait — Fase 2
 *
 * Operaciones de string en ARM64: concatenación, len, substr, typeOf.
 *
 * Estrategia de implementación:
 *   Los strings en Golampi son punteros a C-strings nulos (\0) en memoria.
 *   Se apoyan en la libc estándar (printf, strlen, malloc, memcpy, strcat).
 *
 * Para strings mutables (resultado de concatenación), se usa heap (malloc).
 * Los literales viven en .data (read-only).
 *
 * Helper ARM64 para concatenación (golampi_concat):
 *   x0 = ptr a string izquierdo
 *   x1 = ptr a string derecho
 *   Retorna x0 = ptr a nuevo string en heap (malloc)
 *
 * Funciones built-in:
 *   len(s)         → strlen(s) → x0
 *   substr(s,i,n)  → golampi_substr helper
 *   typeOf(x)      → puntero a string constante en .data
 *   now()          → time(0) + strftime → string en buffer estático
 */
trait StringOpsTrait
{
    /** Flag: ¿ya se emitió el helper golampi_concat? */
    private bool $concatHelperEmitted = false;
    private bool $substrHelperEmitted = false;
    private bool $nowHelperEmitted    = false;

    // ═══════════════════════════════════════════════════════════════════════════
    //  CONCATENACIÓN  (operador + sobre strings)
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Genera código para concatenar dos strings.
     * Precondición: lhs puntero en stack, rhs en x0.
     * Resultado: puntero al nuevo string en x0.
     *
     * Implementación:
     *   1. Llamar strlen(lhs) + strlen(rhs) para calcular longitud total
     *   2. malloc(len_lhs + len_rhs + 1)
     *   3. strcpy(dest, lhs)
     *   4. strcat(dest, rhs)
     */
    protected function emitStringConcat(): void
    {
        $this->ensureConcatHelper();

        // Al llegar aquí: x1 = lhs (recuperado del stack), x0 = rhs
        $this->emit('bl golampi_concat',     'concat(x0=lhs, x1=rhs) → x0');
    }

    /**
     * Emite el helper golampi_concat una sola vez al final del assembly.
     * Convención: x0=str_a, x1=str_b → x0=resultado (heap)
     */
    private function ensureConcatHelper(): void
    {
        if ($this->concatHelperEmitted) return;
        $this->concatHelperEmitted = true;

        // Se agrega al buffer de texto pero después de todas las funciones
        // usando un mecanismo de "post-text" (ver buildAssembly)
        $helper = <<<'ASM'

// ── golampi_concat(x0=a, x1=b) → x0=heap string ─────────────────────────
// Registro de activación:
//   [fp - 8]  = puntero a
//   [fp - 16] = puntero b
golampi_concat:
    stp x29, x30, [sp, #-48]!
    mov x29, sp
    str x0, [x29, #-8]         // guardar a
    str x1, [x29, #-16]        // guardar b
    // len_a = strlen(a)
    bl strlen
    str x0, [x29, #-24]        // len_a
    // len_b = strlen(b)
    ldr x0, [x29, #-16]
    bl strlen
    str x0, [x29, #-32]        // len_b
    // total = len_a + len_b + 1
    ldr x1, [x29, #-24]
    add x0, x0, x1
    add x0, x0, #1
    // dest = malloc(total)
    bl malloc
    str x0, [x29, #-40]        // guardar dest
    // strcpy(dest, a)
    ldr x1, [x29, #-8]
    bl strcpy
    // strcat(dest, b)
    ldr x0, [x29, #-40]
    ldr x1, [x29, #-16]
    bl strcat
    // retornar dest en x0
    ldr x0, [x29, #-40]
    ldp x29, x30, [sp], #48
    ret
ASM;
        $this->postTextLines[] = $helper;
    }

    // ═══════════════════════════════════════════════════════════════════════════
    //  LEN
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Genera código para len(string).
     * Precondición: puntero al string en x0.
     * Resultado: longitud (entero) en x0.
     */
    protected function emitStrlen(): void
    {
        $this->comment('len(string) → strlen(x0) → x0');
        $this->emit('bl strlen');
    }

    // ═══════════════════════════════════════════════════════════════════════════
    //  SUBSTR
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Genera código para substr(s, start, length).
     * Precondición: x0=s, x1=start, x2=length.
     * Resultado: x0=puntero al nuevo substring (heap).
     */
    protected function emitSubstr(): void
    {
        $this->ensureSubstrHelper();
        $this->emit('bl golampi_substr',     'substr(x0=s, x1=start, x2=len) → x0');
    }

    private function ensureSubstrHelper(): void
    {
        if ($this->substrHelperEmitted) return;
        $this->substrHelperEmitted = true;

        $helper = <<<'ASM'

// ── golampi_substr(x0=s, x1=start, x2=len) → x0=heap string ─────────────
golampi_substr:
    stp x29, x30, [sp, #-48]!
    mov x29, sp
    str x0, [x29, #-8]         // s
    str x1, [x29, #-16]        // start
    str x2, [x29, #-24]        // len
    // dest = malloc(len + 1)
    add x0, x2, #1
    bl malloc
    str x0, [x29, #-32]        // dest
    // src = s + start
    ldr x1, [x29, #-8]
    ldr x2, [x29, #-16]
    add x1, x1, x2             // x1 = s + start
    // memcpy(dest, src, len)
    ldr x2, [x29, #-24]
    bl memcpy
    // dest[len] = '\0'
    ldr x0, [x29, #-32]
    ldr x1, [x29, #-24]
    add x1, x0, x1             // dest + len
    strb wzr, [x1]             // null terminator
    // retornar dest
    ldp x29, x30, [sp], #48
    ret
ASM;
        $this->postTextLines[] = $helper;
    }

    // ═══════════════════════════════════════════════════════════════════════════
    //  NOW
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Genera código para now() → string "YYYY-MM-DD HH:MM:SS" en x0.
     * Usa time(0) + localtime + strftime sobre un buffer estático en .data.
     */
    protected function emitNow(): void
    {
        $this->ensureNowHelper();
        $this->emit('bl golampi_now',        'now() → x0 = ptr a fecha string');
    }

    private function ensureNowHelper(): void
    {
        if ($this->nowHelperEmitted) return;
        $this->nowHelperEmitted = true;

        // Buffer estático de 20 bytes para la fecha
        $this->addData('.align 3');
        $this->addData('golampi_now_buf: .space 20');
        $fmtLabel = $this->internString('%Y-%m-%d %H:%M:%S');

        $helper = <<<ASM

// ── golampi_now() → x0 = "YYYY-MM-DD HH:MM:SS" ───────────────────────────
golampi_now:
    stp x29, x30, [sp, #-32]!
    mov x29, sp
    // time(NULL) → x0 = time_t
    mov x0, #0
    bl time
    str x0, [x29, #-8]
    // localtime(&t) → x0 = struct tm*
    sub x0, x29, #8
    bl localtime
    mov x1, x0                 // tm* en x1
    // strftime(buf, 20, fmt, tm*)
    adrp x0, golampi_now_buf
    add  x0, x0, :lo12:golampi_now_buf
    mov  x2, #20
    adrp x3, $fmtLabel
    add  x3, x3, :lo12:$fmtLabel
    // strftime(x0=buf, x1=size, x2=fmt, x3=tm*) — nota: AArch64 orden
    mov  x3, x1                // tm* al 4to arg
    adrp x2, $fmtLabel
    add  x2, x2, :lo12:$fmtLabel
    mov  x1, #20
    adrp x0, golampi_now_buf
    add  x0, x0, :lo12:golampi_now_buf
    bl   strftime
    // retornar puntero al buffer
    adrp x0, golampi_now_buf
    add  x0, x0, :lo12:golampi_now_buf
    ldp x29, x30, [sp], #32
    ret
ASM;
        $this->postTextLines[] = $helper;
    }

    // ═══════════════════════════════════════════════════════════════════════════
    //  TYPEOF
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Genera código para typeOf(expr) → string del tipo en x0.
     * @param string $type tipo ya conocido en compile-time
     */
    protected function emitTypeOf(string $type): void
    {
        // Mapeo de tipos internos a nombres visibles (igual que en Go)
        $displayName = match ($type) {
            'int32'   => 'int32',
            'float32' => 'float32',
            'bool'    => 'bool',
            'string'  => 'string',
            'rune'    => 'int32',   // rune es alias de int32
            'nil'     => 'nil',
            default   => $type,
        };

        $label = $this->internString($displayName);
        $this->comment("typeOf → '$displayName'");
        $this->emit("adrp x0, $label");
        $this->emit("add x0, x0, :lo12:$label");
    }
}