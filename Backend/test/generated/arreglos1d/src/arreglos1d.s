.section .data
msg:
    .ascii "\n"
buffer:
    .ascii "0\n"
len = . - buffer

.str_0: .string "=== INICIO DE CALIFICACION: ARREGLOS ==="
.str_1: .string "%s"
.str_2: .string "\n"
.str_3: .string "\n--- 5.1 DECLARACION 1D INICIALIZADA Y NO INICIALIZADA ---"
.str_4: .string "No inicializado pos0:"
.str_5: .string "%s "
.str_6: .string "%ld"
.str_7: .string "Inicializado pos2:"
.str_8: .string "\n--- 5.2 ARREGLOS DE TIPOS ESTATICOS ---"
.str_9: .string "Ana"
.str_10: .string "Luis"
.str_11: .string "Maria"
.str_12: .string "%g "
.str_13: .string "false"
.str_14: .string "true"
.str_15: .string "%c "
.str_16: .string "\n--- 5.3 ACCESO Y MODIFICACION 1D ---"
.str_17: .string "Original arregloInit[1]:"
.str_18: .string "Modificado arregloInit[1]:"
.str_19: .string "Longitud arregloInit:"
.str_20: .string "\n--- 5.4 DECLARACION MULTIDIMENSIONAL ---"
.str_21: .string "Matriz no inicializada [1][1]:"
.str_22: .string "Matriz inicializada [0][0]:"
.str_23: .string "\n--- 5.5 ACCESO Y MODIFICACION MULTIDIMENSIONAL ---"
.str_24: .string "Original matrizNoInit[0][1]:"
.str_25: .string "Modificado matrizNoInit[0][1]:"
.str_26: .string "\n=== FIN DE CALIFICACION: ARREGLOS ==="

.section .text
.align 2
.global _start

