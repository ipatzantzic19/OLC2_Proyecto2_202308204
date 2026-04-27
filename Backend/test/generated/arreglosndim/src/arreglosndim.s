.section .data
msg:
    .ascii "\n"
buffer:
    .ascii "0\n"
len = . - buffer

.str_0: .string "=== INICIO DE CALIFICACION: ARREGLOS N-D ==="
.str_1: .string "%s"
.str_2: .string "\n"
.str_3: .string "\n--- 5.6 INDICE DE INESTABILIDAD ---"
.str_4: .string "Indice:"
.str_5: .string "%s "
.str_6: .string "%ld"
.str_7: .string "\n--- 5.7 REGLA DE CRAMER ---"
.str_8: .string "x, y:"
.str_9: .string "%ld "
.str_10: .string "\n--- 5.8 PROMEDIO DE CAPAS ---"
.str_11: .string "Promedios capa 0:"
.str_12: .string "%g "
.str_13: .string "%g"
.str_14: .string "Promedios capa 1:"
.str_15: .string "\n--- 5.9 SOFTMAX ---"
.align 2
.flt_0: .single 1.0
.align 2
.flt_1: .single 2.0
.align 2
.flt_2: .single 3.0
.align 2
.flt_3: .single 4.0
.str_16: .string "Fila 0:"
.str_17: .string "Fila 1:"
.str_18: .string "\n=== FIN DE CALIFICACION: ARREGLOS N-D ==="
.align 2
.flt_4: .single 0.0
.align 2
.flt_5: .single 0.1

.section .text
.align 2
.global _start

