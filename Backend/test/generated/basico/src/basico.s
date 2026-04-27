.section .data
msg:
    .ascii "\n"
buffer:
    .ascii "0\n"
len = . - buffer

.str_0: .string "=== INICIO DE CALIFICACION: FUNCIONALIDADES BASICAS ==="
.str_1: .string "%s"
.str_2: .string "\n"
.str_3: .string "\n--- 1.1 DECLARACION LARGA ---"
.align 2
.flt_0: .single 3.14
.str_4: .string "Golampi"
.str_5: .string "%ld "
.str_6: .string "%g "
.str_7: .string "false"
.str_8: .string "true"
.str_9: .string "%s "
.str_10: .string "\n--- 1.2 ASIGNACION DE VARIABLES ---"
.align 2
.flt_1: .single 9.75
.str_11: .string "Actualizado"
.str_12: .string "\n--- 1.3 FORMATO DE IDENTIFICADORES ---"
.str_13: .string "Case sensitive:"
.str_14: .string "%ld"
.str_15: .string "\n--- 1.4 DECLARACION CORTA ---"
.align 2
.flt_2: .single 2.5
.str_16: .string "Inferencia"
.str_17: .string "\n--- 1.5 DECLARACION LARGA SIN INICIALIZAR ---"
.str_18: .string ""
.str_19: .string "\n--- 1.6 DECLARACION MULTIPLE ---"
.str_20: .string "Hola"
.str_21: .string "Mundo"
.str_22: .string "\n--- 1.7 CONSTANTES ---"
.align 2
.flt_3: .single 3.14159
.str_23: .string "\n--- 1.8 MANEJO DE NIL ---"
.str_24: .string "Impresion de nil:"
.str_25: .string "<nil>"
.str_26: .string "Comparacion nil == nil:"
.str_27: .string "\n--- 1.11 OPERACIONES ARITMETICAS ---"
.str_28: .string "+:"
.str_29: .string "-:"
.str_30: .string "*:"
.str_31: .string "/:"
.str_32: .string "%:"
.str_33: .string "\n--- 1.12 OPERACIONES RELACIONALES ---"
.str_34: .string "==:"
.str_35: .string "!=:"
.str_36: .string "<:"
.str_37: .string ">:"
.str_38: .string "\n--- 1.13 OPERACIONES LOGICAS ---"
.str_39: .string "true && false:"
.str_40: .string "true || false:"
.str_41: .string "!true:"
.str_42: .string "\n--- 1.14 CORTO CIRCUITO ---"
.str_43: .string "AND:"
.str_44: .string "OR:"
.str_45: .string "\n--- 1.15 OPERADORES DE ASIGNACION ---"
.str_46: .string "Resultado final:"
.str_47: .string "\n=== FIN DE CALIFICACION: FUNCIONALIDADES BASICAS ==="

.section .text
.align 2
.global _start

