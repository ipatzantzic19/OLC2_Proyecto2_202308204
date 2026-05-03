.section .data
msg:
    .ascii "\n"
buffer:
    .ascii "0\n"
len = . - buffer

.str_0: .string "=== INICIO DE CALIFICACION: ARREGLOS ==="
.str_1: .string "%s"
.str_2: .string "\n"
.str_3: .string "\n--- 5.1 DECLARACION MULTIDIMENSIONAL ---"
.str_4: .string "Matriz no inicializada [1][1]:"
.str_5: .string "%s "
.str_6: .string "%ld"
.str_7: .string "Matriz inicializada [0][0]:"
.str_8: .string "\n--- 5.2 ACCESO Y MODIFICACION MULTIDIMENSIONAL ---"
.str_9: .string "Original matrizNoInit[0][1]:"
.str_10: .string "Modificado matrizNoInit[0][1]:"
.str_11: .string "\n=== FIN DE CALIFICACION: ARREGLOS ==="

.section .text
.align 2
.global _start

_start:
	stp x29, x30, [sp, #-16]!
	# guardar frame pointer y link register
	mov x29, sp
	# x29 = frame pointer (SP actual)
	sub sp, sp, #128
	# reservar 128 bytes para variables locales
	# frame.size=128, locals=0
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
	# var matrizNoInit [2][2]int32 (ya alocado en prescan)
	mov x0, xzr
	# array default slot 0 = 0
	str x0, [x29, #-8]
	mov x0, xzr
	# array default slot 1 = 0
	str x0, [x29, #-16]
	mov x0, xzr
	# array default slot 2 = 0
	str x0, [x29, #-24]
	mov x0, xzr
	# array default slot 3 = 0
	str x0, [x29, #-32]
	# matrizInit := expr (tipo inferido)
	mov x0, xzr
	# array literal → valor manejado por el destino
	# matrizInit := array literal (array registrado en prescan)
	mov x0, #1
	# matrizInit[0] ← literal
	str x0, [x29, #-40]
	mov x0, #2
	# matrizInit[1] ← literal
	str x0, [x29, #-48]
	mov x0, #3
	# matrizInit[2] ← literal
	str x0, [x29, #-56]
	mov x0, #4
	# matrizInit[3] ← literal
	str x0, [x29, #-64]
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
	# matrizNoInit[idx0][idx1] (lectura)
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
	# dirección base del array matrizNoInit → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr x0, [x3]
	# matrizNoInit[idx] (lectura) → x0
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
	adrp x0, .str_5
	add x0, x0, :lo12:.str_5
	bl printf
	# matrizInit[idx0][idx1] (lectura)
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
	sub x2, x29, #40
	# dirección base del array matrizInit → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr x0, [x3]
	# matrizInit[idx] (lectura) → x0
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
	adrp x0, .str_8
	add x0, x0, :lo12:.str_8
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
	adrp x0, .str_9
	add x0, x0, :lo12:.str_9
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_5
	add x0, x0, :lo12:.str_5
	bl printf
	# matrizNoInit[idx0][idx1] (lectura)
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
	# dirección base del array matrizNoInit → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr x0, [x3]
	# matrizNoInit[idx] (lectura) → x0
	# fmt.Println arg 1 (int32)
	mov x1, x0
	# int32 → x1 para printf %ld
	adrp x0, .str_6
	add x0, x0, :lo12:.str_6
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# matrizNoInit[idx0][idx1] = expr
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
	mov x0, #77
	# int32 literal (64-bit per AArch64)
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
	sub x2, x29, #8
	# dirección base del array matrizNoInit → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	str x0, [x3]
	# matrizNoInit[idx0][idx1] ← valor
	# string literal → x0 (puntero)
	adrp x0, .str_10
	add x0, x0, :lo12:.str_10
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_5
	add x0, x0, :lo12:.str_5
	bl printf
	# matrizNoInit[idx0][idx1] (lectura)
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
	# dirección base del array matrizNoInit → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr x0, [x3]
	# matrizNoInit[idx] (lectura) → x0
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
	adrp x0, .str_11
	add x0, x0, :lo12:.str_11
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
	add sp, sp, #128
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