_start:
	stp x29, x30, [sp, #-16]!
	# guardar frame pointer y link register
	mov x29, sp
	# x29 = frame pointer (SP actual)
	sub sp, sp, #704
	# reservar 704 bytes para variables locales
	# frame.size=704, locals=0
	# string literal → x0 (puntero)
	adrp x0, .str_0
	add x0, x0, :lo12:.str_0
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# string literal → x0 (puntero)
	adrp x0, .str_3
	add x0, x0, :lo12:.str_3
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# matrizInstabilidad := expr (tipo inferido)
	mov x0, xzr
	# array literal → valor manejado por el destino
	# matrizInstabilidad := array literal (array registrado en prescan)
	mov x0, #2
	# matrizInstabilidad[0] ← literal
	str x0, [x29, #-8]
	mov x0, #5
	# matrizInstabilidad[1] ← literal
	str x0, [x29, #-16]
	mov x0, #3
	# matrizInstabilidad[2] ← literal
	str x0, [x29, #-24]
	mov x0, #8
	# matrizInstabilidad[3] ← literal
	str x0, [x29, #-32]
	mov x0, #1
	# matrizInstabilidad[4] ← literal
	str x0, [x29, #-40]
	mov x0, #1
	# matrizInstabilidad[5] ← literal
	str x0, [x29, #-48]
	mov x0, #4
	# matrizInstabilidad[6] ← literal
	str x0, [x29, #-56]
	mov x0, #6
	# matrizInstabilidad[7] ← literal
	str x0, [x29, #-64]
	mov x0, #7
	# matrizInstabilidad[8] ← literal
	str x0, [x29, #-72]
	mov x0, #3
	# matrizInstabilidad[9] ← literal
	str x0, [x29, #-80]
	mov x0, #9
	# matrizInstabilidad[10] ← literal
	str x0, [x29, #-88]
	mov x0, #9
	# matrizInstabilidad[11] ← literal
	str x0, [x29, #-96]
	mov x0, xzr
	# array literal → valor manejado por el destino
	# string literal → x0 (puntero)
	adrp x0, .str_4
	add x0, x0, :lo12:.str_4
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_5
	add x0, x0, :lo12:.str_5
	bl printf
	# llamada a indiceInestabilidad (1 arg(s))
	sub x0, x29, #8
	# matrizInstabilidad (array base address)
	sub sp, sp, #16
	str x0, [sp]
	# arg[0] array → stack
	ldr x0, [sp, #0]
	# arg[0] array → x0
	add sp, sp, #16
	# limpiar args temporales del stack
	bl indiceInestabilidad
	# fmt.Println arg 1 (int32)
	mov x1, x0
	# int32 → x1 para printf %ld
	adrp x0, .str_6
	add x0, x0, :lo12:.str_6
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# string literal → x0 (puntero)
	adrp x0, .str_7
	add x0, x0, :lo12:.str_7
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# matrizSistema := expr (tipo inferido)
	mov x0, xzr
	# array literal → valor manejado por el destino
	# matrizSistema := array literal (array registrado en prescan)
	mov x0, #2
	# matrizSistema[0] ← literal
	str x0, [x29, #-104]
	mov x0, #1
	# matrizSistema[1] ← literal
	str x0, [x29, #-112]
	mov x0, #1
	# matrizSistema[2] ← literal
	str x0, [x29, #-120]
	mov x0, #3
	# matrizSistema[3] ← literal
	str x0, [x29, #-128]
	mov x0, xzr
	# array literal → valor manejado por el destino
	# vectorSistema := expr (tipo inferido)
	mov x0, xzr
	# array literal → valor manejado por el destino
	# vectorSistema := array literal (array registrado en prescan)
	mov x0, #5
	# vectorSistema[0] ← literal
	str x0, [x29, #-136]
	mov x0, #6
	# vectorSistema[1] ← literal
	str x0, [x29, #-144]
	mov x0, xzr
	# array literal → valor manejado por el destino
	# resultadoCramer := expr (tipo inferido)
	# llamada a reglaCramer (2 arg(s))
	sub x0, x29, #104
	# matrizSistema (array base address)
	sub sp, sp, #16
	str x0, [sp]
	# arg[0] array → stack
	sub x0, x29, #136
	# vectorSistema (array base address)
	sub sp, sp, #16
	str x0, [sp]
	# arg[1] array → stack
	ldr x0, [sp, #16]
	# arg[0] array → x0
	ldr x1, [sp, #0]
	# arg[1] array → x1
	add sp, sp, #32
	# limpiar args temporales del stack
	bl reglaCramer
	# resultadoCramer := retorno array (copiar desde puntero)
	mov x11, x0
	# resultadoCramer ptr retorno
	mov x14, x11
	# resultadoCramer[0] base retorno
	ldr x12, [x14]
	# resultadoCramer[0] leer retorno
	str x12, [x29, #-152]
	# resultadoCramer[0] guardar
	sub x14, x11, #8
	# resultadoCramer[1] dirección retorno
	ldr x12, [x14]
	# resultadoCramer[1] leer retorno
	str x12, [x29, #-160]
	# resultadoCramer[1] guardar
	# string literal → x0 (puntero)
	adrp x0, .str_8
	add x0, x0, :lo12:.str_8
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_5
	add x0, x0, :lo12:.str_5
	bl printf
	# resultadoCramer[idx0] (lectura)
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	ldr x1, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	lsl x1, x1, #3
	# offset = idx * 8
	sub x2, x29, #152
	# dirección base del array resultadoCramer → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr x0, [x3]
	# resultadoCramer[idx] (lectura) → x0
	# fmt.Println arg 1 (int32)
	mov x1, x0
	# int32 → x1 para printf %ld
	adrp x0, .str_9
	add x0, x0, :lo12:.str_9
	bl printf
	# resultadoCramer[idx0] (lectura)
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	ldr x1, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	lsl x1, x1, #3
	# offset = idx * 8
	sub x2, x29, #152
	# dirección base del array resultadoCramer → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr x0, [x3]
	# resultadoCramer[idx] (lectura) → x0
	# fmt.Println arg 2 (int32)
	mov x1, x0
	# int32 → x1 para printf %ld
	adrp x0, .str_6
	add x0, x0, :lo12:.str_6
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# string literal → x0 (puntero)
	adrp x0, .str_10
	add x0, x0, :lo12:.str_10
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# cubo := expr (tipo inferido)
	mov x0, xzr
	# array literal → valor manejado por el destino
	# cubo := array literal (array registrado en prescan)
	mov x0, #1
	# cubo[0] ← literal
	str x0, [x29, #-168]
	mov x0, #3
	# cubo[1] ← literal
	str x0, [x29, #-176]
	mov x0, #5
	# cubo[2] ← literal
	str x0, [x29, #-184]
	mov x0, #7
	# cubo[3] ← literal
	str x0, [x29, #-192]
	mov x0, #2
	# cubo[4] ← literal
	str x0, [x29, #-200]
	mov x0, #4
	# cubo[5] ← literal
	str x0, [x29, #-208]
	mov x0, #6
	# cubo[6] ← literal
	str x0, [x29, #-216]
	mov x0, #8
	# cubo[7] ← literal
	str x0, [x29, #-224]
	mov x0, xzr
	# array literal → valor manejado por el destino
	# promedios := expr (tipo inferido)
	# llamada a promedioCapas (1 arg(s))
	sub x0, x29, #168
	# cubo (array base address)
	sub sp, sp, #16
	str x0, [sp]
	# arg[0] array → stack
	ldr x0, [sp, #0]
	# arg[0] array → x0
	add sp, sp, #16
	# limpiar args temporales del stack
	bl promedioCapas
	# promedios := retorno array (copiar desde puntero)
	mov x11, x0
	# promedios ptr retorno
	mov x14, x11
	# promedios[0] base retorno
	ldr x12, [x14]
	# promedios[0] leer retorno
	str x12, [x29, #-232]
	# promedios[0] guardar
	sub x14, x11, #8
	# promedios[1] dirección retorno
	ldr x12, [x14]
	# promedios[1] leer retorno
	str x12, [x29, #-240]
	# promedios[1] guardar
	sub x14, x11, #16
	# promedios[2] dirección retorno
	ldr x12, [x14]
	# promedios[2] leer retorno
	str x12, [x29, #-248]
	# promedios[2] guardar
	sub x14, x11, #24
	# promedios[3] dirección retorno
	ldr x12, [x14]
	# promedios[3] leer retorno
	sub x13, x29, #256
	# promedios[3] dirección destino
	str x12, [x13]
	# promedios[3] guardar
	# string literal → x0 (puntero)
	adrp x0, .str_11
	add x0, x0, :lo12:.str_11
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_5
	add x0, x0, :lo12:.str_5
	bl printf
	# promedios[idx0][idx1] (lectura)
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x1, xzr
	# x1 = offset acumulador
	ldr x4, [sp]
	# índice 1 ← stack
	add sp, sp, #16
	add x1, x1, x4
	# offset += idx[1] * stride
	ldr x4, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	mov x5, #2
	# stride = 2
	mul x4, x4, x5
	# idx[0] * stride
	add x1, x1, x4
	# offset += idx[0] * stride
	lsl x1, x1, #3
	# offset *= 8
	sub x2, x29, #232
	# dirección base del array promedios → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr s0, [x3]
	# promedios[idx] (lectura) → s0 (float32)
	# fmt.Println arg 1 (float32)
	fcvt d0, s0
	# float32 a float64 para printf variadic
	fmov x1, d0
	# copiar float64 a x1 para variadic ABI
	adrp x0, .str_12
	add x0, x0, :lo12:.str_12
	bl printf
	# promedios[idx0][idx1] (lectura)
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x1, xzr
	# x1 = offset acumulador
	ldr x4, [sp]
	# índice 1 ← stack
	add sp, sp, #16
	add x1, x1, x4
	# offset += idx[1] * stride
	ldr x4, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	mov x5, #2
	# stride = 2
	mul x4, x4, x5
	# idx[0] * stride
	add x1, x1, x4
	# offset += idx[0] * stride
	lsl x1, x1, #3
	# offset *= 8
	sub x2, x29, #232
	# dirección base del array promedios → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr s0, [x3]
	# promedios[idx] (lectura) → s0 (float32)
	# fmt.Println arg 2 (float32)
	fcvt d0, s0
	# float32 a float64 para printf variadic
	fmov x1, d0
	# copiar float64 a x1 para variadic ABI
	adrp x0, .str_13
	add x0, x0, :lo12:.str_13
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# string literal → x0 (puntero)
	adrp x0, .str_14
	add x0, x0, :lo12:.str_14
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_5
	add x0, x0, :lo12:.str_5
	bl printf
	# promedios[idx0][idx1] (lectura)
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x1, xzr
	# x1 = offset acumulador
	ldr x4, [sp]
	# índice 1 ← stack
	add sp, sp, #16
	add x1, x1, x4
	# offset += idx[1] * stride
	ldr x4, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	mov x5, #2
	# stride = 2
	mul x4, x4, x5
	# idx[0] * stride
	add x1, x1, x4
	# offset += idx[0] * stride
	lsl x1, x1, #3
	# offset *= 8
	sub x2, x29, #232
	# dirección base del array promedios → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr s0, [x3]
	# promedios[idx] (lectura) → s0 (float32)
	# fmt.Println arg 1 (float32)
	fcvt d0, s0
	# float32 a float64 para printf variadic
	fmov x1, d0
	# copiar float64 a x1 para variadic ABI
	adrp x0, .str_12
	add x0, x0, :lo12:.str_12
	bl printf
	# promedios[idx0][idx1] (lectura)
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x1, xzr
	# x1 = offset acumulador
	ldr x4, [sp]
	# índice 1 ← stack
	add sp, sp, #16
	add x1, x1, x4
	# offset += idx[1] * stride
	ldr x4, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	mov x5, #2
	# stride = 2
	mul x4, x4, x5
	# idx[0] * stride
	add x1, x1, x4
	# offset += idx[0] * stride
	lsl x1, x1, #3
	# offset *= 8
	sub x2, x29, #232
	# dirección base del array promedios → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr s0, [x3]
	# promedios[idx] (lectura) → s0 (float32)
	# fmt.Println arg 2 (float32)
	fcvt d0, s0
	# float32 a float64 para printf variadic
	fmov x1, d0
	# copiar float64 a x1 para variadic ABI
	adrp x0, .str_13
	add x0, x0, :lo12:.str_13
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# string literal → x0 (puntero)
	adrp x0, .str_15
	add x0, x0, :lo12:.str_15
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# matrizSoft := expr (tipo inferido)
	mov x0, xzr
	# array literal → valor manejado por el destino
	# matrizSoft := array literal (array registrado en prescan)
	adrp x9, .flt_0
	# matrizSoft[0] float literal
	ldr s0, [x9, :lo12:.flt_0]
	# matrizSoft[0] cargar float32
	sub x10, x29, #264
	# direccion efectiva slot float de frame
	str s0, [x10]
	adrp x9, .flt_1
	# matrizSoft[1] float literal
	ldr s0, [x9, :lo12:.flt_1]
	# matrizSoft[1] cargar float32
	sub x10, x29, #272
	# direccion efectiva slot float de frame
	str s0, [x10]
	adrp x9, .flt_2
	# matrizSoft[2] float literal
	ldr s0, [x9, :lo12:.flt_2]
	# matrizSoft[2] cargar float32
	sub x10, x29, #280
	# direccion efectiva slot float de frame
	str s0, [x10]
	adrp x9, .flt_3
	# matrizSoft[3] float literal
	ldr s0, [x9, :lo12:.flt_3]
	# matrizSoft[3] cargar float32
	sub x10, x29, #288
	# direccion efectiva slot float de frame
	str s0, [x10]
	adrp x9, .flt_1
	# matrizSoft[4] float literal
	ldr s0, [x9, :lo12:.flt_1]
	# matrizSoft[4] cargar float32
	sub x10, x29, #296
	# direccion efectiva slot float de frame
	str s0, [x10]
	adrp x9, .flt_0
	# matrizSoft[5] float literal
	ldr s0, [x9, :lo12:.flt_0]
	# matrizSoft[5] cargar float32
	sub x10, x29, #304
	# direccion efectiva slot float de frame
	str s0, [x10]
	mov x0, xzr
	# array literal → valor manejado por el destino
	# soft := expr (tipo inferido)
	# llamada a softmax (1 arg(s))
	sub x0, x29, #264
	# matrizSoft (array base address)
	sub sp, sp, #16
	str x0, [sp]
	# arg[0] array → stack
	ldr x0, [sp, #0]
	# arg[0] array → x0
	add sp, sp, #16
	# limpiar args temporales del stack
	bl softmax
	# soft := retorno array (copiar desde puntero)
	mov x11, x0
	# soft ptr retorno
	mov x14, x11
	# soft[0] base retorno
	ldr x12, [x14]
	# soft[0] leer retorno
	sub x13, x29, #312
	# soft[0] dirección destino
	str x12, [x13]
	# soft[0] guardar
	sub x14, x11, #8
	# soft[1] dirección retorno
	ldr x12, [x14]
	# soft[1] leer retorno
	sub x13, x29, #320
	# soft[1] dirección destino
	str x12, [x13]
	# soft[1] guardar
	sub x14, x11, #16
	# soft[2] dirección retorno
	ldr x12, [x14]
	# soft[2] leer retorno
	sub x13, x29, #328
	# soft[2] dirección destino
	str x12, [x13]
	# soft[2] guardar
	sub x14, x11, #24
	# soft[3] dirección retorno
	ldr x12, [x14]
	# soft[3] leer retorno
	sub x13, x29, #336
	# soft[3] dirección destino
	str x12, [x13]
	# soft[3] guardar
	sub x14, x11, #32
	# soft[4] dirección retorno
	ldr x12, [x14]
	# soft[4] leer retorno
	sub x13, x29, #344
	# soft[4] dirección destino
	str x12, [x13]
	# soft[4] guardar
	sub x14, x11, #40
	# soft[5] dirección retorno
	ldr x12, [x14]
	# soft[5] leer retorno
	sub x13, x29, #352
	# soft[5] dirección destino
	str x12, [x13]
	# soft[5] guardar
	# string literal → x0 (puntero)
	adrp x0, .str_16
	add x0, x0, :lo12:.str_16
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_5
	add x0, x0, :lo12:.str_5
	bl printf
	# soft[idx0][idx1] (lectura)
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x1, xzr
	# x1 = offset acumulador
	ldr x4, [sp]
	# índice 1 ← stack
	add sp, sp, #16
	add x1, x1, x4
	# offset += idx[1] * stride
	ldr x4, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	mov x5, #3
	# stride = 3
	mul x4, x4, x5
	# idx[0] * stride
	add x1, x1, x4
	# offset += idx[0] * stride
	lsl x1, x1, #3
	# offset *= 8
	sub x2, x29, #312
	# dirección base del array soft → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr s0, [x3]
	# soft[idx] (lectura) → s0 (float32)
	# fmt.Println arg 1 (float32)
	fcvt d0, s0
	# float32 a float64 para printf variadic
	fmov x1, d0
	# copiar float64 a x1 para variadic ABI
	adrp x0, .str_12
	add x0, x0, :lo12:.str_12
	bl printf
	# soft[idx0][idx1] (lectura)
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x1, xzr
	# x1 = offset acumulador
	ldr x4, [sp]
	# índice 1 ← stack
	add sp, sp, #16
	add x1, x1, x4
	# offset += idx[1] * stride
	ldr x4, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	mov x5, #3
	# stride = 3
	mul x4, x4, x5
	# idx[0] * stride
	add x1, x1, x4
	# offset += idx[0] * stride
	lsl x1, x1, #3
	# offset *= 8
	sub x2, x29, #312
	# dirección base del array soft → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr s0, [x3]
	# soft[idx] (lectura) → s0 (float32)
	# fmt.Println arg 2 (float32)
	fcvt d0, s0
	# float32 a float64 para printf variadic
	fmov x1, d0
	# copiar float64 a x1 para variadic ABI
	adrp x0, .str_12
	add x0, x0, :lo12:.str_12
	bl printf
	# soft[idx0][idx1] (lectura)
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x0, #2
	# int32 literal (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x1, xzr
	# x1 = offset acumulador
	ldr x4, [sp]
	# índice 1 ← stack
	add sp, sp, #16
	add x1, x1, x4
	# offset += idx[1] * stride
	ldr x4, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	mov x5, #3
	# stride = 3
	mul x4, x4, x5
	# idx[0] * stride
	add x1, x1, x4
	# offset += idx[0] * stride
	lsl x1, x1, #3
	# offset *= 8
	sub x2, x29, #312
	# dirección base del array soft → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr s0, [x3]
	# soft[idx] (lectura) → s0 (float32)
	# fmt.Println arg 3 (float32)
	fcvt d0, s0
	# float32 a float64 para printf variadic
	fmov x1, d0
	# copiar float64 a x1 para variadic ABI
	adrp x0, .str_13
	add x0, x0, :lo12:.str_13
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# string literal → x0 (puntero)
	adrp x0, .str_17
	add x0, x0, :lo12:.str_17
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_5
	add x0, x0, :lo12:.str_5
	bl printf
	# soft[idx0][idx1] (lectura)
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x1, xzr
	# x1 = offset acumulador
	ldr x4, [sp]
	# índice 1 ← stack
	add sp, sp, #16
	add x1, x1, x4
	# offset += idx[1] * stride
	ldr x4, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	mov x5, #3
	# stride = 3
	mul x4, x4, x5
	# idx[0] * stride
	add x1, x1, x4
	# offset += idx[0] * stride
	lsl x1, x1, #3
	# offset *= 8
	sub x2, x29, #312
	# dirección base del array soft → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr s0, [x3]
	# soft[idx] (lectura) → s0 (float32)
	# fmt.Println arg 1 (float32)
	fcvt d0, s0
	# float32 a float64 para printf variadic
	fmov x1, d0
	# copiar float64 a x1 para variadic ABI
	adrp x0, .str_12
	add x0, x0, :lo12:.str_12
	bl printf
	# soft[idx0][idx1] (lectura)
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x1, xzr
	# x1 = offset acumulador
	ldr x4, [sp]
	# índice 1 ← stack
	add sp, sp, #16
	add x1, x1, x4
	# offset += idx[1] * stride
	ldr x4, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	mov x5, #3
	# stride = 3
	mul x4, x4, x5
	# idx[0] * stride
	add x1, x1, x4
	# offset += idx[0] * stride
	lsl x1, x1, #3
	# offset *= 8
	sub x2, x29, #312
	# dirección base del array soft → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr s0, [x3]
	# soft[idx] (lectura) → s0 (float32)
	# fmt.Println arg 2 (float32)
	fcvt d0, s0
	# float32 a float64 para printf variadic
	fmov x1, d0
	# copiar float64 a x1 para variadic ABI
	adrp x0, .str_12
	add x0, x0, :lo12:.str_12
	bl printf
	# soft[idx0][idx1] (lectura)
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x0, #2
	# int32 literal (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x1, xzr
	# x1 = offset acumulador
	ldr x4, [sp]
	# índice 1 ← stack
	add sp, sp, #16
	add x1, x1, x4
	# offset += idx[1] * stride
	ldr x4, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	mov x5, #3
	# stride = 3
	mul x4, x4, x5
	# idx[0] * stride
	add x1, x1, x4
	# offset += idx[0] * stride
	lsl x1, x1, #3
	# offset *= 8
	sub x2, x29, #312
	# dirección base del array soft → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr s0, [x3]
	# soft[idx] (lectura) → s0 (float32)
	# fmt.Println arg 3 (float32)
	fcvt d0, s0
	# float32 a float64 para printf variadic
	fmov x1, d0
	# copiar float64 a x1 para variadic ABI
	adrp x0, .str_13
	add x0, x0, :lo12:.str_13
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# string literal → x0 (puntero)
	adrp x0, .str_18
	add x0, x0, :lo12:.str_18
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
.epilogue__start:
	add sp, sp, #704
	# restaurar stack pointer
	ldp x29, x30, [sp], #16
	# restaurar frame pointer y link register
	mov x0, xzr
	# fflush(NULL)
	bl fflush
	# vaciar buffers stdio
	mov x0, #0
	# exit code = 0
	mov x8, #93
	# syscall exit
	svc #0
	# invoke
indiceInestabilidad:
	stp x29, x30, [sp, #-16]!
	# guardar frame pointer y link register
	mov x29, sp
	# x29 = frame pointer (SP actual)
	sub sp, sp, #224
	# reservar 224 bytes para variables locales
	mov x11, x0
	# param array matriz: ptr de entrada
	mov x14, x11
	# matriz[0] base param
	ldr x12, [x14]
	# matriz[0] leer param
	str x12, [x29, #-8]
	# matriz[0] copiar a frame
	sub x14, x11, #8
	# matriz[1] dirección param
	ldr x12, [x14]
	# matriz[1] leer param
	str x12, [x29, #-16]
	# matriz[1] copiar a frame
	sub x14, x11, #16
	# matriz[2] dirección param
	ldr x12, [x14]
	# matriz[2] leer param
	str x12, [x29, #-24]
	# matriz[2] copiar a frame
	sub x14, x11, #24
	# matriz[3] dirección param
	ldr x12, [x14]
	# matriz[3] leer param
	str x12, [x29, #-32]
	# matriz[3] copiar a frame
	sub x14, x11, #32
	# matriz[4] dirección param
	ldr x12, [x14]
	# matriz[4] leer param
	str x12, [x29, #-40]
	# matriz[4] copiar a frame
	sub x14, x11, #40
	# matriz[5] dirección param
	ldr x12, [x14]
	# matriz[5] leer param
	str x12, [x29, #-48]
	# matriz[5] copiar a frame
	sub x14, x11, #48
	# matriz[6] dirección param
	ldr x12, [x14]
	# matriz[6] leer param
	str x12, [x29, #-56]
	# matriz[6] copiar a frame
	sub x14, x11, #56
	# matriz[7] dirección param
	ldr x12, [x14]
	# matriz[7] leer param
	str x12, [x29, #-64]
	# matriz[7] copiar a frame
	sub x14, x11, #64
	# matriz[8] dirección param
	ldr x12, [x14]
	# matriz[8] leer param
	str x12, [x29, #-72]
	# matriz[8] copiar a frame
	sub x14, x11, #72
	# matriz[9] dirección param
	ldr x12, [x14]
	# matriz[9] leer param
	str x12, [x29, #-80]
	# matriz[9] copiar a frame
	sub x14, x11, #80
	# matriz[10] dirección param
	ldr x12, [x14]
	# matriz[10] leer param
	str x12, [x29, #-88]
	# matriz[10] copiar a frame
	sub x14, x11, #88
	# matriz[11] dirección param
	ldr x12, [x14]
	# matriz[11] leer param
	str x12, [x29, #-96]
	# matriz[11] copiar a frame
	# frame.size=224, locals=4
	# var total int32 = expr
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	str x0, [x29, #-104]
	# guardar int32
	# for init
	# i := expr (tipo inferido)
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	str x0, [x29, #-112]
	# guardar int32 inferido (64-bit)
.for_start_0:
	# for condición
	ldr x0, [x29, #-112]
	# i (int32 - 64-bit)
	mov x1, x0
	mov x0, #3
	# int32 literal (64-bit per AArch64)
	cmp x1, x0
	# comparar x1(lhs) vs x0(rhs) - flags setup
	cset x0, lt
	# materializar resultado bool en x0
	b.ge .for_end_1
	# falso salir del bucle (comparación simple)
	# for init
	# j := expr (tipo inferido)
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	str x0, [x29, #-120]
	# guardar int32 inferido (64-bit)
.for_start_3:
	# for condición
	ldr x0, [x29, #-120]
	# j (int32 - 64-bit)
	mov x1, x0
	mov x0, #4
	# int32 literal (64-bit per AArch64)
	cmp x1, x0
	# comparar x1(lhs) vs x0(rhs) - flags setup
	cset x0, lt
	# materializar resultado bool en x0
	b.ge .for_end_4
	# falso salir del bucle (comparación simple)
	# dif := expr (tipo inferido)
	# matriz[idx0][idx1] (lectura)
	ldr x0, [x29, #-112]
	# i (int32 - 64-bit)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	ldr x0, [x29, #-120]
	# j (int32 - 64-bit)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x1, xzr
	# x1 = offset acumulador
	ldr x4, [sp]
	# índice 1 ← stack
	add sp, sp, #16
	add x1, x1, x4
	# offset += idx[1] * stride
	ldr x4, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	mov x5, #4
	# stride = 4
	mul x4, x4, x5
	# idx[0] * stride
	add x1, x1, x4
	# offset += idx[0] * stride
	lsl x1, x1, #3
	# offset *= 8
	sub x2, x29, #8
	# dirección base del array matriz → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr x0, [x3]
	# matriz[idx] (lectura) → x0
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	# matriz[idx0][idx1] (lectura)
	ldr x0, [x29, #-112]
	# i (int32 - 64-bit)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	ldr x0, [x29, #-120]
	# j (int32 - 64-bit)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	ldr x1, [sp]
	# lhs ← stack (int32)
	add sp, sp, #16
	sub x0, x1, x0
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x1, xzr
	# x1 = offset acumulador
	ldr x4, [sp]
	# índice 1 ← stack
	add sp, sp, #16
	add x1, x1, x4
	# offset += idx[1] * stride
	ldr x4, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	mov x5, #4
	# stride = 4
	mul x4, x4, x5
	# idx[0] * stride
	add x1, x1, x4
	# offset += idx[0] * stride
	lsl x1, x1, #3
	# offset *= 8
	sub x2, x29, #8
	# dirección base del array matriz → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr x0, [x3]
	# matriz[idx] (lectura) → x0
	ldr x1, [sp]
	# lhs ← stack (int32)
	add sp, sp, #16
	sub x0, x1, x0
	str x0, [x29, #-128]
	# guardar int32 inferido (64-bit)
	# if condición #1
	ldr x0, [x29, #-128]
	# dif (int32 - 64-bit)
	mov x1, x0
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	cmp x1, x0
	# comparar x1(lhs) vs x0(rhs) - flags setup
	cset x0, lt
	# materializar resultado bool en x0
	b.ge .if_end_6
	# branch falso (comparación simple)
	# dif = expr
	ldr x0, [x29, #-128]
	# dif (int32 - 64-bit)
	neg x0, x0
	# negación int32
	str x0, [x29, #-128]
	# guardar int32 (64-bit)
	b .if_end_6
	# saltar al final del if
.if_end_6:
	# total += expr
	ldr x0, [x29, #-104]
	# cargar int32 (64-bit)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	ldr x0, [x29, #-128]
	# dif (int32 - 64-bit)
	ldr x1, [sp]
	# lhs ← stack
	add sp, sp, #16
	add x0, x1, x0
	# int32 suma
	str x0, [x29, #-104]
	# guardar int32 (64-bit)
.for_post_5:
	# for post
	# j++
	ldr x0, [x29, #-120]
	# cargar j (int32)
	add x0, x0, #1
	# j + 1
	str x0, [x29, #-120]
	# guardar j++
	b .for_start_3
	# volver al test
.for_end_4:
.for_post_2:
	# for post
	# i++
	ldr x0, [x29, #-112]
	# cargar i (int32)
	add x0, x0, #1
	# i + 1
	str x0, [x29, #-112]
	# guardar i++
	b .for_start_0
	# volver al test
.for_end_1:
	# return — valor único
	ldr x0, [x29, #-104]
	# total (int32 - 64-bit)
	b .epilogue_indiceInestabilidad
	# return → epílogo
.epilogue_indiceInestabilidad:
	add sp, sp, #224
	ldp x29, x30, [sp], #16
	ret
reglaCramer:
	stp x29, x30, [sp, #-16]!
	# guardar frame pointer y link register
	mov x29, sp
	# x29 = frame pointer (SP actual)
	sub sp, sp, #160
	# reservar 160 bytes para variables locales
	mov x11, x0
	# param array matriz: ptr de entrada
	mov x14, x11
	# matriz[0] base param
	ldr x12, [x14]
	# matriz[0] leer param
	str x12, [x29, #-8]
	# matriz[0] copiar a frame
	sub x14, x11, #8
	# matriz[1] dirección param
	ldr x12, [x14]
	# matriz[1] leer param
	str x12, [x29, #-16]
	# matriz[1] copiar a frame
	sub x14, x11, #16
	# matriz[2] dirección param
	ldr x12, [x14]
	# matriz[2] leer param
	str x12, [x29, #-24]
	# matriz[2] copiar a frame
	sub x14, x11, #24
	# matriz[3] dirección param
	ldr x12, [x14]
	# matriz[3] leer param
	str x12, [x29, #-32]
	# matriz[3] copiar a frame
	mov x11, x1
	# param array vector: ptr de entrada
	mov x14, x11
	# vector[0] base param
	ldr x12, [x14]
	# vector[0] leer param
	str x12, [x29, #-40]
	# vector[0] copiar a frame
	sub x14, x11, #8
	# vector[1] dirección param
	ldr x12, [x14]
	# vector[1] leer param
	str x12, [x29, #-48]
	# vector[1] copiar a frame
	# frame.size=160, locals=3
	# var respuesta [2]int32 (ya alocado en prescan)
	mov x0, xzr
	# array default slot 0 = 0
	str x0, [x29, #-56]
	mov x0, xzr
	# array default slot 1 = 0
	str x0, [x29, #-64]
	# det := expr (tipo inferido)
	# matriz[idx0][idx1] (lectura)
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x1, xzr
	# x1 = offset acumulador
	ldr x4, [sp]
	# índice 1 ← stack
	add sp, sp, #16
	add x1, x1, x4
	# offset += idx[1] * stride
	ldr x4, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	mov x5, #2
	# stride = 2
	mul x4, x4, x5
	# idx[0] * stride
	add x1, x1, x4
	# offset += idx[0] * stride
	lsl x1, x1, #3
	# offset *= 8
	sub x2, x29, #8
	# dirección base del array matriz → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr x0, [x3]
	# matriz[idx] (lectura) → x0
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	# matriz[idx0][idx1] (lectura)
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x1, xzr
	# x1 = offset acumulador
	ldr x4, [sp]
	# índice 1 ← stack
	add sp, sp, #16
	add x1, x1, x4
	# offset += idx[1] * stride
	ldr x4, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	mov x5, #2
	# stride = 2
	mul x4, x4, x5
	# idx[0] * stride
	add x1, x1, x4
	# offset += idx[0] * stride
	lsl x1, x1, #3
	# offset *= 8
	sub x2, x29, #8
	# dirección base del array matriz → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr x0, [x3]
	# matriz[idx] (lectura) → x0
	ldr x1, [sp]
	# lhs ← stack (int32)
	add sp, sp, #16
	mul x0, x1, x0
	# int32 mul
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	# matriz[idx0][idx1] (lectura)
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x1, xzr
	# x1 = offset acumulador
	ldr x4, [sp]
	# índice 1 ← stack
	add sp, sp, #16
	add x1, x1, x4
	# offset += idx[1] * stride
	ldr x4, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	mov x5, #2
	# stride = 2
	mul x4, x4, x5
	# idx[0] * stride
	add x1, x1, x4
	# offset += idx[0] * stride
	lsl x1, x1, #3
	# offset *= 8
	sub x2, x29, #8
	# dirección base del array matriz → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr x0, [x3]
	# matriz[idx] (lectura) → x0
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	# matriz[idx0][idx1] (lectura)
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x1, xzr
	# x1 = offset acumulador
	ldr x4, [sp]
	# índice 1 ← stack
	add sp, sp, #16
	add x1, x1, x4
	# offset += idx[1] * stride
	ldr x4, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	mov x5, #2
	# stride = 2
	mul x4, x4, x5
	# idx[0] * stride
	add x1, x1, x4
	# offset += idx[0] * stride
	lsl x1, x1, #3
	# offset *= 8
	sub x2, x29, #8
	# dirección base del array matriz → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr x0, [x3]
	# matriz[idx] (lectura) → x0
	ldr x1, [sp]
	# lhs ← stack (int32)
	add sp, sp, #16
	mul x0, x1, x0
	# int32 mul
	ldr x1, [sp]
	# lhs ← stack (int32)
	add sp, sp, #16
	sub x0, x1, x0
	str x0, [x29, #-72]
	# guardar int32 inferido (64-bit)
	# if condición #1
	ldr x0, [x29, #-72]
	# det (int32 - 64-bit)
	mov x1, x0
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	cmp x1, x0
	# comparar x1(lhs) vs x0(rhs) - flags setup
	cset x0, eq
	# materializar resultado bool en x0
	b.ne .if_end_7
	# branch falso (comparación simple)
	# return — valor único
	sub x0, x29, #56
	# respuesta (array base address)
	b .epilogue_reglaCramer
	# return → epílogo
	b .if_end_7
	# saltar al final del if
.if_end_7:
	# detX := expr (tipo inferido)
	# vector[idx0] (lectura)
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	ldr x1, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	lsl x1, x1, #3
	# offset = idx * 8
	sub x2, x29, #40
	# dirección base del array vector → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr x0, [x3]
	# vector[idx] (lectura) → x0
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	# matriz[idx0][idx1] (lectura)
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x1, xzr
	# x1 = offset acumulador
	ldr x4, [sp]
	# índice 1 ← stack
	add sp, sp, #16
	add x1, x1, x4
	# offset += idx[1] * stride
	ldr x4, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	mov x5, #2
	# stride = 2
	mul x4, x4, x5
	# idx[0] * stride
	add x1, x1, x4
	# offset += idx[0] * stride
	lsl x1, x1, #3
	# offset *= 8
	sub x2, x29, #8
	# dirección base del array matriz → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr x0, [x3]
	# matriz[idx] (lectura) → x0
	ldr x1, [sp]
	# lhs ← stack (int32)
	add sp, sp, #16
	mul x0, x1, x0
	# int32 mul
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	# matriz[idx0][idx1] (lectura)
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x1, xzr
	# x1 = offset acumulador
	ldr x4, [sp]
	# índice 1 ← stack
	add sp, sp, #16
	add x1, x1, x4
	# offset += idx[1] * stride
	ldr x4, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	mov x5, #2
	# stride = 2
	mul x4, x4, x5
	# idx[0] * stride
	add x1, x1, x4
	# offset += idx[0] * stride
	lsl x1, x1, #3
	# offset *= 8
	sub x2, x29, #8
	# dirección base del array matriz → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr x0, [x3]
	# matriz[idx] (lectura) → x0
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	# vector[idx0] (lectura)
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	ldr x1, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	lsl x1, x1, #3
	# offset = idx * 8
	sub x2, x29, #40
	# dirección base del array vector → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr x0, [x3]
	# vector[idx] (lectura) → x0
	ldr x1, [sp]
	# lhs ← stack (int32)
	add sp, sp, #16
	mul x0, x1, x0
	# int32 mul
	ldr x1, [sp]
	# lhs ← stack (int32)
	add sp, sp, #16
	sub x0, x1, x0
	str x0, [x29, #-80]
	# guardar int32 inferido (64-bit)
	# detY := expr (tipo inferido)
	# matriz[idx0][idx1] (lectura)
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x1, xzr
	# x1 = offset acumulador
	ldr x4, [sp]
	# índice 1 ← stack
	add sp, sp, #16
	add x1, x1, x4
	# offset += idx[1] * stride
	ldr x4, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	mov x5, #2
	# stride = 2
	mul x4, x4, x5
	# idx[0] * stride
	add x1, x1, x4
	# offset += idx[0] * stride
	lsl x1, x1, #3
	# offset *= 8
	sub x2, x29, #8
	# dirección base del array matriz → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr x0, [x3]
	# matriz[idx] (lectura) → x0
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	# vector[idx0] (lectura)
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	ldr x1, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	lsl x1, x1, #3
	# offset = idx * 8
	sub x2, x29, #40
	# dirección base del array vector → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr x0, [x3]
	# vector[idx] (lectura) → x0
	ldr x1, [sp]
	# lhs ← stack (int32)
	add sp, sp, #16
	mul x0, x1, x0
	# int32 mul
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	# vector[idx0] (lectura)
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	ldr x1, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	lsl x1, x1, #3
	# offset = idx * 8
	sub x2, x29, #40
	# dirección base del array vector → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr x0, [x3]
	# vector[idx] (lectura) → x0
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	# matriz[idx0][idx1] (lectura)
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x1, xzr
	# x1 = offset acumulador
	ldr x4, [sp]
	# índice 1 ← stack
	add sp, sp, #16
	add x1, x1, x4
	# offset += idx[1] * stride
	ldr x4, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	mov x5, #2
	# stride = 2
	mul x4, x4, x5
	# idx[0] * stride
	add x1, x1, x4
	# offset += idx[0] * stride
	lsl x1, x1, #3
	# offset *= 8
	sub x2, x29, #8
	# dirección base del array matriz → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr x0, [x3]
	# matriz[idx] (lectura) → x0
	ldr x1, [sp]
	# lhs ← stack (int32)
	add sp, sp, #16
	mul x0, x1, x0
	# int32 mul
	ldr x1, [sp]
	# lhs ← stack (int32)
	add sp, sp, #16
	sub x0, x1, x0
	str x0, [x29, #-88]
	# guardar int32 inferido (64-bit)
	# respuesta[idx0] = expr
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	ldr x0, [x29, #-80]
	# detX (int32 - 64-bit)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	ldr x0, [x29, #-72]
	# det (int32 - 64-bit)
	ldr x1, [sp]
	# lhs ← stack (int32)
	add sp, sp, #16
	sdiv x0, x1, x0
	# int32 div (signed)
	ldr x1, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	lsl x1, x1, #3
	# offset = idx * 8
	sub x2, x29, #56
	# dirección base del array respuesta → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	str x0, [x3]
	# respuesta[idx0] ← valor
	# respuesta[idx0] = expr
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	ldr x0, [x29, #-88]
	# detY (int32 - 64-bit)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	ldr x0, [x29, #-72]
	# det (int32 - 64-bit)
	ldr x1, [sp]
	# lhs ← stack (int32)
	add sp, sp, #16
	sdiv x0, x1, x0
	# int32 div (signed)
	ldr x1, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	lsl x1, x1, #3
	# offset = idx * 8
	sub x2, x29, #56
	# dirección base del array respuesta → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	str x0, [x3]
	# respuesta[idx0] ← valor
	# return — valor único
	sub x0, x29, #56
	# respuesta (array base address)
	b .epilogue_reglaCramer
	# return → epílogo
.epilogue_reglaCramer:
	add sp, sp, #160
	ldp x29, x30, [sp], #16
	ret
promedioCapas:
	stp x29, x30, [sp, #-16]!
	# guardar frame pointer y link register
	mov x29, sp
	# x29 = frame pointer (SP actual)
	sub sp, sp, #224
	# reservar 224 bytes para variables locales
	mov x11, x0
	# param array cubo: ptr de entrada
	mov x14, x11
	# cubo[0] base param
	ldr x12, [x14]
	# cubo[0] leer param
	str x12, [x29, #-8]
	# cubo[0] copiar a frame
	sub x14, x11, #8
	# cubo[1] dirección param
	ldr x12, [x14]
	# cubo[1] leer param
	str x12, [x29, #-16]
	# cubo[1] copiar a frame
	sub x14, x11, #16
	# cubo[2] dirección param
	ldr x12, [x14]
	# cubo[2] leer param
	str x12, [x29, #-24]
	# cubo[2] copiar a frame
	sub x14, x11, #24
	# cubo[3] dirección param
	ldr x12, [x14]
	# cubo[3] leer param
	str x12, [x29, #-32]
	# cubo[3] copiar a frame
	sub x14, x11, #32
	# cubo[4] dirección param
	ldr x12, [x14]
	# cubo[4] leer param
	str x12, [x29, #-40]
	# cubo[4] copiar a frame
	sub x14, x11, #40
	# cubo[5] dirección param
	ldr x12, [x14]
	# cubo[5] leer param
	str x12, [x29, #-48]
	# cubo[5] copiar a frame
	sub x14, x11, #48
	# cubo[6] dirección param
	ldr x12, [x14]
	# cubo[6] leer param
	str x12, [x29, #-56]
	# cubo[6] copiar a frame
	sub x14, x11, #56
	# cubo[7] dirección param
	ldr x12, [x14]
	# cubo[7] leer param
	str x12, [x29, #-64]
	# cubo[7] copiar a frame
	# frame.size=224, locals=4
	# var salida [2][2]float32 (ya alocado en prescan)
	mov x0, xzr
	# array default slot 0 = 0
	str x0, [x29, #-72]
	mov x0, xzr
	# array default slot 1 = 0
	str x0, [x29, #-80]
	mov x0, xzr
	# array default slot 2 = 0
	str x0, [x29, #-88]
	mov x0, xzr
	# array default slot 3 = 0
	str x0, [x29, #-96]
	# for init
	# k := expr (tipo inferido)
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	str x0, [x29, #-104]
	# guardar int32 inferido (64-bit)
.for_start_8:
	# for condición
	ldr x0, [x29, #-104]
	# k (int32 - 64-bit)
	mov x1, x0
	mov x0, #2
	# int32 literal (64-bit per AArch64)
	cmp x1, x0
	# comparar x1(lhs) vs x0(rhs) - flags setup
	cset x0, lt
	# materializar resultado bool en x0
	b.ge .for_end_9
	# falso salir del bucle (comparación simple)
	# for init
	# i := expr (tipo inferido)
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	str x0, [x29, #-112]
	# guardar int32 inferido (64-bit)
.for_start_11:
	# for condición
	ldr x0, [x29, #-112]
	# i (int32 - 64-bit)
	mov x1, x0
	mov x0, #2
	# int32 literal (64-bit per AArch64)
	cmp x1, x0
	# comparar x1(lhs) vs x0(rhs) - flags setup
	cset x0, lt
	# materializar resultado bool en x0
	b.ge .for_end_12
	# falso salir del bucle (comparación simple)
	# var suma int32 = expr
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	str x0, [x29, #-120]
	# guardar int32
	# for init
	# j := expr (tipo inferido)
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	str x0, [x29, #-128]
	# guardar int32 inferido (64-bit)
.for_start_14:
	# for condición
	ldr x0, [x29, #-128]
	# j (int32 - 64-bit)
	mov x1, x0
	mov x0, #2
	# int32 literal (64-bit per AArch64)
	cmp x1, x0
	# comparar x1(lhs) vs x0(rhs) - flags setup
	cset x0, lt
	# materializar resultado bool en x0
	b.ge .for_end_15
	# falso salir del bucle (comparación simple)
	# suma += expr
	ldr x0, [x29, #-120]
	# cargar int32 (64-bit)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	# cubo[idx0][idx1][idx2] (lectura)
	ldr x0, [x29, #-104]
	# k (int32 - 64-bit)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	ldr x0, [x29, #-112]
	# i (int32 - 64-bit)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	ldr x0, [x29, #-128]
	# j (int32 - 64-bit)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x1, xzr
	# x1 = offset acumulador
	ldr x4, [sp]
	# índice 2 ← stack
	add sp, sp, #16
	add x1, x1, x4
	# offset += idx[2] * stride
	ldr x4, [sp]
	# índice 1 ← stack
	add sp, sp, #16
	mov x5, #2
	# stride = 2
	mul x4, x4, x5
	# idx[1] * stride
	add x1, x1, x4
	# offset += idx[1] * stride
	ldr x4, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	mov x5, #4
	# stride = 4
	mul x4, x4, x5
	# idx[0] * stride
	add x1, x1, x4
	# offset += idx[0] * stride
	lsl x1, x1, #3
	# offset *= 8
	sub x2, x29, #8
	# dirección base del array cubo → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr x0, [x3]
	# cubo[idx] (lectura) → x0
	ldr x1, [sp]
	# lhs ← stack
	add sp, sp, #16
	add x0, x1, x0
	# int32 suma
	str x0, [x29, #-120]
	# guardar int32 (64-bit)
.for_post_16:
	# for post
	# j++
	ldr x0, [x29, #-128]
	# cargar j (int32)
	add x0, x0, #1
	# j + 1
	str x0, [x29, #-128]
	# guardar j++
	b .for_start_14
	# volver al test
.for_end_15:
	# salida[idx0][idx1] = expr
	ldr x0, [x29, #-104]
	# k (int32 - 64-bit)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	ldr x0, [x29, #-112]
	# i (int32 - 64-bit)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	ldr x0, [x29, #-120]
	# suma (int32 - 64-bit)
	scvtf s0, x0
	# int32 → float32
	sub sp, sp, #16
	# reservar slot float temporal
	str s0, [sp]
	# s0 stack temporal
	# float32 literal 2.0 → s0
	adrp x9, .flt_1
	# página de la constante float
	ldr s0, [x9, :lo12:.flt_1]
	# cargar float32 IEEE-754 en s0
	ldr s1, [sp]
	# stack s1 (lhs float)
	add sp, sp, #16
	fdiv s0, s1, s0
	# float32 división
	mov x1, xzr
	# x1 = offset acumulador
	ldr x4, [sp]
	# índice 1 ← stack
	add sp, sp, #16
	add x1, x1, x4
	# offset += idx[1] * stride
	ldr x4, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	mov x5, #2
	# stride = 2
	mul x4, x4, x5
	# idx[0] * stride
	add x1, x1, x4
	# offset += idx[0] * stride
	lsl x1, x1, #3
	# offset *= 8 (sizeof element)
	sub x2, x29, #72
	# dirección base del array salida → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	str s0, [x3]
	# salida[idx0][idx1] ← valor float32
.for_post_13:
	# for post
	# i++
	ldr x0, [x29, #-112]
	# cargar i (int32)
	add x0, x0, #1
	# i + 1
	str x0, [x29, #-112]
	# guardar i++
	b .for_start_11
	# volver al test
.for_end_12:
.for_post_10:
	# for post
	# k++
	ldr x0, [x29, #-104]
	# cargar k (int32)
	add x0, x0, #1
	# k + 1
	str x0, [x29, #-104]
	# guardar k++
	b .for_start_8
	# volver al test
.for_end_9:
	# return — valor único
	sub x0, x29, #72
	# salida (array base address)
	b .epilogue_promedioCapas
	# return → epílogo
.epilogue_promedioCapas:
	add sp, sp, #224
	ldp x29, x30, [sp], #16
	ret
softmax:
	stp x29, x30, [sp, #-16]!
	# guardar frame pointer y link register
	mov x29, sp
	# x29 = frame pointer (SP actual)
	sub sp, sp, #288
	# reservar 288 bytes para variables locales
	mov x11, x0
	# param array matriz: ptr de entrada
	mov x14, x11
	# matriz[0] base param
	ldr x12, [x14]
	# matriz[0] leer param
	str x12, [x29, #-8]
	# matriz[0] copiar a frame
	sub x14, x11, #8
	# matriz[1] dirección param
	ldr x12, [x14]
	# matriz[1] leer param
	str x12, [x29, #-16]
	# matriz[1] copiar a frame
	sub x14, x11, #16
	# matriz[2] dirección param
	ldr x12, [x14]
	# matriz[2] leer param
	str x12, [x29, #-24]
	# matriz[2] copiar a frame
	sub x14, x11, #24
	# matriz[3] dirección param
	ldr x12, [x14]
	# matriz[3] leer param
	str x12, [x29, #-32]
	# matriz[3] copiar a frame
	sub x14, x11, #32
	# matriz[4] dirección param
	ldr x12, [x14]
	# matriz[4] leer param
	str x12, [x29, #-40]
	# matriz[4] copiar a frame
	sub x14, x11, #40
	# matriz[5] dirección param
	ldr x12, [x14]
	# matriz[5] leer param
	str x12, [x29, #-48]
	# matriz[5] copiar a frame
	# frame.size=288, locals=5
	# var salida [2][3]float32 (ya alocado en prescan)
	mov x0, xzr
	# array default slot 0 = 0
	str x0, [x29, #-56]
	mov x0, xzr
	# array default slot 1 = 0
	str x0, [x29, #-64]
	mov x0, xzr
	# array default slot 2 = 0
	str x0, [x29, #-72]
	mov x0, xzr
	# array default slot 3 = 0
	str x0, [x29, #-80]
	mov x0, xzr
	# array default slot 4 = 0
	str x0, [x29, #-88]
	mov x0, xzr
	# array default slot 5 = 0
	str x0, [x29, #-96]
	# for init
	# i := expr (tipo inferido)
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	str x0, [x29, #-104]
	# guardar int32 inferido (64-bit)
.for_start_17:
	# for condición
	ldr x0, [x29, #-104]
	# i (int32 - 64-bit)
	mov x1, x0
	mov x0, #2
	# int32 literal (64-bit per AArch64)
	cmp x1, x0
	# comparar x1(lhs) vs x0(rhs) - flags setup
	cset x0, lt
	# materializar resultado bool en x0
	b.ge .for_end_18
	# falso salir del bucle (comparación simple)
	# var max float32 = expr
	# matriz[idx0][idx1] (lectura)
	ldr x0, [x29, #-104]
	# i (int32 - 64-bit)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x1, xzr
	# x1 = offset acumulador
	ldr x4, [sp]
	# índice 1 ← stack
	add sp, sp, #16
	add x1, x1, x4
	# offset += idx[1] * stride
	ldr x4, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	mov x5, #3
	# stride = 3
	mul x4, x4, x5
	# idx[0] * stride
	add x1, x1, x4
	# offset += idx[0] * stride
	lsl x1, x1, #3
	# offset *= 8
	sub x2, x29, #8
	# dirección base del array matriz → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr s0, [x3]
	# matriz[idx] (lectura) → s0 (float32)
	str s0, [x29, #-112]
	# guardar float32
	# for init
	# j := expr (tipo inferido)
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	str x0, [x29, #-120]
	# guardar int32 inferido (64-bit)
.for_start_20:
	# for condición
	ldr x0, [x29, #-120]
	# j (int32 - 64-bit)
	mov x1, x0
	mov x0, #3
	# int32 literal (64-bit per AArch64)
	cmp x1, x0
	# comparar x1(lhs) vs x0(rhs) - flags setup
	cset x0, lt
	# materializar resultado bool en x0
	b.ge .for_end_21
	# falso salir del bucle (comparación simple)
	# if condición #1
	# matriz[idx0][idx1] (lectura)
	ldr x0, [x29, #-104]
	# i (int32 - 64-bit)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	ldr x0, [x29, #-120]
	# j (int32 - 64-bit)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x1, xzr
	# x1 = offset acumulador
	ldr x4, [sp]
	# índice 1 ← stack
	add sp, sp, #16
	add x1, x1, x4
	# offset += idx[1] * stride
	ldr x4, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	mov x5, #3
	# stride = 3
	mul x4, x4, x5
	# idx[0] * stride
	add x1, x1, x4
	# offset += idx[0] * stride
	lsl x1, x1, #3
	# offset *= 8
	sub x2, x29, #8
	# dirección base del array matriz → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr s0, [x3]
	# matriz[idx] (lectura) → s0 (float32)
	sub sp, sp, #16
	# reservar slot float temporal
	str s0, [sp]
	# s0 stack temporal
	ldr s0, [x29, #-112]
	# max (float32)
	ldr s1, [sp]
	# stack s1 (lhs float)
	add sp, sp, #16
	fcmp s1, s0
	# comparar floats (lhs s1 vs rhs s0)
	cset x0, gt
	# bool resultado comparación float (>)
	cbz x0, .if_end_23
	# falso → siguiente rama
	# max = expr
	# matriz[idx0][idx1] (lectura)
	ldr x0, [x29, #-104]
	# i (int32 - 64-bit)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	ldr x0, [x29, #-120]
	# j (int32 - 64-bit)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x1, xzr
	# x1 = offset acumulador
	ldr x4, [sp]
	# índice 1 ← stack
	add sp, sp, #16
	add x1, x1, x4
	# offset += idx[1] * stride
	ldr x4, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	mov x5, #3
	# stride = 3
	mul x4, x4, x5
	# idx[0] * stride
	add x1, x1, x4
	# offset += idx[0] * stride
	lsl x1, x1, #3
	# offset *= 8
	sub x2, x29, #8
	# dirección base del array matriz → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr s0, [x3]
	# matriz[idx] (lectura) → s0 (float32)
	str s0, [x29, #-112]
	# guardar float32
	b .if_end_23
	# saltar al final del if
.if_end_23:
.for_post_22:
	# for post
	# j++
	ldr x0, [x29, #-120]
	# cargar j (int32)
	add x0, x0, #1
	# j + 1
	str x0, [x29, #-120]
	# guardar j++
	b .for_start_20
	# volver al test
.for_end_21:
	# var sum float32 = expr
	# float32 literal 0.0 → s0
	adrp x9, .flt_4
	# página de la constante float
	ldr s0, [x9, :lo12:.flt_4]
	# cargar float32 IEEE-754 en s0
	str s0, [x29, #-128]
	# guardar float32
	# var exp [3]float32 (ya alocado en prescan)
	mov x0, xzr
	# array default slot 0 = 0
	str x0, [x29, #-136]
	mov x0, xzr
	# array default slot 1 = 0
	str x0, [x29, #-144]
	mov x0, xzr
	# array default slot 2 = 0
	str x0, [x29, #-152]
	# for init
	# j := expr (tipo inferido)
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	str x0, [x29, #-120]
	# guardar int32 inferido (64-bit)
.for_start_24:
	# for condición
	ldr x0, [x29, #-120]
	# j (int32 - 64-bit)
	mov x1, x0
	mov x0, #3
	# int32 literal (64-bit per AArch64)
	cmp x1, x0
	# comparar x1(lhs) vs x0(rhs) - flags setup
	cset x0, lt
	# materializar resultado bool en x0
	b.ge .for_end_25
	# falso salir del bucle (comparación simple)
	# v := expr (tipo inferido)
	# float32 literal 1.0 → s0
	adrp x9, .flt_0
	# página de la constante float
	ldr s0, [x9, :lo12:.flt_0]
	# cargar float32 IEEE-754 en s0
	sub sp, sp, #16
	# reservar slot float temporal
	str s0, [sp]
	# s0 stack temporal
	# matriz[idx0][idx1] (lectura)
	ldr x0, [x29, #-104]
	# i (int32 - 64-bit)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	ldr x0, [x29, #-120]
	# j (int32 - 64-bit)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x1, xzr
	# x1 = offset acumulador
	ldr x4, [sp]
	# índice 1 ← stack
	add sp, sp, #16
	add x1, x1, x4
	# offset += idx[1] * stride
	ldr x4, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	mov x5, #3
	# stride = 3
	mul x4, x4, x5
	# idx[0] * stride
	add x1, x1, x4
	# offset += idx[0] * stride
	lsl x1, x1, #3
	# offset *= 8
	sub x2, x29, #8
	# dirección base del array matriz → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr s0, [x3]
	# matriz[idx] (lectura) → s0 (float32)
	sub sp, sp, #16
	# reservar slot float temporal
	str s0, [sp]
	# s0 stack temporal
	ldr s0, [x29, #-112]
	# max (float32)
	ldr s1, [sp]
	# stack s1 (lhs float)
	add sp, sp, #16
	fsub s0, s1, s0
	# float32 resta
	ldr s1, [sp]
	# stack s1 (lhs float)
	add sp, sp, #16
	fadd s0, s1, s0
	# float32 suma
	str s0, [x29, #-160]
	# guardar float32 inferido
	# if condición #1
	ldr s0, [x29, #-160]
	# v (float32)
	sub sp, sp, #16
	# reservar slot float temporal
	str s0, [sp]
	# s0 stack temporal
	# float32 literal 0.1 → s0
	adrp x9, .flt_5
	# página de la constante float
	ldr s0, [x9, :lo12:.flt_5]
	# cargar float32 IEEE-754 en s0
	ldr s1, [sp]
	# stack s1 (lhs float)
	add sp, sp, #16
	fcmp s1, s0
	# comparar floats (lhs s1 vs rhs s0)
	cset x0, mi
	# bool resultado comparación float (<)
	cbz x0, .if_end_27
	# falso → siguiente rama
	# v = expr
	# float32 literal 0.1 → s0
	adrp x9, .flt_5
	# página de la constante float
	ldr s0, [x9, :lo12:.flt_5]
	# cargar float32 IEEE-754 en s0
	str s0, [x29, #-160]
	# guardar float32
	b .if_end_27
	# saltar al final del if
.if_end_27:
	# exp[idx0] = expr
	ldr x0, [x29, #-120]
	# j (int32 - 64-bit)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	ldr s0, [x29, #-160]
	# v (float32)
	ldr x1, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	lsl x1, x1, #3
	# offset = idx * 8
	sub x2, x29, #136
	# dirección base del array exp → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	str s0, [x3]
	# exp[idx0] ← valor float32
	# sum += expr
	ldr s0, [x29, #-128]
	# cargar float32
	sub sp, sp, #16
	# reservar slot float temporal
	str s0, [sp]
	# s0 stack temporal
	ldr s0, [x29, #-160]
	# v (float32)
	ldr s1, [sp]
	# stack s1 (lhs float)
	add sp, sp, #16
	fadd s0, s1, s0
	# float32 suma
	str s0, [x29, #-128]
	# guardar float32
.for_post_26:
	# for post
	# j++
	ldr x0, [x29, #-120]
	# cargar j (int32)
	add x0, x0, #1
	# j + 1
	str x0, [x29, #-120]
	# guardar j++
	b .for_start_24
	# volver al test
.for_end_25:
	# for init
	# j := expr (tipo inferido)
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	str x0, [x29, #-120]
	# guardar int32 inferido (64-bit)
.for_start_28:
	# for condición
	ldr x0, [x29, #-120]
	# j (int32 - 64-bit)
	mov x1, x0
	mov x0, #3
	# int32 literal (64-bit per AArch64)
	cmp x1, x0
	# comparar x1(lhs) vs x0(rhs) - flags setup
	cset x0, lt
	# materializar resultado bool en x0
	b.ge .for_end_29
	# falso salir del bucle (comparación simple)
	# salida[idx0][idx1] = expr
	ldr x0, [x29, #-104]
	# i (int32 - 64-bit)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	ldr x0, [x29, #-120]
	# j (int32 - 64-bit)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	# exp[idx0] (lectura)
	ldr x0, [x29, #-120]
	# j (int32 - 64-bit)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	ldr x1, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	lsl x1, x1, #3
	# offset = idx * 8
	sub x2, x29, #136
	# dirección base del array exp → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr s0, [x3]
	# exp[idx] (lectura) → s0 (float32)
	sub sp, sp, #16
	# reservar slot float temporal
	str s0, [sp]
	# s0 stack temporal
	ldr s0, [x29, #-128]
	# sum (float32)
	ldr s1, [sp]
	# stack s1 (lhs float)
	add sp, sp, #16
	fdiv s0, s1, s0
	# float32 división
	mov x1, xzr
	# x1 = offset acumulador
	ldr x4, [sp]
	# índice 1 ← stack
	add sp, sp, #16
	add x1, x1, x4
	# offset += idx[1] * stride
	ldr x4, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	mov x5, #3
	# stride = 3
	mul x4, x4, x5
	# idx[0] * stride
	add x1, x1, x4
	# offset += idx[0] * stride
	lsl x1, x1, #3
	# offset *= 8 (sizeof element)
	sub x2, x29, #56
	# dirección base del array salida → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	str s0, [x3]
	# salida[idx0][idx1] ← valor float32
.for_post_30:
	# for post
	# j++
	ldr x0, [x29, #-120]
	# cargar j (int32)
	add x0, x0, #1
	# j + 1
	str x0, [x29, #-120]
	# guardar j++
	b .for_start_28
	# volver al test
.for_end_29:
.for_post_19:
	# for post
	# i++
	ldr x0, [x29, #-104]
	# cargar i (int32)
	add x0, x0, #1
	# i + 1
	str x0, [x29, #-104]
	# guardar i++
	b .for_start_17
	# volver al test
.for_end_18:
	# return — valor único
	sub x0, x29, #56
	# salida (array base address)
	b .epilogue_softmax
	# return → epílogo
.epilogue_softmax:
	add sp, sp, #288
	ldp x29, x30, [sp], #16
	ret