_start:
	stp x29, x30, [sp, #-16]!
	# guardar frame pointer y link register
	mov x29, sp
	# x29 = frame pointer (SP actual)
	sub sp, sp, #480
	# reservar 480 bytes para variables locales
	# frame.size=480, locals=0
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
	# var arregloNoInit [5]int32 (ya alocado en prescan)
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
	mov x0, xzr
	# array default slot 4 = 0
	str x0, [x29, #-40]
	# arregloInit := expr (tipo inferido)
	mov x0, xzr
	# array literal → valor manejado por el destino
	# arregloInit := array literal (array registrado en prescan)
	mov x0, #10
	# arregloInit[0] ← literal
	str x0, [x29, #-48]
	mov x0, #20
	# arregloInit[1] ← literal
	str x0, [x29, #-56]
	mov x0, #30
	# arregloInit[2] ← literal
	str x0, [x29, #-64]
	mov x0, #40
	# arregloInit[3] ← literal
	str x0, [x29, #-72]
	mov x0, #50
	# arregloInit[4] ← literal
	str x0, [x29, #-80]
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
	# arregloNoInit[idx0] (lectura)
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
	sub x2, x29, #8
	# dirección base del array arregloNoInit → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr x0, [x3]
	# arregloNoInit[idx] (lectura) → x0
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
	# arregloInit[idx0] (lectura)
	mov x0, #2
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
	sub x2, x29, #48
	# dirección base del array arregloInit → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr x0, [x3]
	# arregloInit[idx] (lectura) → x0
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
	# arrFloat := expr (tipo inferido)
	mov x0, xzr
	# array literal → valor manejado por el destino
	# arrFloat := array literal (array registrado en prescan)
	mov x0, xzr
	# arrFloat[0] ← float placeholder
	str x0, [x29, #-88]
	mov x0, xzr
	# arrFloat[1] ← float placeholder
	str x0, [x29, #-96]
	mov x0, xzr
	# arrFloat[2] ← float placeholder
	str x0, [x29, #-104]
	mov x0, xzr
	# array literal → valor manejado por el destino
	# arrBool := expr (tipo inferido)
	mov x0, xzr
	# array literal → valor manejado por el destino
	# arrBool := array literal (array registrado en prescan)
	mov x0, #1
	# arrBool[0] ← literal
	str x0, [x29, #-112]
	mov x0, #0
	# arrBool[1] ← literal
	str x0, [x29, #-120]
	mov x0, #1
	# arrBool[2] ← literal
	str x0, [x29, #-128]
	mov x0, xzr
	# array literal → valor manejado por el destino
	# arrRune := expr (tipo inferido)
	mov x0, xzr
	# array literal → valor manejado por el destino
	# arrRune := array literal (array registrado en prescan)
	mov x0, #65
	# arrRune[0] ← literal
	str x0, [x29, #-136]
	mov x0, #66
	# arrRune[1] ← literal
	str x0, [x29, #-144]
	mov x0, #67
	# arrRune[2] ← literal
	str x0, [x29, #-152]
	mov x0, xzr
	# array literal → valor manejado por el destino
	# arrString := expr (tipo inferido)
	mov x0, xzr
	# array literal → valor manejado por el destino
	# arrString := array literal (array registrado en prescan)
	adrp x0, .str_9
	add x0, x0, :lo12:.str_9
	str x0, [x29, #-160]
	# arrString[0] ← string literal
	adrp x0, .str_10
	add x0, x0, :lo12:.str_10
	str x0, [x29, #-168]
	# arrString[1] ← string literal
	adrp x0, .str_11
	add x0, x0, :lo12:.str_11
	str x0, [x29, #-176]
	# arrString[2] ← string literal
	mov x0, xzr
	# array literal → valor manejado por el destino
	# arrFloat[idx0] (lectura)
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
	sub x2, x29, #88
	# dirección base del array arrFloat → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr s0, [x3]
	# arrFloat[idx] (lectura) → s0 (float32)
	# fmt.Println arg 0 (float32)
	fcvt d0, s0
	# float32 a float64 para printf variadic
	fmov x1, d0
	# copiar float64 a x1 para variadic ABI
	adrp x0, .str_12
	add x0, x0, :lo12:.str_12
	bl printf
	# arrBool[idx0] (lectura)
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
	sub x2, x29, #112
	# dirección base del array arrBool → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr x0, [x3]
	# arrBool[idx] (lectura) → x0
	# fmt.Println arg 1 (bool)
	cbnz x0, .bool_true_0
	# si true → imprimir "true"
	adrp x0, .str_13
	add x0, x0, :lo12:.str_13
	mov x1, x0
	# bool string → x1
	adrp x0, .str_5
	add x0, x0, :lo12:.str_5
	bl printf
	b .bool_done_1
.bool_true_0:
	adrp x0, .str_14
	add x0, x0, :lo12:.str_14
	mov x1, x0
	# bool string → x1
	adrp x0, .str_5
	add x0, x0, :lo12:.str_5
	bl printf
.bool_done_1:
	# arrRune[idx0] (lectura)
	mov x0, #2
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
	sub x2, x29, #136
	# dirección base del array arrRune → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr x0, [x3]
	# arrRune[idx] (lectura) → x0
	# fmt.Println arg 2 (rune)
	mov x1, x0
	# rune → x1 para printf %c
	adrp x0, .str_15
	add x0, x0, :lo12:.str_15
	bl printf
	# arrString[idx0] (lectura)
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
	sub x2, x29, #160
	# dirección base del array arrString → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr x0, [x3]
	# arrString[idx] (lectura) → x0
	# fmt.Println arg 3 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# string literal → x0 (puntero)
	adrp x0, .str_16
	add x0, x0, :lo12:.str_16
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
	adrp x0, .str_17
	add x0, x0, :lo12:.str_17
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_5
	add x0, x0, :lo12:.str_5
	bl printf
	# arregloInit[idx0] (lectura)
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
	sub x2, x29, #48
	# dirección base del array arregloInit → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr x0, [x3]
	# arregloInit[idx] (lectura) → x0
	# fmt.Println arg 1 (int32)
	mov x1, x0
	# int32 → x1 para printf %ld
	adrp x0, .str_6
	add x0, x0, :lo12:.str_6
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# arregloInit[idx0] = expr
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x0, #99
	# int32 literal (64-bit per AArch64)
	ldr x1, [sp]
	# índice 0 ← stack
	add sp, sp, #16
	lsl x1, x1, #3
	# offset = idx * 8
	sub x2, x29, #48
	# dirección base del array arregloInit → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	str x0, [x3]
	# arregloInit[idx0] ← valor
	# string literal → x0 (puntero)
	adrp x0, .str_18
	add x0, x0, :lo12:.str_18
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_5
	add x0, x0, :lo12:.str_5
	bl printf
	# arregloInit[idx0] (lectura)
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
	sub x2, x29, #48
	# dirección base del array arregloInit → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	ldr x0, [x3]
	# arregloInit[idx] (lectura) → x0
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
	adrp x0, .str_19
	add x0, x0, :lo12:.str_19
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_5
	add x0, x0, :lo12:.str_5
	bl printf
	sub x0, x29, #48
	# arregloInit (array base address)
	mov x0, #5
	# len(array) → tamaño real
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
	adrp x0, .str_20
	add x0, x0, :lo12:.str_20
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
	str x0, [x29, #-184]
	mov x0, xzr
	# array default slot 1 = 0
	str x0, [x29, #-192]
	mov x0, xzr
	# array default slot 2 = 0
	str x0, [x29, #-200]
	mov x0, xzr
	# array default slot 3 = 0
	str x0, [x29, #-208]
	# matrizInit := expr (tipo inferido)
	mov x0, xzr
	# array literal → valor manejado por el destino
	# matrizInit := array literal (array registrado en prescan)
	mov x0, #1
	# matrizInit[0] ← literal
	str x0, [x29, #-216]
	mov x0, #3
	# matrizInit[1] ← literal
	str x0, [x29, #-224]
	mov x0, #0
	# matrizInit[2] ← literal
	str x0, [x29, #-232]
	mov x0, #0
	# matrizInit[3] ← literal
	str x0, [x29, #-240]
	mov x0, xzr
	# array literal → valor manejado por el destino
	# string literal → x0 (puntero)
	adrp x0, .str_21
	add x0, x0, :lo12:.str_21
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
	# índice 0 ← stack
	add sp, sp, #16
	mov x5, #2
	# stride = 2
	mul x4, x4, x5
	# idx[0] * stride
	add x1, x1, x4
	# offset += idx[0] * stride
	ldr x4, [sp]
	# índice 1 ← stack
	add sp, sp, #16
	add x1, x1, x4
	# offset += idx[1] * stride
	lsl x1, x1, #3
	# offset *= 8
	sub x2, x29, #184
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
	adrp x0, .str_22
	add x0, x0, :lo12:.str_22
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
	# índice 0 ← stack
	add sp, sp, #16
	mov x5, #2
	# stride = 2
	mul x4, x4, x5
	# idx[0] * stride
	add x1, x1, x4
	# offset += idx[0] * stride
	ldr x4, [sp]
	# índice 1 ← stack
	add sp, sp, #16
	add x1, x1, x4
	# offset += idx[1] * stride
	lsl x1, x1, #3
	# offset *= 8
	sub x2, x29, #216
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
	adrp x0, .str_23
	add x0, x0, :lo12:.str_23
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
	adrp x0, .str_24
	add x0, x0, :lo12:.str_24
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
	# índice 0 ← stack
	add sp, sp, #16
	mov x5, #2
	# stride = 2
	mul x4, x4, x5
	# idx[0] * stride
	add x1, x1, x4
	# offset += idx[0] * stride
	ldr x4, [sp]
	# índice 1 ← stack
	add sp, sp, #16
	add x1, x1, x4
	# offset += idx[1] * stride
	lsl x1, x1, #3
	# offset *= 8
	sub x2, x29, #184
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
	# índice 0 ← stack
	add sp, sp, #16
	mov x5, #2
	# stride = 2
	mul x4, x4, x5
	# idx[0] * stride
	add x1, x1, x4
	# offset += idx[0] * stride
	ldr x4, [sp]
	# índice 1 ← stack
	add sp, sp, #16
	add x1, x1, x4
	# offset += idx[1] * stride
	lsl x1, x1, #3
	# offset *= 8 (sizeof element)
	sub x2, x29, #184
	# dirección base del array matrizNoInit → x2
	sub x3, x2, x1
	# x3 = base - offset_dinámico
	str x0, [x3]
	# matrizNoInit[idx0][idx1] ← valor
	# string literal → x0 (puntero)
	adrp x0, .str_25
	add x0, x0, :lo12:.str_25
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
	# índice 0 ← stack
	add sp, sp, #16
	mov x5, #2
	# stride = 2
	mul x4, x4, x5
	# idx[0] * stride
	add x1, x1, x4
	# offset += idx[0] * stride
	ldr x4, [sp]
	# índice 1 ← stack
	add sp, sp, #16
	add x1, x1, x4
	# offset += idx[1] * stride
	lsl x1, x1, #3
	# offset *= 8
	sub x2, x29, #184
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
	adrp x0, .str_26
	add x0, x0, :lo12:.str_26
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	add sp, sp, #480
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