_start:
	stp x29, x30, [sp, #-16]!
	# guardar frame pointer y link register
	mov x29, sp
	# x29 = frame pointer (SP actual)
	sub sp, sp, #256
	# reservar 256 bytes para variables locales
	# frame.size=256, locals=31
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
	# var varInt int32 = expr
	mov x0, #42
	# int32 literal (64-bit per AArch64)
	str x0, [x29, #-8]
	# guardar int32
	# var varFloat float32 = expr
	# float32 literal 3.14 → s0
	adrp x9, .flt_0
	# página de la constante float
	ldr s0, [x9, :lo12:.flt_0]
	# cargar float32 IEEE-754 en s0
	str s0, [x29, #-16]
	# guardar float32
	# var varBool bool = expr
	mov x0, #1
	# bool true = 1 (64-bit per AArch64)
	str x0, [x29, #-24]
	# guardar bool
	# var varRune rune = expr
	mov x0, #71
	# rune 'G' = U+71 (64-bit per AArch64)
	str x0, [x29, #-32]
	# guardar rune
	# var varString string = expr
	# string literal → x0 (puntero)
	adrp x0, .str_4
	add x0, x0, :lo12:.str_4
	str x0, [x29, #-40]
	# guardar string
	ldr x0, [x29, #-8]
	# varInt (int32 - 64-bit)
	# fmt.Println arg 0 (int32)
	mov x1, x0
	# int32 → x1 para printf %ld
	adrp x0, .str_5
	add x0, x0, :lo12:.str_5
	bl printf
	ldr s0, [x29, #-16]
	# varFloat (float32)
	# fmt.Println arg 1 (float32)
	fcvt d0, s0
	# float32 a float64 para printf variadic
	fmov x1, d0
	# copiar float64 a x1 para variadic ABI
	adrp x0, .str_6
	add x0, x0, :lo12:.str_6
	bl printf
	ldr x0, [x29, #-24]
	# varBool (bool - 64-bit)
	# fmt.Println arg 2 (bool)
	cbnz x0, .bool_true_0
	# si true → imprimir "true"
	adrp x0, .str_7
	add x0, x0, :lo12:.str_7
	mov x1, x0
	# bool string → x1
	adrp x0, .str_9
	add x0, x0, :lo12:.str_9
	bl printf
	b .bool_done_1
.bool_true_0:
	adrp x0, .str_8
	add x0, x0, :lo12:.str_8
	mov x1, x0
	# bool string → x1
	adrp x0, .str_9
	add x0, x0, :lo12:.str_9
	bl printf
.bool_done_1:
	ldr x0, [x29, #-32]
	# varRune (rune - 64-bit)
	# fmt.Println arg 3 (rune)
	mov x1, x0
	# rune → x1 para printf %ld
	adrp x0, .str_5
	add x0, x0, :lo12:.str_5
	bl printf
	ldr x0, [x29, #-40]
	# varString (string - 64-bit)
	# fmt.Println arg 4 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
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
	# varInt = expr
	mov x0, #120
	# int32 literal (64-bit per AArch64)
	str x0, [x29, #-8]
	# guardar int32 (64-bit)
	# varFloat = expr
	# float32 literal 9.75 → s0
	adrp x9, .flt_1
	# página de la constante float
	ldr s0, [x9, :lo12:.flt_1]
	# cargar float32 IEEE-754 en s0
	str s0, [x29, #-16]
	# guardar float32
	# varBool = expr
	mov x0, xzr
	# bool false = 0 (64-bit per AArch64)
	str x0, [x29, #-24]
	# guardar bool (64-bit)
	# varRune = expr
	mov x0, #90
	# rune 'Z' = U+90 (64-bit per AArch64)
	str x0, [x29, #-32]
	# guardar rune (64-bit)
	# varString = expr
	# string literal → x0 (puntero)
	adrp x0, .str_11
	add x0, x0, :lo12:.str_11
	str x0, [x29, #-40]
	# guardar string (64-bit)
	ldr x0, [x29, #-8]
	# varInt (int32 - 64-bit)
	# fmt.Println arg 0 (int32)
	mov x1, x0
	# int32 → x1 para printf %ld
	adrp x0, .str_5
	add x0, x0, :lo12:.str_5
	bl printf
	ldr s0, [x29, #-16]
	# varFloat (float32)
	# fmt.Println arg 1 (float32)
	fcvt d0, s0
	# float32 a float64 para printf variadic
	fmov x1, d0
	# copiar float64 a x1 para variadic ABI
	adrp x0, .str_6
	add x0, x0, :lo12:.str_6
	bl printf
	ldr x0, [x29, #-24]
	# varBool (bool - 64-bit)
	# fmt.Println arg 2 (bool)
	cbnz x0, .bool_true_2
	# si true → imprimir "true"
	adrp x0, .str_7
	add x0, x0, :lo12:.str_7
	mov x1, x0
	# bool string → x1
	adrp x0, .str_9
	add x0, x0, :lo12:.str_9
	bl printf
	b .bool_done_3
.bool_true_2:
	adrp x0, .str_8
	add x0, x0, :lo12:.str_8
	mov x1, x0
	# bool string → x1
	adrp x0, .str_9
	add x0, x0, :lo12:.str_9
	bl printf
.bool_done_3:
	ldr x0, [x29, #-32]
	# varRune (rune - 64-bit)
	# fmt.Println arg 3 (rune)
	mov x1, x0
	# rune → x1 para printf %ld
	adrp x0, .str_5
	add x0, x0, :lo12:.str_5
	bl printf
	ldr x0, [x29, #-40]
	# varString (string - 64-bit)
	# fmt.Println arg 4 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# string literal → x0 (puntero)
	adrp x0, .str_12
	add x0, x0, :lo12:.str_12
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# var identificador int32 = expr
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	str x0, [x29, #-48]
	# guardar int32
	# var Identificador int32 = expr
	mov x0, #2
	# int32 literal (64-bit per AArch64)
	str x0, [x29, #-56]
	# guardar int32
	# string literal → x0 (puntero)
	adrp x0, .str_13
	add x0, x0, :lo12:.str_13
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_9
	add x0, x0, :lo12:.str_9
	bl printf
	ldr x0, [x29, #-48]
	# identificador (int32 - 64-bit)
	# fmt.Println arg 1 (int32)
	mov x1, x0
	# int32 → x1 para printf %ld
	adrp x0, .str_5
	add x0, x0, :lo12:.str_5
	bl printf
	ldr x0, [x29, #-56]
	# Identificador (int32 - 64-bit)
	# fmt.Println arg 2 (int32)
	mov x1, x0
	# int32 → x1 para printf %ld
	adrp x0, .str_14
	add x0, x0, :lo12:.str_14
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
	# cInt := expr (tipo inferido)
	mov x0, #7
	# int32 literal (64-bit per AArch64)
	str x0, [x29, #-64]
	# guardar int32 inferido (64-bit)
	# cFloat := expr (tipo inferido)
	# float32 literal 2.5 → s0
	adrp x9, .flt_2
	# página de la constante float
	ldr s0, [x9, :lo12:.flt_2]
	# cargar float32 IEEE-754 en s0
	str s0, [x29, #-72]
	# guardar float32 inferido
	# cBool := expr (tipo inferido)
	mov x0, #1
	# bool true = 1 (64-bit per AArch64)
	str x0, [x29, #-80]
	# guardar bool inferido (64-bit)
	# cRune := expr (tipo inferido)
	mov x0, #88
	# rune 'X' = U+88 (64-bit per AArch64)
	str x0, [x29, #-88]
	# guardar rune inferido (64-bit)
	# cString := expr (tipo inferido)
	# string literal → x0 (puntero)
	adrp x0, .str_16
	add x0, x0, :lo12:.str_16
	str x0, [x29, #-96]
	# guardar string inferido (64-bit)
	ldr x0, [x29, #-64]
	# cInt (int32 - 64-bit)
	# fmt.Println arg 0 (int32)
	mov x1, x0
	# int32 → x1 para printf %ld
	adrp x0, .str_5
	add x0, x0, :lo12:.str_5
	bl printf
	ldr s0, [x29, #-72]
	# cFloat (float32)
	# fmt.Println arg 1 (float32)
	fcvt d0, s0
	# float32 a float64 para printf variadic
	fmov x1, d0
	# copiar float64 a x1 para variadic ABI
	adrp x0, .str_6
	add x0, x0, :lo12:.str_6
	bl printf
	ldr x0, [x29, #-80]
	# cBool (bool - 64-bit)
	# fmt.Println arg 2 (bool)
	cbnz x0, .bool_true_4
	# si true → imprimir "true"
	adrp x0, .str_7
	add x0, x0, :lo12:.str_7
	mov x1, x0
	# bool string → x1
	adrp x0, .str_9
	add x0, x0, :lo12:.str_9
	bl printf
	b .bool_done_5
.bool_true_4:
	adrp x0, .str_8
	add x0, x0, :lo12:.str_8
	mov x1, x0
	# bool string → x1
	adrp x0, .str_9
	add x0, x0, :lo12:.str_9
	bl printf
.bool_done_5:
	ldr x0, [x29, #-88]
	# cRune (rune - 64-bit)
	# fmt.Println arg 3 (rune)
	mov x1, x0
	# rune → x1 para printf %ld
	adrp x0, .str_5
	add x0, x0, :lo12:.str_5
	bl printf
	ldr x0, [x29, #-96]
	# cString (string - 64-bit)
	# fmt.Println arg 4 (string)
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
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# var defInt int32 (valor por defecto)
	mov x0, xzr
	# int32 default = 0 (64-bit)
	str x0, [x29, #-104]
	# var defFloat float32 (valor por defecto)
	movi d0, #0
	# float32 default = 0.0
	str s0, [x29, #-112]
	# var defBool bool (valor por defecto)
	mov x0, xzr
	# bool default = false (64-bit)
	str x0, [x29, #-120]
	# var defRune rune (valor por defecto)
	mov x0, xzr
	# rune default = '\0' (64-bit)
	str x0, [x29, #-128]
	# var defString string (valor por defecto)
	adrp x0, .str_18
	# string default = ""
	add x0, x0, :lo12:.str_18
	str x0, [x29, #-136]
	ldr x0, [x29, #-104]
	# defInt (int32 - 64-bit)
	# fmt.Println arg 0 (int32)
	mov x1, x0
	# int32 → x1 para printf %ld
	adrp x0, .str_5
	add x0, x0, :lo12:.str_5
	bl printf
	ldr s0, [x29, #-112]
	# defFloat (float32)
	# fmt.Println arg 1 (float32)
	fcvt d0, s0
	# float32 a float64 para printf variadic
	fmov x1, d0
	# copiar float64 a x1 para variadic ABI
	adrp x0, .str_6
	add x0, x0, :lo12:.str_6
	bl printf
	ldr x0, [x29, #-120]
	# defBool (bool - 64-bit)
	# fmt.Println arg 2 (bool)
	cbnz x0, .bool_true_6
	# si true → imprimir "true"
	adrp x0, .str_7
	add x0, x0, :lo12:.str_7
	mov x1, x0
	# bool string → x1
	adrp x0, .str_9
	add x0, x0, :lo12:.str_9
	bl printf
	b .bool_done_7
.bool_true_6:
	adrp x0, .str_8
	add x0, x0, :lo12:.str_8
	mov x1, x0
	# bool string → x1
	adrp x0, .str_9
	add x0, x0, :lo12:.str_9
	bl printf
.bool_done_7:
	ldr x0, [x29, #-128]
	# defRune (rune - 64-bit)
	# fmt.Println arg 3 (rune)
	mov x1, x0
	# rune → x1 para printf %ld
	adrp x0, .str_5
	add x0, x0, :lo12:.str_5
	bl printf
	ldr x0, [x29, #-136]
	# defString (string - 64-bit)
	# fmt.Println arg 4 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
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
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# var m1 int32 = expr
	mov x0, #10
	# int32 literal (64-bit per AArch64)
	str x0, [x29, #-144]
	# guardar int32
	# var m2 int32 = expr
	mov x0, #20
	# int32 literal (64-bit per AArch64)
	str x0, [x29, #-152]
	# guardar int32
	# m3 := expr (tipo inferido)
	# string literal → x0 (puntero)
	adrp x0, .str_20
	add x0, x0, :lo12:.str_20
	str x0, [x29, #-160]
	# guardar string inferido (64-bit)
	# m4 := expr (tipo inferido)
	# string literal → x0 (puntero)
	adrp x0, .str_21
	add x0, x0, :lo12:.str_21
	str x0, [x29, #-168]
	# guardar string inferido (64-bit)
	ldr x0, [x29, #-144]
	# m1 (int32 - 64-bit)
	# fmt.Println arg 0 (int32)
	mov x1, x0
	# int32 → x1 para printf %ld
	adrp x0, .str_5
	add x0, x0, :lo12:.str_5
	bl printf
	ldr x0, [x29, #-152]
	# m2 (int32 - 64-bit)
	# fmt.Println arg 1 (int32)
	mov x1, x0
	# int32 → x1 para printf %ld
	adrp x0, .str_5
	add x0, x0, :lo12:.str_5
	bl printf
	ldr x0, [x29, #-160]
	# m3 (string - 64-bit)
	# fmt.Println arg 2 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_9
	add x0, x0, :lo12:.str_9
	bl printf
	ldr x0, [x29, #-168]
	# m4 (string - 64-bit)
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
	adrp x0, .str_22
	add x0, x0, :lo12:.str_22
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# const PI float32 = expr
	# float32 literal 3.14159 → s0
	adrp x9, .flt_3
	# página de la constante float
	ldr s0, [x9, :lo12:.flt_3]
	# cargar float32 IEEE-754 en s0
	str s0, [x29, #-176]
	# const PI (float32)
	# const MAX int32 = expr
	mov x0, #1000
	# int32 literal (64-bit per AArch64)
	str x0, [x29, #-184]
	# const MAX (int32)
	ldr s0, [x29, #-176]
	# PI (float32)
	# fmt.Println arg 0 (float32)
	fcvt d0, s0
	# float32 a float64 para printf variadic
	fmov x1, d0
	# copiar float64 a x1 para variadic ABI
	adrp x0, .str_6
	add x0, x0, :lo12:.str_6
	bl printf
	ldr x0, [x29, #-184]
	# MAX (int32 - 64-bit)
	# fmt.Println arg 1 (int32)
	mov x1, x0
	# int32 → x1 para printf %ld
	adrp x0, .str_14
	add x0, x0, :lo12:.str_14
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
	adrp x0, .str_9
	add x0, x0, :lo12:.str_9
	bl printf
	mov x0, xzr
	# nil = 0 (puntero nulo)
	# fmt.Println arg 1 (nil)
	adrp x0, .str_25
	add x0, x0, :lo12:.str_25
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
	adrp x0, .str_9
	add x0, x0, :lo12:.str_9
	bl printf
	mov x0, xzr
	# nil = 0 (puntero nulo)
	mov x0, xzr
	# nil = 0 (puntero nulo)
	mov x0, xzr
	# nil comparison → nil
	# fmt.Println arg 1 (nil)
	adrp x0, .str_25
	add x0, x0, :lo12:.str_25
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# string literal → x0 (puntero)
	adrp x0, .str_27
	add x0, x0, :lo12:.str_27
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
	adrp x0, .str_28
	add x0, x0, :lo12:.str_28
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_9
	add x0, x0, :lo12:.str_9
	bl printf
	mov x0, #15
	# int32 literal (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x0, #25
	# int32 literal (64-bit per AArch64)
	ldr x1, [sp]
	# lhs ← stack (int32)
	add sp, sp, #16
	add x0, x1, x0
	# fmt.Println arg 1 (int32)
	mov x1, x0
	# int32 → x1 para printf %ld
	adrp x0, .str_14
	add x0, x0, :lo12:.str_14
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# string literal → x0 (puntero)
	adrp x0, .str_29
	add x0, x0, :lo12:.str_29
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_9
	add x0, x0, :lo12:.str_9
	bl printf
	mov x0, #50
	# int32 literal (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x0, #18
	# int32 literal (64-bit per AArch64)
	ldr x1, [sp]
	# lhs ← stack (int32)
	add sp, sp, #16
	sub x0, x1, x0
	# fmt.Println arg 1 (int32)
	mov x1, x0
	# int32 → x1 para printf %ld
	adrp x0, .str_14
	add x0, x0, :lo12:.str_14
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# string literal → x0 (puntero)
	adrp x0, .str_30
	add x0, x0, :lo12:.str_30
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_9
	add x0, x0, :lo12:.str_9
	bl printf
	mov x0, #7
	# int32 literal (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x0, #8
	# int32 literal (64-bit per AArch64)
	ldr x1, [sp]
	# lhs ← stack (int32)
	add sp, sp, #16
	mul x0, x1, x0
	# int32 mul
	# fmt.Println arg 1 (int32)
	mov x1, x0
	# int32 → x1 para printf %ld
	adrp x0, .str_14
	add x0, x0, :lo12:.str_14
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# string literal → x0 (puntero)
	adrp x0, .str_31
	add x0, x0, :lo12:.str_31
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_9
	add x0, x0, :lo12:.str_9
	bl printf
	mov x0, #100
	# int32 literal (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x0, #3
	# int32 literal (64-bit per AArch64)
	ldr x1, [sp]
	# lhs ← stack (int32)
	add sp, sp, #16
	sdiv x0, x1, x0
	# int32 div (signed)
	# fmt.Println arg 1 (int32)
	mov x1, x0
	# int32 → x1 para printf %ld
	adrp x0, .str_14
	add x0, x0, :lo12:.str_14
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# string literal → x0 (puntero)
	adrp x0, .str_32
	add x0, x0, :lo12:.str_32
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_9
	add x0, x0, :lo12:.str_9
	bl printf
	mov x0, #17
	# int32 literal (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x0, #5
	# int32 literal (64-bit per AArch64)
	ldr x1, [sp]
	# lhs ← stack (int32)
	add sp, sp, #16
	sdiv x2, x1, x0
	# x2 = lhs / rhs (cociente int32)
	msub x0, x2, x0, x1
	# x0 = lhs - cociente * rhs (resto int32)
	# fmt.Println arg 1 (int32)
	mov x1, x0
	# int32 → x1 para printf %ld
	adrp x0, .str_14
	add x0, x0, :lo12:.str_14
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# string literal → x0 (puntero)
	adrp x0, .str_33
	add x0, x0, :lo12:.str_33
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# var r1 int32 = expr
	mov x0, #10
	# int32 literal (64-bit per AArch64)
	str x0, [x29, #-192]
	# guardar int32
	# var r2 int32 = expr
	mov x0, #20
	# int32 literal (64-bit per AArch64)
	str x0, [x29, #-200]
	# guardar int32
	# string literal → x0 (puntero)
	adrp x0, .str_34
	add x0, x0, :lo12:.str_34
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_9
	add x0, x0, :lo12:.str_9
	bl printf
	ldr x0, [x29, #-192]
	# r1 (int32 - 64-bit)
	mov x1, x0
	ldr x0, [x29, #-200]
	# r2 (int32 - 64-bit)
	cmp x1, x0
	# comparar x1(lhs) vs x0(rhs) - flags setup
	cset x0, eq
	# materializar resultado bool en x0
	# fmt.Println arg 1 (bool)
	cbnz x0, .bool_true_8
	# si true → imprimir "true"
	adrp x0, .str_7
	add x0, x0, :lo12:.str_7
	mov x1, x0
	# bool string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	b .bool_done_9
.bool_true_8:
	adrp x0, .str_8
	add x0, x0, :lo12:.str_8
	mov x1, x0
	# bool string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
.bool_done_9:
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# string literal → x0 (puntero)
	adrp x0, .str_35
	add x0, x0, :lo12:.str_35
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_9
	add x0, x0, :lo12:.str_9
	bl printf
	ldr x0, [x29, #-192]
	# r1 (int32 - 64-bit)
	mov x1, x0
	ldr x0, [x29, #-200]
	# r2 (int32 - 64-bit)
	cmp x1, x0
	# comparar x1(lhs) vs x0(rhs) - flags setup
	cset x0, ne
	# materializar resultado bool en x0
	# fmt.Println arg 1 (bool)
	cbnz x0, .bool_true_10
	# si true → imprimir "true"
	adrp x0, .str_7
	add x0, x0, :lo12:.str_7
	mov x1, x0
	# bool string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	b .bool_done_11
.bool_true_10:
	adrp x0, .str_8
	add x0, x0, :lo12:.str_8
	mov x1, x0
	# bool string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
.bool_done_11:
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# string literal → x0 (puntero)
	adrp x0, .str_36
	add x0, x0, :lo12:.str_36
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_9
	add x0, x0, :lo12:.str_9
	bl printf
	ldr x0, [x29, #-192]
	# r1 (int32 - 64-bit)
	mov x1, x0
	ldr x0, [x29, #-200]
	# r2 (int32 - 64-bit)
	cmp x1, x0
	# comparar x1(lhs) vs x0(rhs) - flags setup
	cset x0, lt
	# materializar resultado bool en x0
	# fmt.Println arg 1 (bool)
	cbnz x0, .bool_true_12
	# si true → imprimir "true"
	adrp x0, .str_7
	add x0, x0, :lo12:.str_7
	mov x1, x0
	# bool string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	b .bool_done_13
.bool_true_12:
	adrp x0, .str_8
	add x0, x0, :lo12:.str_8
	mov x1, x0
	# bool string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
.bool_done_13:
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# string literal → x0 (puntero)
	adrp x0, .str_37
	add x0, x0, :lo12:.str_37
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_9
	add x0, x0, :lo12:.str_9
	bl printf
	ldr x0, [x29, #-192]
	# r1 (int32 - 64-bit)
	mov x1, x0
	ldr x0, [x29, #-200]
	# r2 (int32 - 64-bit)
	cmp x1, x0
	# comparar x1(lhs) vs x0(rhs) - flags setup
	cset x0, gt
	# materializar resultado bool en x0
	# fmt.Println arg 1 (bool)
	cbnz x0, .bool_true_14
	# si true → imprimir "true"
	adrp x0, .str_7
	add x0, x0, :lo12:.str_7
	mov x1, x0
	# bool string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	b .bool_done_15
.bool_true_14:
	adrp x0, .str_8
	add x0, x0, :lo12:.str_8
	mov x1, x0
	# bool string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
.bool_done_15:
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# string literal → x0 (puntero)
	adrp x0, .str_38
	add x0, x0, :lo12:.str_38
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# var t bool = expr
	mov x0, #1
	# bool true = 1 (64-bit per AArch64)
	str x0, [x29, #-208]
	# guardar bool
	# var f bool = expr
	mov x0, xzr
	# bool false = 0 (64-bit per AArch64)
	str x0, [x29, #-216]
	# guardar bool
	# string literal → x0 (puntero)
	adrp x0, .str_39
	add x0, x0, :lo12:.str_39
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_9
	add x0, x0, :lo12:.str_9
	bl printf
	ldr x0, [x29, #-208]
	# t (bool - 64-bit)
	cbz x0, .and_end_16
	# cortocircuito AND: si false → saltar
	ldr x0, [x29, #-216]
	# f (bool - 64-bit)
.and_end_16:
	cmp x0, #0
	cset x0, ne
	# bool resultado AND
	# fmt.Println arg 1 (bool)
	cbnz x0, .bool_true_17
	# si true → imprimir "true"
	adrp x0, .str_7
	add x0, x0, :lo12:.str_7
	mov x1, x0
	# bool string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	b .bool_done_18
.bool_true_17:
	adrp x0, .str_8
	add x0, x0, :lo12:.str_8
	mov x1, x0
	# bool string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
.bool_done_18:
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# string literal → x0 (puntero)
	adrp x0, .str_40
	add x0, x0, :lo12:.str_40
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_9
	add x0, x0, :lo12:.str_9
	bl printf
	ldr x0, [x29, #-208]
	# t (bool - 64-bit)
	cbnz x0, .or_end_19
	# cortocircuito OR: si true → saltar
	ldr x0, [x29, #-216]
	# f (bool - 64-bit)
.or_end_19:
	cmp x0, #0
	cset x0, ne
	# bool resultado OR
	# fmt.Println arg 1 (bool)
	cbnz x0, .bool_true_20
	# si true → imprimir "true"
	adrp x0, .str_7
	add x0, x0, :lo12:.str_7
	mov x1, x0
	# bool string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	b .bool_done_21
.bool_true_20:
	adrp x0, .str_8
	add x0, x0, :lo12:.str_8
	mov x1, x0
	# bool string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
.bool_done_21:
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# string literal → x0 (puntero)
	adrp x0, .str_41
	add x0, x0, :lo12:.str_41
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_9
	add x0, x0, :lo12:.str_9
	bl printf
	ldr x0, [x29, #-208]
	# t (bool - 64-bit)
	eor x0, x0, #1
	# NOT lógico
	# fmt.Println arg 1 (bool)
	cbnz x0, .bool_true_22
	# si true → imprimir "true"
	adrp x0, .str_7
	add x0, x0, :lo12:.str_7
	mov x1, x0
	# bool string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	b .bool_done_23
.bool_true_22:
	adrp x0, .str_8
	add x0, x0, :lo12:.str_8
	mov x1, x0
	# bool string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
.bool_done_23:
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# string literal → x0 (puntero)
	adrp x0, .str_42
	add x0, x0, :lo12:.str_42
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# var divisor int32 = expr
	mov x0, xzr
	# int32 literal 0 (64-bit per AArch64)
	str x0, [x29, #-224]
	# guardar int32
	# ccAnd := expr (tipo inferido)
	mov x0, xzr
	# bool false = 0 (64-bit per AArch64)
	cbz x0, .and_end_24
	# cortocircuito AND: si false → saltar
	mov x0, #100
	# int32 literal (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	ldr x0, [x29, #-224]
	# divisor (int32 - 64-bit)
	ldr x1, [sp]
	# lhs ← stack (int32)
	add sp, sp, #16
	sdiv x0, x1, x0
	# int32 div (signed)
	mov x1, x0
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	cmp x1, x0
	# comparar x1(lhs) vs x0(rhs) - flags setup
	cset x0, eq
	# materializar resultado bool en x0
.and_end_24:
	cmp x0, #0
	cset x0, ne
	# bool resultado AND
	str x0, [x29, #-232]
	# guardar bool inferido (64-bit)
	# ccOr := expr (tipo inferido)
	mov x0, #1
	# bool true = 1 (64-bit per AArch64)
	cbnz x0, .or_end_25
	# cortocircuito OR: si true → saltar
	mov x0, #100
	# int32 literal (64-bit per AArch64)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	ldr x0, [x29, #-224]
	# divisor (int32 - 64-bit)
	ldr x1, [sp]
	# lhs ← stack (int32)
	add sp, sp, #16
	sdiv x0, x1, x0
	# int32 div (signed)
	mov x1, x0
	mov x0, #1
	# int32 literal (64-bit per AArch64)
	cmp x1, x0
	# comparar x1(lhs) vs x0(rhs) - flags setup
	cset x0, eq
	# materializar resultado bool en x0
.or_end_25:
	cmp x0, #0
	cset x0, ne
	# bool resultado OR
	str x0, [x29, #-240]
	# guardar bool inferido (64-bit)
	# string literal → x0 (puntero)
	adrp x0, .str_43
	add x0, x0, :lo12:.str_43
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_9
	add x0, x0, :lo12:.str_9
	bl printf
	ldr x0, [x29, #-232]
	# ccAnd (bool - 64-bit)
	# fmt.Println arg 1 (bool)
	cbnz x0, .bool_true_26
	# si true → imprimir "true"
	adrp x0, .str_7
	add x0, x0, :lo12:.str_7
	mov x1, x0
	# bool string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	b .bool_done_27
.bool_true_26:
	adrp x0, .str_8
	add x0, x0, :lo12:.str_8
	mov x1, x0
	# bool string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
.bool_done_27:
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# string literal → x0 (puntero)
	adrp x0, .str_44
	add x0, x0, :lo12:.str_44
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_9
	add x0, x0, :lo12:.str_9
	bl printf
	ldr x0, [x29, #-240]
	# ccOr (bool - 64-bit)
	# fmt.Println arg 1 (bool)
	cbnz x0, .bool_true_28
	# si true → imprimir "true"
	adrp x0, .str_7
	add x0, x0, :lo12:.str_7
	mov x1, x0
	# bool string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	b .bool_done_29
.bool_true_28:
	adrp x0, .str_8
	add x0, x0, :lo12:.str_8
	mov x1, x0
	# bool string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
.bool_done_29:
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# string literal → x0 (puntero)
	adrp x0, .str_45
	add x0, x0, :lo12:.str_45
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# var asig int32 = expr
	mov x0, #50
	# int32 literal (64-bit per AArch64)
	str x0, [x29, #-248]
	# guardar int32
	# asig += expr
	ldr x0, [x29, #-248]
	# cargar int32 (64-bit)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x0, #10
	# int32 literal (64-bit per AArch64)
	ldr x1, [sp]
	# lhs ← stack
	add sp, sp, #16
	add x0, x1, x0
	# int32 suma
	str x0, [x29, #-248]
	# guardar int32 (64-bit)
	# asig -= expr
	ldr x0, [x29, #-248]
	# cargar int32 (64-bit)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x0, #5
	# int32 literal (64-bit per AArch64)
	ldr x1, [sp]
	# lhs ← stack
	add sp, sp, #16
	sub x0, x1, x0
	# int32 resta
	str x0, [x29, #-248]
	# guardar int32 (64-bit)
	# asig *= expr
	ldr x0, [x29, #-248]
	# cargar int32 (64-bit)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x0, #2
	# int32 literal (64-bit per AArch64)
	ldr x1, [sp]
	# lhs ← stack
	add sp, sp, #16
	mul x0,x1, x0
	# int32 mul
	str x0, [x29, #-248]
	# guardar int32 (64-bit)
	# asig /= expr
	ldr x0, [x29, #-248]
	# cargar int32 (64-bit)
	sub sp, sp, #16
	# reservar slot temporal
	str x0, [sp]
	# x0 → stack temporal (int32)
	mov x0, #5
	# int32 literal (64-bit per AArch64)
	ldr x1, [sp]
	# lhs ← stack
	add sp, sp, #16
	sdiv x0, x1, x0
	# int32 div (con signo)
	str x0, [x29, #-248]
	# guardar int32 (64-bit)
	# string literal → x0 (puntero)
	adrp x0, .str_46
	add x0, x0, :lo12:.str_46
	# fmt.Println arg 0 (string)
	mov x1, x0
	# ptr string → x1
	adrp x0, .str_9
	add x0, x0, :lo12:.str_9
	bl printf
	ldr x0, [x29, #-248]
	# asig (int32 - 64-bit)
	# fmt.Println arg 1 (int32)
	mov x1, x0
	# int32 → x1 para printf %ld
	adrp x0, .str_14
	add x0, x0, :lo12:.str_14
	bl printf
	adrp x0, .str_2
	add x0, x0, :lo12:.str_2
	bl printf
	# string literal → x0 (puntero)
	adrp x0, .str_47
	add x0, x0, :lo12:.str_47
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
	add sp, sp, #256
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